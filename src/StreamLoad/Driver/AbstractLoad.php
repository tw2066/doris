<?php

declare(strict_types=1);

namespace Doris\StreamLoad\Driver;

abstract class AbstractLoad implements LoadInterface
{
    protected mixed $fp = null;

    protected int $row = 0;

    public function __construct(protected ?string $filePath = null)
    {
    }

    public function __destruct()
    {
        $this->fp && fclose($this->fp);
        $this->filePath && @unlink($this->filePath);
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function getCurrentRow(): int
    {
        return $this->row;
    }
}
