<?php

declare(strict_types=1);

namespace Messenger\Aggregate;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AggregateRootId
{
    /** @var string */
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromUUID(UuidInterface $uuid): self
    {
        return new static($uuid->toString());
    }

    /**
     * @throws \Exception
     */
    public static function generate(): self
    {
        return new static(Uuid::uuid4()->toString());
    }

    public function toUUID(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
