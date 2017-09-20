<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\User;
use Cache;

class SwooleRepository
{
    public function onOpen($ws, $request)
    {
        echo "client  >> {$request->fd} <<  is connected\n";
    }

    public function onMessage($ws, $frame)
    {
        $receive = json_decode($frame->data, true);

        if ($receive['type'] == 'init') {
            $this->chatInit($receive['data']['id'], $frame->fd);
        } elseif ($receive['type'] == 'send') {
            $user_id = $this->mapping_get('fd', $frame->fd);
            $user = User::find($user_id);
            $to_id = $receive['data']['to'];
            $to_fd = $this->mapping_get('user', $to_id);

            if (isset($receive['data']['msg'])) {
                $message = $receive['data']['msg'];
                $send = json_encode(['from' => $user_id, 'name' => $user->name, 'msg' => $message]);
                if ($to_fd) {
                    $ws->push($to_fd, $send);
                } else {
                    Message::create(['from_id' => $user_id, 'to_id' => $to_id, 'message' => $message]);
                }
            } elseif (!isset($receive['data']['msg']) && !empty($to_fd)) {
                $ws->push($to_fd, json_encode(['type' => 'add']));
            }

        } else {
            print_r('json_error\n');
        }

        print_r(Cache::get('mapping'));
    }

    public function onClose($ws, $fd)
    {
        echo "client-{$fd} is closed\n";
        $this->chatOut($fd);
    }

    private function chatInit($id, $fd)
    {
        $mapping = Cache::get('mapping');
        $mapping[] = ['user' => $id, 'fd' => $fd];
        Cache::forever('mapping', $mapping);
    }

    private function chatOut($fd)
    {
        $mapping = Cache::get('mapping');
        foreach ($mapping as $key => $pair) {
            if ($pair['fd'] == $fd) {
                unset($mapping[$key]);
            }
        }
        Cache::forever('mapping', $mapping);
    }

    /**
     * @param $key string 'fd' or 'user'
     * @param $value id
     * @return int
     */
    private function mapping_get($key, $value)
    {
        $key2 = $key == 'user' ? 'fd' : 'user';
        $mapping = Cache::get('mapping');
        foreach ($mapping as $k => $v) {
            if ($v[$key] == $value) {
                return $v[$key2];
            }
        }
    }
}
