<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderClient;
use App\Jobs\SaveOrderDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;

class FetchOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch orders by date or id';

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
     * @return int
     */
    public function handle(OrderClient $orderClient)
    {
        $orderClient->run();
    }
}
