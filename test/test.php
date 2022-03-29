<?php
require_once '../vendor/autoload.php';

use Sabine\Tools\Idcard\IdValidator;
$obj = new IdValidator();
$idcard = '11010120000101568X';
$valid = $obj->isValid($idcard,true);
if($valid) {
    $info = $obj->getInfo($idcard);
    var_dump($info);
}
