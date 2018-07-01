<?php
namespace frontend\tests\unit;

use Codeception\Test\Unit;
use shop\entities\User;
use yii\base\Exception;
use yii\validators\EmailValidator;

//use yii\validators\EmailValidator;

class OurEmailValidatorTest extends Unit
{
    protected $tester;

    /**
     * @dataProvider getEmailVariants
     * @param $email
     * @param $result
     */
    public function testEmail($email, $result)
    {
//        $validator = new EmailValidator();
//        $this->assertTrue($validator->validate('mail@site.com'));
//        $this->assertEquals($validator->validate('mail.site.com'), false);
//        $this->assertFalse($validator->validate('mail_site.com'));

        // we use special notation for data provider
//        $this->assertEquals($validator->validate($email), $result);
        // mark test is incomplete
        $this->markTestIncomplete();
        // mark test
    }

    /**
     * data provider for unit test
     * @return array
     */
    public function getEmailVariants()
    {
        return [
            ['mail@site.com', true],
            ['mail_site.com', false],
        ];
    }

    public function testUser()
    {
        $user = new User(['auth_key' => '1231133224']);
        $this->assertEquals($user->save(false), true);
    }

    public function testSaveToMSSQL()
    {
        if (!extension_loaded('mssql')) {
            // mark test as 'incomplete'
            $this->markTestSkipped('the MSSQL extension is not available.');
        }
    }

    /**
     * @group future
     * to run: ... --group future -- group common
     * also to skip: ... --skip-group future
     */
    public function testSomeFuture()
    {
        $this->markTestIncomplete();
    }

    /**
     * @group common
     */
    public function testOtherFuture()
    {
        $this->markTestIncomplete();
    }

    /**
     * @group exceptions
     * @expectedException Exception
     */
    public function testExpectedExcept()
    {
        $user = 2 / 0;
    }
}