<?php

namespace TheAentMachine\Registry;


use PHPUnit\Framework\TestCase;

class RegistryClientTest extends TestCase
{
    public function testImageTags()
    {
        $client = new RegistryClient();
        $tags = $client->getImageTagsOnDockerHub('thecodingmachine/php');

        $this->assertNotEmpty($tags);
        $this->assertInternalType('string', $tags[0]);
    }
}
