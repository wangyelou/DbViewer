<?php
/**
 *数据库数据可视化工具
 *@author wangyelou <[<email address>]>
 *@date 2018-08-21
 */
require_once './config/autoloader.php';

try {
	//获取参数
	$param = $_POST;
	if (!isset($param['type']) || !isset($param['query'])) {
		throw new Exception('', -100);
	} else {
		$type = $param['type'];
		$query = $param['query'];
		$dbName = isset($param['dbName']) ? $param['dbName'] : false;
	}

	//是否存在配置
	if (!isset(DBCONFIGS[$type]) || count(DBCONFIGS[$type]) <= 0) {
		throw new Exception('', -101);
	} else {
		$config = DBCONFIGS[$type];
	}

	$classType = ucfirst(strtolower($config['type']));
	$class = '\DbViewer\DbData\\' . $classType;
	if (!file_exists(dirname(BASE_PATH) . SEPATATOR . strtr($class, '\\', SEPATATOR) . '.' . EXT)){
		throw new Exception('', -101);
	}
	$handler = new $class();
	$handler->connect($config);
	$result = $handler->findAll($query, $dbName);

	\DbViewer\Log\Prints::write(0, NULL, $result);


} catch (Exception $e) {
	\DbViewer\Log\Prints::write($e->getCode() ? $e->getCode() : -199, $e->getMessage() ? $e->getMessage() : NULL);
}