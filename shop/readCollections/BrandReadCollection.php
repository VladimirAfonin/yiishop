<?php
namespace shop\readCollections;

use shop\entities\Shop\Brand;

class BrandReadCollection
{
    public function find($id): Brand
    {
        return Brand::findOne($id);
    }
}