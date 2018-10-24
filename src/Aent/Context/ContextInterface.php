<?php

namespace TheAentMachine\Aent\Context;

interface ContextInterface
{
    /**
     * @return void
     */
    public function toMetadata(): void;

    /**
     * @return mixed
     */
    public static function fromMetadata();
}
