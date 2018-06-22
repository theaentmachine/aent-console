<?php


namespace TheAentMachine;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        return 'reply';
    }

    protected function executeEvent(?string $payload): ?string
    {
        $this->replyAggregator->storeReply($payload);
        return null;
    }
}
