<?php

namespace TheAentMachine\Registry;

use Safe\Exceptions\ArrayException;
use Safe\Exceptions\StringsException;
use function Safe\preg_match;
use function Safe\usort;
use function Safe\substr;

final class TagsAnalyzer
{

    /**
     * Returns a sorted array of the most interesting tags to use out of the list
     *
     * For instance:
     *  1, 1.0, 1.1, 1.0.4, 1.0.5, 1.1.0, 1.1.1 => 1, 1.0, 1.1, 1.0.5, 1.1.1
     *
     * @param string[] $tags
     * @return string[]
     * @throws ArrayException
     * @throws StringsException
     */
    public function filterBestTags(array $tags): array
    {
        // filter numeric versions only
        $versions = \array_filter($tags, function (string $tag) {
            return preg_match('/^\d+(\.\d+)*$/', $tag);
        });

        // Let's build a tree of versions.
        $tree = [];

        foreach ($versions as $version) {
            $numbers = \explode('.', $version);
            $base = &$tree;
            foreach ($numbers as $number) {
                if (!isset($base[$number])) {
                    $base[$number] = [];
                }
                $base = &$base[$number];
            }
        }

        // List of tags with the maximum precision and the highest point value.
        $maxLeafs = $this->getMaxLeafs($tree);

        $versionsByKey = [];

        foreach ($maxLeafs as $tag) {
            while (true) {
                if (\in_array($tag, $versions, true)) {
                    $versionsByKey[$tag] = true;
                }
                $tag = $this->shortenTag($tag);
                if ($tag === null) {
                    break;
                }
            }
        }

        $interestingVersion = \array_keys($versionsByKey);

        usort($interestingVersion, [$this, 'compareVersion']);

        return $interestingVersion;
    }

    /**
     * @param array[] $subtree
     * @return string[]
     */
    private function getMaxLeafs(array $subtree): array
    {
        if ($this->isLeaf($subtree)) {
            if (empty($subtree)) {
                return ['0'];
            }
            return [max(\array_keys($subtree))];
        }
        $arr = [];
        foreach ($subtree as $key => $subsubtree) {
            $values = $this->getMaxLeafs($subsubtree);
            foreach ($values as $val) {
                $arr[] = $key . '.' . $val;
            }
        }
        return $arr;
    }

    /**
     * @param array[] $branch
     * @return bool
     */
    private function isLeaf(array $branch): bool
    {
        foreach ($branch as $value) {
            if (!empty($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Removes the last digit of a tag. Returns null if there is nothing to remove.
     * @throws StringsException
     */
    private function shortenTag(string $tag): ?string
    {
        $lastPos = \strrpos($tag, '.');
        if ($lastPos === false) {
            return null;
        }
        return substr($tag, 0, $lastPos);
    }

    private function compareVersion(string $v1, string $v2): int
    {
        $level1 = \substr_count($v1, '.');
        $level2 = \substr_count($v2, '.');
        if ($level1 !== $level2) {
            return $level1 <=> $level2;
        }
        $array1 = \explode('.', $v1);
        $array2 = \explode('.', $v2);
        for ($i = 0; $i <= $level1; $i++) {
            if ($array1[$i] !== $array2[$i]) {
                return $array2[$i] <=> $array1[$i];
            }
        }
        return 0;
    }
}
