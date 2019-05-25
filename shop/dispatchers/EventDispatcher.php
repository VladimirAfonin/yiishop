<?php
namespace shop\dispatchers;

Interface EventDispatcher
{
    public function dispatch($event): void;
}