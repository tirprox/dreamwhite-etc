<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 9/26/18
 * Time: 4:25 PM
 */

namespace Dreamwhite\Assortment;


class MongoHelper
{

    private const LOGIN = 'admin', PASSWORD = '6h8s4ksoq';
    //private const URI = 'mongodb://' . self::LOGIN . ':' . self::PASSWORD . '@localhost:27017';

    private static function constructUri($login, $password) {
        return 'mongodb://' . $login . ':' . $password . '@localhost:27017';
    }

    public static function makeClient() {
        return new \MongoDB\Client(self::constructUri(self::LOGIN, self::PASSWORD));
    }


}