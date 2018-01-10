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
        if(!$product =  Product::findOne($id)) {
            throw new \RuntimeException('product is not found.');
        }
        return $product;
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

    /**
     * Does we have product with certain brand
     *
     * @param $brandId
     * @return bool|bool
     */
    public function existsByBrand($brandId): bool
    {
        return Product::find()->andWhere(['brand_id' => $brandId])->exists();
    }
}