<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 7/12/18
 * Time: 5:56 PM
 */

$tree = [];

$categories = array_diff(scandir(__DIR__), array('..', '.'));

$filtered = [];

foreach ($categories as $file) {
    if (is_dir($file)) {$filtered[] =  $file;}
}


foreach ($filtered as $cat) {
    $dir = __DIR__ . "/" . $cat;
    $articles = array_diff(scandir($dir), array('..', '.'));


    foreach ($articles as $article) {

        $adir = $dir . "/" . $article;
        $colors = array_diff(scandir($adir), array('..', '.'));

        foreach ($colors as $color) {

            //File renaming starts here

            $cdir = $adir . "/" . $color;
            $files = array_diff(scandir($cdir ), array('..', '.'));

            $i = 1;
            $isBkp = false;

            if (in_array('1.jpg', $files) || in_array('1.JPG', $files)) {
                $name = $article . "-" . $color . "-" ."1.jpg";

                if (in_array($name , $files)) {
                    rename($cdir . "/" . $name, $cdir . "/" . 'bkp.jpg' );
                    $isBkp = true;
                }

                rename($cdir . "/" . "1.jpg", $cdir . "/" . $name );
                rename($cdir . "/" . "1.JPG", $cdir . "/" . $name );

                $files = array_diff(scandir($cdir ), array('..', '.'));

                if ($isBkp) {
                    // first randomizing names except for 1 and bkp
                    $j = 2;
                    foreach ($files as $file) {
                        $path = $cdir . "/" . $file;

                        $newName = $j . ".jpg";
                        if ($file !== $name && $file !== 'bkp.jpg') {
                            rename($path, $cdir . "/" . $newName );
                        }
                        $j++;
                    }
                    $name2 = $article . "-" . $color . "-" ."2.jpg";
                    rename($cdir . "/" . "bkp.jpg", $cdir . "/" . $name2 );

                    $i=3;
                    $files = array_diff(scandir($cdir ), array('..', '.'));

                    foreach ($files as $file) {
                        $path = $cdir . "/" . $file;

                        $newName = $article . "-" . $color . "-" . $i . ".jpg";
                        if ($file !== $name && $file !== $name2) {
                            rename($path, $cdir . "/" . $newName );
                            $i++;
                        }
                    }


                }

                else {
                    // first randomizing names except for 1 and bkp
                    $j = 2;
                    foreach ($files as $file) {
                        $path = $cdir . "/" . $file;

                        $newName = $j . ".jpg";
                        if ($file !== $name) {
                            rename($path, $cdir . "/" . $newName );
                        }
                        $j++;
                    }


                    $i=2;
                    $files = array_diff(scandir($cdir ), array('..', '.'));

                    foreach ($files as $file) {
                        $path = $cdir . "/" . $file;

                        $newName = $article . "-" . $color . "-" . $i . ".jpg";
                        if ($file !== $name) {
                            rename($path, $cdir . "/" . $newName );
                            $i++;
                        }
                    }
                }

            }

            else {
                foreach ($files as $file) {
                    $path = $cdir . "/" . $file;

                    $newName = $article . "-" . $color . "-" . $i . ".jpg";
                    if (!in_array($newName, $files)) {
                        rename($path, $cdir . "/" . $newName );
                    }

                    $i++;
                }
            }

        }
    }
}

