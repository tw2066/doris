# PHP Doris
[中文](README.md)
> When using Doris to write data with a very small amount of data (every 5 minutes), you can use the INSERT method. For high throughput scenarios, it is recommended to use Stream Load to write data via HTTP.
## Stream Load
### Installation

```

composer require tangwei/doris
```
### Usage
Quick Start
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
#### Get streamLoad Object
Configure through .env file and get the streamLoad object
```
dotenv
DORIS_FE_HOST=http://192.168.1.72:8040
DORIS_DB=testdb
DORIS_USER=root
DORIS_PASSWORD=''
```
```php
$streamLoad = Doris::streamLoad();
```
#### Set Submission Format
By default, JSON format is used for submission. You can set the format using the format method.
```php
$streamLoad = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $streamLoad->format(Format::CSV)->table('test_stream_load');
```
#### Memory Mode
When submitting large amounts of individual data, you can enable memory mode by setting constMemory to true to reduce memory usage.
```php
$streamLoad = Doris::streamLoad();
$builder = $streamLoad->constMemory(true)->table('test_stream_load');
```
#### Set Header Parameters
You can set parameters using the setHeader method. Refer to [Official Documentation](https://doris.apache.org/zh-CN/docs/data-operate/import/import-way/stream-load-manual) for Header parameters.

```php
$streamLoad = Doris::streamLoad();
$builder = $streamLoad->table('test_stream_load');
$builder->setHeader(\Doris\StreamLoad\Header::COLUMNS, 'user_id,name,age');
```
#### Asynchronous Mode

```php
$builder->setHeader(\Doris\StreamLoad\Header::GROUP_COMMIT, 'async_mode');
```
#### Submit Data via File
If you have a local CSV file, you can directly import and upload it.

```php
$streamLoad = Doris::streamLoad();
$builder = $streamLoad->table('test_stream_load');
$builder->setHeader(\Doris\StreamLoad\Header::COLUMNS, 'user_id,name,age')
->file('/user/test.csv')
->load();
```
#### Hyperf Framework Usage
The underlying system automatically detects coroutine environments, no additional handling is required.

#### Performance Testing
| File Format | Data Volume | constMemory | Time Consumed(s) | Memory Size(MB) |
| ----------- | ----------- | ----------- | ---------------- | --------------- |
| json        | 1 million   | false       | 1.93             | 48.58           |
| json        | 1 million   | true        | 7.59             | 1.21            |
| csv         | 1 million   | false       | 1.62             | 22.83           |
| csv         | 1 million   | true        | 7.52             | 1.21            |
```
