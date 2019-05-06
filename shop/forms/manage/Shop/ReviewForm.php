<?php
namespace shop\forms\manage\Shop;

use yii\base\Model;

class ReviewForm extends Model
{
    public $vote;
    public $text;

    public function rules()
    {
        return [
            [['vote', 'text'],'required'],
            [['vote'], 'in', 'range' => array_keys($this->voteList())],
            [['text'], 'string'],
        ];
    }

    public function voteList()
    {
        return [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];
    }

}