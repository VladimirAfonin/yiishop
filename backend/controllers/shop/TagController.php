<?php
namespace backend\controllers\shop;

use shop\forms\manage\Shop\TagForm;
use shop\services\manage\Shop\TagManageService;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\base\Module;
use Yii;
use shop\entities\Shop\Tag;
use backend\forms\shop\TagSearch;
use yii\web\Response;

class TagController extends Controller
{
    private $_service;

    public function __construct($id, Module $module, TagManageService $service, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_service = $service;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    /**
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', ['tag' => $this->findModel($id)]);
    }

    /**
     * @return Response
     */
    public function actionCreate()
    {
        $form = new TagForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $tag = $this->_service->create($form);
                return $this->redirect(['view', 'id' => $tag->id]);
            } catch(\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('create', ['model' => $form]);
    }

    /**
     * @param $id
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $tag = $this->findModel($id);
        $form = new TagForm($tag);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_service->edit($id, $form);
                Yii::$app->session->setFlash('success', 'tag successfully edit');
                return $this->redirect(['view', 'id' => $tag->id]);
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $form,
            'tag' => $tag
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function actionDelete($id)
    {
        try {
            $this->_service->remove($id);
            Yii::$app->session->setFlash('success', 'tag successfully deleted.');
            return $this->redirect(['index']);
        } catch(\RuntimeException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @param $id
     * @return Tag
     */
    public function findModel($id): Tag
    {
        if (($model = Tag::findOne($id)) !== null) {
            return $model;
        }
        throw new \RuntimeException('The requested brand does not exist.');

    }
}