<?php
namespace shop\validators;

use yii\validators\RegularExpressionValidator;

class SlugValidator extends RegularExpressionValidator
{
    public $pattern = '#^[a-z0-9_-]*$s';
    public $message = 'не верный формат "slug".';
}