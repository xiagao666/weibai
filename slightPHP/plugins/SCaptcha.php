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

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "captcha/captcha.php");

/**
 * @package SlightPHP
 */
class SCaptcha extends SlightPHP\SimpleCaptcha
{
    static $session_prefix = "SCaptcha_";

    function __construct()
    {
        $this->wordsFile = "";
        $this->session_var = SCaptcha::$session_prefix;
        $this->minWordLength = 4;
        $this->maxWordLength = 5;
        $this->width = 140;
        $this->height = 50;
        $this->Yamplitude = 6;
        $this->Xamplitude = 4;
        $this->scale = 3;
        $this->blur = true;
        $this->imageFormat = "png";
        $this->transprent = false;
    }

    /**
     * 验证验证码
     * @param $captcha_code 验证码
     * @param string $key 验证码key
     * @param int $isDel 是否删除
     * @return bool
     */
    static function check($captcha_code, $key = "", $isDel = 0)
    {
        if (empty($_SESSION[SCaptcha::$session_prefix. "_" . $captcha_code. "_" . $key]) ||
            $_SESSION[SCaptcha::$session_prefix. "_" . $captcha_code. "_" . $key] != $captcha_code
        ) {
            return false;
        } else {
            if ($isDel) {
                self::del($captcha_code, $key);
            }
            return true;
        }
    }

    /**
     * 删除验证码
     * @param $captcha_code 验证码
     * @param string $key
     */
    static function del($captcha_code, $key = "")
    {
        if (!empty($_SESSION[SCaptcha::$session_prefix. "_" . $captcha_code. "_" . $key])) {
            unset ($_SESSION[SCaptcha::$session_prefix. "_" . $captcha_code. "_" . $key]);
        }
    }
}