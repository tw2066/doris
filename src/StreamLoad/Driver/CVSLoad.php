<?php

declare(strict_types=1);

namespace Doris\StreamLoad\Driver;

class CVSLoad extends AbstractLoad
{
    public function add(array $data): void
    {
        $this->fp ??= fopen('php://memory', 'r+');
        $this->putCVS($data);
    }

    public function addFile(array $data): void
    {
        $this->filePath ??= tempnam(sys_get_temp_dir(), 'doris') . '.cvs';
        $this->fp ??= fopen($this->filePath, 'a');
        $this->putCVS($data);
    }

    public function getContents(): bool|string
    {
        rewind($this->fp);
        return stream_get_contents($this->fp);
    }

    public function getParameters(): array
    {
        return [
            'column_separator' => ',',
            // 'columns' => 'user_id,name,age',
        ];
    }

    protected function putCVS(array $data): void
    {
        $isArrayMultidimensionalMap = is_array(current($data));
        if ($isArrayMultidimensionalMap) {
            array_map(function ($item) {
                fputcsv($this->fp, array_values($item));
            }, $data);
        } else {
            fputcsv($this->fp, array_values($data));
        }
    }
}
