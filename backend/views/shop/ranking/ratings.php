<?php
use backend\entities\RankingHelper;

/** @var $arrOfMainRatings array */
/** @var $univRank array */
/** @var $univIndicators array */

$arrOfMainRatings = Yii::$app->params['dataMainRankings'];

$result = [];
foreach ($arrOfMainRatings as $k => $value) {
    $univRank = RankingHelper::createDataFile("$k"."_r", RankingHelper::getItemUrl("$k")['r'], RankingHelper::getItemUrl("$k")['base']);
    $univIndicators = RankingHelper::createDataFile("$k"."_i", RankingHelper::getItemUrl($k)['i']);
    $result[] = RankingHelper::saveToArr($univRank, $value);
    sleep(1);
}

echo '<pre>';
print_r($result);
echo '</pre>';