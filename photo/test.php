<?php

const FILE_FOLDER =  "dirs";


const BASE_PATH = __DIR__ . '/' .  FILE_FOLDER;

const DIR_PERM_CMD = "find " . FILE_FOLDER . " -type d -exec chmod 755 {} \;";
const FILE_PERM_CMD = "find " . FILE_FOLDER . " -type f  -exec chmod 644 {} \;";


fix_permissions();
set_extensions_to_lowercase();

recursive_dirname_cleanup(BASE_PATH);
report_empty_dirs(BASE_PATH);

//fix_filerun_path();

function fix_permissions() {

  echo shell_exec( DIR_PERM_CMD);
  echo shell_exec( FILE_PERM_CMD);
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
  $fdir = basename(dirname($path, 3));

  if ($fdir === FILE_FOLDER) {
    $color = basename($path); // Get dir name from path
    $article = basename(dirname($path, 1));
    $category = basename(dirname($path, 2));

    $prefix = "$article-$color-";

    // File sorting buckets:
    $preordered = []; // 1.jpg
    $existing = []; // Черный-1.jpg
    $random = []; // asd123.jpg


    $file_count = 0;
    foreach (new DirectoryIterator($path) as $file) {
      if ($file->isFile()) {
        if ($file->isDot()) continue;

        $file_count++;

        $strip_ext = str_replace('.' . $file->getExtension(), '', $file->getFilename());

        $f = [];
        $f['name'] = $file->getFilename();
        $f['strip_ext'] = $strip_ext;
        $f['ext'] = $file->getExtension();
        $f['inode'] = $file->getInode();

        // 1. Sort files into buckets (auto priority)

        if (ctype_digit($strip_ext)) {
          // Preordered
          $n = intval($strip_ext);

//          echo "Preordered: $strip_ext, position: $n\n";
          $f['preferred_position'] = $n;
          $preordered[$f['name']] = $f;

        }  else if (strpos($strip_ext, $prefix) !== false) {
          // Existing
          $n = intval(str_replace($prefix, '', $strip_ext));
//          echo "Existing: $strip_ext, position: $n\n";
          $f['preferred_position'] = $n;
          $existing[$f['name']] = $f;


        } else {
          // Random
//          echo "Random: $strip_ext\n";
          $f['preferred_position'] = 0;
          $random[$f['name']] = $f;

        }
      }
    }

    $sorted = [];

    // if preordered file number is bigger then total amount of files, move it to random
    foreach ($preordered as $f) {
      if ($f['preferred_position'] > $file_count) {
        $f['preferred_position'] = 0;
        $random[$f['name']] = $f;
        unset($preordered[$f['name']]);
      } else {
        $sorted[$f['preferred_position']] = $f;
      }
    }

    $available = [];

    for ($i = 1; $i <= $file_count; $i++) {
      if (!array_key_exists($i, $sorted)) {
        $available[] = $i;
      }
    }


    $conflicting = [];

    ksort($existing);

    foreach ($existing as $f) {
      // resolve position conflict
      if (isset($sorted[$f['preferred_position']])) {
        $conflicting[$f['preferred_position']] = $f;
      } else {
        $sorted[$f['preferred_position']] = $f;
        unset($available[$f['preferred_position']]);
      }

    }



    /*
    $first_available_position = array_pop(array_reverse($available));
    $f['preferred_position'] = $first_available_position;
    unset($available[$i]);

    */


    echo "Sorted $color";





    // 2. Assign numbers

    // 3. Set random filenames

    // 4. Rename files in specified order by addressing their inode number



  }
}

function resolve_renaming_order($files, $desiredFilenames) {

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