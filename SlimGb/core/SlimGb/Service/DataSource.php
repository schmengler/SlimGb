<?php
interface SlimGb_Service_DataSource
{
	/**
	 * Creates a new resource with the field definitions in $columns
	 * 
	 * @param string $name
	 * @param SlimGb_FieldDefinition[] $columns
	 */
	public function createResource($name, array $columns);
	/**
	 * @param string $resource i.E. a database table
	 * @return string[] column names in $resource
	 */
	public function getColumns($resource);

	/**
	 * Adds new columns to a resource, following the field definitions in $columns
	 * 
	 * @param string $resource i.E. a database table
	 * @param SlimGb_FieldDefinition[] $columns
	 */
	public function addColumns($resource, array $columns);

	/**
	 * Returns a subset of $resource (simple selection, no additional criteria or ordering)
	 * 
	 * @param string $resource i.E. a database table
	 * @param int $offset Offset
	 * @param int $limit maximum number of datasets
	 * @return array Array with unique entry IDs as keys and array represantation of datasets as values
	 * @todo Diese Anforderung wird momentan gar nicht erfüllt (id als key), also aufgepasst bei implementierung von l�schen und bearbeiten
	 */
	public function fetch($resource, $offset, $limit);
	
	/**
	 * Returns number of datasets in $resource
	 * 
	 * @param string $resource i.E. a database table
	 * @return int total number of datasets
	 */
	public function count($resource);
	
	/**
	 * Inserts a new dataset
	 * 
	 * @param string $resource i.E. a database table
	 * @param array $data associative array [ column : value ]
	 */
	public function insert($resource, array $data);
	
	/**
	 * Deletes a single dataset
	 * 
	 * @param string $resource i.E. a database table
	 * @param scalar $id unique identifier
	 */
	public function delete($resource, $id);
	
	/**
	 * Updates a single dataset
	 * 
	 * @param string $resource i.E. a database table
	 * @param scalar $id unique identifier
	 * @param array $data new data as associative array [ column : value ]
	 */
	public function update($resource, $id, $data);
}