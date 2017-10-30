<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

// 心跳间隔20秒
define('HEARTBEAT_TIME', 220);

$channel = new Channel\Server('127.0.0.1', 2206);

$tcp = new Worker("tcp://0.0.0.0:4000");
$tcp->count = 1;
$mapping = [];

// tcp
$tcp->onWorkerStart = function ($tcp) {
    Channel\Client::connect('127.0.0.1', 2206);
    global $mapping;

    Channel\Client::on('broadcast', function ($event_data) use ($tcp) {
        foreach ($tcp->connections as $con) {
            $con->send($event_data);
        }
    });

    // 订阅 p2p 事件并注册事件处理函数
    Channel\Client::on('p2p', function ($event_data) use ($tcp) {
        $to_connection_id = $event_data['to_connection_id'];
        $to_worker_id = $event_data['to_worker_id'];
        $message = $event_data['content'];
        if ($to_worker_id != $tcp->id || !isset($tcp->connections[$to_connection_id])) {
            return;
        }
        $to_connection = $tcp->connections[$to_connection_id];
        $to_connection->send($message);
    });


    // 心跳
    Timer::add(1, function () use ($tcp) {
        $time_now = time();
        foreach ($tcp->connections as $connection) {
            if (empty($connection->lastMessageTime)) {
                $connection->lastMessageTime = $time_now;
                continue;
            }
            // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
            if ($time_now - $connection->lastMessageTime > HEARTBEAT_TIME) {
                $connection->close();
            }
        }
    });
};


$tcp->onConnect = 'handle_connect';
$tcp->onMessage = 'handle_message';
$tcp->onClose = 'handle_close';

function handle_connect($connection)
{
    echo "connectionID: $connection->id\n";
    $connection->send("welcome to fu*king test room\n");
}

function handle_message($connection, $data)
{
    global $mapping;
    global $tcp;

    print("worker: $tcp->id; connection: $connection->id Receive: $data \n");
    $connection->lastMessageTime = time();

    $data = json_decode($data, true);
    if ($data['code'] == 'init') {
        $mapping[] = ['user' => $data['data']['id'], 'worker' => $tcp->id, 'connection' => $connection->id];
    } elseif ($data['code'] == 'msg') {
        $id = $data['data']['id'];
        $map = mapping(['id' => $id]);
        if (! empty($map)) {
            $to_worker_id = $map['worker'];
            $to_connection_id = $map['connection'];
            $content = $data['data']['content']['body'];

            Channel\Client::publish('p2p', array(
                'to_worker_id'     => $to_worker_id,
                'to_connection_id' => $to_connection_id,
                'content'          => $content
            ));
        }
    }
    print_r($mapping);
}

function handle_close($connection)
{
    global $mapping;
    global $tcp;

    echo "Connection : {$connection->id}  closed\n";

    foreach ($mapping as $key => $item) {
        if ($item['worker'] == $tcp->id && $item['connection'] == $connection->id) {
            unset($mapping[$key]);
        }
    }
    print_r($mapping);
}

function mapping($select)
{
    global $mapping;
    if (array_has($select, 'id')) {
        foreach ($mapping as $item) {
            if ($item['user'] == $select['id']) {
                return $item;
            }
        }
    }
}

// Run worker
Worker::runAll();
