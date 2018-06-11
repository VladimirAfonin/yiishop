<?php
namespace backend\controllers\shop;

use SebastianBergmann\CodeCoverage\RuntimeException;
use shop\entities\Shop\Product\Product;
use shop\forms\manage\Shop\Product\PhotosForm;
use shop\forms\manage\Shop\Product\PriceForm;
use shop\forms\manage\Shop\Product\ProductCreateForm;
use shop\forms\manage\Shop\Product\ProductEditForm;
use shop\forms\manage\Shop\Product\TagsForm;
use shop\services\manage\Shop\ProductManageService;
use yii\base\Controller;
use yii\base\Module;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use Yii;
use yii\web\NotFoundHttpException;
use backend\forms\shop\ProductSearch;
use shop\entities\Shop\Product\Modification;

class ProductController extends Controller
{
    private $_service;

    public function __construct($id, Module $module, ProductManageService $service , array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_service = $service;
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-photo' => ['POST'],
                    'move-photo-up' => ['POST'],
                    'move-photo-down' => ['POST'],
                    'activate' => ['POST'],
                    'draft' => ['POST'],
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    /**
     * @param $id
     * @return string
     */
    public function actionView($id=1) // todo delete
    {
        $product = $this->findModel($id);

        $modificationsProvider = new ActiveDataProvider([
            'query' => $product->getModifications()->orderBy('name'),
            'key' => function(Modification $modification) use($product) { // по умолчанию берутся ключи id меняем на свои, котрые будут на button's
                return [
                    'product_id' => $product->id,
                    'id' => $modification->id,
                ];
            },
            'pagination' => false,
        ]);

        $photosForm = new PhotosForm();
        if($photosForm->load(Yii::$app->request->post()) && $photosForm->validate()) {
            try {
                $this->_service->addPhotos($product->id, $photosForm);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch(\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('view', [
            'product' => $product,
            'modificationsProvider' => $modificationsProvider,
            'photosForm' => $photosForm
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $form = new ProductCreateForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $product = $this->_service->create($form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('create', [
            'model' => $form
        ]);
    }

    public function actionUpdate($id)
    {
        $product = $this->findModel($id);
        $form = new ProductEditForm($product);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_service->edit($product->id, $form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $form,
            'product' => $product
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionPrice($id)
    {
        $product = $this->findModel($id);
        $form = new PriceForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_service->changePrice($product->id, $form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('price', [
            'model' => $form,
            'product' => $product
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try{
            $this->_service->remove($id);
        } catch(\RuntimeException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @param $photoId
     * @return string
     */
    public function actionDeletePhoto($id, $photoId)
    {
        try {
            $this->_service->removePhoto($id, $photoId);
        } catch(\RuntimeException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->render(['view', 'id' => $id, '#' => 'photos']);
    }

    /**
     * @param $id
     * @param $photoId
     * @return mixed
     */
    public function actionMovePhotoUp($id, $photoId)
    {
        $this->_service->movePhotoUp($id, $photoId);
        return $this->redirect(['view', 'id' => $id, '#' => 'photos']);
    }

    /**
     * @param $productId
     * @param $photoId
     * @return mixed
     */
    public function actionMovePhotoDown($productId, $photoId)
    {
        $this->_service->movePhotoDown($productId, $photoId);
        return $this->redirect(['view', 'id' => $productId, '#' => 'photos']);
    }

    /**
     * @param $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('the requested page doesnot exists');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionActivate($id)
    {
        try {
            $this->_service->activate($id);
            Yii::$app->session->setFlash('success', 'status has been successfully changed!');
        } catch (\RuntimeException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionDraft($id)
    {
        try {
            $this->_service->draft($id);
            Yii::$app->session->setFlash('success', 'statuc has been successfully changed!');
        } catch (\RuntimeException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['view', 'id' => $id]);
    }
}