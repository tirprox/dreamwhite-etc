<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 06.09.2017
 * Time: 3:38
 */
namespace Dreamwhite\Assortment;

use MongoDB\BSON\ObjectId;


class DescriptionManager {

    const OPTIONS = ['upsert' => true];

    static function import() {
        $mongoClient = MongoHelper::makeClient();

        $dreamwhiteDB = $mongoClient->selectDatabase('dreamwhite');
        $dwProductCollection = $dreamwhiteDB->selectCollection('products');

        $descriptionsDB = $mongoClient->selectDatabase('descriptions');
        $descProductCollection = $descriptionsDB->selectCollection('product');
        $descArticleCollection = $descriptionsDB->selectCollection('article');

        // Cursors from Descriptions DB (Article and Product)
        $descProducts = $descProductCollection->find();
        $descArticles = $descArticleCollection->find();

        //Cursor from Dreamwhite/Products
        $dwProducts = $dwProductCollection->find();

        // MS ID from products to Article ID, both from Strapi DB
        $msIdToArticleIdMap = [];

        // Article name to Article ID, both from Strapi DB
        $articleNameToIdMap = [];

        // Article ID to Article, both from Strapi DB. Created for performance reasons
        $articleIdToArticleMap = [];


        //Populating maps
        foreach ($descProducts as $descProduct) {
            $msIdToArticleIdMap[$descProduct["msid"]] = (string)$descProduct["article"];
        }

        foreach ($descArticles as $descArticle) {
            $articleNameToIdMap[$descArticle["name"]] = (string)$descArticle["_id"];
            $articleIdToArticleMap[(string)$descArticle["_id"]] = $descArticle;
        }



        // Looping dreamwhite product map
        foreach ($dwProducts as $product) {
            //Check if article is in a map already

            if (isset($product['article']) && $product['article'] !== "") {

                //First check if article id is mapped to ms product ID
                if (isset($msIdToArticleIdMap[$product["id"]] )) {

                    //Update article name and import product to article from map
                    $filter = ['id' => $msIdToArticleIdMap[$product["id"]]];

                    $update = ['name' => $product['article'], '__v' => 0 ];

                    $article = $articleIdToArticleMap[$msIdToArticleIdMap[$product["id"]]] ?? null;

                    if ($article !== null && $article['description'] === '') {
                        $update['description'] = $product['description'] !== '' ? $product['name'] . PHP_EOL .$product['description'] : '';
                    }

                    $set = ['$set' => $update];
                    $descArticleCollection->updateOne($filter, $set);

                    //Import product
                    $filter = ['msid' => $product["id"]];
                    $update = [
                        '$set' => [
                            'name' => $product['name'],
                            'msid' => $product['id'],
                            'primaryPhoto' => $product['primaryPhoto'],
                            'gallery' => $product['gallery'],
                            'article' => new ObjectId($msIdToArticleIdMap[$product["id"]]),
                            '__v' => 0
                        ]
                    ];
                    $descProductCollection->updateOne($filter, $update, self::OPTIONS);

                }
                //Then check by article name
                else if (isset( $articleNameToIdMap[$product["article"]] )) {

                    /*$filter = ['id' =>  $articleNameToIdMap[$product["article"]] ];
                    $update = [
                        '$set' => [
                            //'name' => $product['article'],
                            'description' => $product['description'] !== '' ? $product['name'] . PHP_EOL .$product['description'] : '',
                            '__v' => 0
                        ]
                    ];


                    $descArticleCollection->updateOne($filter, $update);*/

                    //Import product
                    $filter = ['msid' => $product["id"]];
                    $update = [
                        '$set' => [
                            'name' => $product['name'],
                            'msid' => $product['id'],
                            'primaryPhoto' => $product['primaryPhoto'],
                            'gallery' => $product['gallery'],

                            'article' => new ObjectId($articleNameToIdMap[$product["article"]]),
                        ]
                    ];
                    $msIdToArticleIdMap[$product["id"]]  = $articleNameToIdMap[$product["article"]];
                    $descProductCollection->updateOne($filter, $update, self::OPTIONS);

                }

                else {

                    //If not exists, create new article and store its _id
                    $newArticle = [
                        'name' => $product['article'],
                        'description' => $product['description'] !== '' ? $product['name'] . PHP_EOL .$product['description'] : '',
                        '__v' => 0
                    ];

                    $result = $descArticleCollection->insertOne($newArticle);
                    $msIdToArticleIdMap[$product['id']] = (string)$result->getInsertedId();
                    $articleNameToIdMap[$product["article"]] = (string)$result->getInsertedId();


                    $filter = ['msid' => $product["id"]];
                    $update = [
                        '$set' => [
                            'name' => $product['name'],
                            'msid' => $product['id'],
                            'primaryPhoto' => $product['primaryPhoto'],
                            'gallery' => $product['gallery'],
                            'article' => $result->getInsertedId(),
                            '__v' => 0
                        ]
                    ];
                    //Insert product to stored _id

                    $descProductCollection->updateOne($filter, $update, self::OPTIONS);


                }
            }
        }
    }

    static function export() {

    }
}
?>