<?php

use yii\db\Migration;

/**
 * Handles the creation of table `university_subject_rankings`.
 */
class m180213_051859_create_university_subject_rankings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('university_subject_rankings', [
            'id' => $this->primaryKey(),
            'nid' => $this->integer(),
            'core_id' => $this->integer(),
            'name' => $this->text(),
            'country' => $this->text(),
            'year' => $this->text(),
            'arts' => $this->text(),
            'engineering_technology' => $this->text(),
            'life_science_medicine' => $this->text(),
            'natural_science' => $this->text(),
            'social_sciences' => $this->text(),
            'accounting_finance' => $this->text(),
            'agriculture_forestry' => $this->text(),
            'anatomy_physiology' => $this->text(),
            'anthropology' => $this->text(),
            'archaeology' => $this->text(),
            'architecture' => $this->text(),
            'art & design' => $this->text(),
            'biological_sciences' => $this->text(),
            'business_management' => $this->text(),
            'chemical_engineering' => $this->text(),
            'chemistry' => $this->text(),
            'civil_structural_engineering' => $this->text(),
            'communication_media_studies' => $this->text(),
            'computer_science' => $this->text(),
            'dentistry' => $this->text(),
            'development_studies' => $this->text(),
            'earth_marine_sciences' => $this->text(),
            'economics_econometrics' => $this->text(),
            'electrical_electronic_engineering' => $this->text(),
            'english_language_literature' => $this->text(),
            'environmental_sciences' => $this->text(),
            'geography' => $this->text(),
            'history' => $this->text(),
            'hospitality_leisure_management' => $this->text(),
            'law' => $this->text(),
            'linguistics' => $this->text(),
            'materials_science' => $this->text(),
            'mathematics' => $this->text(),
            'mechanical_aeronautical_manufacturing_engineering' => $this->text(),
            'medicine' => $this->text(),
            'mineral_mining_engineering' => $this->text(),
            'modern_languages' => $this->text(),
            'nursing' => $this->text(),
            'performing_arts' => $this->text(),
            'pharmacy_pharmacology' => $this->text(),
            'philosophy' => $this->text(),
            'physics_astronomy' => $this->text(),
            'politics_international_studies' => $this->text(),
            'psychology' => $this->text(),
            'social_policy_administration' => $this->text(),
            'sociology' => $this->text(),
            'sports_related_subjects' => $this->text(),
            'statistics' => $this->text(),
            'theology_divinity_religious_studies' => $this->text(),
            'veterinary_science' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('university_subject_rankings');
    }
}
