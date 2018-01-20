<?php
namespace shop\helpers;

use shop\entities\Shop\Product\Product;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ProductHelper
{
    /**
     * @return array
     */
    public static function statusList(): array
    {
        return [
            Product::STATUS_ACTIVE => 'status',
            Product::STATUS_DRAFT => 'draft'
        ];
    }

    /**
     * @param $status
     * @return string
     */
    public static function statusName($status): string
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }

    /**
     * @param $status
     * @return string
     */
    public static function statusLabel($status)
    {
        switch($status) {
            case Product::STATUS_ACTIVE:
                $class = 'label label-success';
                break;
            case Product::STATUS_DRAFT:
                $class = 'label label-default';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), ['class' => $class]);
    }


}