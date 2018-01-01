<?php
namespace shop\forms\manage;

use yii\base\Model;
use shop\entities\Meta;

class MetaForm extends Model
{
    public $title;
    public $description;
    public $keywords;

    public function __construct(Meta $meta = null, $config = [])
    {
        if($meta) {
            $this->title = $meta->title;
            $this->description = $meta->description;
            $this->keywords = $meta->keywords;
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['title', 'description', 'keywords'], 'required'],
            [['title', 'description', 'keywords'], 'string', 'max' => 255],
        ];
    }
}