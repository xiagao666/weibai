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
        $referer = '';
        if(isset($_SERVER['HTTP_REFERER'])) {
            $referer = (string)$_SERVER['HTTP_REFERER'];
            if (preg_match('/'.preg_quote(BOSS_URL.'/sign','/').'/', $referer) || !preg_match('/'.preg_quote(BOSS_URL,'/').'/',$referer)){
                $referer = '/';
            }
        }

        if ( !empty($_POST) ) {
            $managerName = core_lib_Comm::getStr(trim($_POST['username']));
            $password = core_lib_Comm::getStr(trim($_POST['password']));
//            $captchaCode = core_lib_Comm::getStr(trim($_POST['captchaCode']));
//            $captchaKey = core_lib_Comm::getStr(trim($_POST['captchaKey']));
            $referer = $_POST['referer'] ? core_lib_Comm::getStr(trim($_POST['referer'])) : '/';
//            if (empty($captchaCode)){
//                return $this->alert(array('status'=>'error','msg'=>'请输入验证码'));
//            }
            if (empty($managerName)){
                return $this->alert(array('status'=>'error','msg'=>'请输入用户名'));
            }
            if (empty($password)){
                return $this->alert(array('status'=>'error','msg'=>'请输入密码'));
            }

//            if (SCaptcha::check($captchaCode, $captchaKey) === false){
//                return $this->alert(array('status'=>'error','msg'=>'验证码错误，请重试'));
//            }

            $dbManager = new core_db_Manager();
            $manager = $dbManager->getMangerByManagerName($managerName);

            if ($manager === false){
                return $this->alert(array('status'=>'error','msg'=>"用户名或密码错误"));
            }
            $sign = $dbManager->sign($managerName, $password);
            if ($sign === false) {
                return $this->alert(array('status'=>'error','msg'=>"用户名或密码错误"));
            }

            $_SESSION['manager']['managerId'] = $manager['manager_id'];
            $_SESSION['manager']['managerName'] = $manager['manager_name'];
            $_SESSION['manager']['managerKey'] = md5($manager['manager_id'] . $manager['manager_name'] . SECURITY_KEY);
            $data['manager_id'] = $manager['manager_id'];
            $data['last_in_ip'] = $manager['curr_in_ip'];
            $data['last_in_time'] = $manager['curr_in_time'];
            $data['curr_in_ip'] = SUtil::getIP(true);
            $data['curr_in_time'] = time();
            $dbManager->edit($data);
            return $this->alert(array('status'=>'success','msg'=>'登录成功','backurl'=>$referer,'second'=>0));
        }

        $params = array(
            'referer' => $referer,
//            'captchaKey' => uniqid()
        );
        return $this->render('/sign/in.html', $params);
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

    /**
     * 生成验证码
     */
    public function pageCaptcha(){
        $captchaKey = (string)$_GET['captchaKey'];//验证码key

        if (!$captchaKey) {
            return $this->alert(array('status'=>'error', 'msg'=>'缺少生成验证码key'));
        }

        $cap = new SCaptcha();//初始化类
        $cap->CreateImage($captchaKey);//生成图片，返回验证码
    }
}
