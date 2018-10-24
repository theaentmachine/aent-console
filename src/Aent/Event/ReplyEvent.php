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
     * @return bool
     */
    protected function shouldRegisterEvents(): bool
    {
        return false;
    }

    /**
     * @return void
     */
    protected function beforeExecute(): void
    {
        // Let's do nothing.
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

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        // Let's do nothing.
    }
}
