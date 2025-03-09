<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

class LoadResponse
{
    public $txnId;

    public $label;

    public $comment;

    public $groupCommit;

    public $twoPhaseCommit;

    public $status;

    public $message;

    public $numberTotalRows;

    public $numberLoadedRows;

    public $numberFilteredRows;

    public $numberUnselectedRows;

    public $loadBytes;

    public $loadTimeMs;

    public $beginTxnTimeMs;

    public $streamLoadPutTimeMs;

    public $readDataTimeMs;

    public $writeDataTimeMs;

    public $commitAndPublishTimeMs;

    public $errorURL;

    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $key = lcfirst($key);
            $this->{$key} = $value;
        }
    }
}
