<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 8/15/18
 * Time: 2:22 PM
 */

class TaxonomyParams
{
    private $params = [];

    public function getParams()
    {
        return $this->params;
    }

    public function __construct($taxonomy)
    {
        $meta = get_term_meta($taxonomy->term_id);

        foreach ($meta as $param => $value) {
            $this->params[$param] = $value[0] !== '' ? explode(',', $value[0]) : [];
        }

        /*$this->params['type'] = ['Пальто'];
        $this->params['gender'] = ['Женский'];*/

        /*foreach ($this->params as $param => $value) {
            if (!empty($value)) {
                Renderer::header($param);
                foreach ($value as $item) {
                    Renderer::tag($item);
                }
            }
        }*/
    }

}