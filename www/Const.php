<?php
if (defined('ROOT') === false) {
    define('ROOT', str_replace("\\", DIRECTORY_SEPARATOR, dirname(__FILE__)) . DIRECTORY_SEPARATOR);
}
define("slightPHPV", "slightPHP");
define("ROOT_ENTRY", ROOT . "entry");
define("ROOT_APP", ROOT . "app");
define("ROOT_CONF", ROOT . "../conf");
define("ROOT_SLIGHTPHP", ROOT . "../" . slightPHPV);
define("ROOT_CORE", ROOT . "/../core");
define("ROOT_SLIGHTPHP_PLIGUNS", ROOT_SLIGHTPHP."/plugins");

define('SLIGHT\DEBUG', true);
define('SLIGHT\DEFAULT_ZONE', 'index');
define('SLIGHT\DEFAULT_PAGE', 'main');
define('SLIGHT\DEFAULT_ENTRY', 'index');
//define('SLIGHT\URL_SUFFIX', "html");
//define('SLIGHT\URL_FORMAT', '-');
define('SLIGHT\URL_SPLIT_FLAG', '-_.');

//配置文件
define("DB_CONF", ROOT_CONF . "/db.conf");
define("ROUTE_CONF", ROOT_CONF . "/route.conf");
define("REDIS_CONF", ROOT_CONF . "/redis.conf");
define("CONST_CONF", ROOT_CONF . "/const.conf");

//定义MYSQL配置文件 目前我们的项目不需要
//define('APP\DB_MYSQL_CONFIG_FILE', implode(DIRECTORY_SEPARATOR, array(SLIGHT\APP_DIR, 'conf', 'Mysql.ini.php')));