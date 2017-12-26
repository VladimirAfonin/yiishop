<?php
namespace common\bootstrap;

use frontend\services\auth\PasswordResetService;
use frontend\services\contact\ContactService;
use yii\base\BootstrapInterface;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        // configure our 'passwordReset' service for dependency injection
        $container->setSingleton(PasswordResetService::class, function() use($app) {
            return new PasswordResetService( [$app->params['supportEmail'] => $app->name . ' robot'] );
        });

        // set 'contactService'
        $container->setSingleton(ContactService::class, function() use($app) {
            return new ContactService(
                [$app->params['supportEmail'] => $app->name . ' robot'],
                $app->params['adminEmail'] );
        });


    }
}
