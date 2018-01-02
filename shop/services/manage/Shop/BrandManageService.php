<?php
namespace shop\services\manage\Shop;

use shop\collections\BrandCollection;
use shop\entities\Meta;
use shop\entities\Shop\Brand;
use shop\forms\manage\MetaForm;
use shop\forms\manage\Shop\BrandForm;

class BrandManageService
{
    private $_brandCollect;

    public function __construct(BrandCollection $brandsCollect)
    {
        $this->_brandCollect = $brandsCollect;
    }

    /**
     * @param BrandForm $form
     * @return Brand
     */
    public function create(BrandForm $form /*, MetaForm $metaForm */): Brand
    {
        $brand = Brand::create(
            $form->name,
            $form->slug,
            new Meta( $form->meta->title, $form->meta->description, $form->meta->keywords)
        );
        $this->_brandCollect->save($brand);
        return $brand;
    }

    /**
     * @param $id
     * @param BrandForm $form
     */
    public function edit($id, BrandForm $form): void
    {
        $brand = $this->_brandCollect->get($id);
        $brand->edit(
            $form->name,
            $form->slug,
            new Meta($form->meta->title, $form->meta->description, $form->meta->keywords)
        );
        $this->_brandCollect->save($brand);
    }

    /**
     * @param $id
     */
    public function remove($id): void
    {
        $brand = $this->_brandCollect->get($id);
        $this->_brandCollect->remove($brand);
    }
}