<?php
/**
 * Created by PhpStorm.
 * User: Hans
 * Email: hans01@foxmail.com
 * Date: 2019/2/25
 * Time: 10:32
 */

namespace app\index\controller;


use think\Controller;

class Base extends Controller
{
    /**
     * 直接返回
     */
    public function back($error = null)
    {
        session('error', $error);   // 保存错误信息

        return "<script> window.history.go(-1) </script>";
    }


}
