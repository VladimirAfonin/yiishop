<?php
namespace shop\exceptions;

use shop\entities\Shop\Brand;
use Throwable;

class UnableToRemoveBrandException extends \DomainException
{
    public $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
        parent::__construct('unable to remove brand with products ' . $brand->name);
    }
}