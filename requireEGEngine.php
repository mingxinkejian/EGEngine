<?php
use Core\EGLoader;
$REQUIRE_PATH=__DIR__.DIRECTORY_SEPARATOR;
require_once $REQUIRE_PATH.'Core/EGLoader.php';
//注册引擎所需的命名空间
EGLoader::addNameSpace('Algorithm', $REQUIRE_PATH.'Algorithm');
EGLoader::addNameSpace('ConfigReader', $REQUIRE_PATH.'ConfigReader');
EGLoader::addNameSpace('Cache', $REQUIRE_PATH.'Cache');
EGLoader::addNameSpace('Core', $REQUIRE_PATH.'Core');
EGLoader::addNameSpace('DB', $REQUIRE_PATH.'DB');
EGLoader::addNameSpace('Server', $REQUIRE_PATH.'Server');
EGLoader::addNameSpace('ServerRun', $REQUIRE_PATH.'ServerRun');
//注册装载器
EGLoader::register();