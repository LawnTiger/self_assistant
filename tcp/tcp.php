<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

// 心跳间隔20秒
define('HEARTBEAT_TIME', 220);

$channel = new Channel\Server('127.0.0.1', 2206);

$tcp = new Worker("tcp://0.0.0.0:4000");
$tcp->count = 1;

// tcp
$tcp->onWorkerStart = function ($tcp) {
    Channel\Client::connect('127.0.0.1', 2206);
    global $db;
    $db = new Workerman\MySQL\Connection();

    Channel\Client::on('broadcast', function ($event_data) use ($tcp) {
        foreach ($tcp->connections as $con) {
            $con->send($event_data);
        }
    });

    // 订阅 p2p-id 事件并注册事件处理函数
    Channel\Client::on('p2p-' . $tcp->id, function ($event_data) use ($tcp) {
        $to_connection_id = $event_data['connection'];
        $message = $event_data['content'];
        if (!isset($tcp->connections[$to_connection_id])) {
            echo 'error connect id' . $to_connection_id;
            return ;
        }
        $tcp->connections[$to_connection_id]->send($message);
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
    global $tcp;
    global $db;

    print("worker: $tcp->id; connection: $connection->id Receive: $data \n");
    $connection->lastMessageTime = time();

    $data = json_decode($data, true);
    if ($data['code'] == 'init') {
        $db->insert('socket_mapping')->cols(array(
            'user_id' => $data['data']['id'],
            'worker' => $tcp->id,
            'connection' => $connection->id))
            ->query();
    } elseif ($data['code'] == 'msg') {
        $id = $data['data']['id'];
        $map = $db->select('*')->from('socket_mapping')->where("user_id=$id")->row();var_dump($map);
        if (!empty($map)) {
            Channel\Client::publish('p2p-' . $map['worker'], array(
                'connection' => $map['connection'],
                'content' => $data['data']['content']['body']
            ));
        }
    }
}

function handle_close($connection)
{
    global $tcp;
    global $db;
    $db->delete('socket_mapping')->where("connection=$connection->id and worker=$tcp->id")->query();

    echo "Connection : {$connection->id}  closed\n";
}

// Run worker
Worker::runAll();
