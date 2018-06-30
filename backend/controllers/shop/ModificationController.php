<?php
namespace backend\controllers\shop;

use shop\entities\Shop\Product\Product;
use shop\forms\manage\Shop\ModificationForm;
use shop\services\manage\Shop\ProductManageService;
use yii\base\Controller;
use yii\base\Module;
use yii\filters\VerbFilter;
use Yii;
use yii\web\NotFoundHttpException;

class ModificationController extends Controller
{
    private $_service;

    public function __construct($id, Module $module, ProductManageService $service, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_service = $service;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'create' => ['POST'],
                ]
            ]
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect('shop/product');
    }

    /**
     * @param $id
     * @return string
     */
    public function actionCreate($id)
    {
//        $product_id = Yii::$app->request->get('product_id');
//        var_dump($id);exit();

        $product = $this->findModel($product_id);
        $form = new ModificationForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_service->addModification($product->id, $form);
                return $this->redirect(['shop/product/view', 'id' => $product->id, '#' => 'modifications']);
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('create', [
            'product' => $product,
            'model' => $form
        ]);
    }

    /**
     * @param $productId
     * @param $id
     * @return string
     */
    public function actionUpdate($productId, $id) // по умолчанию от default buttons идет primary_key
    {
        $product = $this->findModel($productId);
        $modification = $product->getModification($id);

        $form = new ModificationForm($modification);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_service->editModification($product->id, $modification->id, $form);
                return $this->redirect(['shop/product/view', 'id' => $product->id, '#' => 'modifications']); // скроллим к modifications -> anchor
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'product' => $product,
            'model' => $form,
            'modification' => $modification
        ]);
    }

    /**
     * @param $productId
     * @param $id
     * @return mixed
     */
    public function actionDelete($productId, $id) // по умолчанию от default buttons идет primary_key
    {
        $product = $this->findModel($productId);
        try {
            $this->_service->removeModification($productId, $id);
        } catch(\RuntimeException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['shop/product/view', 'id' => $product->id, '#' => 'modifications']);
    }

    /**
     * @param $id
     * @return Product
     */
    public function findModel($id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('the request page doesnot exists');
    }
}