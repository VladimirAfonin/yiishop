<?php
namespace shop\collections;

use shop\entities\Shop\Brand;

class BrandCollection
{
    /**
     * @param $id
     * @return Brand
     */
    public function get($id): Brand
    {
        return Brand::findOne($id);
    }

    /**
     * @param Brand $brand
     */
    public function save(Brand $brand)
    {
        if(!$brand->save()) { throw new NotFoundException('saving error.'); }
    }

    /**
     * @param Brand $brand
     */
    public function remove(Brand $brand)
    {
        if(!$brand->delete()) { throw new NotFoundException('delete error.'); }
    }
}