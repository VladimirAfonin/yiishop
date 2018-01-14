<?php
namespace shop\services;

class ProductReader
{
    public function readCsv($file)
    {
        $result = [];
        $f = fopen($file->tmpName, 'r');
        while($row = fgetcsv($f)) {

            $row = new ProductRow();
            $row->code = $row[0];
            $row->priceNew = $row[1];
            $row->priceOld = $row[2];

//            $result[] = $row;


            yield $row; // проходим ставим на паузу и идем возвращаем генератор
        }
//        return $result;
        fclose($f);
    }
}