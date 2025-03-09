<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

enum Header: string
{
    /**
     *  用于指定 Doris 该次导入的标签，标签相同的数据无法多次导入。如果不指定 label，Doris 会自动生成一个标签。用户可以通过指定 label 的方式来避免一份数据重复导入的问题。Doris 默认保留三天内的导入作业标签，可以 label_keep_max_second 调整保留时长。例如，指定本次导入 label 为 123，需要指定命令 -H "label:123"。label 的使用，可以防止用户重复导入相同的数据。强烈推荐用户同一批次数据使用相同的 label。这样同一批次数据的重复请求只会被接受一次，保证了 At-Most-Once 当 label 对应的导入作业状态为 CANCELLED 时，该 label 可以再次被使用.
     */
    case LABEL = 'label';

    /**
     * 用于指定导入文件中的列分隔符，默认为\t。如果是不可见字符，则需要加\x作为前缀，使用十六进制来表示分隔符。可以使用多个字符的组合作为列分隔符。例如，hive 文件的分隔符 \x01，需要指定命令 -H "column_separator:\x01".
     */
    case COLUMN_SEPARATOR = 'column_separator';

    /**
     * 用于指定导入文件中的换行符，默认为 \n。可以使用做多个字符的组合作为换行符。例如，指定换行符为 \n，需要指定命令 -H "line_delimiter:\n".
     */
    case LINE_DELIMITER = 'line_delimiter';

    /**
     * 用于指定导入文件中的列和 table 中的列的对应关系。如果源文件中的列正好对应表中的内容，那么是不需要指定这个字段的内容的。如果源文件与表 schema 不对应，那么需要这个字段进行一些数据转换。有两种形式 column：直接对应导入文件中的字段，直接使用字段名表示衍生列，语法为 column_name = expression 详细案例参考 导入过程中数据转换.
     */
    case COLUMNS = 'columns';

    /**
     * 用于抽取部分数据。用户如果有需要将不需要的数据过滤掉，那么可以通过设定这个选项来达到。例如，只导入 k1 列等于 20180601 的数据，那么可以在导入时候指定 -H "where: k1 = 20180601".
     */
    case WHERE = 'where';

    /**
     * 最大容忍可过滤（数据不规范等原因）的数据比例，默认零容忍。取值范围是 0~1。当导入的错误率超过该值，则导入失败。数据不规范不包括通过 where 条件过滤掉的行。例如，最大程度保证所有正确的数据都可以导入（容忍度 100%），需要指定命令 -H "max_filter_ratio:1".
     */
    case MAX_FILTER_RATIO = 'max_filter_ratio';

    /**
     * 用于指定这次导入所涉及的 partition。如果用户能够确定数据对应的 partition，推荐指定该项。不满足这些分区的数据将被过滤掉。例如，指定导入到 p1, p2 分区，需要指定命令 -H "partitions: p1, p2".
     */
    case PARTITIONS = 'partitions';

    /**
     * 指定导入的超时时间。单位秒。默认是 600 秒。可设置范围为 1 秒 ~ 259200 秒。例如，指定导入超时时间为 1200s，需要指定命令 -H "timeout:1200".
     */
    case TIMEOUT = 'timeout';

    /**
     * 用户指定此次导入是否开启严格模式，默认为关闭。例如，指定开启严格模式，需要指定命令 -H "strict_mode:true".
     */
    case STRICT_MODE = 'strict_mode';

    /**
     * 指定本次导入所使用的时区。默认为东八区。该参数会影响所有导入涉及的和时区有关的函数结果。例如，指定导入时区为 Africa/Abidjan，需要指定命令 -H "timezone:Africa/Abidjan".
     */
    case TIMEZONE = 'timezone';

    /**
     * 导入内存限制。默认为 2GB。单位为字节
     */
    case EXEC_MEM_LIMIT = 'exec_mem_limit';

    /**
     * 指定导入数据格式，默认是 CSV 格式。目前支持以下格式：CSV, JSON, arrow, csv_with_names（支持 csv 文件行首过滤）csv_with_names_and_types（支持 CSV 文件前两行过滤）Parquet, ORC 例如，指定导入数据格式为 JSON，需要指定命令 -H "format:json".
     */
    case FORMAT = 'format';

    /**
     * 导入 JSON 数据格式有两种方式：简单模式：没有指定 jsonpaths 为简单模式，这种模式要求 JSON 数据是对象类型匹配模式：用于 JSON 数据相对复杂，需要通过 jsonpaths 参数匹配对应的 value 在简单模式下，要求 JSON 中的 key 列与表中的列名是一一对应的，如 JSON 数据 {"k1":1, "k2":2, "k3":"hello"}，其中 k1、k2 及 k3 分别对应表中的列.
     */
    case JSONPATHS = 'jsonpaths';

    /**
     * 指定 strip_outer_array 为 true 时表示 JSON 数据以数组对象开始且将数组对象中进行展平，默认为 false。在 JSON 数据的最外层是 [] 表示的数组时，需要设置 strip_outer_array 为 true。如以下示例数据，在设置 strip_outer_array 为 true 后，导入 Doris 中生成两行数据 [{"k1" : 1, "v1" : 2},{"k1" : 3, "v1" : 4}].
     */
    case STRIP_OUTER_ARRAY = 'strip_outer_array';

    /**
     * json_root 为合法的 jsonpath 字符串，用于指定 json document 的根节点，默认值为 "".
     */
    case JSON_ROOT = 'json_root';

    /**
     * 数据的合并类型，支持三种类型：
     * - APPEND（默认值）：表示这批数据全部追加到现有数据中
     * - DELETE：表示删除与这批数据 Key 相同的所有行
     * - MERGE：需要与 DELETE 条件联合使用，表示满足 DELETE 条件的数据按照 DELETE 语义处理，其余的按照 APPEND 语义处理
     * 例如，指定合并模式为 MERGE：-H "merge_type: MERGE" -H "delete: flag=1".
     */
    case MERGE_TYPE = 'merge_type';

    /**
     * 仅在 MERGE 下有意义，表示数据的删除条件.
     */
    case DELETE = 'delete';

    /**
     * 只适用于 UNIQUE KEYS 模型，相同 Key 列下，保证 Value 列按照 source_sequence 列进行 REPLACE。source_sequence 可以是数据源中的列，也可以是表结构中的一列.
     */
    case SEQUENCE_COL = 'function_column.sequence_col';

    /**
     * 布尔类型，为 true 表示 JSON 将以第一行为 schema 进行解析。开启这个选项可以提高 json 导入效率，但是要求所有 json 对象的 key 的顺序和第一行一致，默认为 false，仅用于 JSON 格式.
     */
    case FUZZY_PARSE = 'fuzzy_parse';

    /**
     * 布尔类型，为 true 表示在解析 JSON 数据时会将数字类型转为字符串，确保不会出现精度丢失的情况下进行导入.
     */
    case NUM_AS_STRING = 'num_as_string';

    /**
     * 布尔类型，为 true 表示支持每行读取一个 JSON 对象，默认值为 false.
     */
    case READ_JSON_BY_LINE = 'read_json_by_line';

    /**
     * 整型，用于设置发送批处理数据的并行度，如果并行度的值超过 BE 配置中的 max_send_batch_parallelism_per_job，那么作为协调点的 BE 将使用 max_send_batch_parallelism_per_job 的值
     */
    case SEND_BATCH_PARALLELISM = 'send_batch_parallelism';

    /**
     * 用于指定导入数据中包含的隐藏列，在 Header 中不包含 Columns 时生效，多个 hidden column 用逗号分割。系统会使用用户指定的数据导入数据。在下例中，导入数据中最后一列数据为 __DORIS_SEQUENCE_COL__。hidden_columns: __DORIS_DELETE_SIGN__,__DORIS_SEQUENCE_COL__.
     */
    case HIDDEN_COLUMNS = 'hidden_columns';

    /**
     * 布尔类型，为 true 表示支持一个任务只导入数据到对应分区的一个 Tablet，默认值为 false。该参数只允许在对带有 random 分桶的 OLAP 表导数的时候设置.
     */
    case LOAD_TO_SINGLE_TABLET = 'load_to_single_tablet';

    /**
     * 指定文件的压缩格式。目前只支持 CSV 文件的压缩。支持 gz, lzo, bz2, lz4, lzop, deflate 压缩格式.
     */
    case COMPRESS_TYPE = 'compress_type';

    /**
     * 布尔类型，默认值为 false，为 true 时表示裁剪掉 CSV 文件每个字段最外层的双引号.
     */
    case TRIM_DOUBLE_QUOTES = 'trim_double_quotes';

    /**
     * 整数类型，默认值为 0，含义为跳过 CSV 文件的前几行。当设置 format 设置为 csv_with_names或csv_with_names_and_types时，该参数会失效.
     */
    case SKIP_LINES = 'skip_lines';

    /**
     * 字符串类型，默认值为空。给任务增加额外的信息.
     */
    case COMMENT = 'comment';

    /**
     * 指定包围符。当 CSV 数据字段中含有行分隔符或列分隔符时，为防止意外截断，可指定单字节字符作为包围符起到保护作用。例如列分隔符为 ","，包围符为 "'"，数据为 "a,'b,c'"，则 "b,c" 会被解析为一个字段。注意：当 enclose 设置为"时，trim_double_quotes 一定要设置为 true.
     */
    case ENCLOSE = 'enclose';

    /**
     * 指定转义符。用于转义在字段中出现的与包围符相同的字符。例如数据为 "a,'b,'c'"，包围符为 "'"，希望 "b,'c 被作为一个字段解析，则需要指定单字节转义符，例如""，将数据修改为 "a,'b,'c'".
     */
    case ESCAPE = 'escape';

    /**
     * 导入数据的时候是否开启 MemTable 前移，默认为 false.
     */
    case MEMTABLE_ON_SINK_NODE = 'memtable_on_sink_node';

    /**
     * Unique 表上的更新模式，目前仅对 Merge-On-Write Unique 表有效，一共支持三种类型 UPSERT, UPDATE_FIXED_COLUMNS, UPDATE_FLEXIBLE_COLUMNS。 UPSERT: 表示以 upsert 语义导入数据; UPDATE_FIXED_COLUMNS: 表示以部分列更新的方式导入数据; UPDATE_FLEXIBLE_COLUMNS: 表示以灵活部分列更新的方式导入数据.
     */
    case UNIQUE_KEY_UPDATE_MODE = 'unique_key_update_mode';

    /**
     * - 异步模式，设置"async_mode"
     * - 同步模式，设置"sync_mode".
     */
    case GROUP_COMMIT = 'group_commit';
}
