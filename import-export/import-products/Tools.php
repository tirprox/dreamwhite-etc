<?php
/**
 * Created by PhpStorm.
 * User: DreamWhite
 * Date: 06.11.2017
 * Time: 14:14
 */

class Tools {
	static function match($haystack, $needle){
		if( mb_stripos( $haystack, $needle, 0, 'UTF-8' ) !== false ) {
			return true;
		}
		else return false;
	}
	
	static $imageDirList = [];
}

