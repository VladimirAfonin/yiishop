<?php
namespace shop\services\manage\Shop;


use shop\collections\BrandCollection;
use shop\collections\CategoryCollection;
use shop\entities\Shop\Product\Product;
use shop\forms\manage\Shop\Product\PhotosForm;
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

        // category
        foreach($form->_categories->others as $otherId) {
            $category = $this->_categories->get($otherId);
            $product->assignCategory($category->id);
        }

        // values
        foreach($form->values as $value) {
            $product->setValue($value->id, $value->value);
        }

        // photos
        foreach($form->photos->files as $file) {
            $product->addPhoto($file);
        }

        $this->_products->save($product);

        return $product;
    }

    /**
     * @param $id
     * @param CategoriesForm $form
     */
    public function changeCategories($id, CategoriesForm $form): void
    {
        $product = $this->_products->get($id);
        $category = $this->_categories->get($form->main);
        $product->changeMainCategory($category->id);
        $product->revokeCategories();
        foreach($form->others as $otherid) {
            $category = $this->_categories->get($otherid);
            $product->assignCategory($category->id);
        }
        $this->_products->save($product);
    }

    /**
     * @param $id
     * @param PhotosForm $form
     */
    public function addPhotos($id, PhotosForm $form): void
    {
        $product = $this->_products->get($id);
        foreach ($form->files as $file) {
            $product->addPhoto($file);
        }
        $this->_products->save($product);
    }

    /**
     * @param $productId
     * @param $photoId
     */
    public function movePhotoUp($productId, $photoId): void
    {
        $product = $this->_products->get($productId);
        $product->movePhotoUp($photoId);
        $this->_products->save($product);
    }

    /**
     * @param $productId
     * @param $photoId
     */
    public function movePhotoDown($productId, $photoId): void
    {
        $product = $this->_products->get($productId);
        $product->movePhotoDown($photoId);
        $this->_products->save($product);
    }

    /**
     * remove one photo in product
     *
     * @param $productId
     * @param $photoId
     */
    public function removePhoto($productId, $photoId): void
    {
        $product = $this->_products->get($productId);
        $product->removePhoto($photoId);
        $this->_products->save($product);
    }

    /**
     * remove all photos in product
     *
     * @param $productId
     * @internal param $photoId
     */
    public function removePhotos($productId): void
    {
        $product = $this->_products->get($productId);
        $product->removePhotos();
        $this->_products->save($product);
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