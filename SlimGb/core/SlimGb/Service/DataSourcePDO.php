<?php
class SlimGb_Service_DataSourcePDO implements SlimGb_Service_DataSource
{
	private $pdo;
	
	public function __construct($dsn, $user, $password, $options)
	{
		$this->pdo = new PDO($dsn, $user, $password, $options);
		SlimGb_DateTime::$defaultFormat = 'Y-m-d H:i:s';
	}
	/**
	 * @param array $columns
	 */
	public function addColumns(array $columns) {
		//TODO: implement, needed by plugins
	}

	/**
	 * @param string $resource table name (NEVER COMES FROM USER INPUT!)
	 * @param int $id
	 */
	public function delete($resource, $id) {
		$stmt = $this->pdo->prepare("DELETE FROM `$resource` WHERE `id`=?");
		$stmt->execute(array($id));
	}

	/**
	 * @param string $resource table name (NEVER COMES FROM USER INPUT!)
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function fetch($resource, $offset, $limit) {
		$stmt = $this->pdo->prepare("SELECT * FROM `$resource` WHERE 1 LIMIT ?,?");
		$stmt->execute(array($offset, $limit));
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @param string $resource table name (NEVER COMES FROM USER INPUT!)
	 * @return int
	 */
	public function count($resource)
	{
		$stmt = $this->pdo->query("SELECT COUNT(*) FROM `$resource` WHERE 1");
		return $stmt->fetchColumn(0);
	}
	/**
	 * 
	 */
	public function getColumns($resource) {
		$stmt = $this->pdo->query("SHOW COLUMNS FROM `$resource`");
		$result = array();
		while($col = $stmt->fetchColumn(0)) {
			$result[] = $col;
		}
		return $result;
	}

	/**
	 * @param string $resource table name (NEVER COMES FROM USER INPUT)
	 * @param array $data
	 */
	public function insert($resource, array $data) {
		if (empty($data)) {
			throw new InvalidArgumentException('Cannot insert empty dataset');
		}
		$columns = $this->columnsFromData($data);
		$values = $this->valuesFromData($data);
		$stmt = $this->pdo->prepare("INSERT INTO `$resource` ($columns) VALUES ($values)");
		$stmt->execute($data);
	}

	/**
	 * @param string $resource table name (NEVER COMES FROM USER INPUT!)
	 * @param int $id
	 * @param array $data
	 */
	public function update($resource, $id, $data) {
		if (empty($data)) {
			throw new InvalidArgumentException('Cannot insert empty dataset');
		}
		$columns = $this->columnsFromData($data);
		$values = $this->valuesFromData($data);
		$stmt = $this->pdo->query("REPLACE INTO `$resource` (`id`,$columns) VALUES (?,$values)");
		$stmt->execute(array($id) + $data);
	}
	
	private function columnsFromData($data)
	{
		return '`' . join('`,`', array_keys($data)) . '`';
	}

	private function valuesFromData($data)
	{
		return join(',', array_fill(1, count($data), '?'));
	}
}