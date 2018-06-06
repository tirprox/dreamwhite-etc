<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 5/31/18
 * Time: 5:47 PM
 */

namespace Dreamwhite\Assortment;


class JSONWriter
{

    static function write($data, $filename) {
        $path = dirname(__DIR__) . "/output/" .  $filename;
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        file_put_contents($path, $json);
        return true;
    }
}