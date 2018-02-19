<?php
use backend\entities\RankingHelper;
use app\models\UniversitySubjectRankings;
use app\models\QsRankings;
use backend\entities\WebPage;

/** @var $sub_rank_i array */
/** @var $html string */
/** @var $resultData array */
/** @var $m app\models\UniversitySubjectRankings */
/** @var $university app\models\QsRankings */
/** @var $obj app\models\QsRankings */

$m = UniversitySubjectRankings::find()->all();
foreach($m as $item) {
    $university = QsRankings::findOne(['nid' => $item->nid]);
    if(!$university) {
        $node = $item->nid;
        $url = "https://www.topuniversities.com/node/$node";
        $html = WebPage::getRequestToUrl($url);
        $resultData = RankingHelper::generateResultData($html, $url);
        $obj = new QsRankings();
        $obj->name = $item->name;
        $obj->nid = $item->nid;
        $obj->data = json_encode($resultData);
        $obj->save();
        sleep(1);
    }
}
echo "<div class='alert alert-success' role='alert'>all data save in 'qs_rankings' table!</div>";
