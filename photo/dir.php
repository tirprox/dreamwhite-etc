<?php
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

            $cdir = $adir . "/" . $color;
            $files = array_diff(scandir($cdir ), array('..', '.'));

                foreach ($files as $file) {
                    $path = $cdir . "/" . $file;
                    $tree[$cat][$article][$color][] = $file;
                }

        }
    }
}

echo json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);