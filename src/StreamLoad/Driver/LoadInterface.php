<?php

declare(strict_types=1);

namespace Doris\StreamLoad\Driver;

interface LoadInterface
{
    public function putMemory(array $data): void;

    public function putFile(array $data): void;

    public function getContents();

    public function getHeaders(): array;

    public function getFilePath(): ?string;

    public function getCurrentRow(): int;
}
