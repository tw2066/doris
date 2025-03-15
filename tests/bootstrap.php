<?php

declare(strict_types=1);
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

putenv('DORIS_FE_HOST=http://192.168.1.72:8040');
putenv('DORIS_DB=testdb');
putenv('DORIS_USER=root');
putenv('DORIS_PASSWORD=');
putenv("DORIS_CONST_MEMORY=1");
