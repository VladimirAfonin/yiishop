<?php
namespace frontend\widgets;

use shop\readCollections\ProductReadCollections;
use yii\base\Widget;

class FeaturedProductsWidget extends Widget
{
    public $limit;
    private $_collections;

    public function __construct(ProductReadCollections $collections, array $config = [])
    {
        parent::__construct($config);
        $this->_collections = $collections;
    }

    public function run()
    {
        return $this->render('featured', [
            'products' => $this->_collections->getFeatured($this->limit)
        ]);
    }
}