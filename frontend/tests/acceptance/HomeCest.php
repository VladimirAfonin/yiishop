<?php
namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;
use frontend\tests\Page\SearchPage;
use shop\entities\User;
use yii\helpers\Url;

class HomeCest
{
    /**
     * @param AcceptanceTester $I
//     * @before login
//     * @env screen
     * @group check
     */
    public function checkHome(AcceptanceTester $I)
    {
        // notation @before
//        $this->login($I);

        $I->amOnPage(Url::toRoute('/site/'));
        $I->see('Congratulations!','h1');

/*        $I->seeRecord(User::class, [
            'username' => 'tester',
            'email' => 'tester.email@example.com',
            'status' => User::STATUS_WAIT
        ]);*/

        $I->seeLink('About');
        $I->click('About');
        $I->wait(2); // wait for page to be opened

        $I->see('This is the About page.');

        $I->fillField('search','test');
        $I->click('button[class="btn btn-default btn-lg"]');
        $I->wait(2);
        $I->see('Not Found');
        $I->seeInCurrentUrl('/product/search&search=test');
    }

    protected function login(AcceptanceTester $i)
    {
        $i->amOnPage('/login');
//        $i->fillField('Username', 'miles');
//        $i->fillField('password', 'davies');
//        $i->click('login');
    }

/*    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('ensure that search works');
        $page = SearchPage::openBy($I);
        $I->see('Найти');
        $page->search('codeception');
        if (method_exists($I, 'wait')) {
            $I->wait(3);
        }
        $I->seeInCurrentUrl('/search/?text=Codeception');
        $I->see('codeception - bdd-style php testing');
    }*/


}
