<?php
namespace frontend\tests\Page;

use frontend\tests\AcceptanceTester;

class SearchPage
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    /**
     * @var AcceptanceTester
     */
    protected $tester;

    public function __construct(AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    /**
     * @param AcceptanceTester $I
     * @return static
     */
    public static function openBy(AcceptanceTester $I)
    {
        $page = new static($I);
        $I->amOnPage(self::$URL);
        return $page;
    }

    public function search($value)
    {
        $I = $this->tester;
        $I->fillField('input[name=text]', $value);
        $I->click('button[type=submit]');
    }

}
