<?php
namespace frontend\services\contact;

use Yii;
use shop\forms\auth\ContactForm;
use yii\mail\MailerInterface;

class ContactService
{
    private $supportEmail;
    private $adminEmail;
    private $mailer;

    public function __construct($supportEmail, $adminEmail, MailerInterface $mailer)
    {
        $this->supportEmail = $supportEmail;
        $this->adminEmail = $adminEmail;
        $this->mailer = $mailer;
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
        $sent = $this->mailer
            ->compose()
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