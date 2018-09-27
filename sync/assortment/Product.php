<?php

namespace Dreamwhite\Assortment;
class Product
{
    var $product;
    var $productFolderName;

    var $pathName = '';

    var $categories;
    var $name;
    var $id;
    var $wc_id;
    var $url;
    var $variants = [];

    var $stock = 0;

    var $code;
    var $uom = '';

    var $supplier = '';
    var $description = '';
    public $color = '', $colorGroup = '', $texture = '';
    //var $size = '';
    var $variantName = '';
    var $barcode = '';
    var $regularPrice = '0', $salePrice = '';
    var $isOnSale = false;
    var $article = '';


    var $material = '';
    var $uteplitel = '', $podkladka = '', $season = '';

    var $siluet = '';
    var $dlina = '';
    var $rukav = '';
    var $dlina_rukava = '';
    var $zastezhka = '';
    var $kapushon = '';
    var $vorotnik = '';
    var $poyas = '';
    var $karmany = '';
    var $koketka = '';
    var $uhod = '';

    var $video = '';

    const BASE_URL = 'http://static.dreamwhite.ru/photo/';

    var $productPhotoUrl = '';
    var $galleryUrls = '';

    public $images = [];
    public $hasImages = false;

    var $colors = [], $sizes = '';


    public $size = [];
    var $tags = '';
    var $gender;


    private $stockForSize = [];
    public $type;

    public $stockForCity = [];

    var $attrs = [];


    function __construct($product, $stock, $folderName, $city)
    {
        $this->product = $product;



        $this->productFolderName = $folderName;
        $this->categories = $folderName;
        $this->id = $product->id;
        $this->name = $product->name;
        $this->stock = $stock;
        $this->stockForCity[$city] = $stock;

        //$this->color = 'Серый DR 67 GREY';

        $this->code = $product->code;
        if (property_exists($product, 'uom')) {
            $this->uom = $product->uom->name;
        }
        if (property_exists($product, 'supplier')) {
            $this->supplier = $product->supplier->name;
        }
        if (property_exists($product, 'description')) {
            $this->description = $product->description;
        }
        if (property_exists($product, 'article')) {
            $this->article = $product->article;
        }

        if (count($product->barcodes) > 0) {
            $this->barcode = $product->barcodes[0];
        }

        $photoFileName = $this->article . '.jpg';
        $photoFileName = str_replace(' ', '-', $photoFileName);

        if (in_array($photoFileName, Tools::$imageDirList)) {
            $urlToEncode = self::BASE_URL
                . $this->productFolderName
                . '/' . $this->article
                . '/' . $this->article . '.jpg';
            $urlToEncode = str_replace(' ', '-', $urlToEncode);
            $this->productPhotoUrl = $urlToEncode;
            $this->galleryUrls .= $urlToEncode . ',';
        }

        $this->getPrices();
        $this->setAttributes();
        $this->attrs['size'] = [];
        $this->gender = Tools::match($this->productFolderName, 'Мужские') ? 'Мужские' : 'Женские';
        $this->type = $this->getType($this->name);

        if (Tools::match($this->uteplitel, 'пух')) {
            $this->categories .= ',' . $this->gender . ' пуховики';
            //$this->productFolderName .= ',' . $this->gender . ' пуховики';
        }

        $this->images = $this->getImageUrls();



        Log::d($this->name, 'product', 'p', 'products');
    }

    private const TYPES = [
        'Пальто',
        'Плащ',
        'Пуховик',
        'Жилет'
    ];
    function getType($productName) {

        $currentType = null;
        foreach (self::TYPES as $type) {
            if (Tools::match(mb_strtolower($productName), mb_strtolower($type))) {
                $currentType = $type;
            }
        }

        return $currentType;
    }

    function getAttributeValue($attr)
    {
        $value = '';
        if (is_object($attr)) {
            if (is_bool($attr->value)) {
                $value = $attr->value ? 'Есть' : 'Нет';
            } else if (is_object($attr->value)) {
                $value = $attr->value->name;
            } else if (is_string($attr->value)) {
                $value = $attr->value;
            }
        }
        return $value;
    }

    private function getLengthGroup($length) {
        $val = intval(str_replace('см', '', $length));


        if ($val >= 110) {
            return 'Длинные';
        }
        else {
            return 'Короткие';
        }
    }

    public function setStockForSize($size, $stock) {
        $this->stockForSize[$size] = $stock;
    }

    public function getStockForSize($size) {
        return $this->stockForSize[$size];
    }

    private function addSize($size, $stock)
    {

        //$this->setStockForSize($size, $stock);

        if (!in_array($size, $this->size)) {
            $this->attrs['size'][] = $size;
        }


        if (isset($this->attrs['size'])) {
            if (!in_array($size, $this->attrs['size'])) {
                $this->size[] = $size;
            }
        } else {
            $this->attrs['size'][] = $size;
        }

    }

    function getStockSum() {

    }

    function addVariant($variant)
    {
        $this->variants[] = $variant;
        $this->stock += $variant->stock;


        if ($variant->stock > 0) {
            $this->addSize($variant->size, $variant->stock);
        }

        /*if (!Tools::match($this->sizes, $variant->size)) {
            $this->sizes .= $variant->size . ",";
            $this->attrs['size'] = $this->sizes;
        }*/

    }

    function setAttributes()
    {
        if ($this->product != null) {
            if (property_exists($this->product, 'attributes')) {
                $attrs = $this->product->attributes;

                $attrSet = [];

                foreach ($attrs as $attr) {
                    $attrSet[$attr->name] = $this->getAttributeValue($attr);
                }


                $this->color = $attrSet['Цвет'] ?? '';
                $this->colorGroup = $attrSet['Цветовая группа'] ?? '';
                $this->texture = $attrSet['Текстура'] ?? '';

                $this->material = $attrSet['Материал'] ?? '';

                $this->season = $attrSet['Сезон'] ?? '';
                $this->uteplitel = $attrSet['Утеплитель'] ?? '';
                $this->podkladka = $attrSet['Подкладка'] ?? '';
                $this->siluet = $attrSet['Силуэт'] ?? '';
                $this->dlina = $attrSet['Длина изделия'] ?? '';
                $this->rukav = $attrSet['Рукав'] ?? '';
                $this->dlina_rukava = $attrSet['Длина рукава'] ?? '';
                $this->zastezhka = $attrSet['Застежка'] ?? '';
                // need to convert boolean
                $this->kapushon = $attrSet['Капюшон'] ?? '';
                $this->vorotnik = $attrSet['Воротник'] ?? '';
                $this->poyas = $attrSet['Пояс'] ?? '';
                $this->karmany = $attrSet['Карманы'] ?? '';
                $this->koketka = $attrSet['Кокетка'] ?? '';
                $this->uhod = $attrSet['Уход'] ?? '';

                if (isset($attrSet['Видео'])) $this->video = $attrSet['Видео'];

                $this->attrs = $this->getAttrs();
                $this->attrs['lengthGroup'] = $this->getLengthGroup($this->attrs['dlina']);
            }
        }
    }

    function textFromBool($bool)
    {
        return $bool ? 'Есть' : 'Нет';
    }

    function getImageUrls()
    {
        $base = 'https://static.dreamwhite.ru/photo/new/';
        $path = $base . $this->productFolderName
            . '/' . $this->article
            . '/' . $this->color . '/';

         if ($this->article !== '' && $this->color !== '') {
            $primaryPhotoName = $this->article . '-' . $this->color . '-1.jpg';
         }
         else {
            $primaryPhotoName = '';
         }


        $articlePhotoFolder = Tools::$imageTree[$this->productFolderName][$this->article][$this->color] ?? [];

        $articlePhotoFolder = array_filter($articlePhotoFolder, 'strlen');

        $gallery = array_diff($articlePhotoFolder, [$primaryPhotoName]);
        $primary = implode(array_intersect($articlePhotoFolder, [$primaryPhotoName]));

        $galleryUrls = [];

        foreach ($gallery as $fileName) {
            $galleryUrls[] = Tools::encodeWhitespace($path . $fileName);
        }

        $this->hasImages = !empty($articlePhotoFolder);

        $images = [];
        $images['primary'] = $primary !== '' ? Tools::encodeWhitespace($path . $primary) : '';
        //$images['primary'] = $primaryPhotoName !== '' ? Tools::encodeWhitespace($path . $primaryPhotoName) : '';
        $images['gallery'] = $galleryUrls;

        return $images;
    }

    function getPrices()
    {
        foreach ($this->product->salePrices as $price) {
            if ($price->priceType === 'Цена продажи') {
                $this->regularPrice = $price->value / 100;
            } else if ($price->priceType === 'Распродажа') {
                //var_dump($price);
                if ($price->value > 0) {
                    $this->salePrice = $price->value / 100;
                    $this->isOnSale = true;
                }

            }
        }
    }

    function getAttribute($variant, $attrName)
    {
        foreach ($variant->characteristics as $ch) {
            if ($ch->name == $attrName) {
                return $ch->value;
            }
        }

        return '';
    }

    function getAttrs()
    {

        $attrs = [
            'color' => $this->color,
            'colorGroup' => $this->colorGroup,
            'texture' => $this->texture,

            'material' => $this->material,
            'season' => $this->season,
            'uteplitel' => $this->uteplitel,
            'podkladka' => $this->podkladka,
            'siluet' => $this->siluet,
            'dlina' => $this->dlina,
            'rukav' => $this->rukav,
            'dlina_rukava' => $this->dlina_rukava,
            'zastezhka' => $this->zastezhka,
            'kapushon' => $this->kapushon,
            'vorotnik' => $this->vorotnik,
            'poyas' => $this->poyas,
            'karmany' => $this->karmany,
            'koketka' => $this->koketka,
            'uhod' => $this->uhod,
            //'size' => $this->sizes
        ];

        return $attrs;
    }

}

?>