<?php
namespace frontend\services\contact;

use Yii;
use frontend\forms\ContactForm;

class ContactService
{
    private $supportEmail;
    private $adminEmail;

    public function __construct($supportEmail, $adminEmail)
    {
        $this->supportEmail = $supportEmail;
        $this->adminEmail = $adminEmail;
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param ContactForm $form
     * @return void
     * @internal param ContactForm|string $email the target email address
     */
    public function send(ContactForm $form): void
    {
        $sent = Yii::$app->mailer->compose()
            ->setFrom([$this->supportEmail])
            ->setTo($this->adminEmail)
            ->setSubject($form->subject)
            ->setTextBody($form->body)
            ->send();

        if(!$sent) {
            throw new \RuntimeException('sending error.');
        }
    }
}