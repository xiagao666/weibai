<?php

class lib_comm
{
    /**
     * 判断是否登录
     * @return bool
     */
    static public function isSignIn()
    {
        if ($_SESSION['manager']['mangerId']) {
            if ($_SESSION['manager']['managerKey'] !== md5($_SESSION['manager']['mangerId'] . $_SESSION['manager']['managerName'] . SECURITY_KEY)) {
                session_unset();
                session_destroy();
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}