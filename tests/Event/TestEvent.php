<?php

declare(strict_types=1);

namespace Messenger\Event;

class TestEvent implements EventInterface
{
    /** @var string */
    private $item;

    private function __construct(string $item)
    {
        $this->item = $item;
    }

    public static function deserialize(array $data): self
    {
        return new self($data['item']);
    }

    public function serialize(): array
    {
        return [
            'item' => $this->item,
        ];
    }

    public function getItem(): string
    {
        return $this->item;
    }
}
