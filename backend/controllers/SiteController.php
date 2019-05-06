<?php
namespace backend\controllers;

use shop\entities\User;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use shop\forms\auth\LoginForm;
use yii\base\Module;
use shop\services\auth\AuthService;

/**
 * Site controller
 */
class SiteController extends Controller
{
    private $authService;

    public function __construct(
        $id,
        Module $module,
        AuthService $authService,
        $config = [] )
    {
        parent::__construct($id, $module, $config);
        $this->authService = $authService;
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
//                'denyCallback' => [],
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
//                        'matchCallback' => function($rule, $action) {
//                            return Yii::$app->user->identity->admin;
//                        }
                    ],
                ],
            ],
//            'authenticator' => [
//                'class' => HttpBasicAuth::className(),
//                'realm' => 'Protected area',
//                'auth'  => function ($username, $password) {
//                    /** @var User $user */
//                    $user = User::findByUsername($username);
//                    if ($user && $user->validatePassword($password)) {
//                        return $user;
//                    } else {
//                        return null;
//                    }
//                },
//            ],
            'verbs'         => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';
        ////////////////////
        if ( ! Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $form = new LoginForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $user = $this->authService->auth($form);
                Yii::$app->user->login($user, $form->rememberMe ? 3600 * 24 * 30 : 0);
                return $this->goBack();
            } catch (\RuntimeException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

        }
        return $this->render('login', [
            'model' => $form,
        ]);
        ////////////////////
//
//
//
//        $this->layout = 'main-login';
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        } else {
//            return $this->render('login', [
//                'model' => $model,
//            ]);
//        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
