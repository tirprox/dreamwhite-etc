<?php
/**
 * Created by PhpStorm.
 * User: DreamWhite
 * Date: 06.11.2017
 * Time: 11:49
 */
$path = realpath("../");

$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
   RecursiveIteratorIterator::SELF_FIRST
//   | RecursiveDirectoryIterator::KEY_AS_FILENAME
   | RecursiveDirectoryIterator::CURRENT_AS_FILEINFO);
$Regex = new RegexIterator($objects, '/^.+\.jpg$/i', RegexIterator::MATCH,RegexIterator::USE_KEY);
$fileList = [];
foreach($Regex as $name){
   $fileList[] = $name->getFilename();
}
//var_dump($fileList);
header('Content-type:application/json;charset=utf-8');

echo json_encode($fileList, JSON_UNESCAPED_UNICODE);