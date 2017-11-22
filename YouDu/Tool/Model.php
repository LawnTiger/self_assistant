<?php

namespace Youdu\Tool;

class Model
{
    protected $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function socket_in($type, $id, $worker, $connection)
    {
        $this->db->delete('socket_mapping')->where("type=2 and user_id={$id}")->query();
        $this->db->insert('socket_mapping')->cols(array(
            'type' => $type,
            'user_id' => $id,
            'worker' => $worker,
            'connection' => $connection))
            ->query();
    }

    public function socket_out($type, $worker, $connection)
    {
        $this->db->delete('socket_mapping')
            ->where("type=$type and connection=$connection and worker={$worker}")->query();
    }

    public function socket_trancate()
    {
        $this->db->delete('socket_mapping')->where('1=1')->query();
    }

    public function socket_item($type, $worker, $connection)
    {
        return $this->db->select('*')->from('socket_mapping')
            ->where("type={$type} and worker={$worker} and connection={$connection}")->row();
    }

    public function socket_msg_save($id, $message)
    {
        $this->db->insert('socket_msg')->cols(['user_id' => $id, 'msg' => $message])->query();
    }
}
