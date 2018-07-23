<?php


namespace TheAentMachine\Command;

use TheAentMachine\ReplyAggregator;

/**
 * A special command that is used to receive replies from a dispatch
 */
class ReplyCommand extends EventCommand
{
    private $replyAggregator;

    public function __construct(ReplyAggregator $replyAggregator)
    {
        parent::__construct();
        $this->replyAggregator = $replyAggregator;
    }

    protected function configure()
    {
        parent::configure();
        $this->setHidden(true);
    }

    protected function getEventName(): string
    {
        return 'REPLY';
    }

    protected function executeEvent(?string $payload): ?string
    {
        $this->replyAggregator->storeReply($payload);
        return null;
    }
}
