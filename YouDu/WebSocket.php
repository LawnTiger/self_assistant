<?php

namespace Youdu;

use Workerman\Worker;
use Workerman\Channel;
use Workerman\MySQL;
use Youdu\Tool\Content;
use Youdu\Tool\Model;

class WebSocket
{
    protected $model = null;

    public function onWorkerStart($ws)
    {
        require_once 'Tool/config.php';
        $db_config = $config['database'];
        $db = new MySQL\Connection($db_config['host'], $db_config['port'], $db_config['name'], $db_config['psw'], $db_config['db']);
        $this->model = new Model($db);

        \Channel\Client::connect('127.0.0.1', 2206);
        \Channel\Client::on('broadcast', function ($event_data) use ($ws) {
            foreach ($ws->connections as $con) {
                $con->send($event_data . "\n");
            }
        });

        // 订阅 ws-p2p-id 事件并注册事件处理函数
        \Channel\Client::on('ws-p2p-' . $ws->id, function ($event_data) use ($ws) {
            $to_connection_id = $event_data['connection'];
            $message = $event_data['content'];
            if (!isset($ws->connections[$to_connection_id])) {
                echo "ERROR CONNECT: sw -- $ws->id -- $to_connection_id";
                return;
            }
            $ws->connections[$to_connection_id]->send($message . "\n");
        });
    }

    public function onConnect($connection)
    {
        echo "CONNECT: ws -- {$connection->worker->id} -- $connection->id \n";
        $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
    }

    public function onClose($connection)
    {
        $this->db->delete('socket_mapping')
            ->where("type=2 and connection=$connection->id and worker={$connection->worker->id}")->query();

        echo "CLOSED: ws -- {$connection->worker->id} -- $connection->id \n";
    }

    public function onWorkerStop()
    {
        $this->db->delete('socket_mapping')->where('1=1')->query();
        echo "Worker stopping...\n";
    }

    public function onMessage($connection, $message)
    {
        print("MSG: ws -- {$connection->worker->id} -- $connection->id -- $message \n");

        $data = json_decode($message, true);
        if (!is_array($data)) {
            echo "not a json \n";
            $connection->send("not a json \n");
            return;
        }

        if ($data['code'] == 'init') {
            $this->model->socket_in(2, $data['data']['id'], $connection->worker->id, $connection->id);
            $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
        } elseif ($data['code'] == 'msg') {
            $from = $this->model->socket_item(2, $connection->worker->id, $connection->id);
            $user_name = $this->db->select('name')->from('users')->where("id={$from['user_id']}")->single();
            if ($data['data']['chatType'] == 'p2p') {
                $id = $data['data']['id'];
                $maps = $this->db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
                if (empty($maps)) {
                    save_message($id, $message);
                } else {
                    foreach ($maps as $map) {
                        if ($map['user_id'] == $from['user_id']) {
                            continue;
                        }
                        $type = $map['type'] == 1 ? 'tcp' : 'ws';
                        $content = Content::msg('p2p', $from['user_id'], $user_name, $data['data']['time'], $data['data']['content']['body']);
                        \Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                            'connection' => $map['connection'],
                            'content' => json_encode($content)
                        ));
                    }
                }
                $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
            } elseif ($data['data']['chatType'] == 'group') {
                $group_id = $data['data']['id'];
                $group_name = $this->db->select('name')->from('groups')->where("id={$data['data']['id']}")->single();
                $members = $this->db->select('user_id')->from('group_members')->where("group_id=$group_id")->column();
                foreach ($members as $id) {
                    $maps = $this->db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
                    if (empty($maps)) {
                        save_message($id, $message);
                    } else {
                        foreach ($maps as $map) {
                            if ($map['user_id'] == $from['user_id']) {
                                continue;
                            }
                            $type = $map['type'] == 1 ? 'tcp' : 'ws';
                            $content = Content::msg('group', $from['user_id'], $user_name, $data['data']['time'],
                                $data['data']['content']['body'], $group_id, $group_name);
                            \Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                                'connection' => $map['connection'],
                                'content' => json_encode($content)
                            ));
                        }
                    }
                }
                $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
            } else {
                $connection->send("invalid code\n");
            }
        } elseif ($data['code'] == 'notice') {
            $from = $this->db->select('*')->from('socket_mapping')
                ->where("type=2 and worker={$connection->worker->id} and connection=$connection->id")->row();
            $user_name = $this->db->select('name')->from('users')->where("id={$from['user_id']}")->single();
            $id = $data['data']['id'];
            if ($data['data']['type'] == 'addFriend') {
                $maps = $this->db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
                foreach ($maps as $map) {
                    $type = $map['type'] == 1 ? 'tcp' : 'ws';
                    $content = Content::notice('addFriend', $from['user_id'], $user_name, $data['data']['time'], $data['data']['content']);
                    \Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                        'connection' => $map['connection'],
                        'content' => json_encode($content)
                    ));
                }
            } elseif ($data['data']['type'] == 'responseFriend') {
                $maps = $this->db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
                foreach ($maps as $map) {
                    $type = $map['type'] == 1 ? 'tcp' : 'ws';
                    $content = Content::notice('responseFriend', $from['user_id'], $user_name,
                        $data['data']['time'], $data['data']['content'], $data['data']['isAccept']);
                    \Channel\Client::publish($type . '-p2p-' . $map['worker'], array(
                        'connection' => $map['connection'],
                        'content' => json_encode($content)
                    ));
                }
            }
        }
    }
}
