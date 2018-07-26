<?php


namespace TheAentMachine\Command;

use TheAentMachine\Aenthill\CommonEvents;
use TheAentMachine\Helper\ReplyAggregator;

/**
 * A special command that is used to receive replies from a dispatch
 */
final class ReplyCommand extends AbstractEventCommand
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
        return CommonEvents::REPLY_EVENT;
    }

    protected function executeEvent(?string $payload): ?string
    {
        $this->replyAggregator->storeReply($payload);
        return null;
    }
}
