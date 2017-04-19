<?php
require_once("../Const.php");
require_once(ROOT . "/vendor/autoload.php");
require_once("../Global.php");
require_once("../Init.php");

if (($r = SlightPHP::run()) === false) {
    echo("404 error");
} elseif (is_object($r)) {
    var_dump($r);
} else {
    echo($r);
}
