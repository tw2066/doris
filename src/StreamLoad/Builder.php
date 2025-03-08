<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

use Doris\StreamLoad\Driver\LoadInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;

class Builder
{
    protected array $parameters = [];

    protected string $filePath = '';

    protected LoadInterface $load;

    public function __construct(
        protected readonly Client $client,
        protected readonly string $database,
        protected readonly string $table,
        protected readonly Format $format,
        private readonly string $feHost,
        private readonly string $user = '',
        private readonly string $password = '',
        protected bool $constMemory = true
    ) {
        $this->load = new $this->format->value();
    }

    public function data(array $data): static
    {
        if ($this->constMemory) {
            $this->load->addFile($data);
        } else {
            $this->load->add($data);
        }
        return $this;
    }

    public function file(string $filePath): static
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function load(): Response
    {
        $filePath = $this->filePath ?: $this->load->getFile();
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
        return new Response(json_decode($data, true));
    }

    public function setParam(Param|string $key, $value): static
    {
        $key = is_string($key) ? $key : $key->value;
        $this->parameters[$key] = $value;
        return $this;
    }

    private function buildHeaders(): array
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->user . ':' . $this->password),
        ];
        $loadParam = $this->load->getParameters();
        return array_merge($headers, $loadParam, $this->parameters);
    }
}
