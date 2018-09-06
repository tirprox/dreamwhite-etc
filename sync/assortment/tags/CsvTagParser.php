<?php
namespace Dreamwhite\Assortment;

class CsvTagParser
{
    private const NAME_START = 0, NAME_END = 9;
    private const FILTERABLE_OFFSET = 10;

    private const RELATION_OFFSET = 11;
    private const RELATION_MAP = [
        'parent' => self::RELATION_OFFSET,
        'type' => self::RELATION_OFFSET+1,
        'gender' => self::RELATION_OFFSET+2,
        'group' => self::RELATION_OFFSET+3,
    ];

    private const ATTR_OFFSET = 15;
    private const ATTR_MAP = [
        'article' => self::ATTR_OFFSET,
        'color' => self::ATTR_OFFSET+1,
        'colorGroup' => self::ATTR_OFFSET+2,
        'texture' => self::ATTR_OFFSET+3,
        'season' => self::ATTR_OFFSET+4,
        'material' => self::ATTR_OFFSET+5,
        'uteplitel' => self::ATTR_OFFSET+6,
        'podkladka' => self::ATTR_OFFSET+7,
        'siluet' => self::ATTR_OFFSET+8,
        'dlina' => self::ATTR_OFFSET+9,
        'rukav' => self::ATTR_OFFSET+10,
        'dlina_rukava' => self::ATTR_OFFSET+11,
        'zastezhka' => self::ATTR_OFFSET+12,
        'kapushon' => self::ATTR_OFFSET+13,
        'vorotnik' => self::ATTR_OFFSET+14,
        'poyas' => self::ATTR_OFFSET+15,
        'karmany' => self::ATTR_OFFSET+16,
        'koketka' => self::ATTR_OFFSET+17,
        'uhod' => self::ATTR_OFFSET+18,
        'size' => self::ATTR_OFFSET+19,
    ];

    private const SEO_OFFSET = 35;
    private const SEO_MAP = [
        'h1' => self::SEO_OFFSET,
        'short_name' => self::SEO_OFFSET+1,
        'meta_title_spb' => self::SEO_OFFSET+2,
        'meta_description_spb' => self::SEO_OFFSET+3,
        'description_spb' => self::SEO_OFFSET+4,
        'meta_title_msk' => self::SEO_OFFSET+5,
        'meta_description_msk' => self::SEO_OFFSET+6,
        'description_msk' => self::SEO_OFFSET+7,
    ];

    public static function fromFile($path)
    {

        $csvFile = file($path);
        $data = [];

        foreach ($csvFile as $line) {
            $data[] = str_getcsv($line, ';');
        }

        // removing csv header
        unset($data[0]);

        $tagRows = [];

        foreach ($data as $row) {
            $tagRow = [];
            $tagRow['name'] = self::getName($row)['name'];
            $tagRow['relations']['filterable'] = $row[self::FILTERABLE_OFFSET] == 1 ? 1 : 0;
            $tagRow['relations']['level'] = self::getName($row)['level'];


            foreach (self::RELATION_MAP as $key => $value) {
                $tagRow['relations'][$key] = $row[$value];
            }

            foreach (self::ATTR_MAP as $key => $value) {
                $tagRow['attrs'][$key] = $row[$value];
            }

            foreach (self::SEO_MAP as $key => $value) {
                $tagRow['seo'][$key] = $row[$value];
            }

            $tagRows[] = $tagRow;
        }

        return $tagRows;

    }

    private static function getName($row) {
        $name = 'NOT_FOUND';

        $level = 0;

        for ($i = self::NAME_START; $i <= self::NAME_END; $i++) {
            if ($row[$i] !== '') {
                $name = $row[$i];
                $level = $i;
                break;
            }
        }

        return [
            'name' => $name,
            'level' => $level
        ];
    }

}