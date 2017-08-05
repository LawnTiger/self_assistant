<?php

namespace App\Repositories;

use Cache;
use App\Models\Friend;

class SwooleRepositoriy
{
    public function onOpen($ws, $request)
    {
        $GLOBALS = Cache::pull('fds') ?: [];
        $GLOBALS['fd'][$request->fd] = 0;
        Cache::forever('fds', $GLOBALS);
        echo "client  >> {$request->fd} <<  is connected\n";
    }

    public function onMessage($ws, $frame)
    {
        $GLOBALS = Cache::get('fds');
        $mapping = Cache::get('mapping');
        $receive = json_decode($frame->data, true);
        if ($receive['type'] == 'init') {
            $this->chatInit($frame->fd, $receive['data']);
        } else {
            $to = $mapping[$frame->fd];
            if (!is_array($to)) {
                $ws->push($to, $receive['msg']);
            } else {
                // 保存数据库
            }
        }
        print_r(Cache::get('fds'));
        print_r(Cache::get('mapping'));
    }

    public function onClose($ws, $fd)
    {
        echo "client-{$fd} is closed\n";
        $GLOBALS = Cache::get('fds');
        $mapping = Cache::get('mapping');
        if (array_key_exists($fd, $GLOBALS['fd'])) {
            unset($GLOBALS['fd'][$fd]);
        }

        if (!empty($mapping)) {
            $key = array_search($fd, $mapping);
            if ($key !== false) {
                $uid = $mapping[$key];
                $mapping[$key] = [$uid];
            }
            if (array_key_exists($fd, $mapping)) {
                unset($mapping[$fd]);
            }
            Cache::forever('mapping', $mapping);
        }
        Cache::forever('fds', $GLOBALS);
    }

    /*
     * cache::fds ->  fd => user_id
     * cache::mapping ->  fd => to_fd
     */
    private function chatInit($fd, $receive)
    {
        $GLOBALS = Cache::get('fds');
        $mapping = Cache::get('mapping') ?: [];

        $GLOBALS['fd'][$fd] = $receive['from'];
        Cache::forever('fds', $GLOBALS);

        $map_key = array_search([$receive['from']], $mapping);
        if ($map_key !== false) {
            $mapping[$map_key] = $fd;
        }

        if (!empty($receive['to'])) {
            $to = $this->toId($receive['from'], $receive['to']);
            $key = array_search($to, $GLOBALS['fd']);
            var_dump($key);
            if ($key === false) {
                $mapping[$fd] = [$to];
            } else {
                $mapping[$fd] = $key;
            }
            Cache::forever('mapping', $mapping);
        }
    }

    private function toId($user_id, $key)
    {
        $each = Friend::eachIds($key);

        return $each['user_id'] == $user_id ? $each['friend_id'] : $each['user_id'];
    }
}
