<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'config.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

// 心跳间隔20秒
define('HEARTBEAT_TIME', 220);

$channel = new Channel\Server('127.0.0.1', 2206);

$tcp = new Worker("tcp://0.0.0.0:4000");
$tcp->count = 2;
$ws = new Worker("websocket://0.0.0.0:4001");
$ws->count = 2;

// websocket
$ws->onWorkerStart = function ($ws) use ($config) {
    Channel\Client::connect('127.0.0.1', 2206);
    global $db;
    $db_config = $config['database'];
    $db = new Workerman\MySQL\Connection($db_config['host'], $db_config['port'], $db_config['name'], $db_config['psw'], $db_config['db']);

    Channel\Client::on('broadcast', function ($event_data) use ($ws) {
        foreach ($ws->connections as $con) {
            $con->send($event_data . "\n");
        }
    });

    // 订阅 ws-p2p-id 事件并注册事件处理函数
    Channel\Client::on('ws-p2p-' . $ws->id, function ($event_data) use ($ws) {
        $to_connection_id = $event_data['connection'];
        $message = $event_data['content'];
        if (!isset($ws->connections[$to_connection_id])) {
            echo "ERROR CONNECT: sw -- $ws->id -- $to_connection_id";
            return;
        }
        $ws->connections[$to_connection_id]->send($message . "\n");
    });
};

// tcp
$tcp->onWorkerStart = function ($tcp) use ($config) {
    Channel\Client::connect('127.0.0.1', 2206);
    global $db;
    $db_config = $config['database'];
    $db = new Workerman\MySQL\Connection($db_config['host'], $db_config['port'], $db_config['name'], $db_config['psw'], $db_config['db']);

    Channel\Client::on('broadcast', function ($event_data) use ($tcp) {
        foreach ($tcp->connections as $con) {
            $con->send($event_data . "\n");
        }
    });

    // 订阅 tcp-p2p-id 事件并注册事件处理函数
    Channel\Client::on('tcp-p2p-' . $tcp->id, function ($event_data) use ($tcp) {
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


$tcp->onConnect = function ($connection) use ($tcp) {
    echo "CONNECT: tcp -- $tcp->id -- $connection->id \n";
    $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
};

$tcp->onMessage = function ($connection, $data) use ($tcp) {
    global $db;

    print("MSG: tcp -- $tcp->id -- $connection->id -- $data \n");
    $connection->lastMessageTime = time();

    try {
        $data = json_decode($data, true);
    } catch (Exception $exception) {
        echo "not a json \n";
        $connection->send("not a json \n");
        return;
    }

    if ($data['code'] == 'init') {
        $db->delete('socket_mapping')->where("type=1 and user_id={$data['data']['id']}")->query();
        $db->insert('socket_mapping')->cols(array(
            'type' => 1,
            'user_id' => $data['data']['id'],
            'worker' => $tcp->id,
            'connection' => $connection->id))
            ->query();
        $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
    } elseif ($data['code'] == 'msg') {
        $from = $db->select('*')->from('socket_mapping')->where("type=1 and worker=$tcp->id and connection=$connection->id")->row();
        $user_name = $db->select('name')->from('users')->where("id={$from['user_id']}")->single();
        if ($data['data']['chatType'] == 'p2p') {
            $id = $data['data']['id'];
            $maps = $db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
            foreach ($maps as $map) {
                $type = $map['type'] == 1 ? 'tcp' : 'ws';
                $content = array(
                    'code' => 'msg',
                    'data' => array(
                        'chatType' => 'p2p',
                        'groupId' => 0,
                        'userId' => $from['user_id'],
                        'userName' => $user_name,
                        'groupName' => '',
                        'time' => $data['data']['time'],
                        'content' => array(
                            'contentType' => 'txt',
                            'body' => $data['data']['content']['body']
                        )
                    )
                );
                Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                    'connection' => $map['connection'],
                    'content' => json_encode($content)
                ));
            }
            $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
        } elseif ($data['data']['chatType'] == 'group') {
            $group_id = $data['data']['id'];
            $members = $db->select('user_id')->from('group_members')->where("group_id=$group_id")->column();
            foreach ($members as $id) {
                $maps = $db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
                $group_name = $db->select('name')->from('groups')->where("id={$data['data']['id']}")->single();
                foreach ($maps as $map) {
                    if ($map['user_id'] == $from['user_id']) {
                        continue ;
                    }
                    $type = $map['type'] == 1 ? 'tcp' : 'ws';
                    $content = array(
                        'code' => 'msg',
                        'data' => array(
                            'chatType' => 'group',
                            'groupId' => $group_id,
                            'userId' => $from['user_id'],
                            'userName' => $user_name,
                            'groupName' => $group_name,
                            'time' => $data['data']['time'],
                            'content' => array(
                                'contentType' => 'txt',
                                'body' => $data['data']['content']['body']
                            )
                        )
                    );
                    Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                        'connection' => $map['connection'],
                        'content' => json_encode($content)
                    ));
                }
            }
            $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
        } else {
            $connection->send("invalid code\n");
        }
    } elseif ($data['code'] == 'notice') {
        $from = $db->select('*')->from('socket_mapping')->where("type=1 and worker=$tcp->id and connection=$connection->id")->row();
        $user_name = $db->select('name')->from('users')->where("id={$from['user_id']}")->single();
        $id = $data['data']['id'];
        if ($data['data']['type'] == 'addFriend') {
            $maps = $db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
            foreach ($maps as $map) {
                $type = $map['type'] == 1 ? 'tcp' : 'ws';
                $content = array(
                    'code' => 'notice',
                    'data' => array(
                        'type' => 'addFriend',
                        'id' => $from['user_id'],
                        'name' => $user_name,
                        'time' => $data['data']['time'],
                        'content' => $data['data']['time']
                    )
                );
                Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                    'connection' => $map['connection'],
                    'content' => json_encode($content)
                ));
            }
        } elseif ($data['data']['type'] == 'responseFriend') {
            $maps = $db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
            foreach ($maps as $map) {
                $type = $map['type'] == 1 ? 'tcp' : 'ws';
                $content = array(
                    'code' => 'notice',
                    'data' => array(
                        'type' => 'responseFriend',
                        'isAccept' => $data['data']['isAccept'],
                        'id' => $from['user_id'],
                        'name' => $user_name,
                        'content' => $data['data']['content'],
                        'time' => $data['data']['time'],
                    )
                );
                Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                    'connection' => $map['connection'],
                    'content' => json_encode($content)
                ));
            }
        }
    }
};

$tcp->onClose = function ($connection) use ($tcp) {
    global $db;
    $db->delete('socket_mapping')->where("type=1 and connection=$connection->id and worker=$tcp->id")->query();

    echo "CLOSED: tcp -- $tcp->id -- $connection->id \n";
};


$ws->onConnect = function ($connection) use ($ws) {
    echo "CONNECT: ws -- $ws->id -- $connection->id \n";
    $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
};

$ws->onMessage = function ($connection, $data) use ($ws) {
    global $db;

    print("MSG: ws -- $ws->id -- $connection->id -- $data \n");

    $data = json_decode($data, true);
    if (!is_array($data)) {
        echo "not a json \n";
        $connection->send("not a json \n");
        return;
    }

    if ($data['code'] == 'init') {
        $db->delete('socket_mapping')->where("type=2 and user_id={$data['data']['id']}")->query();
        $db->insert('socket_mapping')->cols(array(
            'type' => 2,
            'user_id' => $data['data']['id'],
            'worker' => $ws->id,
            'connection' => $connection->id))
            ->query();
        $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
    } elseif ($data['code'] == 'msg') {
        $from = $db->select('*')->from('socket_mapping')->where("type=2 and worker=$ws->id and connection=$connection->id")->row();
        $user_name = $db->select('name')->from('users')->where("id={$from['user_id']}")->single();
        if ($data['data']['chatType'] == 'p2p') {
            $id = $data['data']['id'];
            $maps = $db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
            foreach ($maps as $map) {
                if ($map['user_id'] == $from['user_id']) {
                    continue ;
                }
                $type = $map['type'] == 1 ? 'tcp' : 'ws';
                $content = array(
                    'code' => 'msg',
                    'data' => array(
                        'chatType' => 'p2p',
                        'groupId' => 0,
                        'userId' => $from['user_id'],
                        'userName' => $user_name,
                        'groupName' => '',
                        'time' => $data['data']['time'],
                        'content' => array(
                            'contentType' => 'txt',
                            'body' => $data['data']['content']['body']
                        )
                    )
                );
                Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                    'connection' => $map['connection'],
                    'content' => json_encode($content)
                ));
            }
            $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
        } elseif ($data['data']['chatType'] == 'group') {
            $group_id = $data['data']['id'];
            $members = $db->select('user_id')->from('group_members')->where("group_id=$group_id")->column();
            foreach ($members as $id) {
                $maps = $db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
                $group_name = $db->select('name')->from('groups')->where("id={$data['data']['id']}")->single();
                foreach ($maps as $map) {
                    $type = $map['type'] == 1 ? 'tcp' : 'ws';
                    $content = array(
                        'code' => 'msg',
                        'data' => array(
                            'chatType' => 'group',
                            'groupId' => $group_id,
                            'userId' => $from['user_id'],
                            'userName' => $user_name,
                            'groupName' => $group_name,
                            'time' => $data['data']['time'],
                            'content' => array(
                                'contentType' => 'txt',
                                'body' => $data['data']['content']['body']
                            )
                        )
                    );
                    Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                        'connection' => $map['connection'],
                        'content' => json_encode($content)
                    ));
                }
            }
            $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
        } else {
            $connection->send("invalid code\n");
        }
    } elseif ($data['code'] == 'notice') {
        $from = $db->select('*')->from('socket_mapping')->where("type=2 and worker=$ws->id and connection=$connection->id")->row();
        $user_name = $db->select('name')->from('users')->where("id={$from['user_id']}")->single();
        $id = $data['data']['id'];
        if ($data['data']['type'] == 'addFriend') {
            $maps = $db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
            foreach ($maps as $map) {
                $type = $map['type'] == 1 ? 'tcp' : 'ws';
                $content = array(
                    'code' => 'notice',
                    'data' => array(
                        'type' => 'addFriend',
                        'id' => $from['user_id'],
                        'name' => $user_name,
                        'time' => $data['data']['time'],
                        'content' => $data['data']['content']
                    )
                );
                Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                    'connection' => $map['connection'],
                    'content' => json_encode($content)
                ));
            }
        } elseif ($data['data']['type'] == 'responseFriend') {
            $maps = $db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
            foreach ($maps as $map) {
                $type = $map['type'] == 1 ? 'tcp' : 'ws';
                $content = array(
                    'code' => 'notice',
                    'data' => array(
                        'type' => 'responseFriend',
                        'isAccept' => $data['data']['isAccept'],
                        'id' => $from['user_id'],
                        'name' => $user_name,
                        'content' => $data['data']['content'],
                        'time' => $data['data']['time'],
                    )
                );
                Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                    'connection' => $map['connection'],
                    'content' => json_encode($content)
                ));
            }
        }
    }
};

$ws->onClose = function ($connection) use ($ws) {
    global $db;
    $db->delete('socket_mapping')->where("type=2 and connection=$connection->id and worker=$ws->id")->query();

    echo "CLOSED: ws -- $ws->id -- $connection->id \n";
};


// Run worker
Worker::runAll();
