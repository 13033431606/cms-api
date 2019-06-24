<?php
date_default_timezone_set('PRC');
header("Content-type: text/html; charset=utf-8"); //设置编码 utf-8
$utime = date("Y-m-d");//api的尾缀时间
//使用curl提高运行速度 不用动
function httpGet($url) {
    $curl = curl_init();
    $httpheader[] = "Accept:*/*";
    $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
    $httpheader[] = "Connection:close";
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}
$myfile = fopen("binduyan.txt", "a+");//创建文件保存抓取的句子
//循环次数 2018-3-21 至现在日期相差的天数
for ($i=1; $i<83; $i++) {
    $json_string =httpGet('http://www.dutangapp.cn/u/toxic?date='.$utime);//curl 自定义函数访问api
    $data= json_decode($json_string,true);//解析json 转为php
    //2018-4-11之前只有一条数据 so 加判断
    if (isset($data['data']['0']['data'])) {
        $text1= $data['data']['0']['data']."\n";
        fwrite($myfile, $text1);
    }
    if (isset($data['data']['1']['data'])) {
        $text2= $data['data']['1']['data']."\n";
        fwrite($myfile, $text2);
    }
    if (isset($data['data']['2']['data'])) {
        $text3= $data['data']['2']['data']."\n";
        fwrite($myfile, $text3);
    }

    $utime= date("Y-m-d",strtotime("-".strval($i)." day")); //每循环一次 当前日期减去循环变量

}
fclose($myfile);
echo "ok";
?>