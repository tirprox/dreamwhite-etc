<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 06.09.2017
 * Time: 3:38
 */
namespace Dreamwhite\Import;
class Log {
	private static $isLoggingEnabled = false;
	public static $ignore = [
		//'http-client',
		//'groups',
	];
	
	public static $sections;
	
	public static function d( $string = "", $issuer = "default", $tag="p", $section = "config") {
		if ( self::$isLoggingEnabled ) {
         $section = $issuer;
			if ( ! in_array( $issuer, self::$ignore ) ) {
				if ( Settings::get( "fromServer" ) ) {
               self::$sections[$section] .= "<$tag>$string</$tag>";
               //self::$sections[$section] = "Test";
					//print( "<$tag>$string</$tag>");
				} else {
					print( $string . "\n" );
				}
			}
			
		}
	}
	
	public static function writeSections() {
      $first = true;
      echo "<ul class='nav nav-tabs'>";
         foreach (self::$sections as $name => $content) {
            if ($first) {
               echo "<li class='active'><a data-toggle='tab' href='#$name'>$name</a></li>";
               $first = false;
            }
            else {
               echo "<li><a data-toggle='tab' href='#$name'>$name</a></li>";
            }
         }
       echo "</ul>";
	   

	   echo "<div class='panel panel-default'>";
      echo "<div class='panel-body'>";
	   echo "<div class='tab-content'>";
	   
	   $first = true;
	   foreach (self::$sections as $name => $content) {
	      if ($first) {
            echo "<div id='$name' class='tab-pane active'>";
            $first = false;
         }
         else {
            echo "<div id='$name' class='tab-pane'>";
         }
	      
         //echo "<h3>$name</h3>";
	      echo $content;
         echo "</div>";
      }
      echo "</div></div></div>";
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
?>