<?php

$file_folder =  "/dirs";
$base_path = __DIR__ .  $file_folder;

fix_permissions();
set_extensions_to_lowercase();

recursive_dirname_cleanup($base_path);
report_empty_dirs($base_path);

//fix_filerun_path();

function fix_permissions() {
  echo shell_exec( "find . -type d -exec chmod 755 {} \;");
  echo shell_exec( "find . -type f  -exec chmod 644 {} \;");
}

function set_extensions_to_lowercase() {
  $sub = 'a=$(echo "$0" | sed -r "s/([^.]*)\$/\L\1/"); [ "$a" != "$0" ] && mv "$0" "$a" ';
  $cmd = "find . -name '*.*' -exec sh -c '$sub' {} \;";
  echo shell_exec($cmd);
}

function recursive_dirname_cleanup($path) {

  $dirs = get_dirs($path);
  foreach ($dirs as $dir) {

    $fixed_path = fix_dirname($dir);
    recursive_dirname_cleanup($fixed_path);
    rename_files_in_path($fixed_path);

  }
}

function get_dirs($path) {
  return glob($path . '/*' , GLOB_ONLYDIR);
}

function fix_dirname($path) {
  fix_multispace($path);
  trim_dir($path);

  return $path;
}

function fix_multispace(&$path) {
  $dirname = basename($path); // Get dir name from path
  $parent = dirname($path, 1);

  $fixed = preg_replace('/\s+/', ' ', $dirname);

  if ($fixed !== $dirname) {

    $fixed_path = "$parent/$fixed";

    rename($path, $fixed_path);

    show_message("Переименован: '$dirname' -> '$fixed'", "(несколько пробелов подряд)\n");

    $path = $fixed_path;
  }

}

function trim_dir(&$path) {
  $dirname = basename($path); // Get dir name from path
  $parent = dirname($path, 1);

  $trimmed = trim($dirname);

  if ($trimmed !== $dirname) {

    $trimmed_path = "$parent/$trimmed";

    rename($path, $trimmed_path);

    show_message("Переименован: '$dirname' -> '$trimmed'", "(лишний пробел)\n");
    $path = $trimmed_path;
  }
}

function show_message($message, $comment = "\n") {
  if (php_sapi_name() === 'cli') {
    echo "$message $comment";
  } else {
    echo "<p>$message <b>$comment</b></p>";
  }
}

function report_empty_dirs($path) {
  $output = explode("\n", trim(shell_exec( "cd $path && find . -type d -empty")));
  show_message("-------------");
  show_message("Пустые папки:");
  foreach ($output as $line) {
    show_message($line);
  }
}

function recursive_file_rename() {




}

function fix_filerun_path() {
  shell_exec("/opt/plesk/php/7.4/bin/php ./cron/paths_cleanup.php");
}




function rename_files_in_path($path) {
  $color = basename($path); // Get dir name from path
  $article = basename(dirname($path, 1));
  $category = basename(dirname($path, 2));

  echo "$category | $article | $color \n";

}

function rename_files($files, $article, $color, $colorDirPath) {

  if (!empty($files)) {
    $i = 1;
    $isBkp = false;

    /* if file 1.jpg is in a folder */
    if (in_array('1.jpg', $files) || in_array('1.JPG', $files)) {
      $name = "$article-$color-1.jpg";

      if (in_array($name, $files)) {


        rename("$colorDirPath/$name", "$colorDirPath/bkp.jpg");
        $isBkp = true;
      }

      rename("$colorDirPath/1.jpg", "$colorDirPath/$name");
      rename("$colorDirPath/1.JPG", "$colorDirPath/$name");

      $files = array_diff(scandir($colorDirPath), array('..', '.'));

      if ($isBkp) {
        // first randomizing names except for 1 and bkp
        $j = 2;
        foreach ($files as $file) {
          $path = $colorDirPath . "/" . $file;

          $newName = $j . ".jpg";
          if ($file !== $name && $file !== 'bkp.jpg') {
            rename($path, $colorDirPath . "/" . $newName);
          }
          $j++;
        }
        $name2 = $article . "-" . $color . "-" . "2.jpg";
        rename($colorDirPath . "/" . "bkp.jpg", $colorDirPath . "/" . $name2);

        $i = 3;
        $files = array_diff(scandir($colorDirPath), array('..', '.'));

        foreach ($files as $file) {
          $path = $colorDirPath . "/" . $file;

          $newName = $article . "-" . $color . "-" . $i . ".jpg";
          if ($file !== $name && $file !== $name2) {
            rename($path, $colorDirPath . "/" . $newName);
            $i++;
          }
        }

      }
      else {
        // first randomizing names except for 1 and bkp
        $j = 2;
        foreach ($files as $file) {
          $path = $colorDirPath . "/" . $file;

          $newName = $j . ".jpg";
          if ($file !== $name) {
            rename($path, $colorDirPath . "/" . $newName);
          }
          $j++;
        }

        $i = 2;
        $files = array_diff(scandir($colorDirPath), array('..', '.'));

        foreach ($files as $file) {
          $path = $colorDirPath . "/" . $file;

          $newName = $article . "-" . $color . "-" . $i . ".jpg";
          if ($file !== $name) {
            rename($path, $colorDirPath . "/" . $newName);
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
        $path = $colorDirPath . "/" . $files[$j];
        $newName = $article . "-" . $color . "-" . $i . ".jpg";

        if (!in_array($newName, $files)) {
          rename($path, $colorDirPath . "/" . $newName);
          $files[$j] = $newName;
        }

        $i++;
      }
    }
  }
}