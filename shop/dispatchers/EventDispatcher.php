<?php
namespace shop\dispatchers;

Interface EventDispatcher
{
    public function dispatch($event): void;
    public function dispatchAll(array $events): void;
}