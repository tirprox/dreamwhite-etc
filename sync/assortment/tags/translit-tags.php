<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 8/16/18
 * Time: 1:33 PM
 */

namespace Dreamwhite\Assortment;

require_once "../includes.php";

$file = file(__DIR__ . '/tags-new');
$data = '';

$data .= "Метка,Ссылка на СПб,Ссылка на Москву" . PHP_EOL;

foreach ($file as $line) {
    $line = str_replace(PHP_EOL, '', $line);

    $spbTag = 'https://dreamwhite.ru/catalog/' . strtolower(Tools::transliterate($line)) . '/';
    $mskTag = 'https://msk.dreamwhite.ru/catalog/' . strtolower(Tools::transliterate($line)) . '/';

    $line = "$line,$spbTag,$mskTag" . PHP_EOL;

    $data .= $line;
}

file_put_contents('links.csv', $data);