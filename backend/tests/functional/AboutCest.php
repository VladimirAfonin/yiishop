<?php
namespace backend\tests\functional;

use backend\tests\FunctionalTester;

class AboutCest
{
    public function checkAbout(FunctionalTester $I)
    {
        $I->amOnRoute('admin');
        $I->see('About', 'h1');
        $I->amGoingTo('test');
    }
}