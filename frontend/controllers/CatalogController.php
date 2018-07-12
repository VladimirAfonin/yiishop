<?php
namespace frontend\controllers;

use backend\entities\Test;
use shop\collections\NotFoundException;
use shop\entities\Category;
use shop\entities\Product;
use shop\entities\TestTags;
use shop\forms\manage\Shop\AddToCartForm;
use shop\forms\manage\Shop\ReviewForm;
use shop\readCollections\BrandReadCollection;
use shop\readCollections\CategoryReadCollections;
use shop\readCollections\ProductReadCollections;
use shop\readCollections\TagReadCollections;
use Yii;
use yii\caching\DbDependency;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\filters\AccessControl;
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

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?']
                    ]
                ]
            ],
           /* [
                'class'      => 'yii\filters\PageCache',
                'duration'   => 60,
                'dependency' => [
                    'class' => 'yii\caching\DbDependency',
                    'sql'   => "SELECT MAX(updated_at) FROM " . Product::tableName(),
                ],
            ],*/
           [
               'class'=> 'yii\filters\PageCache',
               'duration' => 60,
               'dependency' => [
                   'class' => 'yii\caching\ChainedDependency',
                   'dependencies' => [
                       new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM ' . Product::tableName()]),
//                       new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM ' . Category::tableName()]),
//                       new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM ' . TestTags::tableName()]),
                   ],
               ],
           ],
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['index'],
                'lastModified' => function ($action, $params) {
                    return  Product::find()->max('updated_at');
                },
            ],
            [
                'class'    => 'yii\filters\HttpCache',
                'only'     => ['view'],
                'etagSeed' => function () {
                    $product = Product::findOne(Yii::$app->request->get('id'));
                    return serialize([
                        $product->updated_at,
                        $product->getTags()->select('name')->column(),
                        $product->getAttributeValue()->select('value')
                    ]);
                },
            ],
        ];
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
        /*
        Product::find()->active('p') but doesn't work with:
           ->alias('p')->joinWith('categoryAssignments c')
           ->andWhere(['p.status' => 1])->all();
        */

        // other example
        /*
        Product::find()->alias('p')->active()->joinWith('categories c')
            ->andWhere(['p.status' => 1])
            ->andWhere(['c.status' => 1])
            ->all;
        */

        // example of standard ActiveDataProvider:
        /*
         $dataProvider = new ActiveDataProvider([
            'query' => Product::find()->active('p'),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'name' => [
                        'asc' => ['p.name' => SORT_ASC],
                        'desc' => ['p.name' => SORT_DESC],
                    ],
                    'price' => [       - имя отличается от того что хранится в бд
                        'asc' => ['p.price_new' => SORT_ASC],
                        'desc' => ['p.price_new' => SORT_DESC],
                    ],
                    'rating' => [
                        'asc' => ['p.rating' => SORT_ASC],
                        'desc' => ['p.v' => SORT_DESC],
                    ],
                ],
            ]
        ]);
        */

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
            'allModels' => file('somenamefile.txt'), // read array from file...
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

        return $this->render('brand', compact('dataProvider', 'brand'));
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
        $this->layout = 'blank';

        if ( ! $product = $this->_products->find($id)) {
            throw new \RuntimeException('product not found');
        }

        $cartForm = new AddToCartForm($product);
        $reviewForm = new ReviewForm();

        return $this->render('product', compact('product', 'cartForm', 'reviewForm'));
    }

    public function findTagModel($name)
    {
        if ($model = TestTags::findOne(['name' => $name]) !== null) {
            return $model;
        } else {
            throw new NotFoundException('the requested page doesnot exists');
        }
    }
}