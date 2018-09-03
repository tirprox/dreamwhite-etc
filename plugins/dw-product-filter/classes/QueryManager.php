<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 8/15/18
 * Time: 2:21 PM
 */

namespace Dreamwhite\Plugins\ProductFilter;
class QueryManager
{

    private $queryParams = [];


    private const ATTRIBUTES = [

        'color',
        'colorGroup',
        'texture',
        'material',
        'season',
        'uteplitel',
        'podkladka',
        'siluet',
        'dlina',
        'rukav',
        'dlina_rukava',
        'zastezhka',
        'kapushon',
        'vorotnik',
        'poyas',
        'karmany',
        'koketka',
        'uhod',],

        RELATIONS = [
        'type',
        'gender',
        'filterable',
    ];

    public function setQueryParameter($name, $value)
    {
        if ($value !== '' && in_array($name, Attrs::VALUES)) {
            $this->queryParams[$name] = $value;
            set_query_var($name, $value);
        }

    }

    public function getQueryParameter($name)
    {
        return get_query_var($name);
    }

    public function deleteQueryParameter($name)
    {
        unset($this->queryParams[$name]);
        set_query_var($name, '');
    }


    // Accepts TaxonomyParams as an argument
    public function fromTaxonomyParams($taxonomy)
    {
        foreach ($taxonomy->getParams() as $name => $value) {
            $this->setQueryParameter($name, implode(',', $value));
        }
    }

    public function getMongoQuery()
    {
        $metaQuery = [];

        foreach ($this->queryParams as $name => $value) {

            if (in_array($name, self::ATTRIBUTES)) {
                $metaQuery['attributes'][$name] =  $value;
            }
            else {
                $metaQuery['relations'][$name] = $value;
            }

        }

        return $metaQuery;
    }

    public function getQueryArgs()
    {
        $metaQuery = [];

        foreach ($this->queryParams as $name => $value) {
            $metaQuery[] = [
                'key' => $name,
                'value' => $value,
                'compare' => '='
            ];
        }

        $args = [
            'taxonomy' => FilterConfig::TAX_NAME,
            'hide_empty' => true,
            'meta_query' => $metaQuery
        ];


        return $args;
    }
}