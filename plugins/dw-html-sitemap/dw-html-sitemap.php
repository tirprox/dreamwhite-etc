<?php
/*
Plugin Name: DreamWhite HTML Карта Сайта
Plugin URI:
Description: Выводит HTML карту сайта
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-html-sitemap
Domain Path: /languages
*/

require_once(dirname(__DIR__) . "/dw-common/vendor/autoload.php");

use MongoDB\Client;

add_shortcode('dw_html_sitemap', 'dw_html_sitemap_shortcode');

function dw_html_sitemap_shortcode()
{


    $LOGIN = 'admin';
    $PASSWORD = '6h8s4ksoq';
    $URI = 'mongodb://@localhost:27017';

    $client = new Client($URI, [
        "username" => $LOGIN,
        "password" => $PASSWORD
    ]);

    $db = $client->selectDatabase('tags');
    $collection = $db->selectCollection('tag-test');

    $mongo = new MongoHelper($collection);

    $level1 = $collection->find(
        ['relations.level' => 1]
    );

    echo "<h2>Каталог</h2>";
    foreach ($level1 as $topLevel) {
        echo "<h3>";
        LinkHelper::a($topLevel);
        echo "</h3>";


        $children = $mongo->getChildren($topLevel->name);


        echo '<ul>';


        foreach ($children as $child) {

            echo '<li>';
            LinkHelper::a($child);

            $level3 = $mongo->getChildren($child->name);

            echo '<ul>';

            foreach ($level3 as $child3) {
                echo '<li>';
                LinkHelper::a($child3);


                $level4 = $mongo->getChildren($child3->name);
                echo '<ul>';
                foreach ($level4 as $child4) {
                    echo '<li>';
                    LinkHelper::a($child4);

                    $level5 = $mongo->getChildren($child4->name);

                    echo '<ul>';

                    foreach ($level5 as $child5) {
                        echo '<li>';
                        LinkHelper::a($child5);
                        echo '</li>';

                    }
                    echo '</ul>';


                    echo '</li>';

                }
                echo '</ul>';

                echo '</li>';
            }

            echo '</ul>';


            echo '</li>';
        }


        echo '</ul>';

    }

    /*echo "<h3>";
    echo "<a href='/catalog/rasprodazha/'>Распродажа</a>";
    echo "</h3>";*/
    echo '<br>';



}

class LinkHelper
{

    public static function a($item)
    {
        echo "<a href='/catalog/$item->slug/'>$item->name</a>";
    }
}

class MongoHelper
{
    private $collection;

    function __construct($collection)
    {
        $this->collection = $collection;
    }

    function getChildren($name)
    {
        return $this->collection->find(
            [
                'relations.parent' => $name,
                'relations.hasRecords' => 1
            ]
        );
    }
}