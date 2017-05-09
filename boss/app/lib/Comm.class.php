<?php

class lib_Comm
{
    /**
     * 判断是否登录
     * @return bool
     */
    static public function isSignIn()
    {
        if ($_SESSION['manager']['managerId']) {
            if ($_SESSION['manager']['managerKey'] !== md5($_SESSION['manager']['managerId'] . $_SESSION['manager']['managerName'] . SECURITY_KEY)) {
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