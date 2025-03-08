<?php

declare(strict_types=1);

namespace Doris\StreamLoad\Driver;

interface LoadInterface
{
    public function add(array $data): void;

    public function addFile(array $data): void;

    public function getContents();

    public function getParameters(): array;

    public function getFile(): ?string;
}
