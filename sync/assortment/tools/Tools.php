<?php
/**
 * Created by PhpStorm.
 * User: DreamWhite
 * Date: 06.11.2017
 * Time: 14:14
 */
namespace Dreamwhite\Assortment;
class Tools {
   static $iso9_table = [
      'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G',
      'Ґ' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE',
      'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'J',
      'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K',
      'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N',
      'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
      'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS',
      'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '',
      'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',
      'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
      'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
      'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'j',
      'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k',
      'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n',
      'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
      'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
      'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '',
      'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
   ];
   
   
   static function match($haystack, $needle) {
      if (mb_stripos($haystack, $needle, 0, 'UTF-8') !== false) {
         return true;
      }
      else {
         return false;
      }
   }
   static function removeYoutubeBase(string $url) {
   	if ($url === "") {
   		return $url;
    }
    else {
	    $url = str_replace("https://www.youtube.com/watch?v=", "", $url);
	    return "[youtube]" . $url . "[/youtube]";
    }
   	
	}
	
   static function transliterate($text) {
      $text = strtr($text, self::$iso9_table);
      if (function_exists('iconv')) {
         $text = iconv('UTF-8', 'UTF-8//TRANSLIT//IGNORE', $text);
      }
      $text = preg_replace("/[^A-Za-z0-9'_\-\.]/", '-', $text);
      $text = preg_replace('/\-+/', '-', $text);
      $text = preg_replace('/^-+/', '', $text);
      $text = preg_replace('/-+$/', '', $text);
      return $text;
   }

    static function encodeWhitespace($string) {
        return str_replace(' ', '%20', $string);
    }
   
   static $imageDirList = [];

    static $imageTree = [];
}

