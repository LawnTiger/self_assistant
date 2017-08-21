<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\SwooleRepositoriy;

class Swooole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swooole {manage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'manage swoole';

    protected $socket;
    private $swoole;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SwooleRepositoriy $swoole)
    {
        parent::__construct();
        $this->swoole = $swoole;
        \Cache::flush();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arg = $this->argument('manage');
        switch ($arg) {
            case 'start':
                $this->start();
                $this->info('swoole started');
                break;
            default:
                $this->info('waiting for develop');
                break;
        }
    }

    private function start()
    {
        $this->socket = new \swoole_websocket_server("0.0.0.0", 9501);

        $this->socket->on('request', [$this->swoole, 'onRequest']);
        $this->socket->on('open', [$this->swoole, 'onOpen']);
        $this->socket->on('message', [$this->swoole, 'onMessage']);
        $this->socket->on('close', [$this->swoole, 'onClose']);

        $this->socket->start();
    }
}
