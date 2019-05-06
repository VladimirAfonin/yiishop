<?php
use backend\entities\RankingHelper;

/** @var $arr array */
/** @var $fieldsInDB array */
/** @var $sub_rank_r array */
/** @var $sub_rank_i array */

$arr = RankingHelper::getItemSubUrl();
$fieldsInDB = Yii::$app->params['getMapDbFields'];
$result = [];
foreach ($arr as $k => $value) {
    $sub_rank_r = RankingHelper::createDataFile("$k". "_r", RankingHelper::getItemSubUrl("$k")['r'], RankingHelper::getItemSubUrl("$k")['base']);
    $sub_rank_i = RankingHelper::createDataFile("$k". "_i", RankingHelper::getItemSubUrl("$k")['i']);
    $result[] = RankingHelper::saveToArr($sub_rank_r, $fieldsInDB[$k]);
}

echo '<pre>';
print_r($result);
echo '</pre>';