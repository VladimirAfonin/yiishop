<?php
namespace common\bootstrap;

use app\models\Email;
use common\services\EmailService;
use shop\collections\UserCollection;
use shop\dispatchers\DeferredEventDispatcher;
use shop\dispatchers\EventDispatcher;
use shop\dispatchers\events\UserSignUpConfirmed;
use shop\dispatchers\events\UserSignUpRequested;
use shop\dispatchers\listeners\UserSignUpConfirmListener;
use shop\dispatchers\listeners\UserSignUpRequestListener;
use shop\dispatchers\SimpleEventDispatcher;
use shop\services\auth\AuthService;
use shop\services\auth\PasswordResetService;
use shop\services\contact\ContactService;
use yii\base\BootstrapInterface;
use yii\di\Container;
use yii\di\Instance;
use yii\mail\MailerInterface;
use shop\services\auth\SignUpService;

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

        // set 'EmailService'
        $container->setSingleton(EmailService::class, function() use ($app) {
            return new EmailService(
                $app->user->identity->email ?? null,
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

        $container->setSingleton(EventDispatcher::class, DeferredEventDispatcher::class);

        $container->setSingleton(DeferredEventDispatcher::class, function(Container $container) {
            return new DeferredEventDispatcher(
                new SimpleEventDispatcher($container, [
                    UserSignUpRequested::class => [UserSignUpRequestListener::class],
                    UserSignUpConfirmed::class => [UserSignUpConfirmListener::class],
                ])
            );
        });
        //    [$container->get(UserSignUpConfirmListener::class), 'handle'],
        /*function($event) {
            // ...
        },*/
//       ],
    }
}
