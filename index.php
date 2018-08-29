<?php
/**
 *数据库数据可视化工具
 *@author wangyelou <[<email address>]>
 *@date 2018-08-21
 */
require_once './config/autoloader.php';

try {
	//获取参数
	$GLOBALS['param'] = array_merge($_POST, $_GET);

	if (!isset($GLOBALS['param']['contro']) || !isset($GLOBALS['param']['action'])) {
		throw new Exception('', -100);
	}

	$contro = ucfirst(strtolower($GLOBALS['param']['contro']));
	$class = '\DbViewer\Task\\' . $contro;
	if (!file_exists(dirname(BASE_PATH) . SEPATATOR . strtr($class, '\\', SEPATATOR) . '.' . EXT)){
		throw new Exception('', -101);
	}
	$obj = new $class();
	if (!method_exists($obj, $GLOBALS['param']['action'])) {
		throw new Exception('', -101);
	}
	$result = call_user_func(array($obj, $param['action']));
	\DbViewer\Log\Prints::write(0, NULL, $result);


} catch (Exception $e) {
	\DbViewer\Log\Prints::write($e->getCode() ? $e->getCode() : -199, $e->getMessage() ? $e->getMessage() : NULL);
}
