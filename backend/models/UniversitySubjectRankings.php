<?php

namespace app\models;

use backend\shop\RankingInterface;
use Yii;

/**
 * This is the model class for table "university_subject_rankings".
 *
 * @property integer $id
 * @property integer $nid
 * @property integer $core_id
 * @property string $name
 * @property integer $rank_display
 * @property string $country
 * @property integer $stars
 * @property string $region
 * @property string $year
 * @property string $score
 * @property string $arts
 * @property string $engineering_technology
 * @property string $life_science_medicine
 * @property string $natural_science
 * @property string $social_sciences
 * @property string $accounting_finance
 * @property string $agriculture_forestry
 * @property string $anatomy_physiology
 * @property string $anthropology
 * @property string $archaeology
 * @property string $architecture
 * @property string $art & design
 * @property string $biological_sciences
 * @property string $business_management
 * @property string $chemical_engineering
 * @property string $chemistry
 * @property string $civil_structural_engineering
 * @property string $communication_media_studies
 * @property string $computer_science
 * @property string $dentistry
 * @property string $development_studies
 * @property string $earth_marine_sciences
 * @property string $economics_econometrics
 * @property string $electrical_electronic_engineering
 * @property string $english_language_literature
 * @property string $environmental_sciences
 * @property string $geography
 * @property string $history
 * @property string $hospitality_leisure_management
 * @property string $law
 * @property string $linguistics
 * @property string $materials_science
 * @property string $mathematics
 * @property string $mechanical_aeronautical_manufacturing_engineering
 * @property string $medicine
 * @property string $mineral_mining_engineering
 * @property string $modern_languages
 * @property string $nursing
 * @property string $performing_arts
 * @property string $pharmacy_pharmacology
 * @property string $philosophy
 * @property string $physics_astronomy
 * @property string $politics_international_studies
 * @property string $psychology
 * @property string $social_policy_administration
 * @property string $sociology
 * @property string $sports_related_subjects
 * @property string $statistics
 * @property string $theology_divinity_religious_studies
 * @property string $veterinary_science
 */
class UniversitySubjectRankings extends \yii\db\ActiveRecord implements RankingInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'university_subject_rankings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nid', 'core_id', 'rank_display', 'stars'], 'integer'],
            [['name', 'country', 'region', 'year', 'score', 'arts', 'engineering_technology', 'life_science_medicine', 'natural_science', 'social_sciences', 'accounting_finance', 'agriculture_forestry', 'anatomy_physiology', 'anthropology', 'archaeology', 'architecture', 'art & design', 'biological_sciences', 'business_management', 'chemical_engineering', 'chemistry', 'civil_structural_engineering', 'communication_media_studies', 'computer_science', 'dentistry', 'development_studies', 'earth_marine_sciences', 'economics_econometrics', 'electrical_electronic_engineering', 'english_language_literature', 'environmental_sciences', 'geography', 'history', 'hospitality_leisure_management', 'law', 'linguistics', 'materials_science', 'mathematics', 'mechanical_aeronautical_manufacturing_engineering', 'medicine', 'mineral_mining_engineering', 'modern_languages', 'nursing', 'performing_arts', 'pharmacy_pharmacology', 'philosophy', 'physics_astronomy', 'politics_international_studies', 'psychology', 'social_policy_administration', 'sociology', 'sports_related_subjects', 'statistics', 'theology_divinity_religious_studies', 'veterinary_science'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nid' => 'Nid',
            'core_id' => 'Core ID',
            'name' => 'Name',
            'rank_display' => 'Rank Display',
            'country' => 'Country',
            'stars' => 'Stars',
            'region' => 'Region',
            'year' => 'Year',
            'score' => 'Score',
            'arts' => 'Arts',
            'engineering_technology' => 'Engineering Technology',
            'life_science_medicine' => 'Life Science Medicine',
            'natural_science' => 'Natural Science',
            'social_sciences' => 'Social Sciences',
            'accounting_finance' => 'Accounting Finance',
            'agriculture_forestry' => 'Agriculture Forestry',
            'anatomy_physiology' => 'Anatomy Physiology',
            'anthropology' => 'Anthropology',
            'archaeology' => 'Archaeology',
            'architecture' => 'Architecture',
            'art & design' => 'Art & Design',
            'biological_sciences' => 'Biological Sciences',
            'business_management' => 'Business Management',
            'chemical_engineering' => 'Chemical Engineering',
            'chemistry' => 'Chemistry',
            'civil_structural_engineering' => 'Civil Structural Engineering',
            'communication_media_studies' => 'Communication Media Studies',
            'computer_science' => 'Computer Science',
            'dentistry' => 'Dentistry',
            'development_studies' => 'Development Studies',
            'earth_marine_sciences' => 'Earth Marine Sciences',
            'economics_econometrics' => 'Economics Econometrics',
            'electrical_electronic_engineering' => 'Electrical Electronic Engineering',
            'english_language_literature' => 'English Language Literature',
            'environmental_sciences' => 'Environmental Sciences',
            'geography' => 'Geography',
            'history' => 'History',
            'hospitality_leisure_management' => 'Hospitality Leisure Management',
            'law' => 'Law',
            'linguistics' => 'Linguistics',
            'materials_science' => 'Materials Science',
            'mathematics' => 'Mathematics',
            'mechanical_aeronautical_manufacturing_engineering' => 'Mechanical Aeronautical Manufacturing Engineering',
            'medicine' => 'Medicine',
            'mineral_mining_engineering' => 'Mineral Mining Engineering',
            'modern_languages' => 'Modern Languages',
            'nursing' => 'Nursing',
            'performing_arts' => 'Performing Arts',
            'pharmacy_pharmacology' => 'Pharmacy Pharmacology',
            'philosophy' => 'Philosophy',
            'physics_astronomy' => 'Physics Astronomy',
            'politics_international_studies' => 'Politics International Studies',
            'psychology' => 'Psychology',
            'social_policy_administration' => 'Social Policy Administration',
            'sociology' => 'Sociology',
            'sports_related_subjects' => 'Sports Related Subjects',
            'statistics' => 'Statistics',
            'theology_divinity_religious_studies' => 'Theology Divinity Religious Studies',
            'veterinary_science' => 'Veterinary Science',
        ];
    }
}
