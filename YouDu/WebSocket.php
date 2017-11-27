<?php

namespace YouDu;

use Workerman\MySQL;
use YouDu\Tool\Content;
use YouDu\Tool\Model;
use YouDu\Tool\Channel;

class WebSocket
{
    protected $model = null;
    protected $db = null;

    public function onMessage($connection, $message)
    {
        print("MSG: {$connection->worker->name} -- {$connection->worker->id} -- $connection->id -- $message \n");

        $data = json_decode($message, true);
        if (!is_array($data)) {
            echo "not a json \n";
            $connection->send(json_encode(['code' => 'error']) . "\n");
            return ;
        }

        if ($data['code'] == 'init') {
            $this->model->socket_in(2, $data['data']['id'], $connection->worker->id, $connection->id);
            $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
        } elseif ($data['code'] == 'msg') {
            $from = $this->model->socket_item(2, $connection->worker->id, $connection->id);
            $user_name = $this->db->select('name')->from('users')->where("id={$from['user_id']}")->single();
            $id = $data['data']['id'];
            if ($data['data']['chatType'] == 'p2p') {
                $maps = $this->db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
                $content = Content::msg('p2p', $from['user_id'], $user_name, $data['data']['time'], $data['data']['content']['body']);
                if (empty($maps)) {
                    $this->model->socket_msg_save($id, $content);
                } else {
                    foreach ($maps as $map) {
                        $type = $map['type'] == 1 ? 'tcp' : 'ws';
                        Channel::publish_p2p($type . '-p2p-' . $map['worker'], $map['connection'], $content);
                    }
                }
                $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
            } elseif ($data['data']['chatType'] == 'group') {
                $group_name = $this->db->select('name')->from('groups')->where("id={$id}")->single();
                $members = $this->db->select('user_id')->from('group_members')->where("group_id=$id")->column();
                $content = Content::msg('group', $from['user_id'], $user_name, $data['data']['time'],
                    $data['data']['content']['body'], $id, $group_name);
                foreach ($members as $member) {
                    $maps = $this->db->select('*')->from('socket_mapping')->where("user_id=$member")->query() ?: [];
                    if (empty($maps)) {
                        $this->model->socket_msg_save($member, $content);
                    } else {
                        foreach ($maps as $map) {
                            if ($map['user_id'] == $from['user_id']) {
                                continue;
                            }
                            $type = $map['type'] == 1 ? 'tcp' : 'ws';
                            Channel::publish_p2p($type . '-p2p-' . $map['worker'], $map['connection'], $content);
                        }
                    }
                }
                $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
            } else {
                $connection->send(json_encode(['code' => 'error']) . "\n");
            }
        } elseif ($data['code'] == 'notice') {
            $from = $this->db->select('*')->from('socket_mapping')
                ->where("type=2 and worker={$connection->worker->id} and connection=$connection->id")->row();
            $user_name = $this->db->select('name')->from('users')->where("id={$from['user_id']}")->single();
            $id = $data['data']['id'];
            $maps = $this->db->select('*')->from('socket_mapping')->where("user_id=$id")->query() ?: [];
            foreach ($maps as $map) {
                if ($data['data']['type'] == 'addFriend') {
                    $content = Content::notice('addFriend', $from['user_id'], $user_name, $data['data']['time'], $data['data']['content']);
                } elseif ($data['data']['type'] == 'responseFriend') {
                    $content = Content::notice('responseFriend', $from['user_id'], $user_name,
                        $data['data']['time'], $data['data']['content'], $data['data']['isAccept']);
                } else {
                    return ;
                }
                $type = $map['type'] == 1 ? 'tcp' : 'ws';
                Channel::publish_p2p($type . '-p2p-' . $map['worker'], $map['connection'], $content);
            }
        }
    }

    public function onWorkerStart($ws)
    {
        require_once 'Tool/config.php';
        $db_config = $config['database'];
        $this->db = new MySQL\Connection($db_config['host'], $db_config['port'], $db_config['name'], $db_config['psw'], $db_config['db']);
        $this->model = new Model($this->db);

        Channel::connect();
        Channel::listen_brocast('broadcast', $ws);
        Channel::listen_p2p('ws-p2p-' . $ws->id, $ws);
    }

    public function onConnect($connection)
    {
        echo "CONNECT: ws -- {$connection->worker->id} -- $connection->id \n";
        $connection->send(json_encode(['code' => 'response', 'data' => 'success']) . "\n");
    }

    public function onClose($connection)
    {
        $this->model->socket_out(2, $connection->worker->id, $connection->id);

        echo "CLOSED: ws -- {$connection->worker->id} -- $connection->id \n";
    }

    public function onWorkerStop()
    {
        $this->model->socket_trancate();
        echo "Worker stopping...\n";
    }
}
