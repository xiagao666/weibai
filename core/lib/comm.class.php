<?php

class core_lib_comm extends Sabstract
{
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
}