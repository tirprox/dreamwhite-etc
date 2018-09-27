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


    public static function encode($product, $stock) {
        $json = [];

        $json['id'] = $product->id;
        $json['name'] = $product->name;
        $json['gender'] = $product->gender;
        $json['type'] = $product->type;


        $json['stock'] = intval(array_sum($stock[$product->code]));
        $json['inStock'] = $stock  > 0;
        $json['hasImage'] = $product->hasImages;
        $json['sku'] = $product->code;
        $json['attributes'] = $product->attrs;
        $json['variants'] = [];

        foreach ($product->variants as $variant) {
            $varJson = [];
            $varJson['id'] = $variant->id;
            $varJson['name'] = $variant->name;
            $varJson['sku'] = $variant->code;
            $varJson['size'] = $variant->size;

            $varJson['stock'] = intval(array_sum($stock[$variant->code]));

            $json['variants'][] = $varJson;
        }

        return $json;

    }
}