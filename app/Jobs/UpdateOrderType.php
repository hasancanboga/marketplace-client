<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;

class UpdateOrderType implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    
    public $tries = 20;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderClient $orderClient)
    {
        logger('updating order ' . $this->order->id);

        $orderClient->update($this->order);
    }
    
    public function middleware()
    {
        return [new RateLimitedWithRedis('marketplace_client')];
    }
}
