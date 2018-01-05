<?php
namespace shop\collections;

use shop\entities\Shop\Product\Product;

class ProductCollection
{
    /**
     * @param $id
     * @return Product
     */
    public function get($id): Product
    {
        return Product::findOne($id);
    }

    /**
     * @param Product $product
     */
    public function save(Product $product)
    {
        if(!$product->save()) { throw new NotFoundException('saving error.'); }
    }

    /**
     * @param Product $product
     */
    public function remove(Product $product)
    {
        if(!$product->delete()) { throw new NotFoundException('delete error.'); }
    }
}