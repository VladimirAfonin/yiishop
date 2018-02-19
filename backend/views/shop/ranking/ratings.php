<?php
use backend\entities\RankingHelper;

/** @var $arrOfMainRatings array */
/** @var $univRank array */
/** @var $univIndicators array */

$arrOfMainRatings = Yii::$app->params['dataMainRankings'];
foreach ($arrOfMainRatings as $k => $value) {
    $univRank = RankingHelper::createDataFile("$k"."_r", RankingHelper::getItemUrl("$k")['r'], RankingHelper::getItemUrl("$k")['base']);
    $univIndicators = RankingHelper::createDataFile("$k"."_i", RankingHelper::getItemUrl($k)['i']);
    RankingHelper::saveToDb($univRank, $value);
    sleep(1);
}
echo "<div class='alert alert-success' role='alert'>all data save in 'main_rankings' table!</div>";