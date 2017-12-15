<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 06.09.2017
 * Time: 3:38
 */

class Log {
	private static $isLoggingEnabled = false;
	public static $ignore = [
		'http-client',
		'groups',
	];
	
	public static function d( $string = "", $issuer = "default", $tag="p") {
		if ( self::$isLoggingEnabled ) {
			if ( ! in_array( $issuer, self::$ignore ) ) {
				if ( Settings::get( "fromServer" ) ) {
					print( "<$tag>$string</$tag>");
				} else {
					print( $string . "\n" );
				}
			}
			
		}
	}
	
	public static function startSection($name, $tag="p") {
	
	}
	public static function endSection($name, $tag="p") {
	
	}
	
	
	public static function i( $string ) {
		print( $string );
	}
	
	public static function enable() {
		self::$isLoggingEnabled = true;
	}
	
	public static function disable() {
		self::$isLoggingEnabled = false;
	}
	
	public static function isLogging() {
		return self::$isLoggingEnabled;
	}
}