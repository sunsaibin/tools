<?php
require_once '../vendor/autoload.php';

use Sabine\Tools\Idcard\IdValidator;
use Sabine\Tools\Util\Spider\Spider;

$obj = new Spider();
$url = 'https://www.oschina.net/project/widgets/_project_list?company=0&tag=0&lang=22&os=0&sort=time&recommend=false&cn=false&weekly=false&p=0&type=ajax';//https://www.oschina.net/project/lang/22/php';
$field = [
    [
        'field'=>'title',
        'selector'=>'.project-item h3.header',
        'type'=>'text',
        'str_replace'=>[
            ['search'=>'国','replace'=>''],
            ['reg'=>"/ /",'replace'=>''],
        ]
    ],
    [
        'field'=>'href',
        'selector'=>'.project-item h3.header a',
        'type'=>'href',
    ]
];
$res = $obj->query($field,$url);
var_dump($res);die;

$obj = new IdValidator();
$idcard = '11010120000101568X';
$valid = $obj->isValid($idcard,true);
if($valid) {
    $info = $obj->getInfo($idcard);
    var_dump($info);
}
