<?php

declare(strict_types=1);

namespace DorisTest;

use Doris\StreamLoad;
use Doris\StreamLoad\Header;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LoadTest extends TestCase
{
    public function testJSONLoadContents()
    {
        $contents =
            '{"user_id":1,"name":"q","age":18}
{"user_id":2,"name":"w","age":19}
{"user_id":3,"name":"w","age":20}
';
        $Load = new StreamLoad\Driver\JSONLoad();
        $Load->putMemory([
            ['user_id' => 1, 'name' => 'q', 'age' => 18],
            ['user_id' => 2, 'name' => 'w', 'age' => 19],
        ]);
        $Load->putMemory(['user_id' => 3, 'name' => 'w', 'age' => 20]);
        $result = $Load->getContents();
        $this->assertEquals($result, $contents);

        $Load = new StreamLoad\Driver\JSONLoad();
        $Load->putFile([
            ['user_id' => 1, 'name' => 'q', 'age' => 18],
            ['user_id' => 2, 'name' => 'w', 'age' => 19],
        ]);
        $Load->putFile(['user_id' => 3, 'name' => 'w', 'age' => 20]);
        $result = file_get_contents($Load->getFilePath());
        $this->assertEquals($result, $contents);
    }

    public function testCVSLoadContents()
    {
        $contents =
            '1,q,18
2,w,19
3,w,20
';
        $Load = new StreamLoad\Driver\CSVLoad();
        $Load->putMemory([
            ['user_id' => 1, 'name' => 'q', 'age' => 18],
            ['user_id' => 2, 'name' => 'w', 'age' => 19],
        ]);
        $Load->putMemory(['user_id' => 3, 'name' => 'w', 'age' => 20]);
        $result = $Load->getContents();

        $this->assertEquals($result, $contents);
        $Load = new StreamLoad\Driver\CSVLoad();
        $Load->putFile([
            ['user_id' => 1, 'name' => 'q', 'age' => 18],
            ['user_id' => 2, 'name' => 'w', 'age' => 19],
        ]);
        $Load->putFile(['user_id' => 3, 'name' => 'w', 'age' => 20]);
        $result = file_get_contents($Load->getFilePath());
        $this->assertEquals($result, $contents);
    }

    public function testLoad()
    {
        if (PHP_OS === 'Darwin') {
            $this->loadCVS();
            $this->loadJSON();
            $this->async();
        }
    }

    public function loadCVS()
    {
        $streamLoad = new StreamLoad('http://192.168.1.72:8040', 'testdb', 'root');
        $builder = $streamLoad->format(StreamLoad\Format::CSV)->constMemory(false)->table('test_streamload');
        $builder->data([
            [1, 'cvs\1', 11],
            [2, "cvs'2", 12],
        ]);
        $builder->data(
            [3, 'cvs,3', 13],
        );
        $data = $builder
            ->setHeader(Header::COLUMNS, 'user_id,name,age')
            ->load();
        $this->assertEquals($data->status, 'Success');

        $builder = $streamLoad->format(StreamLoad\Format::CSV)->constMemory(false)->table('test_streamload');
        $builder->data([
            ['user_id' => 4, 'name' => 'cvs4', 'age' => 14],
            ['user_id' => 5, 'name' => 'cvs5', 'age' => 15],
        ]);
        $builder->data(
            ['user_id' => 6, 'name' => 'cvs6', 'age' => 16],
        );
        $data = $builder
            ->setHeader(Header::COLUMNS, 'user_id,name,age')
            ->load();
        $this->assertEquals($data->status, 'Success');

        $data = $streamLoad->table('test_streamload')
            ->setHeaders([
                'format' => 'csv',
                'column_separator' => ',',
                'trim_double_quotes' => 'true',
                'enclose' => '"',
            ])
            ->file(BASE_PATH . '/tests/load.csv')
            ->load();
        $this->assertEquals($data->status, 'Success');
    }

    public function loadJSON()
    {
        $streamLoad = new StreamLoad('http://192.168.1.72:8040', 'testdb', 'root');
        $builder = $streamLoad->format(StreamLoad\Format::JSON)->constMemory(true)->table('test_streamload');
        $builder->data([
            ['user_id' => 11, 'name' => 'json"11', 'age' => 111],
            ['user_id' => 12, 'name' => 'json\12', 'age' => 112],
        ]);
        $builder->data(
            ['user_id' => 13, 'name' => 'json,13', 'age' => 113],
        );
        $data = $builder->load();
        $this->assertEquals($data->status, 'Success');

        $builder = $streamLoad->format(StreamLoad\Format::JSON)->constMemory(false)->table('test_streamload');
        $builder->data([
            ['user_id' => 14, 'name' => 'json14_你好', 'age' => 114],
            ['user_id' => 15, 'name' => 'json15_"!@#$%^&*()', 'age' => 115],
        ]);
        $builder->data(
            ['user_id' => 16, 'name' => "json16_`1,',", 'age' => 116],
        );
        $data = $builder
            ->load();
        $this->assertEquals($data->status, 'Success');
    }

    public function async()
    {
        $load = new StreamLoad('http://192.168.1.72:8040', 'testdb', 'root');
        $builder = $load->table('test_streamload');
        $builder->data([
            ['user_id' => 111, 'name' => 'async_mode', 'age' => 66],
        ]);
        $data = $builder
            ->setHeader(Header::GROUP_COMMIT, 'async_mode')
            ->load();
        $this->assertEquals($data->status, 'Success');
        $this->assertEquals($data->groupCommit, true);
    }
}
