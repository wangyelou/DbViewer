<?php
namespace DbViewer\Log;

class Prints
{

	const codeMaps = array(
		0 => '操作成功',
		-100 => '参数缺失',
		-101 => '配置不存在',
		-199 => '未知错误'
	);

	/**
	 * 输出信息
	 * @param  [type] $code  [description]
	 * @param  [type] $msg   [description]
	 * @param  [type] $datas [description]
	 * @return [type]        [description]
	 */
	public static function write($code, $msg = NULL, $datas = NULL)
	{
		if (is_null($msg)) {
			$msg = isset(self::codeMaps[$code]) ? self::codeMaps[$code] : '未知错误';
		}
		$data = array(
			'code' => $code,
			'msg' => $msg
		);
		if (!is_null($datas)) {
			$data['data'] = $datas;
		}
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
		exit;
	}

}