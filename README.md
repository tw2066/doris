# PHP Doris
[English](README_EN.md)
> doris通过极少量数据（5 分钟一次）时可以使用 INSERT 写入数据, 吞吐较高时推荐使用 Stream Load 通过 HTTP 写入数据
## Stream Load
### 安装

```
composer require tangwei/doris
```

### 使用
快速使用
```php
$feHost = 'http://127.0.0.1:8030';       
$db = 'test_db';       
$user = 'root';       
$password = '';       
$streamLoad = new StreamLoad($feHost, $db, $db,$password);
$builder = $streamLoad->table('test_stream_load');
$builder->data([
    ['user_id' => 1, 'name' => 'q', 'age' => 11],
    ['user_id' => 2, 'name' => 'w', 'age' => 118],
]);
$builder->data(
    ['user_id' => 3, 'name' => 'q3', 'age' => 9],
);
$data = $builder->load();
```
#### 获取streamLoad对象
通过`.env`文件配置,获取streamLoad对象
```dotenv
DORIS_FE_HOST=http://192.168.1.72:8040
DORIS_DB=testdb
DORIS_USER=root
DORIS_PASSWORD=''
```
```php
$streamLoad = Doris::streamLoad();
```
#### 设置提交格式
默认使用json提交,可以通过format方法设置格式
```php
$streamLoad = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $streamLoad->format(Format::CSV)->table('test_stream_load');
```

#### 内存模式
提交大量数据时,可以设置constMemory为true,减少内存占用
```php
$streamLoad = Doris::streamLoad();
$builder = $streamLoad->constMemory(true)->table('test_stream_load');
```

#### 设置Header参数
可以通过setHeader方法设置参数,参考[官方文档](https://doris.apache.org/zh-CN/docs/data-operate/import/import-way/stream-load-manual)Header参数

```php
$streamLoad = Doris::streamLoad();
$builder = $streamLoad->table('test_stream_load');
$builder->setHeader(\Doris\StreamLoad\Header::COLUMNS, 'user_id,name,age');
```
#### 异步模式

```php
$builder->setHeader(\Doris\StreamLoad\Header::GROUP_COMMIT, 'async_mode');
```

#### 通过文件提交数据
本地有cvs文件,可以通过文件直接导入上传

```php
$streamLoad = Doris::streamLoad();
$builder = $streamLoad->table('test_stream_load');
$builder->setHeader(\Doris\StreamLoad\Header::COLUMNS, 'user_id,name,age')
->file('/user/test.csv')
->load();
```

#### hyperf 框架使用
底层自动判断协程环境,无需额外处理

#### 性能测试
| 文件格式 | 数据量 | constMemory | 耗时(s) | 内存大小(MB) |
| -------- | ------ | ----------- | ------- | ------------ |
| json     | 100万  | fales       | 1.93    | 48.58        |
| json     | 100万  | true        | 7.59    | 1.21         |
| cvs      | 100万  | fales       | 1.62    | 22.83        |
| cvs      | 100万  | true        | 7.52    | 1.21         |