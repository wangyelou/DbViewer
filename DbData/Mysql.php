<?php
namespace DbViewer\DbData;

class Mysql extends DbAbstract
{
	const OPT_RELATIONS = array(
		'connectTimeOut' => \PDO::ATTR_TIMEOUT,
	);

	private $dbh;

	/**
	 * 数据库连接
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	public function connect($config)
	{
		if (!in_array('PDO', get_loaded_extensions())) {
			throw new \Exception('PDO扩展没有安装', -199);
		}
		$dsn = strtolower($config['type']) . ':host=' . $config['host'] . ';port=' . $config['port'];
		$user = $config['user'];
		$pass = $config['pass'];

		$opt = $this->getOpt($config['options']);
		$opt[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
		$opt[\PDO::MYSQL_ATTR_INIT_COMMAND] = "set names utf8;";
		$this->dbh = new \PDO($dsn, $user, $pass, $opt);
	}

	/**
	 * 获取配置
	 * @param  [type] $opt [description]
	 * @return [type]      [description]
	 */
	private function getOpt($opt)
	{
		$newOpt = array();
		foreach ($opt as $key => $item) {
			if (isset(self::OPT_RELATIONS[$key]))
				$newOpt[self::OPT_RELATIONS[$key]] = $item;
		}
		return $newOpt;
	}

	/**
	 * 查找数据
	 * @param  [type] $query  [description]
	 * @param  [type] $dbName [description]
	 * @return [type]         [description]
	 */
	public function findAll($query, $dbName)
	{
		if ($dbName)
			$this->dbh->query("use {$dbName};");
		$sth = $this->dbh->prepare($query);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_ASSOC);
		return $result;
	}

	public function getTables()
	{

	}

}
