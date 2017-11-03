<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

// 心跳间隔20秒
define('HEARTBEAT_TIME', 220);

$channel = new Channel\Server('127.0.0.1', 2206);

$tcp = new Worker("tcp://0.0.0.0:4000");
$tcp->count = 2;

// tcp
$tcp->onWorkerStart = function ($tcp) {
    Channel\Client::connect('127.0.0.1', 2206);
    global $db;
    $db = new Workerman\MySQL\Connection();

    Channel\Client::on('broadcast', function ($event_data) use ($tcp) {
        foreach ($tcp->connections as $con) {
            $con->send($event_data . "\n");
        }
    });

    // 订阅 p2p-id 事件并注册事件处理函数
    Channel\Client::on('p2p-' . $tcp->id, function ($event_data) use ($tcp) {
        $to_connection_id = $event_data['connection'];
        $message = $event_data['content'];
        if (!isset($tcp->connections[$to_connection_id])) {
            echo 'error connect id' . $to_connection_id;
            return;
        }
        $tcp->connections[$to_connection_id]->send($message . "\n");
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

    print("worker: $tcp->id, connection: $connection->id Receive: $data \n");
    $connection->lastMessageTime = time();

    try {
        $data = json_decode($data, true);
    } catch (Exception $exception) {
        echo "not a json \n";
        $connection->send("not a json \n");
        return;
    }

    if ($data['code'] == 'init') {
        $db->insert('socket_mapping')->cols(array(
            'user_id' => $data['data']['id'],
            'worker' => $tcp->id,
            'connection' => $connection->id))
            ->query();
        $connection->send(json_encode(['code'=>'response','data'=>'success']) . "\n");
    } elseif ($data['code'] == 'msg') {
        if ($data['data']['chatType'] == 'p2p') {
            $id = $data['data']['id'];
            $map = $db->select('*')->from('socket_mapping')->where("user_id=$id")->row();
            $from = $db->select('*')->from('socket_mapping')->where("worker=$tcp->id and connection=$connection->id")->row();
            $user_name = $db->select('name')->from('users')->where("id={$from['user_id']}")->single();
            if (!empty($map)) {
                $content = array(
                    'code' => 'msg',
                    'data' => array(
                        'chatType' => 'p2p',
                        'groupId' => 0,
                        'userId' => $from['user_id'],
                        'userName' => $user_name,
                        'groupName' => '',
                        'content' => array(
                            'contentType' => 'txt',
                            'body' => $data['data']['content']['body']
                        )
                    )
                );
                Channel\Client::publish('p2p-' . $map['worker'], array(
                    'connection' => $map['connection'],
                    'content' => json_encode($content)
                ));
            }
            $connection->send(json_encode(['code'=>'response','data'=>'success']) . "\n");
        } elseif ($data['data']['chatType'] == 'group') {
            $group_id = $data['data']['id'];
            $members = $db->select('user_id')->from('group_members')->where("group_id=$group_id")->column();
            foreach ($members as $id) {
                $map = $db->select('*')->from('socket_mapping')->where("user_id=$id")->row();
                $from = $db->select('*')->from('socket_mapping')->where("worker=$tcp->id and connection=$connection->id")->row();
                $user_name = $db->select('name')->from('users')->where("id={$from['user_id']}")->single();
                $group_name = $db->select('name')->from('groups')->where("id={$data['data']['id']}")->single();
                if (!empty($map)) {
                    $content = array(
                        'code' => 'msg',
                        'data' => array(
                            'chatType' => 'group',
                            'groupId' => 0,
                            'userId' => $from['user_id'],
                            'userName' => $user_name,
                            'groupName' => $group_name,
                            'content' => array(
                                'contentType' => 'txt',
                                'body' => $data['data']['content']['body']
                            )
                        )
                    );
                    Channel\Client::publish('p2p-' . $map['worker'], array(
                        'connection' => $map['connection'],
                        'content' => json_encode($content)
                    ));
                }
            }
            $connection->send(json_encode(['code'=>'response','data'=>'success']) . "\n");
        } else {
            $connection->send("invalid code\n");
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
