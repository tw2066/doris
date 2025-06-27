<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

use Doris\StreamLoad\Driver\LoadInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;

class Builder
{
    protected array $headers = [];

    protected ?string $filePath = null;

    protected ?LoadInterface $load = null;

    public function __construct(
        protected readonly Client $client,
        protected readonly string $database,
        protected readonly string $table,
        protected readonly Format $format,
        private readonly string $feHost,
        private readonly string $user = '',
        private readonly string $password = '',
        protected bool $constMemory = false
    ) {
    }

    public function data(array $data): static
    {
        $this->load ??= new $this->format->value($this->filePath);
        if ($this->constMemory) {
            $this->load->putFile($data);
        } else {
            $this->load->putMemory($data);
        }
        return $this;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;
        $this->constMemory = true;
        return $this;
    }

    public function currentRow(): int
    {
        return $this->load->getCurrentRow();
    }

    public function load(string $file = ''): LoadResponse
    {
        $filePath = $file ?: $this->load->getFilePath();
        if (! empty($filePath)) {
            $data = Utils::tryFopen($filePath, 'r');
        } else {
            $data = $this->load->getContents();
        }

        $options = [
            'body' => $data,
            'headers' => $this->buildHeaders(),
        ];
        $uri = $this->feHost . '/api/' . $this->database . '/' . $this->table . '/_stream_load';

        $response = $this->client->put(
            $uri,
            $options
        );
        $data = $response->getBody()->getContents();
        $loadResponse = new LoadResponse(json_decode($data, true));
        if (in_array($loadResponse->status, ['Fail', 'Label Already Exists'])) {
            throw new LoadException(sprintf('Doris Stream Load Error: %s, errorURL: %s', $loadResponse->message, $loadResponse->errorURL));
        }
        return $loadResponse;
    }

    public function setHeader(Header|string $key, $value): static
    {
        $key = is_string($key) ? $key : $key->value;
        $this->headers[$key] = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        return $this;
    }

    public function setHeaders(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
        return $this;
    }

    public function init(): void
    {
        $this->headers = [];
        $this->filePath = null;
        $this->load = null;
    }

    protected function buildHeaders(): array
    {
        $initHeaders = [
            'Expect' => '100-continue',
            'Authorization' => 'Basic ' . base64_encode($this->user . ':' . $this->password),
        ];
        $loadHeaders = $this->filePath ? [] : $this->load->getHeaders();
        return array_merge($initHeaders, $loadHeaders, $this->headers);
    }
}
