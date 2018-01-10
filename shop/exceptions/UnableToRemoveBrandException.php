<?php
namespace shop\exceptions;

use shop\entities\Shop\Brand;

class UnableToRemoveBrandException extends \DomainException
{
    public $brands;

    public function __construct(Brand $brand)
    {
        $this->brands = $brand;
        parent::__construct('unable to remove brand with products ' . $brand->name);
    }
}