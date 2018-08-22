<?php
namespace DbViewer\Task;

/**
 * 数据库数据接口
 * @author  wangyelou <[<email address>]>
 * @date 2018-08-22
 */
class Dbtask
{
	/**
	 * 获取数库名
	 * @return [type]         [description]
	 */
	public function dbName()
	{
		$param = $GLOBALS['param'];
		if (!isset($param['type']) || !isset(DBCONFIGS[$param['type']])) {
			throw new \Exception('', -100);
		}

		$handler = $this->publicMethod();
		return $handler->getDbs();
	}

	/**
	 * 获取表名
	 * @return [type]         [description]
	 */
	public function tableName()
	{
		$param = $GLOBALS['param'];
		if (!isset($param['type']) || !isset($param['dbName']) || !isset(DBCONFIGS[$param['type']])) {
			throw new \Exception('', -100);
		}

		$handler = $this->publicMethod();
		return $handler->getTables($param['dbName']);
	}

	/**
	 * 获取数据
	 * @return [type] [description]
	 */
	public function getData()
	{
		$param = $GLOBALS['param'];
		if (!isset($param['type']) || !isset($param['dbName']) || !isset(DBCONFIGS[$param['type']])) {
			throw new \Exception('', -100);
		}

		$handler = $this->publicMethod();
		if (isset($param['query']) || isset($param['tableName'])) {
			$query = isset($param['query']) ? $param['query'] : false;
			$tableName = isset($param['tableName']) ? $param['tableName'] : false;
			$command = $handler->formatQuery($query, $tableName);
			return $handler->findAll($command, $param['dbName']);
		} else {
			return array();
		}
	}

	/**
	 * 共有方法
	 * @return [type] [description]
	 */
	private function publicMethod()
	{
		$param = $GLOBALS['param'];
		$classType = ucfirst(strtolower(DBCONFIGS[$param['type']]['type']));
		$class = '\DbViewer\DbData\\' . $classType;
		if (!file_exists(dirname(BASE_PATH) . SEPATATOR . strtr($class, '\\', SEPATATOR) . '.' . EXT)){
			throw new \Exception('', -101);
		}
		$handler = new $class();
		$handler->connect(DBCONFIGS[$param['type']]);
		return $handler;
	}

}
