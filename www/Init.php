<?php
SlightPHP::setDebug(SLIGHT\DEBUG);
SlightPHP::setAppDir(ROOT_APP);
SlightPHP::setDefaultZone(SLIGHT\DEFAULT_ZONE);
SlightPHP::setDefaultPage(SLIGHT\DEFAULT_PAGE);
SlightPHP::setDefaultEntry(SLIGHT\DEFAULT_ENTRY);
SlightPHP::setSplitFlag(SLIGHT\URL_SPLIT_FLAG);

//配置文件
//{{{
SDb::setConfigFile(DB_CONF);
SRoute::setConfigFile(ROUTE_CONF);
SRedis::setConfigFile(REDIS_CONF);

//定义常量配置
$consts = SConfig::getConfig(CONST_CONF);
if ($consts){
    foreach ($consts as $ck=>$cv){
        if (!defined($ck)){
            define($ck, $cv);
        }
    }
}
//}}}