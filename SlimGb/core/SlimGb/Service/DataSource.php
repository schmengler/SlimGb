<?php
interface SlimGb_Service_DataSource
{
	public function getColumns($resource);

	public function addColumns(array $columns);

	/**
	 * @param string $resource i.E. a database table
	 * @param int $offset
	 * @param int $limit
	 * @return array Array with unique entry IDs as keys and array represantation of entry as values
	 */
	public function fetch($resource, $offset, $limit);
	
	/**
	 * @param string $resource
	 * @return int
	 */
	public function count($resource);
	
	public function insert($resource, array $data);
	
	public function delete($resource, $id);
	
	public function update($resource, $id, $data);
}