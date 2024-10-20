<?php

declare(strict_types=1);

namespace App\Entities;

final class Genre implements \JsonSerializable
{
    public function __construct(private readonly int $id, private readonly string $name)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
