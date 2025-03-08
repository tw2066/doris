<?php

declare(strict_types=1);

namespace Doris;

use Doris\StreamLoad\Builder;
use Doris\StreamLoad\Format;
use GuzzleHttp\Client;
use Hyperf\Guzzle\HandlerStackFactory;

class StreamLoad
{
    protected bool $constMemory = true;

    /**
     * @var Client
     */
    private $client;

    private Format $format = Format::JSON;

    public function __construct(
        private readonly string $feHost,
        private string $database = '',
        private readonly string $user = '',
        private readonly string $password = ''
    ) {
    }

    public function setClient($client): static
    {
        $this->client = $client;
        return $this;
    }

    public function constMemory(bool $constMemory = false): static
    {
        $this->constMemory = $constMemory;
        return $this;
    }

    public function format(Format $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function table(string $table): Builder
    {
        $arr = explode('.', $table, 2);
        if (count($arr) > 1) {
            $this->database = $arr[0];
            $table = $arr[1];
        } else {
            $table = $arr[0];
        }
        return new Builder(
            $this->getClient(),
            $this->database,
            $table,
            $this->format,
            $this->feHost,
            $this->user,
            $this->password,
            $this->constMemory
        );
    }

    protected function getClient()
    {
        if ($this->client == null) {
            $config = [];
            if (class_exists(HandlerStackFactory::class)) {
                $factory = new HandlerStackFactory();
                $config = [
                    'handler' => $factory->create(),
                ];
            }
            $this->client = new Client($config);
        }
        return $this->client;
    }
}
