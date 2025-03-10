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
        $Load->add([
            ['user_id' => 1, 'name' => 'q', 'age' => 18],
            ['user_id' => 2, 'name' => 'w', 'age' => 19],
        ]);
        $Load->add(['user_id' => 3, 'name' => 'w', 'age' => 20]);
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
        $Load = new StreamLoad\Driver\CVSLoad();
        $Load->add([
            ['user_id' => 1, 'name' => 'q', 'age' => 18],
            ['user_id' => 2, 'name' => 'w', 'age' => 19],
        ]);
        $Load->add(['user_id' => 3, 'name' => 'w', 'age' => 20]);
        $result = $Load->getContents();

        $this->assertEquals($result, $contents);
        $Load = new StreamLoad\Driver\CVSLoad();
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
            $this->loadJSON();
            $this->loadCVS();
            $this->async();
        }
    }

    public function loadCVS()
    {
        $load = new StreamLoad('http://192.168.1.72:8040', 'testdb', 'root');
        $builder = $load->format(StreamLoad\Format::CVS)->constMemory(true)->table('test_streamload');
        $builder->data([
            ['user_id' => 1, 'name' => 'cvs1', 'age' => 11],
            ['user_id' => 2, 'name' => 'cvs2', 'age' => 12],
        ]);
        $builder->data(
            ['user_id' => 3, 'name' => 'cvs3', 'age' => 13],
        );
        $data = $builder
            ->setHeader(Header::COLUMNS, 'user_id,name,age')
            ->load();
        $this->assertEquals($data->status, 'Success');

        $builder = $load->format(StreamLoad\Format::CVS)->constMemory(false)->table('test_streamload');
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
    }

    public function loadJSON()
    {
        $load = new StreamLoad('http://192.168.1.72:8040', 'testdb', 'root');
        $builder = $load->format(StreamLoad\Format::JSON)->constMemory(true)->table('test_streamload');
        $builder->data([
            ['user_id' => 11, 'name' => 'json11', 'age' => 111],
            ['user_id' => 12, 'name' => 'json12', 'age' => 112],
        ]);
        $builder->data(
            ['user_id' => 13, 'name' => 'json13', 'age' => 113],
        );
        $data = $builder->load();
        $this->assertEquals($data->status, 'Success');

        $builder = $load->format(StreamLoad\Format::JSON)->constMemory(false)->table('test_streamload');
        $builder->data([
            ['user_id' => 14, 'name' => 'json14', 'age' => 114],
            ['user_id' => 15, 'name' => 'json15', 'age' => 115],
        ]);
        $builder->data(
            ['user_id' => 16, 'name' => 'json16', 'age' => 116],
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
