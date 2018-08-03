<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 8/3/18
 * Time: 3:29 PM
 */

namespace Dreamwhite\Assortment;


class TagMap
{
    public static $colors = [], $sizes = [];
    public static $attrs = [
        'color' => [],
        'size' => []
    ];

    public static function addAttribute($attr, $value) {
        if (isset(self::$attrs[$attr])) {
            if (!in_array($value, self::$attrs[$attr])) {
                self::$attrs[$attr][] = $value;
            }
        }
        else {
            self::$attrs[$attr][] = $value;
        }

    }

    public static function getAll() {
        return self::$attrs;
    }

    public static function getAttribute($attr) {
        return self::$attrs[$attr];
    }

    public static function getAttributeString($attr) {
        return implode(',', self::$attrs[$attr]);
    }

}