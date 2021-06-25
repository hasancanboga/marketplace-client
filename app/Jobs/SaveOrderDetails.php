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

class SaveOrderDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderClient $orderClient)
    {
        logger('saving order ' . $this->orderId);
        $order = $orderClient->fetch($this->orderId);

        $orderClient->store($order);

        UpdateOrderType::dispatch((object) ['id' => $this->orderId]);
    }

    public function middleware()
    {
        return [new RateLimitedWithRedis('save_order_details')];
    }
}
