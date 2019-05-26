<?php
namespace shop\dispatchers\traits;

trait EventTrait
{
    private $_events = [];

    /**
     * @param $event
     */
    protected function recordEvent($event): void
    {
        $this->_events[] = $event;
    }

    /**
     * @return array
     */
    public function releaseEvents()
    {
        $events = $this->_events;
        $this->_events = [];
        return $events;
    }
}