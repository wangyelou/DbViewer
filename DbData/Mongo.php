<?php
namespace DbViewer\DbData;

class Mongo extends DbAbstract
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
		if (!in_array('mongodb', get_loaded_extensions())) {
			throw new \Exception('mongodb扩展没有安装', -199);
		}
        if (!empty($config['user']) && !empty($config['pass'])) {
            $info = "{$config['user']}:{$config['pass']}@";
        } else {
            $info = '';
        }
		$dsn = "mongodb://{$info}{$config['host']}:{$config['port']}";

        $this->dbh = new \MongoDB\Driver\Manager($dsn);
        $ping = $this->command(array('ping' => 1), $config['dbName']);
        if ('[{"ok":1}]' != json_encode($ping)) {
            throw new \Exception('mongo connect error');
        }
	}

    public function command($command, $dbname)
    {
        $reCommand = new \MongoDB\Driver\Command($command);
        $cursor = $this->dbh->executeCommand($dbname, $reCommand);
        return $cursor->toArray();
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
        if (($command = json_decode($query, true)) === NULL) {
            throw new \Exception('query is invalid');
        }
        $result = $this->command($command, $dbName); 
		return $result;
	}

	public function getTables()
	{

	}

}
