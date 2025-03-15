<?php

declare(strict_types=1);

namespace Doris\StreamLoad\Driver;

class CSVLoad extends AbstractLoad
{
    public function putMemory(array $data): void
    {
        $this->fp ??= fopen('php://memory', 'r+');
        $this->putCSV($data);
    }

    public function putFile(array $data): void
    {
        $this->filePath ??= tempnam(sys_get_temp_dir(), 'doris_csv_');
        $this->fp ??= fopen($this->filePath, 'a');
        $this->putCSV($data);
    }

    public function getContents(): string
    {
        rewind($this->fp);
        return stream_get_contents($this->fp);
    }

    public function getHeaders(): array
    {
        return [
            'format' => 'csv',
            'column_separator' => ',',
            'trim_double_quotes' => 'true',
            'enclose' => '"',
        ];
    }

    protected function putCSV(array $data): void
    {
        $isArrayMultidimensionalMap = is_array(current($data));
        if ($isArrayMultidimensionalMap) {
            array_map(function ($item) {
                fputcsv($this->fp, array_values($item));
                ++$this->row;
            }, $data);
        } else {
            fputcsv($this->fp, array_values($data));
            ++$this->row;
        }
    }
}
