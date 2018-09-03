<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 05.11.2017
 * Time: 0:13
 */
namespace Dreamwhite\Assortment;
class Tag {
   public $name, $slug;

   public $relations = [];

   public $attributes = [];

   public $seo = [];

   public $realAttrs = [];

   function fromGlobal($globalAttrs) {
       $this->name = "GLOBAL_TAG";

       foreach ($globalAttrs as $name => $values) {

           foreach ($values as $value) {
               if ($value !== '') {
                   $this->realAttrs[$name][] = $value;
               }
           }
           //$this->realAttrs[$name] = array_filter($values);
       }
       //$this->realAttrs = $globalAttrs;

   }

    public function addRealAttribute($attr, $value) {
        if ($value !== '') {
            if (isset($this->realAttrs[$attr])) {
                if (!in_array($value, $this->realAttrs[$attr])) {
                    $this->realAttrs[$attr][] = $value;
                }
            }
            else {
                $this->realAttrs[$attr][] = $value;
            }
        }
    }

    public function getRealAttributes() {
        return $this->realAttrs;
    }

    public function getRealAttribute($attr) {
        return $this->realAttrs[$attr];
    }

    public function getAttributeString($attr) {
        return implode(',', $this->realAttrs[$attr]);
    }

}