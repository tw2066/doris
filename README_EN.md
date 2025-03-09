# PHP Doris
> When inserting a small amount of data into Doris (e.g., every 5 minutes), you can use the INSERT method. For high-throughput scenarios, it is recommended to use Stream Load via HTTP.

## Stream Load

### Installation

```
composer require tangwei/doris
```


### Usage
Quick Start
```php
$feHost = 'http://127.0.0.1:8040';       
$db = 'test_db';       
$user = 'root';       
$password = '';       
$streamLoad = new StreamLoad($feHost, $db, $db, $password);
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


#### Set Submission Format
By default, JSON format is used for submission. You can set the format using the `format` method.
```php
$streamLoad = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $streamLoad->format(Format::CSV)->table('test_stream_load');
```


#### Memory Mode
* The default memory mode (internally submits data through files) is particularly suitable for submitting large amounts of data at once.
* When submitting small amounts of data, you can disable the constant memory mode by setting `constMemory` to `false`.
```php
$load = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $load->constMemory(false)->table('test_stream_load');
```


#### Set Parameters
You can set parameters using the `setHeader` method. Refer to the [official documentation](https://doris.apache.org/zh-CN/docs/data-operate/import/import-way/stream-load-manual) for Header parameters.

```php
$load = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $load->table('test_stream_load');
$builder->setHeader(\Doris\StreamLoad\Header::COLUMNS, 'user_id,name,age');
```


#### Asynchronous Mode

```php
$builder->setHeader(\Doris\StreamLoad\Header::GROUP_COMMIT, 'async_mode');
```


#### Submit Data via File
If you have a local CSV file, you can directly upload and import the data.

```php
$load = new StreamLoad('http://127.0.0.1:8040', 'test_db', 'root','');
$builder = $load->table('test_stream_load');
$builder->setHeader(\Doris\StreamLoad\Header::COLUMNS, 'user_id,name,age')
->file('/user/test.csv')
->load();
```


#### Usage in Hyperf Framework
The underlying system automatically detects the Hyperf runtime environment, so no additional configuration is required.
