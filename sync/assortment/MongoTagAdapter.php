<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 8/29/18
 * Time: 5:35 PM
 */

namespace Dreamwhite\Assortment;

require "includes.php";

class MongoTagAdapter
{
    private const LOGIN = 'admin', PASSWORD = '6h8s4ksoq';
    private const URI = 'mongodb://' . self::LOGIN . ':' . self::PASSWORD . '@localhost:27017';

    private const MOCK_ATTRS = [
        'colorGroup' => 'Черный',
    ];
    private const MOCK_RELS = [
        'gender' => 'Женские',
        'type' => 'Пальто'
    ];

    private $client;

    private $db, $collection;

    private $query = [];

    public function __construct()
    {
        $this->client = new \MongoDB\Client(self::URI);

        $this->setCollection();

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