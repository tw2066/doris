<?php

declare(strict_types=1);

namespace Doris\StreamLoad\Driver;

class JSONLoad extends AbstractLoad
{
    protected string $content = '';

    public function putMemory(array $data): void
    {
        $isArrayMultidimensionalMap = is_array(current($data));
        if ($isArrayMultidimensionalMap) {
            array_map(fn ($item) => $this->content .= json_encode($item) . PHP_EOL, $data);
        } else {
            $this->content .= json_encode($data) . PHP_EOL;
        }
    }

    public function putFile(array $data): void
    {
        $this->filePath ??= tempnam(sys_get_temp_dir(), 'doris_json_');
        $this->fp ??= fopen($this->filePath, 'a');
        $isArrayMultidimensionalMap = is_array(current($data));
        if ($isArrayMultidimensionalMap) {
            array_map(function ($item) {
                fwrite($this->fp, json_encode($item) . PHP_EOL);
                ++$this->row;
            }, $data);
        } else {
            fwrite($this->fp, json_encode($data) . PHP_EOL);
            ++$this->row;
        }
    }

    public function getContents(): string
    {
        return $this->content;
    }

    public function getHeaders(): array
    {
        return [
            'format' => 'json',
            'read_json_by_line' => 'true',
            'strip_outer_array' => 'false',
        ];
    }
}
