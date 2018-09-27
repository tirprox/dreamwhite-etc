<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 8/29/18
 * Time: 5:35 PM
 */

namespace Dreamwhite\Plugins\ProductFilter;


use MongoDB\Client;


require_once dirname(__DIR__) . "/includes.php";

//require_once dirname(__DIR__) . "/vendor/autoload.php";

class MongoAdapter
{
    private const LOGIN = 'admin', PASSWORD = '6h8s4ksoq';
    private const URI = 'mongodb://' . self::LOGIN . ':' . self::PASSWORD . '@localhost:27017';
    private const URI2 = 'mongodb://@localhost:27017';
    private $client;

    private $db, $collection;

    public static function makeClient() {
        return new Client(self::URI2, [
            "username" => self::LOGIN,
            "password" => self::PASSWORD
        ]);
    }

    public static function selectCollection($database, $collection) {
        $client = self::makeClient();
        return $client->selectCollection($database, $collection);
    }

    public function __construct()
    {
        //$this->client = new Client(self::URI);
        $this->client = new Client(self::URI2, [
            "username" => self::LOGIN,
            "password" => self::PASSWORD
        ]);
        $this->setCollection();

    }

    public function getCollection() {
        return $this->collection;
    }

    public function setDB($db = 'tags') {
        $this->db = $this->client->selectDatabase($db);
    }

    public function setCollection($collection = 'tags') {
        if ($this->db !== null) {
            $this->collection = $this->db->selectCollection($collection);
        }
        else {
            $this->setDB();
            $this->collection = $this->db->selectCollection($collection);
        }

    }

    public function distinct($query, $filter) {
        return $this->collection->distinct($query, $filter);
    }

    public function getDistinct($attr, $gender, $type) {

        return $this->distinct('attributes.' . $attr,
            [
                'relations.filterable' => 1,
                'relations.hasRecords' => 1,
                'relations.type' => $type,
                'relations.gender' => $gender,
            ]);

    }

    public function findOne($query) {
        return $this->collection->find($query);
    }

    // Takes map of attribute name => value
    public function find($attributes, $relations) {

        $query = [];

        foreach ($attributes as $name => $value) {
            $query[$this->getAttributeName($name)] = [
                '$eq' => $value,
//                '$size' => 1
            ];
        }
        foreach ($relations as $name => $value) {
            $query[$this->getRelationName($name)] = $value;
        }


        return $this->collection->find($query);

    }

    private function getAttributeName($attr) {
        return "attributes.$attr";
    }
    private function getRelationName($relation) {
        return "relations.$relation";
    }


}