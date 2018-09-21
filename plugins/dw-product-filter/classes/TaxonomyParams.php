<?php
namespace Dreamwhite\Plugins\ProductFilter;
class TaxonomyParams
{
    private $params = [];

    public function getParams()
    {
        return $this->params;
    }

    public function getParameter($name) {
        return isset($this->params[$name]) ? $this->params[$name] : [];
    }

    public function __construct($taxonomy)
    {
        $meta = get_term_meta($taxonomy->term_id);

        foreach ($meta as $param => $value) {
            $this->params[$param] = $value[0] !== '' ? explode(',', $value[0]) : [];
        }


    }

}