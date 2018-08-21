<?php
namespace DbViewer\DbData;

abstract class DbAbstract
{
	public function connct($config){}

	public function findAll($query, $dbName){}

	public function getTables(){}

}