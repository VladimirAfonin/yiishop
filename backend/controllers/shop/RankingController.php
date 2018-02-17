<?php
namespace backend\controllers\shop;

use backend\entities\WebRanking;
use yii\web\Controller;
use Yii;
use app\models\{MainRankings, UniversitySubjectRankings, QsRankings};

ini_set('max_execution_time', 70);
ini_set('memory_limit', '256M');
class RankingController extends Controller
{
    /**
     * get url for some rating for json response
     * 'r' - rating response, 'i' - indicator response
     * @param $key
     * @return mixed
     */
    public function getItemUrl($key = false)
    {
        $urlsOfRanking = Yii::$app->params['urlsOfRanking'];
        if($key != false) {
            return $urlsOfRanking[$key];
        }
        return $urlsOfRanking;
    }

    /**
     * get url for sub rankings
     * @param bool|false $key
     * @return array
     */
    public function getItemSubUrl($key = false)
    {
        $arr = Yii::$app->params['urlsOfSubjectRanking'];
        if($key != false) {
            return $arr[$key];
        } else {
            return $arr;
        }
    }

    /**
     * action for a most popular rating's
     * @return string
     */
    public function actionRating()
    {
        $arrOfMainRatings = Yii::$app->params['dataMainRankings'];
        $this->runGetMainRatings($arrOfMainRatings);
        return $this->render('ratings', ['result' => 'all done!']);
    }

    /**
     * get main rating
     * @param array $arrRankings
     */
    public function runGetMainRatings(array $arrRankings = [])
    {
        foreach ($arrRankings as $k => $value) {
            $univRank = $this->createDataFile("$k"."_r", $this->getItemUrl("$k")['r'], $this->getItemUrl("$k")['base']);
            $univIndicators = $this->createDataFile("$k"."_i", $this->getItemUrl($k)['i']);
            $this->saveToDb($univRank, $value);
            sleep(1);
        }
    }

    /**
     * action for sub ratings( > 50)
     */
    public function actionSubRating()
    {
        $this->runMechanism();
    }

    /**
     * get info for one item
     * @return string
     */
    public function actionData()
    {
        $m = UniversitySubjectRankings::find()->all();
        foreach($m as $item) {
            $university = QsRankings::findOne(['nid' => $item->nid]);
            if(!$university) {
                $node = $item->nid;
                $url = "https://www.topuniversities.com/node/$node";
                $html = WebRanking::getRequestToUrl($url);
                $resultData = WebRanking::generateResultData($html, $url);
                $obj = new QsRankings();
                $obj->name = $item->name;
                $obj->nid = $item->nid;
                $obj->data = json_encode($resultData);
                $obj->save();
                sleep(1);
            }
        }
        return $this->render('data', ['result' => 'all done!']);
    }

    /**
     * all sub rankings
     * @return mixed
     */
    public function runMechanism()
    {
        $arr = $this->getItemSubUrl();
        $fieldsInDB = Yii::$app->params['getMapDbFields'];
        foreach ($arr as $k => $value) {
            $sub_rank_r = $this->createDataFile("$k". "_r", $this->getItemSubUrl("$k")['r'], $this->getItemSubUrl("$k")['base']);
            $sub_rank_i = $this->createDataFile("$k". "_i", $this->getItemSubUrl("$k")['i']);
            $this->updateToDbWithSubject($sub_rank_r, $fieldsInDB[$k]);
        }
    }

    /**
     * @param $data
     * @param $year
     * @return \UniversitySubjectRankings
     */
    public function addItemsToDb($data, $year)
    {
        $m = new UniversitySubjectRankings();
        $m->nid = $data['nid'];
        $m->name = $data['title'];
        $m->country = $data['country'];
        $m->core_id = $data['core_id'];
        $m->year = $year;
        $m->save(false);
        return $m;
    }

    /**
     * @param $data
     * @param $field
     */
    public function updateToDbWithSubject($data, $field)
    {
        $year = $data['year'];
        $finalData = $data['data'];
        foreach ($finalData as $item) {
            $obj = UniversitySubjectRankings::find()->where(['core_id' => $item['core_id']])->one();
            if($obj) {
                $obj->$field = $item['rank_display'];
                $obj->save(false);
            } else {
                $obj = $this->addItemsToDb($item, $year);
                $obj->$field = $item['rank_display'];
                $obj->save(false);
            }
        }
    }

    /**
     * save info to db
     *
     * @param $data
     * @param string $rankingName
     */
    public function saveToDb($data, $rankingName)
    {
        $year = $data['year'];
        $data = $data['data'];
        foreach ($data as $item) {
            $ranking = MainRankings::findOne(['nid' => $item['nid']]);
            if ( ! $ranking) {
                $m = new MainRankings();
                $m->nid = $item['nid'];
                $m->name = $item['title'];
                $m->$rankingName = $item['rank_display'];
                $m->year = $year;
                $m->save(false);
            } else {
                $ranking->$rankingName = $item['rank_display'];
                $ranking->save(false);
            }
        }
    }

    /**
     * get year from url
     * @param $url
     * @return null
     */
    public function getYear($url)
    {
        preg_match('#[0-9]{4}?#ui', $url, $matches);
        if ( ! empty($matches[0])) {
            $year = $matches[0];
        }
        return $year ?? null;
    }

    /**
     * create json file with all data
     * @param $name
     * @param $url
     * @param string $baseUrl
     * @return mixed
     */
    public function createDataFile($name, $url, $baseUrl = '')
    {
        $year = $this->getYear($baseUrl);
        if(!file_exists("rankings/$name.json")) {
            $univIndicators = WebRanking::getRequestToUrl($url);
            $fp = fopen("rankings/$name.json", "w");
            fwrite($fp, $univIndicators);
            fclose($fp);
        }
        $univIndicators = json_decode(file_get_contents("rankings/$name.json"), true);
        $univIndicators['year'] = $year;
        return $univIndicators;
    }
}