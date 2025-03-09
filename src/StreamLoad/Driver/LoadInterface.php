<?php

declare(strict_types=1);

namespace Doris\StreamLoad\Driver;

interface LoadInterface
{
    public function add(array $data): void;

    public function putFile(array $data): void;

    public function getContents();

    public function getHeaders(): array;

    public function getFilePath(): ?string;
}
