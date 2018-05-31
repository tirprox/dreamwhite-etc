<?php

namespace Dreamwhite\Assortment;
class CSVReportGenerator
{
    private static $file;

    private static $csvHeader = "Код основного товара,Группа,Ед.изм.,Поставщик,Описание товара,Характеристика:цвет,Характеристика:размер,Наименование модификации,Код модификации,Штрихкод по умолчанию,Остаток,Цена продажи,Наименование основного товара,Артикул,material,uteplitel,podkladka,siluet,dlina_izdeliya,rukav,dlina_rukava,zastezhka,kapushon,vorotnik,poyas,karmany,koketka,uhod\n";
    private static $csvContent = "";

    static function openFile()
    {
        $path = dirname(__DIR__) . "/output/assortment.csv";
        self::$file = fopen($path, "w+");
    }

    static function writeCsvHeader()
    {
        fwrite(self::$file, self::$csvHeader);
    }

    static function appendCSVString($stringToAppend)
    {
        self::$csvContent .= $stringToAppend;
    }

    static function writeToFile($string)
    {
        fwrite(self::$file, $string);
    }

    static function writeCSVToFile()
    {
        fwrite(self::$file, self::$csvContent);
    }
}