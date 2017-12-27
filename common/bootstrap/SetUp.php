<?php
namespace common\bootstrap;

use common\collections\UserCollection;
use common\services\auth\AuthService;
use frontend\services\auth\PasswordResetService;
use frontend\services\contact\ContactService;
use yii\base\BootstrapInterface;
use yii\di\Instance;
use yii\mail\MailerInterface;
use frontend\services\auth\SignUpService;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        // configure our 'passwordReset' service for dependency injection
        $container->setSingleton(PasswordResetService::class, function() use($app) {
            return new PasswordResetService(
                [$app->params['supportEmail'] => $app->name . ' robot'],
                $app->mailer,
                new UserCollection()
            );
        });

        // set 'signUp' service
        $container->setSingleton(SignUpService::class, function() use($app) {
            return new SignUpService(
                [$app->params['supportEmail'] => $app->name . ' robot'],
                $app->mailer
            );
        });

        // set 'auth' service
        $container->setSingleton(AuthService::class, function() use($app) {
            return new AuthService(
                new UserCollection()
            );
        });

        // set 'contactService'
        $container->setSingleton(ContactService::class, function() use($app) {
            return new ContactService(
                [$app->params['supportEmail'] => $app->name . ' robot'],
                $app->params['adminEmail'],
                $app->mailer
            );
        });





        /* --- set 'contactService' -> second variant  --- */
        $container->setSingleton(MailerInterface::class, function() use ($app) {
            return $app->mailer;
        });

//        $container->setSingleton(ContactService::class, [], [
//            $app->params['supportEmail'],
//            $app->params['adminEmail'],
//            Instance::of(MailerInterface::class)
//        ]);
        /* ---  /.set 'contactService' -> second variant --- */



    }
}
