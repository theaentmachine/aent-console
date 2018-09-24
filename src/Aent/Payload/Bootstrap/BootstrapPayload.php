<?php

namespace TheAentMachine\Aent\Payload\Bootstrap;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;
use TheAentMachine\Aent\Registry\AentItemRegistry;

final class BootstrapPayload implements JsonPayloadInterface
{
    /** @var AentItemRegistry */
    private $CIAent;

    /** @var array<string,string> */
    private $CIMetadata;

    /**
     * BootstrapPayload constructor.
     * @param AentItemRegistry $CIAent
     * @param array<string,string> $CIMetadata
     */
    public function __construct(AentItemRegistry $CIAent, array $CIMetadata)
    {
        $this->CIAent = $CIAent;
        $this->CIMetadata = $CIMetadata;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'CI_AENT' => $this->CIAent->toArray(),
            'CI_METADATA' => $this->CIMetadata,
        ];
    }

    /**
     * @param mixed[] $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        $CIAent = $assoc['CI_AENT'];
        $CIMetadata = $assoc['CI_METADATA'];
        return new self($CIAent, $CIMetadata);
    }

    /**
     * @return AentItemRegistry
     */
    public function getCIAent(): AentItemRegistry
    {
        return $this->CIAent;
    }

    /**
     * @return array<string,string>
     */
    public function getCIMetadata(): array
    {
        return $this->CIMetadata;
    }
}
