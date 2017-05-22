<?php

class core_lib_Comm
{
    /**
     * 打印数据 上线删除方法
     * @param $a
     */
    public static function p($a)
    {
        echo "<pre>";
        var_dump($a);
        echo "</pre>";
    }

    /**
     * 根据solr查询返回的response
     * @param $rs ;
     * @param $facet ;
     * return $result;
     */
    public static function solr_parse($rs, $facet = '')
    {
        $items = array();
        if (is_array($rs->response->docs)) {
            $i = 0;
            foreach ($rs->response->docs as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $items[$i][$k1] = $v1;
                }
                $i++;
            }
            $i = 0;
            if (!empty($rs->highlighting)) {
                foreach ($rs->highlighting as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                        $items[$i]['hl'][$k1] = $v1[0];
                    }
                    $i++;
                }
            }
        }

        $result = array('items' => $items, 'totalsize' => $rs->response->numFound);

        //统计
        if (!empty($facet)) {
            $result['facet'] = $oRes->facet_counts->facet_fields->$facet;
        }

        return $result;
    }

    /**
     * 上传文件接口
     * 返回上传成功失败的数组
     *
     * @param object $postvar 数据流
     * @param string $file_name 文件名
     * @param string $tmp_name 临时文件名
     * @param string $servers_url 指定上传的地址,该地址需要遵守一定规则
     * @return array
     */
    public static function upload($parameters)
    {
        $http = new SHttp();
        $result = $http->request(UPLOAD_URL, 'POST', $parameters, true);
        if (preg_match_all('(\{.*\})', $result, $matched)) {
            return json_decode($matched[0][0], true);
        } else {
            return array('state' => -1, 'msg' => '未知错误');
        }
    }

    public static function file2url($file, $width = 0, $height = 0, $cut = 0)
    {
        if (empty($file)) {
            return $file;
        } else {
            preg_match('/([0-9a-zA-z\/]+)(\.[a-z]+)/', $file, $matches);
            if (!empty($matches)) {
                $ext = $matches[2];
                $url = FILE_URL . "/" . $matches[1];
                if (!empty($width) || !empty($height) || !empty($cut)) {
                    $url .= '_' . $width . '_' . $height . '_' . $cut;
                }
                $url .= $ext;
                return $url;
            } else {
                return $file;
            }
        }
    }

    public static function isSignIn($uid = '', $token = '')
    {
        if ($token && $uid) {
            $mdl_user = SModel::init('core_model_mb_user', $uid);
            $display_name = $mdl_user->getData('display_name');
            if ($token == self::accessToken($display_name, $uid)) {
                $time = time();
                $ymd = date('Y-m-d H:i:s');
                $mdl_user->set(
                    array(
                        'last_activity_time' => time(),
                        'last_activity_ymd' => $ymd,
                        'last_activity_ip' => SUtil::getIP(true),
                    )
                );
                $mdl_user->save();
                return true;
            }
        }
        return false;
    }

    public static function accessToken($display_name, $uid)
    {
        //return md5($display_name.$uid.SECURITY_KEY);
        return md5($uid . SECURITY_KEY);
    }

    /**
     * 是否是ajax提交
     *
     * @return bool
     * @author glzaboy<glzaboy@163.com>
     */
    public static function isAjax()
    {
        if (isset ($_SERVER ['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER ['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') { //是否ajax请求
            return true;
        } else {
            return false;
        }
    }

    /**
     * 安全过滤数据
     *
     * @param string|array $str 需要处理的字符或数组
     * @param string $type 返回的字符类型，支持，string,int,float,html
     * @param mixed $default 当出现错误或无数据时默认返回值
     *
     * @return string|array|mixed 当出现错误或无数据时默认返回值
     */
    public static function getStr($str, $type = 'string', $default = '')
    {
        //如果为空则为默认值
        if ($str === '') {
            return $default;
        }

        if (is_array($str)) {
            $_str = array();
            foreach ($str as $key => $val) {
                $_str[$key] = self::getStr($val, $type, $default);
            }
            return $_str;
        }

        //转义
        if (!get_magic_quotes_gpc()) {
            $str = addslashes($str);
        }

        switch ($type) {
            case 'string':    //字符处理
                $_str = strip_tags($str);
                $_str = str_replace("'", '&#39;', $_str);
                $_str = str_replace("\"", '&quot;', $_str);
                $_str = str_replace("\\", '', $_str);
                $_str = str_replace("\/", '', $_str);
                $_str = str_replace("+/v", '', $_str);
                break;
            case 'int':    //获取整形数据
                $_str = intval($str);
                break;
            case 'float':    //获浮点形数据
                $_str = floatval($str);
                break;
            case 'html':    //获取HTML，防止XSS攻击
                $_str = self::reMoveXss($str);
                break;
            default:    //默认当做字符处理
                $_str = strip_tags($str);
        }
        return $_str;
    }

    /**
     * 过滤XSS攻击
     *
     * @param string $val
     *
     * @return string
     */
    public static function reMoveXss($val)
    {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        //$val = preg_replace('/([\x00-\x08|\x0b-\x0c|\x0e-\x19])/', '', $val);
        $val = preg_replace('/([\x00-\x08])/', '', $val);
        $val = preg_replace('/([\x0b-\x0c])/', '', $val);
        $val = preg_replace('/([\x0e-\x19])/', '', $val);

        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = Array(
            'javascript',
            'vbscript',
            'expression',
            'applet',
            'meta',
            'xml',
            'blink',
            'link',
            'script',
            'embed',
            'object',
            'iframe',
            'frame',
            'frameset',
            'ilayer',
            'layer',
            'bgsound',
            'base',
        );
        $ra2 = Array(
            'onabort',
            'onactivate',
            'onafterprint',
            'onafterupdate',
            'onbeforeactivate',
            'onbeforecopy',
            'onbeforecut',
            'onbeforedeactivate',
            'onbeforeeditfocus',
            'onbeforepaste',
            'onbeforeprint',
            'onbeforeunload',
            'onbeforeupdate',
            'onblur',
            'onbounce',
            'oncellchange',
            'onchange',
            'onclick',
            'oncontextmenu',
            'oncontrolselect',
            'oncopy',
            'oncut',
            'ondataavailable',
            'ondatasetchanged',
            'ondatasetcomplete',
            'ondblclick',
            'ondeactivate',
            'ondrag',
            'ondragend',
            'ondragenter',
            'ondragleave',
            'ondragover',
            'ondragstart',
            'ondrop',
            'onerror',
            'onerrorupdate',
            'onfilterchange',
            'onfinish',
            'onfocus',
            'onfocusin',
            'onfocusout',
            'onhelp',
            'onkeydown',
            'onkeypress',
            'onkeyup',
            'onlayoutcomplete',
            'onload',
            'onlosecapture',
            'onmousedown',
            'onmouseenter',
            'onmouseleave',
            'onmousemove',
            'onmouseout',
            'onmouseover',
            'onmouseup',
            'onmousewheel',
            'onmove',
            'onmoveend',
            'onmovestart',
            'onpaste',
            'onpropertychange',
            'onreadystatechange',
            'onreset',
            'onresize',
            'onresizeend',
            'onresizestart',
            'onrowenter',
            'onrowexit',
            'onrowsdelete',
            'onrowsinserted',
            'onscroll',
            'onselect',
            'onselectionchange',
            'onselectstart',
            'onstart',
            'onstop',
            'onsubmit',
            'onunload',
        );
        $ra = array_merge($ra1, $ra2);
        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }

        return $val;
    }

    /**
     * 驼峰命名法转下划线风格
     */
    public static function toUnderScore($str)
    {
        $array = array();
        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] == strtolower($str[$i])) {
                $array[] = $str[$i];
            } else {
                if ($i > 0) {
                    $array[] = '_';
                }
                $array[] = strtolower($str[$i]);
            }
        }

        $result = implode('', $array);
        return $result;
    }

    /**
     * 下划线风格转驼峰命名法
     */
    public static function toCamelCase($str){
        $array = explode('_', $str);
        $result = '';
        if (empty($array)) {
            foreach($array as $value){
                $result.= ucfirst($value);
            }
        }
        return $result;
    }

    /**
     * 从常量文件中组装表头array,参数以，隔开的字符串
     */
    public static function getTableColumns($str){
        $columns = explode(",", $str);
        $columnsNew = array_filter($columns);//去掉空元素
        $columnRow = array();
        foreach ($columnsNew as $v){
            $columnsItem = array();
            $columnsItem = explode("_",$v);
            $key = core_lib_Comm::toUnderScore($columnsItem[0]);
            $columnRow[$key] = $columnsItem[1];
        }
        return $columnRow;
    }

    /**
     * 发送邮件
     */
    public function sendMail($toMail, $fromMail = '', $data = array())
    {
        $headers = "From: {$fromMail}";
        $subject = "用户：{$data['name']} 发来建议邮件";
        $message = "用户：{$data['name']}\n电话：{$data['telephone']}\n建议：{$data['suggest']}";
        mail($toMail, $subject, $message, $headers);
        return true;
    }

    /**
     * 产品对应字段名称
     */
    public function productKeyToName()
    {
        $productKeyToName = array(
            'brand'=>'品牌',
            'catalog_number'=>'货号',
            'package'=>'包装',
            'price'=>'价格',
            'chinese_name'=>'中文名',
            'abbreviation'=>'简写',
            'origin'=>'产地',
            'application_process'=>'应用/处理',
            'other_name'=>'别名',
            'storage_temperature'=>'储存温度',
            'type'=>'类别',
            'raise_from'=>'来源种属',
            'reacts_with'=>'反应种属',
            'application'=>'应用类别',
            'label'=>'标记物',
            'cas_no'=>'CAS号',
            'molecular_formula'=>'分子式',
            'molecular_weight'=>'分子量',
            'grade'=>'级别',
            'category'=>'类目',
        );
        return $productKeyToName;
    }
}