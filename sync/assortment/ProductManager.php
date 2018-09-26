<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 9/26/18
 * Time: 4:17 PM
 */

namespace Dreamwhite\Assortment;


class ProductManager
{


    public static function encode($product) {
        $json = [];

        $json['id'] = $product->id;
        $json['name'] = $product->name;
        $json['gender'] = $product->gender;
        $json['type'] = $product->type;
        $json['sku'] = $product->code;
        $json['attributes'] = $product->attrs;
        $json['variants'] = [];

        foreach ($product->variants as $variant) {
            $varJson = [];
            $varJson['id'] = $variant->id;
            $varJson['name'] = $variant->name;
            $varJson['sku'] = $variant->code;
            $varJson['size'] = $variant->size;

            $varJson['stock'] = intval($variant->stock);

            $json['variants'][] = $varJson;
        }

        return $json;

    }
}