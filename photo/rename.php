<?php

$categoryDirNames = getDirectories(__DIR__);

foreach ($categoryDirNames as $categoryDirName) {
    $catDirPath = __DIR__ . "/" . $categoryDirName;
    $articleDirNames = getDirectories($catDirPath, $catDirPath);

    foreach ($articleDirNames as $articleDirName) {

        $articleDirPath = $catDirPath . "/" . $articleDirName;
        $colorDirNames = getDirectories($articleDirPath, $articleDirPath);

        if (!empty($colorDirNames)) {
            foreach ($colorDirNames as $colorDirName) {
                $colorDirPath = $articleDirPath . "/" . $colorDirName;
                renameFiles(getFiles($colorDirPath), $articleDirName, $colorDirName, $colorDirPath);
            }
        }
    }
}

function getDirectories($directory, $prefix = "") {
    $files = array_diff(scandir($directory), array('..', '.'));

    $dirs = [];
    foreach ($files as $file) {
        if ($prefix !== "") {
            if (is_dir($prefix . "/" . $file)) {
                $dirs[] = $file;
            }
        }
        else {
            if (is_dir($file)) {
                $dirs[] = $file;
            }
        }

    }
    return $dirs;
}

function getFiles($directory) {
    return array_diff(scandir($directory), array('..', '.'));
}

function renameFiles($files, $article, $color, $colorDir) {
    if (!empty($files)) {
        $i = 1;
        $isBkp = false;

        /* if file 1.jpg is in a folder */
        if (in_array('1.jpg', $files) || in_array('1.JPG', $files)) {
            $name = $article . "-" . $color . "-" . "1.jpg";

            if (in_array($name, $files)) {
                rename($colorDir . "/" . $name, $colorDir . "/" . 'bkp.jpg');
                $isBkp = true;
            }

            rename($colorDir . "/" . "1.jpg", $colorDir . "/" . $name);
            rename($colorDir . "/" . "1.JPG", $colorDir . "/" . $name);

            $files = array_diff(scandir($colorDir), array('..', '.'));

            if ($isBkp) {
                // first randomizing names except for 1 and bkp
                $j = 2;
                foreach ($files as $file) {
                    $path = $colorDir . "/" . $file;

                    $newName = $j . ".jpg";
                    if ($file !== $name && $file !== 'bkp.jpg') {
                        rename($path, $colorDir . "/" . $newName);
                    }
                    $j++;
                }
                $name2 = $article . "-" . $color . "-" . "2.jpg";
                rename($colorDir . "/" . "bkp.jpg", $colorDir . "/" . $name2);

                $i = 3;
                $files = array_diff(scandir($colorDir), array('..', '.'));

                foreach ($files as $file) {
                    $path = $colorDir . "/" . $file;

                    $newName = $article . "-" . $color . "-" . $i . ".jpg";
                    if ($file !== $name && $file !== $name2) {
                        rename($path, $colorDir . "/" . $newName);
                        $i++;
                    }
                }

            }
            else {
                // first randomizing names except for 1 and bkp
                $j = 2;
                foreach ($files as $file) {
                    $path = $colorDir . "/" . $file;

                    $newName = $j . ".jpg";
                    if ($file !== $name) {
                        rename($path, $colorDir . "/" . $newName);
                    }
                    $j++;
                }

                $i = 2;
                $files = array_diff(scandir($colorDir), array('..', '.'));

                foreach ($files as $file) {
                    $path = $colorDir . "/" . $file;

                    $newName = $article . "-" . $color . "-" . $i . ".jpg";
                    if ($file !== $name) {
                        rename($path, $colorDir . "/" . $newName);
                        $i++;
                    }
                }
            }

        }

        /* if no file named 1.jpg present in a folder */
        else {

            $files = array_values($files);
            $fileCount = count($files);
            for ($j = 0; $j < $fileCount; $j++) {
                $path = $colorDir . "/" . $files[$j];
                $newName = $article . "-" . $color . "-" . $i . ".jpg";

                if (!in_array($newName, $files)) {
                    rename($path, $colorDir . "/" . $newName);
                    $files[$j] = $newName;
                }

                $i++;
            }
        }
    }
}

