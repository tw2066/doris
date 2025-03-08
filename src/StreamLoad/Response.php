<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

class Response
{
    public int $txnId;

    public string $label;

    public string $comment;

    public string $twoPhaseCommit;

    public string $status;

    public string $message;

    public int $numberTotalRows;

    public int $numberLoadedRows;

    public int $numberFilteredRows;

    public int $numberUnselectedRows;

    public int $loadBytes;

    public int $loadTimeMs;

    public int $beginTxnTimeMs;

    public int $streamLoadPutTimeMs;

    public int $readDataTimeMs;

    public int $writeDataTimeMs;

    public int $commitAndPublishTimeMs;

    public string $errorURL = '';

    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $key = lcfirst($key);
            $this->{$key} = $value;
        }
    }
}
