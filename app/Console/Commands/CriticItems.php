<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\NotificationsController;


class CriticItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'critic:items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar inventario de cada usuario y enviar notificación';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notifications = new NotificationsController;
        $notifications->notifications('WMah%-R-TZ@jJXPMccLF6+94%m2N9p');
        
    }
}
