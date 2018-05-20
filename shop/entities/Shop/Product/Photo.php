<?php
namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yiidreamteam\upload\ImageUploadBehavior;

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

    public function behaviors()
    {
        return [
            [
                'class' => ImageUploadBehavior::className(),
                'attribute'=> 'file',
                'createThumbsOnRequest' => true,
                'filePath' => '@backend/web/upload/origin/products/[[attribute_product_id]]/[[id]].[[extension]]',
                'fileUrl' => '@web/upload/origin/products/[[attribute_product_id]]/[[id]].[[extension]]',
                'thumbPath' => '@backend/web/upload/cache/products/[[attribute_product_id]]/[[profile]]_[[id]].[[extension]]',
                'thumbUrl' => '@web/upload/cache/products/[[attribute_product_id]]/[[profile]]_[[id]].[[extension]]',
                'thumbs' => [
                    'admin' => ['width' => 100, 'height' => 70],
                    'thumb'=> ['width' => 640, 'height' => 480],
                    'catalog_list' => ['width' => 228, 'height' => 228]
                ]
            ]
        ];
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
     * @param int $sort
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