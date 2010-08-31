<?php
//TODO: alles
class SlimGb_Service_ViewRss implements SlimGb_Service_View
{
	/**
	 * @param unknown_type $var
	 */
	public function __get($var) {
		
	}

	/**
	 * @param unknown_type $var
	 * @param unknown_type $value
	 */
	public function __set($var, $value) {
		
	}

	/**
	 * @param OutputFilterWrapperInterface $fw
	 */
	public function appendOutputFilterWrapper(OutputFilterWrapperInterface $fw) {
		
	}

	/**
	 * @param OutputFilterWrapperInterface $fw
	 */
	public function prependOutputFilterWrapper(OutputFilterWrapperInterface $fw) {
		
	}

	/**
	 * @param unknown_type $page
	 */
	public function render($page) {
		// is natürlich quatsch so, das hier ist nur runtergeschrieben, was render('entries') im prinzip machen wird:
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->formatOutput = true;

		$root = $doc->createElement('rss');
		$root->setAttribute('version', '2.0');
		$doc->appendChild($root);
		
		$channel = $doc->createElement('channel');
		$root->appendChild($channel);

		$channel->appendChild($doc->createElement(
			'title', utf8_encode('$config[application][title]')
		));
		$channel->appendChild($doc->createElement(
			'description', utf8_encode('Neue Gästebucheinträge')
		));
		$channel->appendChild($doc->createElement(
			'language', utf8_encode('de')
		));
		$channel->appendChild($doc->createElement(
			'link', utf8_encode('$config[application][link]')
		));
		$channel->appendChild($doc->createElement(
			'lastBuildDate', utf8_encode(date("D, j M Y H:i:s ").'GMT')
		));

		foreach($this->entries as $entry)
		{
			$item = $doc->createElement('item');
			$channel->appendChild($item);
		}
/*
    $dat = $xml->createElement('title',
        utf8_encode('Titel der Nachricht'));
    $itm->appendChild($dat);
 

      $dat = $xml->createElement('description',
        utf8_encode('Die Nachricht an sich'));
    $itm->appendChild($dat);   
 
    $dat = $xml->createElement('link',
        htmlentities('Der Link zur Nachricht'));
    $itm->appendChild($dat);
 
    $dat = $xml->createElement('pubDate',
        utf8_encode('Datum der Nachricht'));
    $itm->appendChild($dat);
 
    $dat = $xml->createElement('guid',
        htmlentities('Einzigartige ID'));
    $itm->appendChild($dat);
*/
}