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
            if (! empty($to)) {
                $ws->push($to,$receive['msg']);
            }
        }
        print_r(Cache::get('fds'));
        print_r(Cache::get('mapping'));
    }

    public function onClose($ws, $fd)
    {
        echo "client-{$fd} is closed\n";
        $GLOBALS = Cache::pull('fds');
        $mapping = Cache::get('mapping');
        $user_id = '';
        foreach($GLOBALS['fd'] as $key => $fds) {
            if ($fds[0] == $fd) {
                $user_id = $GLOBALS['fd'][$key];
                unset($GLOBALS['fd'][$key]);
            }
        }
        $key = array_search($user_id, $mapping);
        $mapping[$key] = '';
        Cache::forever('fds', $GLOBALS);
        Cache::forever('mapping', $mapping);
    }

    /**
     * cache::fds ->  fd => user_id
     * cache::mapping ->  fd => to_fd
     */
    private function chatInit($fd, $receive)
    {
        $GLOBALS = Cache::pull('fds');
        $GLOBALS['fd'][$fd] = $receive['from'];
        Cache::forever('fds', $GLOBALS);

        if (! empty($receive['to'])) {
            $to = $this->toId($receive['from'], $receive['to']);
            $key = array_search($to, $GLOBALS['fd']);
            $mapping = Cache::get('mapping') ?: [];
            $mapping[$fd] = $key;
            Cache::forever('mapping', $mapping);
        }
    }

    private function toId($user_id, $key)
    {
        $each = Friend::eachIds($key);

        return $each['user_id'] == $user_id ? $each['friend_id'] : $each['user_id'];
    }
}
