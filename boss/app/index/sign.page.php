<?php

/**
 * 登录／退出
 * Class index_sign
 */
class index_sign extends STpl
{
    /**
     * 登录
     */
    public function pageIn($inPath)
    {
        $referer = (string)$_SERVER['HTTP_REFERER'];
        if (preg_match('/'.preg_quote(BOSS_URL.'/sign','/').'/', $referer) || !preg_match('/'.preg_quote(BOSS_URL,'/').'/',$referer)){
            $referer = '/';
        }

        if ( empty($_POST) ) {
            $managerName = trim($_POST['managerName']);
            $password = $_POST['password'];
            $captcha = trim($_POST['captcha']);
            $captchaKey = trim($_POST['captchaKey']);
            $referer = $_POST['referer'] ? $_POST['referer'] : '/';

            if (empty($captcha)){
                return $this->alert(array('status'=>'error','msg'=>'请输入验证码'));
            }
            if (empty($managerName)){
                return $this->alert(array('status'=>'error','msg'=>'请输入用户名'));
            }
            if (empty($password)){
                return $this->alert(array('status'=>'error','msg'=>'请输入密码'));
            }
            if (SCaptcha::check($captcha, $captchaKey) === false){
                return $this->alert(array('status'=>'error','msg'=>'验证码错误，请重试'));
            }

            $dbManager = new core_db_manager();
            $manager = $dbManager->getMangerByManagerName($managerName);
            if ($manager === false){
                return $this->alert(array('status'=>'error','msg'=>"登录失败，请输入正确的用户名和密码重新登录"));
            }
            $sign = $dbManager->sign($managerName, $password);
            if ($sign === false) {
                return $this->alert(array('status'=>'error','msg'=>"登录失败，请输入正确的用户名和密码重新登录"));
            }
            $_SESSION['manager']['mangerId'] = $manager['manager_id'];
            $_SESSION['manager']['managerName'] = $manager['manager_name'];
            $_SESSION['manager']['managerKey'] = md5($manager['manager_id'] . $manager['manager_name'] . SECURITY_KEY);
            $data['manager_id'] = $manager['manager_id'];
            $data['last_in_ip'] = $manager['curr_in_ip'];
            $data['last_in_time'] = $manager['curr_in_time'];
            $data['curr_in_ip'] = SUtil::getIP(true);
            $data['curr_in_time'] = $this->_time;
            $dbManager->edit($data);
            return $this->alert(array('status'=>'success','msg'=>'登录成功','backurl'=>$referer,'second'=>0));
        }

        $params = array(
            'referer' => $referer,
            'captchaKey' => uniqid()
        );
        return $this->render('sign/in.html', $params);
    }

    /**
     * 退出
     */
    public function pageOut()
    {
        session_unset();
        session_destroy();
        return $this->alert(array('status'=>'success', 'msg'=>'已退出', 'backurl'=>"/sign/in", 'second'=>0));
    }

    public function pageCaptcha(){
        $captchaKey = $_GET['captchaKey'];
        /**
         * 初始化类
         */
        $cap = new SCaptcha();

        /**
         * 生成图片，返回验证码
         */
        $cap->CreateImage($captchaKey);
    }
}
