<?php

namespace TheAentMachine\Aent\Registry;

abstract class AbstractRegistry
{
    /**
     * @return array<string,string>
     */
    abstract public static function getList(): array;
}
