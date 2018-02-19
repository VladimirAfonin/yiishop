<?php
use backend\entities\RankingHelper;

/** @var $arr array */
/** @var $fieldsInDB array */
/** @var $sub_rank_r array */
/** @var $sub_rank_i array */

$arr = RankingHelper::getItemSubUrl();
$fieldsInDB = Yii::$app->params['getMapDbFields'];
foreach ($arr as $k => $value) {
    $sub_rank_r = RankingHelper::createDataFile("$k". "_r", RankingHelper::getItemSubUrl("$k")['r'], RankingHelper::getItemSubUrl("$k")['base']);
    $sub_rank_i = RankingHelper::createDataFile("$k". "_i", RankingHelper::getItemSubUrl("$k")['i']);
    RankingHelper::updateToDbWithSubject($sub_rank_r, $fieldsInDB[$k]);
}
echo "<div class='alert alert-success' role='alert'>all data save in 'university_subject_rankings' table!</div>";