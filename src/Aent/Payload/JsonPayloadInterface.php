<?php

namespace TheAentMachine\Aent\Payload;

interface JsonPayloadInterface
{
    /**
     * @return mixed[]
     */
    public function toArray(): array;

    /**
     * @param mixed[] $assoc
     * @return mixed
     */
    public static function fromArray(array $assoc);
}
