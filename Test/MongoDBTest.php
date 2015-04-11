<?php
use DB\EGDBFactory;
use ConfigReader\EGJson;
define ( 'DS', DIRECTORY_SEPARATOR );
// 默认路径为该工程的根目录
define ( 'WEB_ROOT', __DIR__ . DS . '..' . DS );

// 为方便起见，使用autoload自动加载
include WEB_ROOT . 'ConfigReader/EGJson.php';
include WEB_ROOT . 'DB/EGIDB.php';
include WEB_ROOT . 'DB/EGDBFactory.php';
include WEB_ROOT . 'DB/EGADB.php';
include WEB_ROOT . 'DB/DBDriver/EGMongoDB.php';

$configPath = WEB_ROOT . 'DB'.DS . 'dbConf.json';
$jsonConfData=EGJson::parse($configPath);
$mongoDb=EGDBFactory::getInstance($jsonConfData,EGDBFactory::DBTYPE_MONGODB);
$mongoDb->switchCollection('t_admin','gameManager');
$mongoDb->select('t_admin');
$cursor=$mongoDb->limit(1,0);
$sql=$mongoDb->getLastSql();
var_dump(iterator_to_array($cursor));