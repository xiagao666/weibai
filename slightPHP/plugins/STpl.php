<?php
/*{{{LICENSE
+-----------------------------------------------------------------------+
| SlightPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2008-2009. All Rights Reserved.                         |
+-----------------------------------------------------------------------+
| Supports: http://www.slightphp.com                                    |
+-----------------------------------------------------------------------+
}}}*/

if (!defined("SLIGHTPHP_PLUGINS_DIR")) {
    define("SLIGHTPHP_PLUGINS_DIR", dirname(__FILE__));
}
require_once(SLIGHTPHP_PLUGINS_DIR . "/tpl/Tpl.php");

/**
 * @package SlightPHP
 */
class STpl extends SlightPHP\Tpl
{
    static $engine;

    /**
     * @deprecated , 新版本请使用 display
     * render a .tpl
     */
    public function render($tpl, $parames = array())
    {
        parent::$compile_dir = SlightPHP::$appDir . DIRECTORY_SEPARATOR . "templates_c";
//        parent::$template_dir = SlightPHP::$appDir . DIRECTORY_SEPARATOR . "templates";
        parent::$template_dir = SlightPHP::$appDir . DIRECTORY_SEPARATOR . "../assets/dist/";
        parent::$left_delimiter = '{';
        parent::$right_delimiter = '}';
        parent::assign($parames);
        return parent::fetch("$tpl");
    }

    /**
     * like as render except delimiter
     */
    public function display($tpl, $parames = array())
    {
        parent::$compile_dir = SlightPHP::$appDir . DIRECTORY_SEPARATOR . "templates_c";
        parent::$template_dir = SlightPHP::$appDir . DIRECTORY_SEPARATOR . "templates";
        parent::$left_delimiter = '<{';
        parent::$right_delimiter = '}>';
        parent::assign($parames);
        return parent::fetch("$tpl");
    }

    /**
     * 302 redirect
     */
    public function redirect($url)
    {
        header('Location:' . $url);
        exit;
    }

    /**
     *  提示信息
     * @param array $params e.g. array("state"=>1,msg=>"msg","backurl"=>"http://..","second"=3); state 1:OK -1:需要登录,<-1错误代码
     * @param int $json 1为js请求 0为非js请求
     */
    public function alert($params, $json = 0)
    {
        if (empty($json)) {
            $json = isset($_REQUEST["json"]) ? $_REQUEST["json"] : 0;
        }

        if ($json) {
            return $this->json($params);
        } else {
            if (!isset($params["second"])) {
                $params["second"] = 3;
            }
            if ($params["second"] == 0 && isset($params["backurl"])) {//0秒不显示提示，
                $this->redirect($params['backurl']);
            }
            if (empty($params['echo'])) {
                return $this->render('comm/alert.html', $params);
            } else {
                echo $this->render('comm/alert.html', $params);
                exit;
            }
        }
    }

    /**
     * 返回json数据
     * @param array $data
     */
    public function json($data)
    {
        $jsonpcallback = isset($_REQUEST['callback']) ? core_lib_Comm::getStr($_REQUEST['callback']) : '';
        if (empty($jsonpcallback)) {
            return json_encode($data);
        } else {
            return $jsonpcallback . '(' . json_encode($data) . ')';
        }
    }
}
