<?php
date_default_timezone_set("Asia/Shanghai");

//{{{
spl_autoload_register(function ($class) {
    if ($class{0} === "S") {
        $file = ROOT_SLIGHTPHP_PLIGUNS.DIRECTORY_SEPARATOR."$class.class.php";
    } elseif ('core' == substr($class,0,4)) {
        $file = ROOT . ".." . DIRECTORY_SEPARATOR . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".class.php";
    } else {
        $file = ROOT_APP . DIRECTORY_SEPARATOR . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".class.php";
    }
    if (is_file($file)) {
        return require_once($file);
    }
});
//}}}
