<?php
namespace shop\entities\Shop\Product;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $created_at
 * @property int $user_id
 * @property int $vote
 * @property string $text
 * @property bool $active
 *
 * Class Review
 * @package shop\entities\Shop\Product
 */
class Review extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'shop_reviews';
    }
    /**
     * @param int $userId
     * @param int $vote
     * @param string $text
     * @return Review
     */
    public static function create(int $userId, int $vote, string $text): self
    {
        $review = new static();
        $review->user_id = $userId;
        $review->vote = $vote;
        $review->text = $text;
        $review->created_at = time();
        $review->active = false;
        return $review;
    }

    /**
     * @param $vote
     * @param $text
     */
    public function edit($vote, $text): void
    {
        $this->vote = $vote;
        $this->text = $text;
    }

    /**
     *
     */
    public function active(): void
    {
        $this->active = true;
    }

    /**
     * to draft
     */
    public function draft(): void
    {
        $this->active = false;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active === true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function isIdEqualTo($id): bool
    {
        return $this->id == $id;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->vote;
    }
}