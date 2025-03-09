# PHP Doris
[English](README_EN.md)
> doris通过极少量数据（5 分钟一次）时可以使用 INSERT 写入数据, 吞吐较高时推荐使用 Stream Load 通过 HTTP 写入数据
## Stream Load
### 安装

```
composer require tangwei/doris
```

### 使用
快速上手
```php
$feHost = 'http://127.0.0.1:8040';       
$db = 'test_db';       
$user = 'root';       
$password = '';       
$streamLoad = new StreamLoad($feHost, $db, $db,$password);
$builder = $load->table('test_stream_load');
$builder->data([
    ['user_id' => 1, 'name' => 'q', 'age' => 11],
    ['user_id' => 2, 'name' => 'w', 'age' => 118],
]);
$builder->data(
    ['user_id' => 3, 'name' => 'q3', 'age' => 9],
);
$data = $builder->load();
```

#### 设置提交格式
默认使用json提交,可以通过format方法设置格式
```php
$streamLoad = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $streamLoad->format(Format::CSV)->table('test_stream_load');
```

#### 内存模式
* 默认内存模式(底层通过文件进行提交),特别适用于大量数据提交;
* 提交数据量小时,可以通过constMemory方法设置false
```php
$load = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $load->constMemory(false)->table('test_stream_load');
```

#### 设置参数
可以通过setHeader方法设置参数,参考[官方文档](https://doris.apache.org/zh-CN/docs/data-operate/import/import-way/stream-load-manual)Header参数

```php
$load = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $load->table('test_stream_load');
$builder->setHeader(\Doris\StreamLoad\Header::COLUMNS, 'user_id,name,age');
```
#### 异步模式

```php
$builder->setHeader(\Doris\StreamLoad\Header::GROUP_COMMIT, 'async_mode');
```

#### 通过文件提交数据
本地有cvs文件,可以通过文件直接导入上传

```php
$load = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $load->table('test_stream_load');
$builder->setHeader(\Doris\StreamLoad\Header::COLUMNS, 'user_id,name,age')
->file('/user/test.csv')
->load();
```

#### hyperf 框架使用
底层自动判断hyperf的运行环境,无需做额外处理