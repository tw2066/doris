<?php

declare(strict_types=1);

namespace Doris\StreamLoad;

class LoadResponse
{
    /**
     * 导入事务的 ID.
     */
    public $txnId;

    /**
     * 导入作业的 label，通过 -H "label:<label_id>" 指定.
     */
    public $label;

    public $comment;

    /**
     * true，说明进入了 group commit 的流程.
     */
    public $groupCommit;

    public $twoPhaseCommit;

    /**
     * 导入的最终状态
     * - Success：表示导入成功
     * - Publish Timeout：该状态也表示导入已经完成，但数据可能会延迟可见，无需重试
     * - Label Already Exists：Label 重复，需要更换 label
     * - Fail：导入失败.
     */
    public $status;

    /**
     * 已存在的 Label 对应的导入作业的状态。这个字段只有在当 Status 为 "Label Already Exists" 时才会显示。用户可以通过这个状态，知晓已存在 Label 对应的导入作业的状态。"RUNNING" 表示作业还在执行，"FINISHED" 表示作业成功
     */
    public $existingJobStatus;

    /**
     * 导入错误信息.
     */
    public $message;

    /**
     *导入总处理的行数.
     */
    public $numberTotalRows;

    /**
     *  成功导入的行数.
     */
    public $numberLoadedRows;

    /**
     * 数据质量不合格的行数.
     */
    public $numberFilteredRows;

    /**
     * 被 where 条件过滤的行数.
     */
    public $numberUnselectedRows;

    /**
     * 导入的字节数.
     */
    public $loadBytes;

    /**
     * 导入完成时间。单位毫秒.
     */
    public $loadTimeMs;

    /**
     * 向 FE 请求开始一个事务所花费的时间，单位毫秒.
     */
    public $beginTxnTimeMs;

    /**
     * 向 FE 请求获取导入数据执行计划所花费的时间，单位毫秒.
     */
    public $streamLoadPutTimeMs;

    /**
     * 读取数据所花费的时间，单位毫秒.
     */
    public $readDataTimeMs;

    /**
     * 执行写入数据操作所花费的时间，单位毫秒.
     */
    public $writeDataTimeMs;

    /**
     * 向 FE 请求提交并且发布事务所花费的时间，单位毫秒.
     */
    public $commitAndPublishTimeMs;

    /**
     * 如果有数据质量问题，通过访问这个 URL 查看具体错误行.
     */
    public $errorURL;

    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $key = lcfirst($key);
            $this->{$key} = $value;
        }
    }
}
