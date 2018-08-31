<?php

namespace Dreamwhite\Assortment;
class TagFactory
{
    var $tags = [];

    var $parsedTags = [];

    function loadTagsFromFile()
    {

        $filePath = __DIR__ . '/tags.csv';

        $tagRows = CsvTagParser::fromFile($filePath);

        foreach ($tagRows as $tagRow) {
            $tag = $this->createTag2($tagRow);

            $this->tags[] = $tag;
        }


        usort($this->tags, function ($tag1, $tag2) {
            return $tag1->relations['level'] <=> $tag2->relations['level'];
        });



    }

    function getTagList($globalAttrs)
    {
        XMLTaxonomyListGenerator::createDocument();
        foreach ($this->tags as $tag) {

            $colors = [];
            $sizes = [];
            if (isset($tag->attributes['color'])) {
                foreach ($tag->attributes['color'] as $color) {
                    $colors[] = $color->attribute;
                }
                $tag->attributes['colorGroup'] = array_intersect($colors, $globalAttrs['color']);
            }

            if (isset($tag->attributes['size'])) {
                foreach ($tag->attributes['size'] as $size) {
                    $sizes[] = $size->attribute;
                }
                $tag->attributes['size'] = array_intersect($colors, $globalAttrs['size']);
            }

            XMLTaxonomyListGenerator::addTag($tag);
        }

        $globalTag = new Tag();
        $globalTag->fromGlobal($globalAttrs);

        $json = json_encode($this->tags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents("tags.json", $json);

        XMLTaxonomyListGenerator::addTag($globalTag);

        XMLTaxonomyListGenerator::writeXmlToFile();

        $mongo = new MongoTagAdapter();

        $data = json_decode($json, true);

        $mongo->updateAll($data);

    }

    private function createTag2($tagRow)
    {
        $tag = new Tag();

        $tag->name = $tagRow['name'];
        $tag->slug = strtolower(Tools::transliterate($tag->name));
        $tag->relations = $tagRow['relations'];

        //$tag->relations['slug'] = strtolower(Tools::transliterate($tag->name));
        $tag->seo = $tagRow['seo'];

        foreach ($tagRow['attrs'] as $name => $value) {
            $splitted = $this->splitAttr($value);
            if (!empty($splitted)) {
                $tag->attributes[$name] = $splitted;
            }

        }


        return $tag;
    }

    /* Creating a tag from a csv row, where row is represented as an array. */
    /*function createTag($row)
    {
        $tag = new Tag();
        $tag->name = $row[0];
        $tag->group = $this->splitAttr($row[1]);

        $tag->color = $this->splitAttr($row[2]);

        if (!empty($tag->color)) {
            $tag->hasColors = true;
        }

        $tag->size = $this->splitAttr($row[3]);

        $tag->material = $this->splitAttr($row[4]);
        $tag->uteplitel = $this->splitAttr($row[5]);
        $tag->podkladka = $this->splitAttr($row[6]);
        $tag->siluet = $this->splitAttr($row[7]);
        $tag->dlina = $this->splitAttr($row[8]);
        $tag->rukav = $this->splitAttr($row[9]);
        $tag->dlina_rukava = $this->splitAttr($row[10]);
        $tag->zastezhka = $this->splitAttr($row[11]);
        $tag->kapushon = $this->splitAttr($row[12]);
        $tag->vorotnik = $this->splitAttr($row[13]);
        $tag->poyas = $this->splitAttr($row[14]);
        $tag->karmany = $this->splitAttr($row[15]);
        $tag->koketka = $this->splitAttr($row[16]);
        $tag->uhod = $this->splitAttr($row[17]);


        $tag->filterAttrs = $tag->getFilterAttrs();

        //var_dump($tag->name, $tag->filterAttrs);
        $this->tags[] = $tag;
    }*/

    /* Determine whether attribute should be included or excluded.
    If prepended with -, attribute is excluded from a tag (is inverted) */
    private function splitAttr($atrrString)
    {
        $data = str_getcsv($atrrString, ',');
        $attrs = [];
        foreach ($data as $item) {

            $item = trim($item);
            if ($item !== '') {
                if (substr($item, 0, 1) === '-') {
                    $item = substr($item, 1);
                    $attrs[] = new InvertableAttribute($item, true);
                } else {
                    $attrs[] = new InvertableAttribute($item, false);
                }
            }
        }

        return array_filter($attrs);
    }


    public function setProductTag2($product)
    {
        // sale tag
        if ($product->isOnSale) {
            $product->tags .= 'Распродажа,';
        }

        foreach ($this->tags as $tag) {


            //if (!$this->compareAttrs($tag->relations['group'], $product->productFolderName)) continue;

            if ($tag->relations['group'] !== $product->productFolderName) continue;

            $result = true;

            if (!empty($tag->attributes)) {
                foreach ($tag->attributes as $name => $value) {

                    if ($name === 'article') {
                        $result = $this->compareAttrs($tag->attributes[$name], $product->article);
                    }

                    else if ($name === 'size') {
                        $tagSizes = [];
                        $productSizes = $product->size;

                        foreach ($value as $size) {
                            $tagSizes[] = $size->attribute;
                        }

                        if (!empty(array_intersect($tagSizes, $productSizes))) {
                            $result = true;
                            $tag->addRealAttribute('size', implode(',', $tagSizes));
                        }
                    }

                    else {
                        if (isset($product->attrs[$name])) {
                            $result = $this->compareAttrs($tag->attributes[$name], $product->attrs[$name]);
                        }

                        if ($result === false) break;
                    }

                }
            }

            if ($result === false) continue;

            $product->tags .= $tag->name . ',';

            foreach ($product->attrs as $attr => $value) {

                if (!empty($tag->attributes[$attr])) {
                    if ($attr !== 'size') {
                        $tag->addRealAttribute($attr, $value);
                    }
                }

            }
        }
    }

    function setProductTag($product)
    {
        // sale tag
        if ($product->isOnSale) {
            $product->tags .= 'Распродажа,';
        }

        foreach ($this->tags as $tag) {
            //check basic attrs

            if (!$this->compareAttrs($tag->group, $product->productFolderName)) continue;

            // colors do not support attr inversion for now
            //
            if ($tag->hasColors) {
                if (!$this->compareColors($tag, $product->color)) continue;
            }
            /*else {
                if (!$this->compareAttrs($tag->color, $product->color)) continue;
            }*/

            if (!$this->compareAttrs($tag->size, $product->sizes)) continue;

            if (!$this->compareAttrs($tag->material, $product->material)) continue;
            if (!$this->compareAttrs($tag->uteplitel, $product->uteplitel)) continue;
            if (!$this->compareAttrs($tag->podkladka, $product->podkladka)) continue;
            if (!$this->compareAttrs($tag->siluet, $product->siluet)) continue;
            if (!$this->compareAttrs($tag->dlina, $product->dlina)) continue;
            if (!$this->compareAttrs($tag->rukav, $product->rukav)) continue;

            if (!$this->compareAttrs($tag->dlina_rukava, $product->dlina_rukava)) continue;

            if (!$this->compareAttrs($tag->zastezhka, $product->zastezhka)) continue;

            if (!$this->compareAttrs($tag->kapushon, $product->kapushon)) continue;
            if (!$this->compareAttrs($tag->vorotnik, $product->vorotnik)) continue;
            if (!$this->compareAttrs($tag->poyas, $product->poyas)) continue;
            if (!$this->compareAttrs($tag->karmany, $product->karmany)) continue;
            if (!$this->compareAttrs($tag->koketka, $product->koketka)) continue;
            if (!$this->compareAttrs($tag->uhod, $product->uhod)) continue;


            //echo $tag->name . PHP_EOL;

            $product->tags .= $tag->name . ',';

            foreach ($product->attrs as $attr => $value) {
                //var_dump($attr, $value);
                if (!empty($tag->filterAttrs[$attr])) {
                    //var_dump($tag->filterAttrs[$attr]);
                    $tag->addRealAttribute($attr, $value);
                }
            }


        }
    }


    /* The order is following:
    First, if attr is empty,  tag match is returned instantly.
    Second, comparing tag attrs to product attrs, on match check for inversion.
    If not inverted, match is found. Else not.
    If match not found, but attr is inverted, return match.
     */

    private function compareAttrs($tagAttrArray, $productAttr)
    {

        if (empty($tagAttrArray)) return true;

        $matchCount = 0;
        $match = false;
        foreach ($tagAttrArray as $attr) {
            //comparing tag strings to attributes, if string match is found and not inverted set match true and return match;
            //string match found
            if (Tools::match($productAttr, $attr->attribute)) {
                $match = $attr->isInverted ? false : true;
            } //string match not found
            else {
                if ($attr->isInverted)
                    $matchCount++;
            }
            if ($matchCount === count($tagAttrArray)) $match = true;
            //else $match =  $attr->isInverted ? true : false;
            if ($match) return true;
        }
        return $match;
    }

    private function compareColors($tag, $productColor)
    {
        $tagColors = $tag->color;
        $match = false;

        foreach ($tagColors as $tagColor) {

            if (Tools::match($productColor, $tagColor->attribute)) {
                $match = true;
                $productColorTranslit = strtolower(Tools::transliterate($productColor));
                if (!in_array($productColorTranslit, $tag->realColors)) {
                    $tag->realColors[] = $productColorTranslit;
                }
            }
        }

        return $match;
    }

}