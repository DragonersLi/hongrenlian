<?php
//系统公共函数目录

//生成商品编码
function generateGoodsSn(){
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    return $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

}

//获取header
function get_all_header()
{
    // 忽略获取的header数据。这个函数后面会用到。主要是起过滤作用
    $ignore = array('host','accept','content-length','content-type');

    $headers = array();

    foreach($_SERVER as $key=>$value){
        if(substr($key, 0, 5)==='HTTP_'){
            //这里取到的都是'http_'开头的数据。
            //前去开头的前5位
            $key = substr($key, 5);
            //把$key中的'_'下划线都替换为空字符串
            $key = str_replace('_', ' ', $key);
            //再把$key中的空字符串替换成‘-’
            $key = str_replace(' ', '-', $key);
            //把$key中的所有字符转换为小写
            $key = strtolower($key);

            //这里主要是过滤上面写的$ignore数组中的数据
            if(!in_array($key, $ignore)){
                $headers[$key] = $value;
            }
        }
    }
    return $headers;
}
//邀请码，根据用户id加密
function uidEncode($user_id) {
    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
    $num = $user_id;
    $code = '';
    while ( $num > 0) {
        $mod = $num % 35;
        $num = ($num - $mod) / 35;
        $code = $source_string[$mod].$code;
    }
    if(empty($code[3]))
        $code = str_pad($code,4,'0',STR_PAD_LEFT);
    return $code;
}
//邀请码，解密出用户id
function uidDecode($code)
{
    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
    if (strrpos($code, '0') !== false)
        $code = substr($code, strrpos($code, '0') + 1);
    $len = strlen($code);
    $code = strrev($code);
    $num = 0;
    for ($i = 0; $i < $len; $i++) {
        $num += strpos($source_string, $code[$i]) * pow(35, $i);
    }
    return $num;

}