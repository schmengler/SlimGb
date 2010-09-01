<?php
class SlimGb_Service_ViewHtml implements SlimGb_Service_View
{
	private static $startTemplateToken = '<!-- BEGIN TEMPLATE -->';
	private static $endTemplateToken = '<!-- END TEMPLATE -->';
	private static $startPhpToken = '__PHP_START__';
	private static $endPhpToken = '__PHP_END__';
	
	/**
	 * @var array|FilteredArray
	 */
	private $data = array();
	
	/**
	 * @var sfEventDispatcher
	 */
	private $eventDispatcher;
	
	/**
	 * @var OutputFilterWrapperChain
	 */
	private $outputFilterWrapperChain;

	/**
	 * @param sfEventDispatcher $ed
	 */
	public function __construct(sfEventDispatcher $ed, OutputFilterWrapperChain $fw)
	{
		$this->eventDispatcher = $ed;
		$this->outputFilterWrapperChain = $fw;
	}
	
	/**
	 * @param scalar $var
	 */
	public function __get($var) {
		if (!isset($this->data[$var])) {
			return null;
		}
		return $this->data[$var];
	}

	/**
	 * @param scalar $var
	 * @param mixed $value
	 */
	public function __set($var, $value) {
		$this->data[$var] = $this->outputFilterWrapperChain->filterRecursive($value);
	}
	/**
	 * @param scalar $var
	 */
	public function __isset($var)
	{
		return isset($this->data[$var]);
	}

	/**
	 * @param OutputFilterWrapperInterface $fw
	 */
	public function appendOutputFilterWrapper(OutputFilterWrapperInterface $fw) {
		$this->outputFilterWrapperChain->pushWrapper($fw);
	}

	/**
	 * @param OutputFilterWrapperInterface $fw
	 */
	public function prependOutputFilterWrapper(OutputFilterWrapperInterface $fw) {
		$this->outputFilterWrapperChain->prependWrapper($fw);
	}

	/**
	 * @param string $page
	 */
	public function render($page) {
		/* Problem hier: $this->data vs. $data
		 * Frage: ist ein zus�tzliches Filter-Event nötig? Es gibt ja für die
		 * Attribute den Outputfilter
		$event = new sfEvent($this, 'view.filter_before_render');
		$this->eventDispatcher->filter($event, $this->data);
		$data = $event->getReturnValue();
		// YAGNI!
		*/
		
		ob_start();
		$this->includePage($page);
		
		/*
		 * Und hier: wozu in aller welt ein afterrender-event? Änderungen am gesamtbild sollte
		 * manipulatedom machen... andererseits würde ich mir vorbehalten, t,b. tidy hier einzuhängen
		$rendered = ob_get_clean();
		$event = new sfEvent($this, 'view.filter_after_render');
		$this->eventDispatcher->filter($event, $rendered);
		return $event->getReturnValue();
		// YAGNI?
		// YAGNI! diese filter wären v.a. abhängig von der view-implementierung
		 */
		return ob_get_clean();
	}
	
	private function includePage($page)
	{
		$originalFile = SLIMGB_BASEPATH . "/templates/$page.phtml";
		$compiledFile = SLIMGB_BASEPATH . "/runtime/templates/$page.phtml";
		if (!file_exists($compiledFile)) {
			$doc = self::loadTemplate($originalFile);
			$event = new sfEvent($this, 'view.manipulate_dom', array('dom' => $doc));
			$this->eventDispatcher->notify($event);
			self::saveTemplate($doc, $compiledFile);
		}
		include $compiledFile;
	}
	
	/**
	 * Loads template file into a DOMDocument
	 * 
	 * To preserve <?php ?> blocks, they are marked as CDATA. Also the template
	 * is enclosed in comments marking start and end because DOMDOcument renders
	 * a full document but we only need a fragment.
	 * 
	 * @param string $file Filename of original template
	 * @return DOMDocument
	 */
	private static function loadTemplate($file)
	{
		$html = '<div>' . self::$startTemplateToken; // <div> notwendig, da der Kommentar (startTemplateToken) sonst vor hinzugef�gtem <html> Tag erscheint
		$html .= self::encodePhp(strtr(file_get_contents($file), array(
			self::$startTemplateToken => '',
			self::$endTemplateToken   => '',
			self::$startPhpToken      => '',
			self::$endPhpToken        => '',
		)));
		$html .= self::$endTemplateToken . '</div>';
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->loadHTML($html);
		return $doc;
	}
	
	/**
	 * Saves modified DOMDocument as compiled template file
	 * 
	 * <?php ?> blocks are beeing restored and everything outside the start and
	 * end markers gets stripped.
	 * 
	 * @param DOMDocument $doc
	 * @param string $file Filename of compiled template
	 */
	private static function saveTemplate(DOMDocument $doc, $file)
	{
		$modified = $doc->saveHTML();
		$modified = substr($modified, strpos($modified, self::$startTemplateToken) + strlen(self::$startTemplateToken));
		$modified = substr($modified, 0, strpos($modified, self::$endTemplateToken));
		$modified = self::decodePhp($modified);
		if (!is_dir(dirname($file))) {
			mkdir(dirname($file), 0777, true);
		}
		file_put_contents($file, $modified);
		chmod($file, 0777);
	}
	
	private static function encodePhp($string)
	{
		return preg_replace(
			'/<\?php(.*?)\?>/ise',
			'\'' . self::$startPhpToken . '\' . base64_encode(\'$1\') . \'' . self::$endPhpToken . '\'',
			$string
		);
	}
	
	private static function decodePhp($string)
	{
		return preg_replace(
			'/' . self::$startPhpToken . '(.*?)' . self::$endPhpToken . '/e',
			'\'<?php\' . base64_decode(\'$1\') . \'?>\'',
			$string
		);
	}

}