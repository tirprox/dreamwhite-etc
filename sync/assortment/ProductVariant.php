<?php
namespace Dreamwhite\Assortment;
class ProductVariant {
	var $variant, $parentProduct;
	
	var $parentProductCode;
	var $parentName = '';
	
	var $id;
	var $vc_id;
	var $url;
	var $name;
	var $stock;
	
	var $price = '';
	var $code = '';
	var $uom = '';
	
	var $supplier = '';
	var $description = '';

	var $color = '', $size = '', $height = '';

	var $variantName = '';
	var $barcode = '';
	
	var $regularPrice = '';
	var $salePrice = '';
	
	var $article = '';
	var $variantPhotoUrl = '';
	
	var $isOnSale = false;
	
	public static $attributeString = "\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"";
	
	function __construct( $variant, $stock, $parentProduct ) {
		$this->variant       = $variant;
		$this->parentProduct = $parentProduct;
		
		$this->id                   = $variant->id;
		$this->name                 = $variant->name;
		$this->stock                = $stock;
		//$this->parentProduct->stock += $this->stock;
		
		$this->parentProductCode = $parentProduct->code;
		$this->uom = $parentProduct->uom;
		$this->supplier = $parentProduct->supplier;

		if ( property_exists( $variant, 'description' ) ) {
			$this->description = $variant->description;
		}
		
		/*$this->color       = $this->getAttribute( $variant, 'Цвет' );
		$this->size        = $this->getAttribute( $variant, 'Размер' );
        $this->size        = $this->getAttribute( $variant, 'Рост' );*/

        $this->setAttributes();


		$this->variantName = $variant->name;
		if ( property_exists( $variant, 'code' ) ) {
			$this->code = $variant->code;
		}
		if ( count( $variant->barcodes ) > 0 ) {
			$this->barcode = $variant->barcodes[ 0 ];
		}
		$this->getPrices();
		$this->parentName = $parentProduct->name;
		$this->article = $parentProduct->article;
		
		$photoFileName = $this->article . '-' . $this->color . '.jpg';
		$photoFileName = str_replace( ' ', '-', $photoFileName );
		$photoFileName = str_replace( '\0', '', $photoFileName );
		$photoFileName = trim( $photoFileName );
		$str1          = 'й';
		$str2          = 'й';
		$photoFileName = str_replace( $str1, $str2, $photoFileName ); // буква й
		
		foreach ( Tools::$imageDirList as $image ) {
			$imgFixed = str_replace($str1, $str2, $image); // перед сравнением конвертим букву й
			if ( $imgFixed === $photoFileName ) {
				
				$urlToEncode           = Product::BASE_URL
				                         . $this->parentProduct->productFolderName
				                         . '/' . $this->article
				                         . '/' . $image; // используем й из имени файла
				$urlToEncode           = str_replace( ' ', '-', $urlToEncode );
				$this->variantPhotoUrl = $urlToEncode;
				
				if ( ! Tools::match( $this->parentProduct->galleryUrls, $urlToEncode ) ) {
					$this->parentProduct->galleryUrls .= $urlToEncode . ',';
				}
				break;
			}
		}

		
		if ( $this->stock > 0 ) {
			/*if ( ! in_array( $this->color, $this->parentProduct->colors ) ) {
				$this->parentProduct->colors[] = $this->color;
			}*/

			if ( ! Tools::match( $this->parentProduct->sizes, $this->size ) ) {
				$this->parentProduct->sizes .= $this->size . ",";
			}
			
		}
		
		Log::d( "$this->name (В наличии $this->stock)", 'variant', 'p', 'products');
	}

	function setAttributes() {
	    $attrs = $this->variant->characteristics;

	    $chars = [];
	    foreach ($attrs as $attr) {
	        $chars[$attr->name] = $attr->value;
        }

        $this->color = $chars['Цвет'] ?? '';
        $this->size = $chars['Размер'] ?? '';
	    $this->height = $chars['Рост'] ?? '';
    }


	function getAttribute( $variant, $attrName ) {
		foreach ( $variant->characteristics as $ch ) {
			if ( $ch->name == $attrName ) {
				return $ch->value;
			}
		}
		
		return "";
	}

	function getPrices () {
	   foreach ($this->variant->salePrices as $price) {
         if ( $price->priceType === 'Цена продажи' ) {
            $this->regularPrice = $price->value / 100;
         }
         else if ($price->priceType === 'Распродажа') {
            if ($price->value > 0) {
               $this->salePrice = $price->value / 100;
               $this->isOnSale = true;
            }
            
         }
      }
   }
	
}