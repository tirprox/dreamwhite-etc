<?php

const FILE_FOLDER =  "dirs";
const DELETE_ATTACHMENTS_URL = 'https://dreamwhite.ru/attachments/update-attachments.php';


const BASE_PATH = __DIR__ . '/' .  FILE_FOLDER;

const DIR_PERM_CMD = "find " . FILE_FOLDER . " -type d -exec chmod 755 {} \;";
const FILE_PERM_CMD = "find " . FILE_FOLDER . " -type f  -exec chmod 644 {} \;";
$renamed_files = [];

fix_permissions();
set_extensions_to_lowercase();

recursive_dirname_cleanup(BASE_PATH);
report_empty_dirs(BASE_PATH);

fix_filerun_path();
request_delete_attachments($renamed_files);



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
  global $renamed_files;

  $dirs = get_dirs($path);
  foreach ($dirs as $dir) {

    $fixed_path = fix_dirname($dir);
    recursive_dirname_cleanup($fixed_path);
    rename_files_in_path($fixed_path, $renamed_files);

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

    show_message("Переименован: '$dirname' -> '$fixed'", "(несколько пробелов подряд)");

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

    if ($comment !== "\n") {
      echo "\n";
    }
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

function fix_filerun_path() {
  shell_exec("/opt/plesk/php/7.4/bin/php ./cron/paths_cleanup.php");
}


function request_delete_attachments($renamed_files) {

  if (count($renamed_files) <= 0) {
    return;
  }

  $json = json_encode($renamed_files, JSON_UNESCAPED_UNICODE);

  $options = array(
    'http' => array(
      'header'  => "Content-type: application/json\r\n",
      'method'  => 'POST',
      'content' => $json
    )
  );

  $context  = stream_context_create($options);

  $result = file_get_contents(DELETE_ATTACHMENTS_URL, false, $context);

  if ($result === false) {
    show_message("Запрос на удаление фото с сайта не удался");
  }

  print_r($result);

}


function rename_files_in_path($path, &$renamed) {
  $fdir = basename(dirname($path, 3));

  if ($fdir === FILE_FOLDER) {
    $color = basename($path); // Get dir name from path
    $article = basename(dirname($path, 1));
//    $category = basename(dirname($path, 2));

    $prefix = "$article-$color-";

    // File sorting buckets:
    $preordered = []; // 1.jpg
    $existing = []; // Черный-1.jpg
    $random = []; // asd123.jpg


    $file_count = 0;

    $position =1;
    foreach (new DirectoryIterator($path) as $file) {
      if ($file->isFile()) {
        if ($file->isDot()) continue;

        $file_count++;

        $strip_ext = str_replace('.' . $file->getExtension(), '', $file->getFilename());

        $f = [];
        $f['name'] = $file->getFilename();
        $f['strip_ext'] = $strip_ext;
        $f['ext'] = $file->getExtension() ?? '';
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



    // 2. Assign numbers
    $sorted = [];

    for ($i = 1; $i <= $file_count; $i++ ) {
      $sorted[$i] = null;
    }

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




    $conflicting = [];

    ksort($existing);

    foreach ($existing as $f) {
      // resolve position conflict
      $pp = $f['preferred_position'];

      if (($pp <= $file_count) && ($sorted[$f['preferred_position']] !== null)) {
        $conflicting[$f['preferred_position']] = $f;
      } else {
        $sorted[$f['preferred_position']] = $f;
      }

    }

    $available = array_reverse(array_keys($sorted, null, true));


    for ($i = 1; $i <= $file_count; $i++ ) {
      if (array_key_exists($i, $conflicting )) {
        $position = array_pop($available);
        $sorted[$position] = $conflicting[$i];
      }
    }


    foreach ($random as $file) {
      $position = array_pop($available);
      $sorted[$position] = $file;
    }


    $renaming_list = [];
    foreach ($sorted as $position => $file) {
      $new_name = $file['ext'] !== '' ? "$prefix$position." . $file['ext'] : "$prefix$position";
      $sorted[$position]['new_name'] = $new_name;

      if ($new_name !== $file['name']) {
        $renaming_list[$file['name']] = $new_name;
        $renamed[] = "$prefix$position";
      }
    }

    // 3. Set random filenames
    $r=[];

    $rc = 1;
    $renamed_count = 0;
    foreach ($renaming_list as $name => $new_name) {
      rename("$path/$name", "$path/$rc");
      $r[$rc] = $new_name;
      $rc++;

      $renamed_count++;
    }

    // 4. Rename files in specified order by addressing their inode number
    foreach ($r as $name => $new_name) {
      rename("$path/$name", "$path/$new_name");

    }

    if ($renamed_count > 0) {
      show_message("Переименован:", "$article $color");
    }
  }
}