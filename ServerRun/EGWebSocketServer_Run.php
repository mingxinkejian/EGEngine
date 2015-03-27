<?php
use ConfigReader\EGJson;
use Server\EGWebSocketServer;
if (version_compare ( PHP_VERSION, '5.4.0', '<' ))
	die ( 'require PHP > 5.4.0 !' );

define ( 'DS', DIRECTORY_SEPARATOR );
// 默认路径为该工程的根目录
define ( 'WEB_ROOT', __DIR__ . DS . '..' . DS );

// 为方便起见，使用autoload自动加载
include WEB_ROOT . 'ConfigReader/EGJson.php';
include WEB_ROOT . 'Server/EGIServer.php';
include WEB_ROOT . 'Server/EGBaseServer.php';
include WEB_ROOT . 'Server/EGWebServer.php';
include WEB_ROOT . 'Server/EGWebSocketServer.php';

$configPath = __DIR__ . DS . 'serverConf.json';
$configData = EGJson::parse ( $configPath );

$httpServer = new EGWebSocketServer( '127.0.0.1', 9502 );
$httpServer->loadConfig ( $configData ['webSocketServer'] );
$httpServer->setWebRoot ( WEB_ROOT );
$httpServer->startServer ();