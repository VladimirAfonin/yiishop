<?php
namespace backend\controllers\shop;

use backend\entities\WebRanking;
use backend\shop\RankingInterface;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use app\models\{
    WorldRanking,
    MbaRanking,
    EmployabilityRanking,
    BusinessRatings,
    BusinessFinanceRatings,
    BusinessManagmentRatings,
    UniversitySubjectRankings,
    QsRankings
};

ini_set('max_execution_time', 70);
ini_set('memory_limit', '256M');
class RankingController extends Controller
{

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ]
            ]
        ];
    }


    /**
     * get url for some rating for json response
     * 'r' - rating response, 'i' - indicator response
     *
     * @param $key
     * @return mixed
     */
    public function getItemUrl($key = false)
    {
        $urlsOfRanking = [
            'world_univ_ranking' => [
                'base' => 'https://www.topuniversities.com/university-rankings/world-university-rankings/2018',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/357051.txt?_=1518425751401',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/357051_indicators.txt',
            ],
            'mba_rankings' => [
                'base' => 'https://www.topuniversities.com/university-rankings/mba-rankings/global/2018',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/375517.txt?_=1518433815495',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/375517_indicators.txt'
            ],
            'employability_ranking' => [
                'base' => 'https://www.topuniversities.com/university-rankings/employability-rankings/2018',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/361806.txt?_=1518434372528',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/361806_indicators.txt',
            ],
            'business_rankings' => [
                'base' => 'https://www.topuniversities.com/university-rankings/business-masters-rankings/business-analytics/2018',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/376239.txt?_=1518448558310',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/376239_indicators.txt'
            ],
            'business_rankings_finance' => [
                'base' => 'https://www.topuniversities.com/university-rankings/business-masters-rankings/finance/2018',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/376107.txt?_=1518449769005',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/376107_indicators.txt',
            ],
            'business_rankings_management' => [
              'base' => 'https://www.topuniversities.com/university-rankings/business-masters-rankings/management/2018',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/375984.txt?_=1518450359218',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/375984_indicators.txt',
            ],
        ];

        if($key != false) {
            return $urlsOfRanking[$key];
        }
        return $urlsOfRanking;
    }

    /**
     * get url for sub rankings
     *
     * @param bool|false $key
     * @return array
     */
    public function getItemSubUrl($key = false)
    {
        $arr = [
            'sub_rank_humanities' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/arts-humanities',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346672.txt?_=1518451795328',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346672_indicators.txt',
            ],
            'sub_rank_engineering_technology' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/engineering-technology',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346673.txt?_=1518504181116',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346673_indicators.txt',
            ],
            'subject_life_science_medicine' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/life-sciences-medicine',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346674.txt?_=1518511371242',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346674_indicators.txt',
            ],
            'sub_rank_natural_science' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/natural-sciences',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346675.txt?_=1518512192890',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346675_indicators.txt'
            ],
            'sub_social_sciences' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/social-sciences-management',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346676.txt?_=1518512598165',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/346676_indicators.txt',
            ],
            'sub_accounting_finance' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/accounting-finance',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335218.txt?_=1518515153454',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335218_indicators.txt',
            ],
            'sub_agriculture_forestry' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/agriculture-forestry',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335207.txt?_=1518516859459',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335207_indicators.txt',
            ],
            'sub_anatomy_physiology' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/anatomy-physiology',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335241.txt?_=1518517176704',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335241_indicators.txt',
            ],
            'sub_anthropology' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/anthropology',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335234.txt?_=1518517280251',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335234_indicators.txt'
            ],
            'sub_archaeology' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/archaeology',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335235.txt?_=1518517680736',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335235_indicators.txt',
            ],
            'sub_architecture' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/architecture',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335226.txt?_=1518517779449',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335226_indicators.txt'
            ],
            'sub_art_design' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/art-design',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335227.txt?_=1518517905959',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335227_indicators.txt',
            ],
            'sub_biological_sciences' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/biological-sciences',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335208.txt?_=1518518004851',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335208_indicators.txt',
            ],
            'sub_business_management' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/business-management-studies',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335228.txt?_=1518518105930',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335228_indicators.txt',
            ],
            'sub_chemical_engineering' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/engineering-chemical',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335203.txt?_=1518521944036',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335203_indicators.txt',
            ],
            'sub_chemistry' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/chemistry',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335212.txt?_=1518522069847',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335212_indicators.txt',
            ],
            'sub_civil_structural_engineering' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/engineering-civil-structural',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335204.txt?_=1518522178694',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335204_indicators.txt',
            ],
            'sub_communication_media_studies' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/communication-media-studies',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335219.txt?_=1518522284879',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335219_indicators.txt',
            ],
            'sub_computer_science' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/computer-science-information-systems',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335202.txt?_=1518522381172',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335202_indicators.txt'
            ],
            'sub_dentistry' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/dentistry',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335229.txt?_=1518522488356',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335229_indicators.txt'
            ],
            'sub_development_studies' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/development-studies',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335230.txt?_=1518522588772',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335230_indicators.txt'
            ],
            'sub_earth_marine_sciences' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/earth-marine-sciences',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335213.txt?_=1518522662847',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335213_indicators.txt'
            ],
            'sub_economics_econometrics' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/economics-econometrics',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335220.txt?_=1518525697014',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335220_indicators.txt'
            ],
            'sub_electrical_electronic_engineering' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/engineering-electrical-electronic',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335205.txt?_=1518522847630',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335205_indicators.txt'
            ],
            'sub_english_language_literature' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/english-language-literature',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335196.txt?_=1518522954508',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335196_indicators.txt'
            ],
            'sub_environmental_sciences' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/environmental-studies',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335214.txt?_=1518523073106',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335214_indicators.txt'
            ],
            'sub_geography' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/geography',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335197.txt?_=1518523157442',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335197_indicators.txt'
            ],
            'sub_history' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/history',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335198.txt?_=1518523241919',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335198_indicators.txt'
            ],
            'sub_hospitality_leisure_management' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/hospitality-leisure-management',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335238.txt?_=1518523487473',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335238_indicators.txt'
            ],
            'sub_law' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/law-legal-studies',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335222.txt?_=1518523574066',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335222_indicators.txt'
            ],
            'sub_linguistics' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/linguistics',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335199.txt?_=1518523685742',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335199_indicators.txt'
            ],
            'sub_materials_science' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/materials-sciences',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335216.txt?_=1518523781013',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335216_indicators.txt'
            ],
            'sub_mathematics' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/mathematics',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335215.txt?_=1518523857478',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335215_indicators.txt'
            ],
            'sub_mechanical_aeronautical_manufacturing_engineering' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/engineering-mechanical',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335206.txt?_=1518523992575',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335206_indicators.txt'
            ],
            'sub_medicine' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/medicine',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335209.txt?_=1518524082796',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335209_indicators.txt'
            ],
            'sub_mineral_mining_engineering' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/engineering-mineral-mining',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335236.txt?_=1518524180428',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335236_indicators.txt'
            ],
            'sub_modern_languages' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/modern-languages',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335200.txt?_=1518524284817',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335200_indicators.txt'
            ],
            'sub_nursing' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/nursing',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335232.txt?_=1518524375510',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335232_indicators.txt'
            ],
            'sub_performing_arts' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/performing-arts',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335233.txt?_=1518526169230',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335233_indicators.txt'
            ],
            'sub_pharmacy_pharmacology' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/pharmacy-pharmacology',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335210.txt?_=1518526251587',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335210_indicators.txt'
            ],
            'sub_philosophy' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/philosophy',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335201.txt?_=1518526322782',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335201_indicators.txt'
            ],
            'sub_physics_astronomy' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/physics-astronomy',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335217.txt?_=1518526453172',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335217_indicators.txt'
            ],
            'sub_politics_international_studies' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/politics',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335223.txt?_=1518526539753',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335223_indicators.txt'
            ],
            'sub_psychology' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/psychology',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335211.txt?_=1518526604387',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335211_indicators.txt'
            ],
            'sub_social_policy_administration' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/social-policy-administration',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335237.txt?_=1518526692582',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335237_indicators.txt'
            ],
            'sub_sociology' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/sociology',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335224.txt?_=1518526814875',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335224_indicators.txt'
            ],
            'sub_sports_related_subjects' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/sports-related-subjects',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335239.txt?_=1518526888031',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335239_indicators.txt'
            ],
            'sub_statistics' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/statistics-operational-research',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335225.txt?_=1518526974728',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335225_indicators.txt'
            ],
            'sub_theology_divinity_religious_studies' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/theology-divinity-religious-studies',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335240.txt?_=1518527063141',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335240_indicators.txt'
            ],
            'sub_veterinary_science' => [
                'base' => 'https://www.topuniversities.com/university-rankings/university-subject-rankings/2017/veterinary-science',
                'r' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335231.txt?_=1518527135074',
                'i' => 'https://www.topuniversities.com/sites/default/files/qs-rankings-data/335231_indicators.txt'
            ]
        ];
        if($key != false) {
            return $arr[$key];
        } else {
            return $arr;
        }
    }

    /**
     * action for a most popular rating's
     *
     * @return string
     */
    public function actionRating()
    {
        // world univ rating
        $univRank = $this->createDataFile('world_univ_ranking_r', $this->getItemUrl('world_univ_ranking')['r'], $this->getItemUrl('world_univ_ranking')['base']);
        $univIndicators = $this->createDataFile('world_univ_ranking_i', $this->getItemUrl('world_univ_ranking')['i']);
        $this->saveToDb($univRank, new WorldRanking());

        // mba_rankings
        $mba_rank = $this->createDataFile('mba_rankings_r', $this->getItemUrl('mba_rankings')['r'], $this->getItemUrl('mba_rankings')['base']);
        $mba_indicator = $this->createDataFile('mba_rankings_i', $this->getItemUrl('mba_rankings')['i']);
        $this->saveToDb($mba_rank, new MbaRanking());

        // employability_ranking
        $employability_rank = $this->createDataFile('employability_ranking_r', $this->getItemUrl('employability_ranking')['r'], $this->getItemUrl('employability_ranking')['base']);
        $employability_indicator = $this->createDataFile('employability_ranking_i', $this->getItemUrl('employability_ranking')['i']);
        $this->saveToDb($employability_rank, new EmployabilityRanking());

        // business rankings
        $business_rankings = $this->createDataFile('business_rankings_r', $this->getItemUrl('business_rankings')['r'], $this->getItemUrl('business_rankings')['base']);
        $business_indicator = $this->createDataFile('business_rankings_i', $this->getItemUrl('business_rankings')['i']);
        $this->saveToDb($business_rankings, new BusinessRatings());

        // business rankings: finance
        $business_rankings_finance = $this->createDataFile('business_rankings_finance_r', $this->getItemUrl('business_rankings_finance')['r'], $this->getItemUrl('business_rankings_finance')['base']);
        $business_rankings_finance_indicator = $this->createDataFile('business_rankings_finance_i', $this->getItemUrl('business_rankings_finance')['i']);
        $this->saveToDb($business_rankings_finance, new BusinessFinanceRatings());

        // business rankings: management
        $business_rankings_management = $this->createDataFile('business_rankings_management_r', $this->getItemUrl('business_rankings_management')['r'], $this->getItemUrl('business_rankings_management')['base']);
        $business_rankings_manag_indicator = $this->createDataFile('business_rankings_management_i', $this->getItemUrl('business_rankings_management')['i']);
        $this->saveToDb($business_rankings_management, new BusinessManagmentRatings());

        return $this->render('ratings', ['result' => 'all done!']);
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
     *
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
     * get values from db:
     * key in array -> field in db
     *
     * @return array
     */
    public function getMapDbFields()
    {
        return [
            'sub_rank_humanities' => 'arts',
            'sub_rank_engineering_technology' => 'engineering_technology',
            'subject_life_science_medicine' => 'life_science_medicine',
            'sub_rank_natural_science' => 'natural_science',
            'sub_social_sciences' => 'social_sciences',
            'sub_accounting_finance' => 'accounting_finance',
            'sub_agriculture_forestry' => 'agriculture_forestry',
            'sub_anatomy_physiology' => 'anatomy_physiology',
            'sub_anthropology' => 'anthropology',
            'sub_archaeology' => 'archaeology',
            'sub_architecture' => 'architecture',
            'sub_art_design' => 'art_design',
            'sub_biological_sciences' => 'biological_sciences',
            'sub_business_management' => 'business_management',
            'sub_chemical_engineering' => 'chemical_engineering',
            'sub_chemistry' => 'chemistry',
            'sub_civil_structural_engineering' => 'civil_structural_engineering',
            'sub_communication_media_studies' => 'communication_media_studies',
            'sub_computer_science' => 'computer_science',
            'sub_dentistry' => 'dentistry',
            'sub_development_studies' => 'development_studies',
            'sub_earth_marine_sciences' => 'earth_marine_sciences',
            'sub_economics_econometrics' => 'economics_econometrics',
            'sub_electrical_electronic_engineering' => 'electrical_electronic_engineering',
            'sub_english_language_literature' => 'english_language_literature',
            'sub_environmental_sciences' => 'environmental_sciences',
            'sub_geography' => 'geography',
            'sub_history' => 'history',
            'sub_hospitality_leisure_management' => 'hospitality_leisure_management',
            'sub_law' => 'law',
            'sub_linguistics' => 'linguistics',
            'sub_materials_science' => 'materials_science',
            'sub_mathematics' => 'mathematics',
            'sub_mechanical_aeronautical_manufacturing_engineering' => 'mechanical_aeronautical_manufacturing_engineering',
            'sub_medicine' => 'medicine',
            'sub_mineral_mining_engineering' => 'mineral_mining_engineering',
            'sub_modern_languages' => 'modern_languages',
            'sub_nursing' => 'nursing',
            'sub_performing_arts' => 'performing_arts',
            'sub_pharmacy_pharmacology' => 'pharmacy_pharmacology',
            'sub_philosophy' => 'philosophy',
            'sub_physics_astronomy' => 'physics_astronomy',
            'sub_politics_international_studies' => 'politics_international_studies',
            'sub_psychology' => 'psychology',
            'sub_social_policy_administration' => 'social_policy_administration',
            'sub_sociology' => 'sociology',
            'sub_sports_related_subjects' => 'sports_related_subjects',
            'sub_statistics' => 'statistics',
            'sub_theology_divinity_religious_studies' => 'theology_divinity_religious_studies',
            'sub_veterinary_science' => 'veterinary_science'
        ];
    }

    /**
     * all sub rankings
     *
     * @return mixed
     */
    public function runMechanism()
    {
        $arr = $this->getItemSubUrl();
        $fieldsInDB = $this->getMapDbFields();
        foreach ($arr as $k => $value) {
            $sub_rank_r = $this->createDataFile("$k". "_r", $this->getItemSubUrl("$k")['r'], $this->getItemSubUrl("$k")['base']);
            $sub_rank_i = $this->createDataFile("$k". "_i", $this->getItemSubUrl("$k")['i']);
            $this->updateToDbWithSubject($sub_rank_r, $fieldsInDB[$k]);
        }
        return $sub_rank_r; // todo: delete return
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
     * @param RankingInterface $model
     */
    public function saveToDb($data, RankingInterface $model)
    {
        $year = $data['year'];
        $data = $data['data'];

        foreach ($data as $item) {
            $m = new $model();
            $m->nid = $item['nid'];
            $m->name = $item['title'];
            $m->rank_display = $item['rank_display'];
            $m->country = $item['country'];
            $m->stars = $item['stars'];
            $m->region = $item['region'];
            $m->score = $item['score'];
            $m->year = $year;
            $m->save(false);
        }
    }

    /**
     * get year from url
     *
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
     *
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