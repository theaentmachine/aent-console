<?php

namespace TheAentMachine\Registry;


use PHPUnit\Framework\TestCase;

class TagsAnalyzerTest extends TestCase
{

    public function testFilterBestTags()
    {
        $tagsAnalyzer = new TagsAnalyzer();
        $results = $tagsAnalyzer->filterBestTags(['1', '1.0', '1.1', '1.0.4', '1.0.5', '1.1.0', '1.1.1', 'foobar', '1.2-alpha']);

        $this->assertEquals([
            '1', '1.1', '1.0', '1.1.1', '1.0.5'
        ], $results);
    }

    public function testNonWellBalancedTryy()
    {
        $tagsAnalyzer = new TagsAnalyzer();
        $results = $tagsAnalyzer->filterBestTags(['5', '4.6']);

        $this->assertEquals([
            '5', '4.6'
        ], $results);
    }
}
