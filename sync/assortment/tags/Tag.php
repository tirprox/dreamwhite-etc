<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 05.11.2017
 * Time: 0:13
 */
namespace Dreamwhite\Assortment;
class Tag {
   public $name;

   public $relations = [];

   public $attributes = [];

   public $seo = [];

   // OLD
   var $group = [];
   
   var $color = [];
   var $colorGroup = [];
   var $size = [];

   var $texture = [];
   var $season = [];
   
   var $material = [];
   var $uteplitel = [];
   var $podkladka = [];
   var $siluet = [];
   var $dlina = [];
   var $rukav = [];
   var $dlina_rukava = [];
   var $zastezhka = [];
   var $vorotnik = [];
   var $koketka = [];
   var $uhod = [];
   
   var $kapushon = [];
   var $poyas = [];
   var $karmany = [];

   var $parent = '';
   var $description = '';
   
   var $hasColors = false;
   var $realColors = [];

   public $filterAttrs = [];
   public $realAttrs = [];


   function fromGlobal($globalAttrs) {
       $this->name = "GLOBAL_TAG";
       $this->realAttrs = $globalAttrs;

   }

   function getFilterAttrs() {

       $attrs = [
           'color' => $this->color,
           'colorGroup' =>  $this->colorGroup,
           'texture' =>  $this->texture,
           'material' => $this->material,
           'season' =>  $this->season,
           'uteplitel' => $this->uteplitel,
           'podkladka' =>  $this->podkladka,
           'siluet' => $this->siluet,
           'dlina' =>  $this->dlina,
           'rukav' => $this->rukav,
           'dlina_rukava' =>  $this->dlina_rukava,
           'zastezhka' => $this->zastezhka,
           'kapushon' =>  $this->kapushon,
           'vorotnik' => $this->vorotnik,
           'poyas' =>  $this->poyas,
           'karmany' =>  $this->karmany,
           'koketka' => $this->koketka,
           'uhod' =>  $this->uhod,
       ];

       return $attrs;
   }

    public function addRealAttribute($attr, $value) {
        if (isset($this->realAttrs[$attr])) {
            if (!in_array($value, $this->realAttrs[$attr])) {
                $this->realAttrs[$attr][] = $value;
            }
        }
        else {
            $this->realAttrs[$attr][] = $value;
        }

    }

    public function getAllReal() {
        return $this->realAttrs;
    }

    public function getRealAttribute($attr) {
        return $this->realAttrs[$attr];
    }

    public function getAttributeString($attr) {
        return implode(',', $this->realAttrs[$attr]);
    }

}