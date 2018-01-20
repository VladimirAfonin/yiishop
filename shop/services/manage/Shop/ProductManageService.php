<?php
namespace shop\services\manage\Shop;


use shop\collections\{BrandCollection, CategoryCollection, TagCollection, ProductCollection};
use shop\entities\Shop\Product\Product;
use shop\entities\Shop\Product\Tag;
use shop\forms\manage\Shop\Product\{
    PhotosForm, CategoriesForm, ProductCreateForm, ProductEditForm, PriceForm
};
use shop\entities\Meta;
use shop\services\manage\TransactionManager;
use shop\services\ProductReader;

class ProductManageService
{
    private $_products;
    private $_brands;
    private $_categories;
    private $_tags;
    private $_transaction;
    private $_reader;

    /**
     * @return BrandCollection
     */
    public function getBrands(): BrandCollection
    {
        return $this->_brands;
    }

    public function __construct(
        ProductCollection $products,
        BrandCollection $brands,
        CategoryCollection $categoryCollection,
        TagCollection $tags,
        ProductReader $reader,
        TransactionManager $transaction)
    {
        $this->_products = $products;
        $this->_brands = $brands;
        $this->_categories = $categoryCollection;
        $this->_tags = $tags;
        $this->_transaction = $transaction;
        $this->_reader = $reader;
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
            $form->code, // артикул.
            $form->name,
            new Meta( $form->meta->title, $form->meta->description, $form->meta->keywords )
        );

        $product->setPrice($form->price->new, $form->price->old);

        // category
        foreach($form->categories->others as $otherId) {
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

        // existing tags
        foreach ($form->tags->existing as $tagId) {
            $tag = $this->_tags->get($tagId);
            $product->assignTag($tag->id);
        }

        $this->_transaction->wrap(function() use($form, $product) {
            // new tags
            foreach ($form->tags->newName as $tagName) {
                if ( ! $tag = $this->_tags->findByName($tagName)) {
                    $tag = Tag::create($tagName);
                    $this->_tags->save($tag);
                }
                $product->assignTag($tag->id);
            }
            $this->_products->save($product);
        });


//        $this->_products->save($product);

        return $product;
    }

    public function edit($id, ProductEditForm $form): void
    {
        $product = $this->_products->get($id);
        $brand = $this->_brands->get($form->brandId);
        $category = $this->_categories->get($form->categories->main);
        $product->edit(
            $brand->id,
            $form->code,
            $form->name,
            $form->description,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );
        $product->changeMainCategory($category->id);

        $this->_transaction->wrap(function () use ($product, $form) {
            $product->revokeCategories();
            $product->revokeTags();
            $this->_products->save($product);

            foreach ($form->categories->others as $otherId) {
                $category = $this->_categories->get($otherId);
                $product->assignCategory($category->id);
            }

            foreach ($form->values as $value) {
                $product->setValue($value->id, $value->value);
            }

            foreach ($form->tags->existing as $tagId) {
                $tag = $this->_tags->get($tagId);
                $product->assignTag($tag->id);
            }

            foreach ($form->tags->newNames as $tagName) {
                if (!$tag = $this->_tags->findByName($tagName)) {
                    $tag = Tag::create($tagName, $tagName);
                    $this->_tags->save($tag);
                }
                $product->assignTag($tag->id);
            }

            $this->_products->save($product);
        });
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
     * @param $id
     * @param PriceForm $form
     */
    public function changePrice($id, PriceForm $form): void
    {
        $product = $this->_products->get($id);
        $product->setPrice($form->new, $form->old);
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
     * remove product
     *
     * @param $id
     */
    public function remove($id): void
    {
        $product = $this->_products->get($id);
        $this->_products->remove($product);
    }

    /**
     * @param $id
     * @param $otherId
     */
    public function addRelatedProduct($id, $otherId): void
    {
        $product = $this->_products->get($id);
        $otherProduct = $this->_products->get($otherId);
        $product->assignRelatedProduct($otherProduct->id);
        $this->_products->save($product);
    }

    /**
     * @param $id
     * @param $otherId
     */
    public function removeRelatedProduct($id, $otherId): void
    {
        $product = $this->_products->get($id);
        $otherProduct = $this->_products->get($id);
        $product->revokeRelatedProduct($otherProduct->id);
        $this->_products->save($product);
    }

    public function import($id, ImportForm $form): void
    {
        $this->_transaction->wrap(function() use($form) {
            $results = $this->_reader->readCsv($form->file->tmpName);
            foreach($results as $result) {
                $product = $this->_products->getByCode($result->code);
                $this->_products->save($product);
            }
        });
    }

    /**
     * @param $id
     */
    public function activate($id): void
    {
        $product = $this->_products->get($id);
        $product->activate();
        $this->_products->save($product);
    }

    /**
     * @param $id
     */
    public function draft($id): void
    {
        $product = $this->_products->get($id);
        $product->activate();
        $this->_products->save($product);
    }

}