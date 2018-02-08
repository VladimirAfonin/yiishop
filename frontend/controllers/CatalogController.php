<?php
namespace frontend\controllers;

use shop\readCollections\BrandReadCollection;
use shop\readCollections\CategoryReadCollections;
use shop\readCollections\ProductReadCollections;
use shop\readCollections\TagReadCollections;
use yii\data\SqlDataProvider;
use yii\web\Controller;
use yii\base\Module;

class CatalogController extends Controller
{
    public $layout = 'catalog';
    private $_brands;
    private $_categories;
    private $_tags;
    private $_products;

    public function __construct(
        $id,
        Module $module,
        ProductReadCollections $products,
        BrandReadCollection $brands,
        TagReadCollections $tags,
        CategoryReadCollections $categories,
        array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_brands = $brands;
        $this->_categories = $categories;
        $this->_tags = $tags;
        $this->_products = $products;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'blank';
        // we use as default, but we have ProductQuery with custom 'find' method
//        Product::find()->andWhere(['status' => Product::STATUS_ACTIVE]);

        // our custom ProductQuery!!!
//        Product::find()->active('p')/* but doesnot work with: ->alias('p')->joinWith('categoryAssignments c')->andWhere(['category_id' => $id])->all() */->all();

        // --- without dataprovider --- //
        /*$query = Product::find()->active(); //  вместо использования DataProvider
        $pagination = new Pagination([
            'totalCount' => $query->count(),
        ]);
        $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        $sort = new Sort([
            'attributes' => [
                'id',
                'price',
                'rating'
            ]
        ]);
        $query->orderBy($sort->getOrders());
        $products = $query->all();*/
        // ---  --- //

        // --- example 'SqlDataProvider' --- //
        /*$dataProvider = new SqlDataProvider([
            'sql' => 'SELECT * FROM ...',
            'sort' => [
                'attributes' => [
                    'id',
                    'price',
                    'rating'
                ]
            ]
        ]);*/
        // --- --- //

        // --- example 'ArrayDataProvider' --- //
        /*$dataProvider = new ArrayDataProvider([
            'allModels' => file(),
            'sort' => [
                'attributes' => [
                    'id',
                    'price',
                    'rating'
                ]
            ]
        ]);*/
        // --- --- //

       $dataProvider = $this->_products->getAll();
       $category = $this->_categories->getRoot();

        return $this->render('index', compact('dataProvider', 'category'));
    }

    /**
     * query products of some category
     *
     * @param $id
     * @return string
     */
    public function actionCategory($id)
    {
        if ( ! $category = $this->_categories->find($id)) {
            throw new \RuntimeException('category not found.');
        }

        $dataProvider = $this->_products->getAllByCategory($category);

        return $this->render('category', compact('dataProvider', 'category'));
    }

    /**
     * @param $id
     * @return string
     */
    public function actionBrand($id)
    {
        if ( ! $brand = $this->_brands->find($id)) {
            throw new \RuntimeException('brand not found.');
        }

        $dataProvider = $this->_products->getAllByBrand($brand);

        return $this->render('category', compact('dataProvider', 'brand'));
    }

    /**
     * @param $id
     * @return string
     */
    public function actionTag($id)
    {
        if ( ! $tag = $this->_tags->find($id)) {
            throw new \RuntimeException('tag not found');
        }

        $dataProvider = $this->_products->getAllByTag($tag);

        return $this->render('tag', compact('dataProvider', 'tag'));
    }

    /**
     * @param $id
     * @return string
     */
    public function actionProduct($id)
    {
        if ( ! $product = $this->_products->find($id)) {
            throw new \RuntimeException('product not found');
        }

        return $this->render('product', compact('product'));
    }
}