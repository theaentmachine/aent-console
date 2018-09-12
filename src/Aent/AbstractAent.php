<?php


namespace TheAentMachine\Aent;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use TheAentMachine\Aent\Event\ReplyEvent;
use TheAentMachine\Aent\Event\VoidEvent;
use TheAentMachine\Helper\ReplyAggregator;

abstract class AbstractAent extends Application
{
    /** @var VoidEvent */
    private $voidEvent;

    public function __construct()
    {
        parent::__construct();
        $this->voidEvent = new VoidEvent();
        $this->add($this->voidEvent);
        $this->add(new ReplyEvent(new ReplyAggregator()));
    }

    /**
     * Overrides the Symfony "find" method to return a default command if no command is found.
     */
    public function find($name)
    {
        try {
            if (!$this->has($name)) {
                return $this->voidEvent;
            }
            return parent::find($name);
        } catch (CommandNotFoundException $e) {
            return $this->voidEvent;
        }
    }
}
