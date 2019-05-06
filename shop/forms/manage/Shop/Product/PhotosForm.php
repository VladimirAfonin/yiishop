<?php
namespace shop\forms\manage\Shop\Product;

use yii\base\Model;
use yii\web\UploadedFile;

class PhotosForm extends Model
{
    public $files;

    public function rules(): array
    {
        return [
            ['files', 'each', 'rule' => ['image']], // ['files', 'image'] -> one image
        ];
    }

    public function beforeValidate(): bool
    {
        if(parent::beforeValidate()) {
            $this->files = UploadedFile::getInstance($this, 'files');
            return true;
        }
        return false;
    }
}