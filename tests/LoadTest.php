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
            $this->load();
            $this->async();
        }
    }

    public function load()
    {
        $load = new StreamLoad('http://192.168.1.72:8040', 'testdb', 'root');
        $builder = $load->table('test_streamload');
        $builder->data([
            ['user_id' => 1, 'name' => 'q', 'age' => 11],
            ['user_id' => 2, 'name' => 'w', 'age' => 118],
        ]);
        $builder->data(
            ['user_id' => 3, 'name' => 'q3', 'age' => 9],
        );
        $builder->data(
            ['user_id' => 6, 'name' => '6y', 'age' => 69],
        );
        $data = $builder
            ->setHeader(Header::COLUMNS, 'user_id,name,age')
            ->setHeader(Header::GROUP_COMMIT, 'async_mode')
            ->load();
        $this->assertEquals($data->status, 'Success');
    }

    public function async()
    {
        $load = new StreamLoad('http://192.168.1.72:8040', 'testdb', 'root');
        $builder = $load->table('test_streamload');
        $builder->setHeader(Header::GROUP_COMMIT, 'async_mode');
        $builder->data([
            ['user_id' => 1, 'name' => 'q', 'age' => 11],
            ['user_id' => 2, 'name' => 'w', 'age' => 118],
        ]);
        $builder->data(
            ['user_id' => 3, 'name' => 'q3', 'age' => 9],
        );
        $builder->data(
            ['user_id' => 6, 'name' => '6y', 'age' => 69],
        );
        $data = $builder
            ->setHeader(Header::COLUMNS, 'user_id,name,age')
            ->setHeader(Header::GROUP_COMMIT, 'async_mode')
            ->load();
        $this->assertEquals($data->groupCommit, true);
    }
}
