<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

use Doris\StreamLoad\Driver\CSVLoad;
use Doris\StreamLoad\Driver\JSONLoad;

enum Format: string
{
    case JSON = JSONLoad::class;
    case CSV = CSVLoad::class;
}
