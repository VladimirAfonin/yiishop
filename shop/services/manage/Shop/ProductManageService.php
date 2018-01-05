<?php
namespace shop\services\manage\Shop;


use shop\collections\BrandCollection;
use shop\collections\CategoryCollection;
use shop\entities\Shop\Product\Product;
use shop\forms\manage\Shop\Product\ProductCreateForm;
use shop\entities\Meta;
use shop\collections\ProductCollection;
use shop\forms\manage\Shop\Product\CategoriesForm;

class ProductManageService
{
    private $_products;
    private $_brands;
    private $_categories;

    public function __construct(ProductCollection $products, BrandCollection $brands, CategoryCollection $categoryCollection)
    {
        $this->_products = $products;
        $this->_brands = $brands;
        $this->_categories = $categoryCollection;
    }

    /**
     * @param ProductCreateForm $form
     * @return Product
     */
    public function create(ProductCreateForm $form): Product
    {
        $brand = $this->_brands->get($form->brandId);
        $category = $this->_categories->get($form->categories->main);

        $product = Product::create(
            $brand->id,
            $category->id,
            $form->code, // articul.
            $form->name,
            new Meta( $form->meta->title, $form->meta->description, $form->meta->keywords )
        );

        $product->setPrice($form->price->new, $form->price->old);

        foreach($form->_categories->others as $otherId) {
            $category = $this->_categories->get($otherId);
            $product->assignCategory($category->id);
        }

        $this->_products->save($product);

        return $product;
    }

    public function changeCategories($id, CategoriesForm $form): void
    {
        $product = $this->_products->get($id);
        $category = $this->_categories->get($form->main);
        $product->changeMainCategory($category->id);
        $product->revokeCategories();
        foreach($form->others as $otherid) {
            $category = $this->_categories->get($otherid);
            // todo:
        }
    }

    /**
     * @param $id
     */
    public function remove($id): void
    {
        $product = $this->_products->get($id);
        $this->_products->remove($product);
    }

}