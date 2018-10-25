<?php

namespace TheAentMachine\Aent\Payload\CI;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class KubernetesReplyDeployJobPayload implements JsonPayloadInterface
{
    /** @var bool */
    private $withManyEnvironments;

    /**
     * KubernetesReplyDeployJobPayload constructor.
     * @param bool $withManyEnvironments
     */
    public function __construct(bool $withManyEnvironments)
    {
        $this->withManyEnvironments = $withManyEnvironments;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'WITH_MANY_ENVIRONMENTS' => $this->withManyEnvironments ? '1' : '0',
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        return new self($assoc['WITH_MANY_ENVIRONMENTS'] === '1');
    }

    /**
     * @return bool
     */
    public function isWithManyEnvironments(): bool
    {
        return $this->withManyEnvironments;
    }
}
