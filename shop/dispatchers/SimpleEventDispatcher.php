<?php
namespace shop\dispatchers;

use yii\di\Container;

class SimpleEventDispatcher implements EventDispatcher
{
    private $listeners;
    private $container;

    public function __construct(Container $container, array $listeners)
    {
        $this->listeners = $listeners;
    }

    public function dispatch($event): void
    {
        $eventName = get_class($event);
        if (array_key_exists($eventName, $this->listeners)) {
            foreach ($this->listeners[$eventName] as $listenerClass) {
                $callback = $this->resolveListener($listenerClass);
                $callback($event);
            }
        }
    }

    /**
     * @param $listenerClass
     * @return callable
     */
    private function resolveListener($listenerClass): callable
    {
        return [$this->container->get($listenerClass), 'handle'];
    }
}
