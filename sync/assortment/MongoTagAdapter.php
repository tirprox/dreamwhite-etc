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
    //private const URI = 'mongodb://' . self::LOGIN . ':' . self::PASSWORD . '@localhost:27017';

    private $client;

    private $db, $collection;

    private function constructUri($login, $password) {
        return 'mongodb://' . $login . ':' . $password . '@localhost:27017';
    }





    public function __construct($login = self::LOGIN, $password = self::PASSWORD)
    {
        $this->client = new \MongoDB\Client($this->constructUri($login, $password));

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

    public function updateOne($item) {
        $options = ['upsert' => true];
        $filter = ['name' => $item['name']];

        $record = [
            'name' => $item['name'],
            'slug' => $item['slug'],
            'relations' => $item['relations'],
            'attributes' => $item['realAttrs'],
            'seo' => $item['seo'],
        ];
        $this->collection->updateOne($filter, ['$set' => $record], $options);
    }

    public function updateAll($data) {
        $options = ['upsert' => true];

        foreach ($data as $item) {
            $filter = ['name' => $item['name']];

            $record = [
                'name' => $item['name'],
                'slug' => $item['slug'],
                'relations' => $item['relations'],
                'attributes' => $item['realAttrs'],
                'seo' => $item['seo'],
            ];

            $this->collection->updateOne($filter, ['$set' => $record], $options);
        }
    }

    private function getAttributeName($attr) {
        return "attributes.$attr";
    }
    private function getRelationName($relation) {
        return "relations.$relation";
    }


}