<?php

namespace Dreamwhite\Assortment;
class XMLReportGenerator {
  private static $document;
  private static $root, $city = '', $stock;

  private const ATTR_CONF = [
    'type' => [
      'name' => 'Тип изделия',
      'type' => 'string',
      'struct' => 'list'
    ],
    'gender' => [
      'name' => 'Пол',
      'type' => 'string',
      'struct' => 'list'
    ],
    'opt' => [
      'name' => 'Опт',
      'type' => 'bool',
      'struct' => 'single'
    ],
    'opt_stock' => [
      'name' => 'Свободный склад',
      'type' => 'bool',
      'struct' => 'single'
    ],
    'color' => [
      'name' => 'Цвет',
      'type' => 'string',
      'struct' => 'list'
    ],
    'colorGroup' => [
      'name' => 'Цветовая группа',
      'type' => 'string',
      'struct' => 'list'
    ],
    'texture' => [
      'name' => 'Текстура',
      'type' => 'string',
      'struct' => 'list'
    ],
    'season' => [
      'name' => 'Сезон',
      'type' => 'string',
      'struct' => 'list'
    ],
    'material' => [
      'name' => 'Материал',
      'type' => 'string',
      'struct' => 'list'
    ],
    'uteplitel' => [
      'name' => 'Утеплитель',
      'type' => 'string',
      'struct' => 'list'
    ],
    'podkladka' => [
      'name' => 'Подкладка',
      'type' => 'string',
      'struct' => 'list'
    ],
    'siluet' => [
      'name' => 'Силуэт',
      'type' => 'string',
      'struct' => 'list'
    ],
    'dlina' => [
      'name' => 'Длина изделия',
      'type' => 'string',
      'struct' => 'list'
    ],
    'lengthGroup' => [
      'name' => 'Тип длины',
      'type' => 'string',
      'struct' => 'list'
    ],
    'rukav' => [
      'name' => 'Рукав',
      'type' => 'string',
      'struct' => 'list'
    ],
    'dlina_rukava' => [
      'name' => 'Длина рукава',
      'type' => 'string',
      'struct' => 'list'
    ],
    'zastezhka' => [
      'name' => 'Застежка',
      'type' => 'string',
      'struct' => 'list'
    ],
    'kapushon' => [
      'name' => 'Капюшон',
      'type' => 'string',
      'struct' => 'list'
    ],
    'vorotnik' => [
      'name' => 'Воротник',
      'type' => 'string',
      'struct' => 'list'
    ],
    'koketka' => [
      'name' => 'Кокетка',
      'type' => 'string',
      'struct' => 'list'
    ],
    'poyas' => [
      'name' => 'Пояс',
      'type' => 'string',
      'struct' => 'list'
    ],
    'karmany' => [
      'name' => 'Карманы',
      'type' => 'string',
      'struct' => 'list'
    ],
    'uhod' => [
      'name' => 'Уход',
      'type' => 'string',
      'struct' => 'list'
    ],
    'video' => [
      'name' => 'Видео',
      'type' => 'string',
      'struct' => 'single'
    ],
    'priority' => [
      'name' => 'Приоритет',
      'type' => 'int',
      'struct' => 'single'
    ],

  ];

  static function getDocument() {
    if (self::$document == null) {
      self::createDocument();
    }
    return self::$document;
  }

  static function createDocument() {
    self::$document = new \DOMDocument('1.0', 'UTF-8');
    self::$document->formatOutput = true;

    self::$root = self::$document->createElement('products');
    self::$root = self::$document->appendChild(self::$root);
  }

  static function city($city) {
    self::$city = $city;
  }

  static function stock($stock) {
    self::$stock = $stock;
  }

  static function writeXmlToFile() {
    if (self::$document != null) {
      $path = dirname(__DIR__) . '/output/assortment-' . self::$city . '.xml';
      self::$document->save($path);
    }
  }

  static function addProduct($product) {
    $xmlProduct = self::addChild('product', self::$root);

    $stock = self::$stock[$product->code];
    self::addNode('id', $product->id, $xmlProduct);
    self::addNode('name', $product->name, $xmlProduct);
    self::addNode('slug', $product->slug, $xmlProduct);
    self::addNode('group', $product->categories, $xmlProduct);
    //self::addNode('group', $product->categories, $xmlProduct);
    self::addNode('sku', $product->code, $xmlProduct);
    self::addNode('barcode', $product->barcode, $xmlProduct);
    self::addNode('supplier', $product->supplier, $xmlProduct);
    self::addNode('article', $product->article, $xmlProduct);
    self::addNode('uom', $product->uom, $xmlProduct);
    //self::addNode('stock', $product->stock, $xmlProduct);

    $stocks = self::addChild('stocks', $xmlProduct);
    $stockSum = 0;

    foreach ($stock as $city => $value) {
      self::addNode('stock-' . $city, $value, $stocks);
      $stockSum += $value;
    }

    self::addNode('stock', $stockSum, $xmlProduct);
    self::addNode('availability',$stockSum > 0 ? 'instock' : 'outofstock', $xmlProduct);
    self::addNode('backorders','no', $xmlProduct);



    self::addNode('price', $product->regularPrice, $xmlProduct);
    self::addNode('salePrice', $product->salePrice, $xmlProduct);
    self::addNode('description', $product->description, $xmlProduct);

    $attrs = self::addChild('attributes', $xmlProduct);


    // Tag attributes:
    // name - Имя в кириллице
    // type - string | bool | int | date
    // struct = single | list

    self::addAttributeNode('type', $product->type, $attrs);
    self::addAttributeNode('gender', $product->gender, $attrs);

    self::addAttributeNode('color', $product->color, $attrs);
    self::addAttributeNode('colorGroup', $product->colorGroup, $attrs);
    self::addAttributeNode('texture', $product->texture, $attrs);

    self::addAttributeNode('material', $product->material, $attrs);
    self::addAttributeNode('uteplitel', $product->uteplitel, $attrs);
    self::addAttributeNode('season', $product->season, $attrs);

    self::addAttributeNode('podkladka', $product->podkladka, $attrs);
    self::addAttributeNode('siluet', $product->siluet, $attrs);
    self::addAttributeNode('dlina', $product->dlina, $attrs);
    if (isset($product->attrs['lengthGroup'])) self::addAttributeNode('lengthGroup', $product->attrs['lengthGroup'], $attrs);
    self::addAttributeNode('rukav', $product->rukav, $attrs);
    self::addAttributeNode('dlina_rukava', $product->dlina_rukava, $attrs);
    self::addAttributeNode('zastezhka', $product->zastezhka, $attrs);
    self::addAttributeNode('kapushon', $product->kapushon, $attrs);
    self::addAttributeNode('vorotnik', $product->vorotnik, $attrs);
    self::addAttributeNode('poyas', $product->poyas, $attrs);
    self::addAttributeNode('karmany', $product->karmany, $attrs);
    self::addAttributeNode('koketka', $product->koketka, $attrs);
    self::addAttributeNode('uhod', $product->uhod, $attrs);
    self::addAttributeNode('opt', $product->opt, $attrs);
    self::addAttributeNode('opt_stock', $product->opt_stock, $attrs);
    self::addAttributeNode('priority', $product->priority, $attrs);


    if ($product->hasImages) {
      self::addNode('photo', $product->images['primary'], $xmlProduct);
      self::addNode('photoGallery', implode(',', $product->images['gallery']), $xmlProduct);
      self::addNode('tags', $product->tags, $xmlProduct);
      self::addNode('visibility', "visible", $xmlProduct);
      self::addNode('fb_visibility', "1", $xmlProduct);

    }
    else {
      self::addNode('tags', "Без фото", $xmlProduct);
      self::addNode('visibility', "hidden", $xmlProduct);
      self::addNode('fb_visibility', "0", $xmlProduct);

    }

    self::addAttributeNode('video', $product->video, $xmlProduct);
    self::addNode('video-youtube-part', Tools::removeYoutubeBase($product->video), $xmlProduct);

    //$xmlProduct = self::addChild('variants', $xmlProduct);

    $variants = XMLReportGenerator::addChild('variations', $xmlProduct);
    foreach ($product->variants as $variant) {
      $xmlVariant = self::addChild('variation', $variants);
      self::addNode('id', $variant->id, $xmlVariant);
      self::addNode('name', $variant->name, $xmlVariant);
      //self::addNode('slug', $variant->slug, $xmlVariant);
      self::addNode('sku', $variant->code, $xmlVariant);
      self::addNode('barcode', $variant->barcode, $xmlVariant);

      $variantStock = self::$stock[$variant->code];

      $varStockSum = 0;
      $varStocks = self::addChild('stocks', $xmlVariant);
      foreach ($variantStock as $city => $value) {
        self::addNode($city, $value, $varStocks);
        $varStockSum += $value;
      }

      self::addNode('stock', $varStockSum, $xmlVariant);

      self::addNode('availability',
        $varStockSum > 0 ? 'instock' : 'outofstock',
        $xmlVariant);
      self::addNode('backorders','no', $xmlVariant);


      self::addNode('enabled',
        $varStockSum > 0 ? 'yes' : 'no',
        $xmlVariant);

      self::addNode('price', $variant->regularPrice, $xmlVariant);
      self::addNode('salePrice', $variant->salePrice, $xmlVariant);

      self::addNode('description', $variant->description, $xmlVariant);

      self::addNode('color', $variant->color, $xmlVariant);

      self::addNode('size', $variant->size, $xmlVariant);

      self::addNode('height', $variant->height, $xmlVariant);

      self::addNode('photo', $variant->variantPhotoUrl, $xmlVariant);
    }

    return $xmlProduct;

  }


  static function addAttributeNode($name, $value, $parent) {
    if ($value !== '') {
      $node = self::addChild($name, $parent);

      foreach (self::ATTR_CONF[$name] as $a => $val) {
        $da = self::$document->createAttribute($a);
        $da->value = $val;
        $node->appendChild($da);
      }

      $nodeVal = self::addTextNode($value, $node);
    }
  }

  static function addNode($name, $value, $parent) {
    if ($value !== '') {
      $node = self::addChild($name, $parent);
      $nodeVal = self::addTextNode($value, $node);
    }

  }

  static function addChild($elementName, $parent) {
    $element = self::$document->createElement($elementName);
    $element = $parent->appendChild($element);
    return $element;
  }

  static function addTextNode($text, $parent) {
    $textNode = self::$document->createTextNode($text);
    $textNode = $parent->appendChild($textNode);
    return $textNode;
  }

}