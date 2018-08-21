<?php
namespace DbViewer\DbData;

class Pgsql extends DbAbstract
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
		if (!in_array('pgsql', get_loaded_extensions())) {
			throw new \Exception('pgsql扩展没有安装', -199);
		}
		$dsn = "host={$config['host']} port={$config['port']} user={$config['user']} password={$config['pass']} dbname={$config['dbName']}";

		//$opt = $this->getOpt($config['options']);
        if (!$this->dbh = pg_connect($dsn)) {
            throw new \Exception("PGSQL connnect error, " . pg_last_error($this->dbh));
        }
        pg_set_client_encoding($this->dbh, 'utf8');
        $this->config = $config;
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
		if ($dbName && pg_dbname($this->dbh) != $dbName) {
            $config = $this->config;
		    $this->dbh = pg_connect("host={$config['host']} port={$config['port']} user={$config['user']} password={$config['pass']} dbname={$dbName}");
        }
		if ($sth = pg_query($this->dbh, $query)) {
            $result = pg_fetch_all($sth);
        } else {
            throw new \Exception(pg_errormessage($this->dbh));
        }
    
		return $result;
	}

	public function getTables()
	{

	}

}
