<?php
namespace kordar\upload\helper;

class UploadHelper
{
    public static function createDir($path = '')
    {
        $arr = explode('/', $path);
        if(!empty($arr)) {
            $_path = '';
            foreach($arr as $k=>$v)
            {
                $_path .= $v.'/';
                if (!file_exists($_path)) {
                    mkdir($_path, 0777);
                    chmod($_path, 0777);
                }
            }
        }
    }
}