<?php
namespace Dreamwhite\Assortment;
class Settings {
	private static $config = [
		"fromServer" => true,
		"async"      => true,
		"showProducts" =>true,
	];
	
	private static $ignoreLogs = [];
	
	public static function get( string $setting ) {
		return self::$config[ $setting ];
	}
	
	public static function set( string $setting, $value ) {
		self::$config[ $setting ] = $value;
	}
	
	public static function load() {
		if (PHP_SAPI === 'cli') {
			self::$config['fromServer'] = false;
		}
		else {
			if (isset($_GET['fromServer'])) {
				self::$config['fromServer'] = $_GET['fromServer'];
			}
		}
		
		if (isset($_GET['async'])) {
			self::$config['async'] = $_GET['async'];
		}
		
		if (isset($_GET['showProducts'])) {
			self::$config['showProducts'] = $_GET['showProducts'];
		}
		if (!self::$config['showProducts']) {
			Log::$ignore[] = "product";
			Log::$ignore[] = "variant";
		}
	}
}