<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 8/15/18
 * Time: 2:20 PM
 */

class TaxonomyDataHolder
{
    public $taxByColor = [];

    public $taxes = [];

    public function __construct()
    {
        global $wpdb;

        //$time = microtime(true);

        foreach (Attrs::VALUES as $attr) {
            $query = "SELECT meta_value, term_id FROM " . $wpdb->termmeta . " WHERE meta_key = '" . $attr . "' AND meta_value <> ''";
            $result = $wpdb->get_results($query, "ARRAY_A");

            foreach ($result as $value) {
                $this->taxes[$attr][$value['term_id']] = explode(',', $value['meta_value']);
            }

        }

        //var_dump((microtime(true) - $time));

    }

    public function matchValues($attr, $values)
    {
        $matches = [];

        foreach ($this->taxes[$attr] as $taxId => $taxValues) {
            if (!empty(array_intersect($values, $taxValues))) {
                $matches[] = $taxId;
            }
        }

        return $matches;
    }


    public function matchValue($attr, $value)
    {
        $matches = [];

        foreach ($this->taxes[$attr] as $taxId => $values) {
            if (in_array($value, $values)) {
                $matches[] = $taxId;
            }
        }

        return $matches;
    }

    public function matchColor($color)
    {
        $matches = [];

        foreach ($this->taxByColor as $taxId => $colors) {
            if (in_array($color, $colors)) {
                $matches[] = $taxId;
            }
        }

        return $matches;
    }
}