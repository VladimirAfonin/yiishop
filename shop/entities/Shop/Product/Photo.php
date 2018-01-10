<?php
namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/*
 * @property integer $id
 * @property string $file
 * @property integer $sort
 */
class Photo extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'shop_photos';
    }

    /**
     * @param UploadedFile $file
     * @return Photo
     */
    public static function create(UploadedFile $file): self
    {
        $photo = new static();
        $photo->file = $file;
        return $photo;
    }

    /**
     * @param $sort
     */
    public function setSort($sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @param $id
     * @return bool
     */
    public function isIdEqualTo($id): bool
    {
        return $this->id == $id;
    }

}