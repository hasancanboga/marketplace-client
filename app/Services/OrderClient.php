<?php

namespace App\Services;

use Exception;
use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Jobs\UpdateOrderType;
use App\Jobs\SaveOrderDetails;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;

class OrderClient
{
    protected $retried = false;

    public function run()
    {
        if (Queue::size() >= 50) {
            return;
        }

        $orders = $this->fetchAll();

        foreach ($orders as $order) {
            $emptyOrder = $this->createEmptyOrder($order);
            SaveOrderDetails::dispatch($emptyOrder);
        }
    }

    public function fetchAll()
    {
        $params = ['api_key' => config('services.marketplace.key')];

        if ($latestOrder = Order::latest()->first()) {
            $params['id'] = $latestOrder->id;
        } else {
            $params['date'] = now()->subMinute()->toDateTimeString();
        }

        $response = Http::log()->get(
            'https://sample-market.despatchcloud.uk/api/orders',
            $params
        );

        if ($response->failed() && !$this->retried) {
            sleep(10);
            $this->retried = true;
            $this->run();
        }

        return array_reverse($response->json()['data']);
    }

    public function fetch(Order $order)
    {
        $response = Http::log()->get(
            "https://sample-market.despatchcloud.uk/api/orders/" . $order->id,
            [
                'api_key' => config('services.marketplace.key'),
            ]
        );

        if ($response->failed()) {
            throw new Exception;
        }

        $order = $this->saveDetails($response->json());
        UpdateOrderType::dispatch($order);
        return $order;
    }

    public function update(Order $order)
    {
        $response = Http::log()->asForm()->post(
            "https://sample-market.despatchcloud.uk/api/orders/" . $order->id,
            [
                'api_key' => config('services.marketplace.key'),
                'type' => 'approved'
            ]
        );

        if ($response->failed()) {
            throw new Exception;
        }

        return $order->approve();
    }

    public function saveDetails(array $order)
    {
        Customer::updateOrCreate(
            $order['customer']
        );

        Address::updateOrCreate(
            $order['shipping_address']
        );

        Address::updateOrCreate(
            $order['billing_address']
        );

        foreach ($order['order_items'] as $orderItem) {
            Product::updateOrCreate($orderItem['product']);
        }

        Order::find($order['id'])->update([
            'customer_id' => $order['customer_id'],
            'shipping_address_id' => $order['shipping_address_id'],
            'billing_address_id' => $order['billing_address_id'],
        ]);

        $orderItems = [];
        foreach ($order['order_items'] as $orderItem) {
            unset($orderItem['product']);
            $orderItems[] = OrderItem::create($orderItem);
        }

        return Order::find($order['id']);
    }

    public function createEmptyOrder($order)
    {
        return Order::create(
            collect($order)->filter(function ($item, $key) {
                return $key !== 'customer_id'
                    && $key !== 'billing_address_id'
                    && $key !== 'shipping_address_id';
            })->toArray()
        );
    }
}
