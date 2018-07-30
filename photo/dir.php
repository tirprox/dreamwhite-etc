<?php
$tree = [];

$categories = getDirsOnly(array_diff(scandir(__DIR__), array('..', '.')));



foreach ($categories as $cat) {
    $dir = __DIR__ . "/" . $cat;

    $articles = getDirsOnly(array_diff(scandir($dir), array('..', '.')), $dir);



    foreach ($articles as $article) {

        $adir = $dir . "/" . $article;
        $colors = getDirsOnly(array_diff(scandir($adir), array('..', '.')), $adir);

        if (!empty($colors)) {
            foreach ($colors as $color) {

                $cdir = $adir . "/" . $color;
                $files = array_diff(scandir($cdir ), array('..', '.'));

                if(!empty($files)) {
                    foreach ($files as $file) {
                        $path = $cdir . "/" . $file;
                        $tree[$cat][$article][$color][] = $file;
                    }
                }
            }
        }
    }
}

echo json_encode($tree, JSON_UNESCAPED_UNICODE);

function getDirsOnly($array, $prefix = "") {
    $dirs = [];
    foreach ($array as $file) {
        if ($prefix !== "") {
            if (is_dir($prefix . "/" . $file)) {$dirs[] =  $file;}
        }
        else {
            if (is_dir($file)) {$dirs[] =  $file;}
        }

    }
    return $dirs;
}