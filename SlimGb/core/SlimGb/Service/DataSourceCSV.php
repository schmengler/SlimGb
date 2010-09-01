<?php
class SlimGb_Service_DataSourceCSV implements SlimGb_Service_DataSource
{
	private $delimiter = ';';
	private $enclosure = '"';
	
	private $lineLength;
	private $columns = array();
	private $counts = array();
	
	/**
	 * @param SlimGb_Service_Config $config
	 */
	public function __construct(SlimGb_Service_Config $config)
	{
		//TODO: determine nessecary line length from plugins
		// 150: reserved for time and other data
		// 4: utf-8 max. bytes/char
		$this->lineLength = ($config['entries']['max_length'] + $config['entries']['author_max_length'] + 150) * 4;
	}
	
	/**
	 * @param string $resource
	 * @param SlimGb_FieldDefinition[] $columns
	 */
	public function addColumns($resource, array $columns) {
		//TODO: implement; nessecary for installing plugins
	}

	/**
	 * @param string $resource
	 * @return int
	 */
	public function count($resource) {
		if(isset($this->count[$resource])) {
			return $this->count[$resource];
		}
		$file = $this->open($resource, 'r');
		$lines = 0;
		while(!feof($file)) {
			if (fgetcsv($file, $this->lineLength, $this->delimiter, $this->enclosure)) {  // ignore last empty line
				++ $lines;
			}
		}
		fclose($file);
		$this->count[$resource] = $lines - 1;
		return $this->count[$resource];
	}

	/**
	 * @param string $resource
	 * @param scalar $id
	 */
	public function delete($resource, $id) {
		//TODO: implement
	}

	/**
	 * @param string $resource
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function fetch($resource, $offset, $limit) {
		$file = $this->open($resource, 'r');
		$current = $this->count($resource);
		fgetcsv($file, $this->lineLength, $this->delimiter, $this->enclosure); // ignore first line
		while(!feof($file) && $current > $offset + $limit) {
			fgetcsv($file,$this->lineLength, $this->delimiter, $this->enclosure);
			-- $current;
		}
		$result = array();
		while(!feof($file) && $current > $offset) {
			$line = fgetcsv($file, $this->lineLength, $this->delimiter, $this->enclosure);
			if ($line) { // ignore last empty line
				$result[] = array('id' => self::idFromLine($line)) + array_combine($this->getColumns($resource), $line);
			}
			-- $current;
		}
		fclose($file);
		return array_reverse($result);
	}

	/**
	 * @param string $resource
	 * @return array
	 */
	public function getColumns($resource) {
		if(isset($this->columns[$resource])) {
			return $this->columns[$resource];
		}
		$file = $this->open($resource, 'r');
		$line = fgetcsv($file, $this->lineLength, $this->delimiter, $this->enclosure);
		if ($line===false) {
			throw new SlimGb_SetupException(__CLASS__ . ': ' . $this->filename($resource) . ' not initialized properly.');
		}
		fclose($file);
		$this->columns = $line;
		return $this->columns;
	}

	/**
	 * @param string $resource
	 * @param array $data
	 */
	public function insert($resource, array $data) {
		$file = $this->open($resource, 'a');
		$keys = array_flip($this->getColumns($resource));
		uksort($data, create_function('$a,$b', '
			static $keys = ' . var_export($keys, true) . ';
			return $keys[$a]-$keys[$b];'
		));
		fputcsv($file, $data, $this->delimiter, $this->enclosure);
		fclose($file);
	}

	/**
	 * @param string $resource
	 * @param scalar $id
	 * @param array $data
	 */
	public function update($resource, $id, $data) {
		//TODO: implement
	}

	/**
	 * @param string $resource NEVER COMES FROM USER INPUT!
	 */
	private function filename($resource)
	{
		return SLIMGB_BASEPATH . '/runtime/data/' . $resource . '.csv';
	}

	/**
	 * Opens the file corresponding to $resource and returns the file resource from fopen()
	 * 
	 * @param string $resource
	 * @param string $mode
	 * @return resource
	 */
	private function open($resource, $mode)
	{
		$filename = $this->filename($resource);
		if (!file_exists($filename)) {
			throw new SlimGb_SetupException(__CLASS__ . ': ' . $filename . ' not found.');
		}
		$file = fopen($filename, $mode);
		if (!is_resource($file)) {
			throw new SlimGb_SetupException(__CLASS__ . ': ' . $filename . ' could not be opened.');
		}
		return $file;
	}

	/**
	 * Calculates a well-defined ID for a line of data
	 * 
	 * @param array $line
	 * @return string
	 */
	private static function idFromLine($line)
	{
		//hash author and time
		//TODO: more searchable id format
		return md5($line[0] . $line[1]);
	}
}