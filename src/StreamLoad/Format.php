<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

use Doris\StreamLoad\Driver\CVSLoad;
use Doris\StreamLoad\Driver\JSONLoad;

enum Format: string
{
    case JSON = JSONLoad::class;
    case CVS = CVSLoad::class;
}
