<?php
namespace shop\forms\manage\Shop\Product;

use yii\base\Model;
use shop\entities\Shop\Product\Review;

class ReviewEditForm extends Model
{
    public $vote;
    public $text;

    public function __construct(Review $review, array $config = [])
    {
        $this->vote = $review->vote;
        $this->text  = $review->text;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['vote', 'text'],'required'],
            [['vote'], 'in', 'range' => [1,2,3,4,5]],
            ['text', 'string']
        ];
    }
}