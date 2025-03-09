<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

enum Header: string
{
    case LABEL = 'label';
    case COLUMN_SEPARATOR = 'column_separator';
    case LINE_DELIMITER = 'line_delimiter';
    case COLUMNS = 'columns';
    case WHERE = 'where';
    case MAX_FILTER_RATIO = 'max_filter_ratio';
    case PARTITIONS = 'partitions';
    case TIMEOUT = 'timeout';
    case STRICT_MODE = 'strict_mode';
    case TIMEZONE = 'timezone';
    case EXEC_MEM_LIMIT = 'exec_mem_limit';
    case FORMAT = 'format';
    case JSONPATHS = 'jsonpaths';
    case STRIP_OUTER_ARRAY = 'strip_outer_array';
    case JSON_ROOT = 'json_root';
    case MERGE_TYPE = 'merge_type';
    case DELETE = 'delete';
    case SEQUENCE_COL = 'function_column.sequence_col';
    case FUZZY_PARSE = 'fuzzy_parse';
    case NUM_AS_STRING = 'num_as_string';
    case READ_JSON_BY_LINE = 'read_json_by_line';
    case SEND_BATCH_PARALLELISM = 'send_batch_parallelism';
    case HIDDEN_COLUMNS = 'hidden_columns';
    case LOAD_TO_SINGLE_TABLET = 'load_to_single_tablet';
    case COMPRESS_TYPE = 'compress_type';
    case TRIM_DOUBLE_QUOTES = 'trim_double_quotes';
    case SKIP_LINES = 'skip_lines';
    case COMMENT = 'comment';
    case ENCLOSE = 'enclose';
    case ESCAPE = 'escape';
    case MEMTABLE_ON_SINK_NODE = 'memtable_on_sink_node';
    case UNIQUE_KEY_UPDATE_MODE = 'unique_key_update_mode';
    case GROUP_COMMIT = 'group_commit';
}
