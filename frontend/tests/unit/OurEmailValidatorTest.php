<?php
namespace frontend\tests\unit;

use Codeception\Test\Unit;
//use yii\validators\EmailValidator;

class EmailValidatorTest extends Unit
{
    protected $tester;

    /**
     * @dataProvider getEmailVariants
     * @param $email
     * @param $result
     */
    public function testEmail($email, $result)
    {
        $validator = new EmailValidator();
//        $this->assertTrue($validator->validate('mail@site.com'));
//        $this->assertEquals($validator->validate('mail.site.com'), false);
//        $this->assertFalse($validator->validate('mail_site.com'));

        // we use special notation for data provider
        $this->assertEquals($validator->validate($email), $result);
    }

    public function getEmailVariants()
    {
        return [
            ['mail@site.com', true],
            ['mail_site.com', false],
        ];
    }
}