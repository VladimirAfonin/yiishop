<?php

namespace shop\entities;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $content
 * @property int $price
 * @property int $active
 *
 * @property AttributeValue $attributeValue
 * @property Category $category
 * @property ProductTag[] $productTags
 * @property TestTags[] $tags
 * @property integer $created_at
 * @property integer $updated_at
 */
class Product extends \yii\db\ActiveRecord
{
    private $_tagsArray;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    public function behaviors()
    {
       return [
           TimestampBehavior::class,
       ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'price', 'active'], 'integer'],
            [['name', 'price'], 'required'],
            [['content'], 'string'],
            [['tagsArray'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category',
            'name' => 'Name',
            'content' => 'Content',
            'price' => 'Price',
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValue()
    {
        return $this->hasOne(AttributeValue::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductTags()
    {
        return $this->hasMany(ProductTag::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(TestTags::className(), ['id' => 'tag_id'])->viaTable('product_tag', ['product_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \shop\entities\query\ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \shop\entities\query\ProductQuery(get_called_class());
    }

    public function getTagsArray()
    {
        if ($this->_tagsArray === null) {
            $this->_tagsArray = $this->getTags()->select('id')->column();
        }
        return $this->_tagsArray;
    }

    public function setTagsArray($value)
    {
        $this->_tagsArray = (array)$value;
    }

    private function updateTags()
    {
        $currentTags = $this->getTags()->select('id')->column();
        $newTags = $this->getTagsArray();

        foreach (array_filter(array_diff($newTags, $currentTags)) as $tagId) {
            if($tag = TestTags::findOne($tagId)) $this->link('tags', $tag);
        }

        foreach (array_filter(array_diff($currentTags, $newTags)) as $tagId) {
            if($tag = TestTags::findOne($tagId)) $this->unlink('tags', $tag, true); // if false then в связующей таблице просто обнулится product_id, tag_id
        }
    }

    public function updateTagsFast()
    {
        $currentTags = $this->getTags()->select('id')->column();
        $newTags = $this->getTagsArray();

        $toInsert = [];
        foreach (array_filter(array_diff($newTags, $currentTags)) as $tagId) {
           $toInsert[] = ['product_id' => $this->id, 'tag_id' => $tagId];
        }
        if($toInsert) ProductTag::getDb()->createCommand()->batchInsert(ProductTag::tableName(), ['product_id', 'tag_id'], $toInsert)->execute();
        if($toRemove = array_filter(array_diff($currentTags, $newTags))) ProductTag::deleteAll(['product_id' => $this->id, 'tag_id' => $toRemove]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->updateTags();
        parent::afterSave($insert, $changedAttributes);
    }
}
