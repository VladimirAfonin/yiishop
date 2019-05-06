<?php
namespace frontend\controllers\cabinet;


use shop\services\auth\NetworkService;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use Yii;
use yii\base\Module;
use yii\helpers\Url;

class NetworkController extends Controller
{
    private $_service;

    public function __construct($id, Module $module, NetworkService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_service = $service;
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => [ $this, 'onAuthSuccess' ],
                'successUrl' => Url::to(['cabinet/default/index'])
            ]
        ];
    }

    public function onAuthSuccess(ClientInterface $client): void
    {
        $network = $client->getId();
        $attributes = $client->getUserAttributes();
        $identity = ArrayHelper::getValue($attributes, 'id');
        $userId = Yii::$app->user->id;

        try {
            $this->_service->attach($userId, $network, $identity);
            Yii::$app->session->setFlash('success', 'network is successfully attached.');
        } catch(\RuntimeException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
    }
}