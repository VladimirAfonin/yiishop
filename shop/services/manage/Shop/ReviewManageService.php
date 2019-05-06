<?php
namespace shop\services\manage\Shop;


use shop\collections\ProductCollection;
use shop\forms\manage\Shop\Product\ReviewEditForm;

class ReviewManageService
{
    private $_products;

    public function __construct(ProductCollection $product)
    {
        $this->_products = $product;
    }

    /**
     * @param $id
     * @param $reviewId
     * @param ReviewEditForm $form
     */
    public function edit($id, $reviewId, ReviewEditForm $form): void
    {
        $product = $this->_products->get($id);
        $product->editReview(
            $reviewId,
            $form->vote,
            $form->text
        );
        $this->_products->save($product);
    }

    /**
     * @param $id
     * @param $reviewId
     */
    public function activate($id, $reviewId): void
    {
        $product = $this->_products->get($id);
        $product->activateReview($reviewId);
        $this->_products->save($product);
    }

    /**
     * @param $id
     * @param $reviewId
     */
    public function draft($id, $reviewId): void
    {
        $product = $this->_products->get($id);
        $product->draftReview($reviewId);
        $this->_products->save($product);
    }

    /**
     * @param $id
     * @param $reviewId
     */
    public function remove($id, $reviewId): void
    {
        $product = $this->_products->get($id);
        $product->removeReview($reviewId);
        $this->_products->save($product);
    }

}