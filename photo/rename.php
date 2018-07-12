<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 7/12/18
 * Time: 5:56 PM
 */



$categories = array_diff(scandir(__DIR__), array('..', '.'));

$filtered = [];
foreach ($categories as $file) {
    if (is_dir($file)) {

        $filtered[] =  $file;
    }
}

//var_dump($filtered);

foreach ($filtered as $cat) {
    $dir = __DIR__ . "/" . $cat;
    $articles = array_diff(scandir($dir), array('..', '.'));


    foreach ($articles as $article) {

        $adir = $dir . "/" . $article;
        $colors = array_diff(scandir($adir), array('..', '.'));

        foreach ($colors as $color) {
            $cdir = $adir . "/" . $color;
            $files = array_diff(scandir($cdir ), array('..', '.'));

            $i = 1;

            foreach ($files as $file) {
                $path = $cdir . "/" . $file;

                $newName = $article . "-" . $color . "-" . $i . ".jpg";

                rename($path, $cdir . "/" . $newName );
                $i++;
            }
        }
        //var_dump($colors);
    }
}


