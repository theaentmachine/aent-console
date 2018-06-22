<?php

namespace TheAentMachine;

use PHPUnit\Framework\TestCase;

class ReplyAggregatorTest extends TestCase
{
    public function testReplies(): void
    {
        $replyAggregator = new ReplyAggregator();
        $replyAggregator->clear();
        $replyAggregator->storeReply('foo');
        $replyAggregator->storeReply('bar');
        $this->assertEquals(['foo', 'bar'], $replyAggregator->getReplies());
        $replyAggregator->clear();
        $this->assertEquals([], $replyAggregator->getReplies());
    }
}
