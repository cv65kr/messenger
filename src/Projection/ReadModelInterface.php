<?php

declare(strict_types=1);

namespace Messenger\Projection;

interface ReadModelInterface
{
    public static function fromSerializable(array $data);

    public function serialize(): array;

    public function getId(): string;
}
