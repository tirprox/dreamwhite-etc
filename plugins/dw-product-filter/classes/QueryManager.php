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

    public const GENDER_TYPE_MAP = [
        'Мужские' => [
            'Пальто' => '/muzhskie-palto/',

        ],
        'Женские' => [
            'Пальто' => '/zhenskie-palto/',
            'Плащ' => '/zhenskie-plashhi/',
            'Жилет' => '/zhilety/',
            'Пуховик' => '/kurtki/zhenskie-kurtki/zhenskie-puhoviki/'
        ]
    ];

    public const BASE_MAP = [
        'Женские пальто' => '/zhenskie-palto/',
        'Женские плащи' => '/zhenskie-plashhi/',
        'Жилеты' => '/zhilety/',
        'Мужские пальто' => '/muzhskie-palto/',
        'Женские пуховики' => '/kurtki/zhenskie-kurtki/zhenskie-puhoviki/',
    ];

    private const WC_PREFIX = 'pa_';
    private const WC_ATTR_ALIAS = [
        'color' => self::WC_PREFIX . 'tsvet',
        'size'=> self::WC_PREFIX . 'razmer',
        'colorGroup'=> self::WC_PREFIX . 'tsvetovaya-gruppa',
        'texture'=> self::WC_PREFIX . 'tekstura',
        'material'=> self::WC_PREFIX . 'material',
        'season'=> self::WC_PREFIX . 'sezon',
        'uteplitel'=> self::WC_PREFIX . 'uteplitel',
        'podkladka'=> self::WC_PREFIX . 'podkladka',
        'siluet'=> self::WC_PREFIX . 'siluet',
        'dlina'=> self::WC_PREFIX . 'dlina-izdeliya',
        'rukav'=> self::WC_PREFIX . 'rukav',
        'dlina_rukava'=> self::WC_PREFIX . 'dlina-rukava',
        'zastezhka'=> self::WC_PREFIX . 'zastezhka',
        'kapushon'=> self::WC_PREFIX . 'kapyushon',
        'vorotnik'=> self::WC_PREFIX . 'tsvet',
        'poyas'=> self::WC_PREFIX . 'poyas',
        'karmany'=> self::WC_PREFIX . 'karmany',
        'koketka'=> self::WC_PREFIX . 'koketka',
        'uhod'=> self::WC_PREFIX . 'uhod',
        'lengthGroup' => self::WC_PREFIX . 'tip-dliny'
    ];

    private $WC_ATTR_ALIAS_FLIP;

    function __construct()
    {
        $this->WC_ATTR_ALIAS_FLIP = array_flip(self::WC_ATTR_ALIAS);
    }


    private const ATTRIBUTES = [

        'color',
        'size',
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
        'uhod',
        'lengthGroup'
        ],

        RELATIONS = [
        'type',
        'gender',
        'group',
        'filterable',
        'hasRecords'
    ];

    public function setQueryParameter($name, $value)
    {
        if ($value !== '' && in_array($name, Attrs::VALUES)) {
            if (isset($this->queryParams[$name]) && $this->match($this->queryParams[$name], $value)) {
                unset($this->queryParams[$name] );
            }
            else {
                $this->queryParams[$name] = $value;
                //set_query_var($name, $value);
            }

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


    public function fromArray($array) {
        foreach ($array as $name => $value) {
                $this->setQueryParameter($name, implode(',', $value));
        }
    }

    public function fromArrayWithWCKeys($array) {
        foreach ($array as $name => $value) {
            if (isset ($this->WC_ATTR_ALIAS_FLIP[$name])) {
                $this->setQueryParameter($this->WC_ATTR_ALIAS_FLIP[$name], $value);
            }
        }
    }

    public function fromGetQuery() {
        foreach ($_GET as $name => $value) {
            if (isset ($this->WC_ATTR_ALIAS_FLIP[$name])) {
                $this->setQueryParameter($this->WC_ATTR_ALIAS_FLIP[$name], $value);
            }
        }
    }

    public function getParamsFromGetQuery() {
        $params = [];
        foreach ($_GET as $name => $value) {
            if (isset ($this->WC_ATTR_ALIAS_FLIP[$name])) {
                $params[$this->WC_ATTR_ALIAS_FLIP[$name]] = $value;
            }
        }

        return $params;

    }

    public function getWooCommerceQuery() {
        $metaQuery = [];
        foreach ($this->queryParams as $name => $value) {

            if (isset (self::WC_ATTR_ALIAS[$name])) {
                $metaQuery[self::WC_ATTR_ALIAS[$name]] =  $value;
            }
        }

        return $metaQuery;
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

    function match($haystack, $needle) {
        if (mb_stripos($haystack, $needle, 0, 'UTF-8') !== false) {
            return true;
        }
        else {
            return false;
        }
    }
}