<?php
namespace DbViewer\Task;

/**
 * 配置数据接口
 * @author  wangyelou <[<email address>]>
 * @date 2018-08-22
 */
class Configtask
{

	/**
	 * 获取配置名
	 * @return [type] [description]
	 */
	public function getConfLists()
	{
		$keys = array_keys(DBCONFIGS);
		return $keys;
	}

}
