<?php

namespace TheAentMachine\Aent\Event;

use TheAentMachine\Helper\ReplyAggregator;

final class ReplyEvent extends AbstractEvent
{
    /** @var ReplyAggregator */
    private $replyAggregator;

    /**
     * ReplyEvent constructor.
     * @param ReplyAggregator $replyAggregator
     */
    public function __construct(ReplyAggregator $replyAggregator)
    {
        parent::__construct();
        $this->replyAggregator = $replyAggregator;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setHidden(true);
    }

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'REPLY';
    }

    /**
     * @param null|string $payload
     * @return null|string
     */
    protected function executeEvent(?string $payload): ?string
    {
        $this->replyAggregator->storeReply($payload ?? '');
        return null;
    }
}
