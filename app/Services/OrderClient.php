<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OrderClient
{
    public function fetchAll()
    {
        $response = Http::get('https://sample-market.despatchcloud.uk/api/orders', [
            'api_key' => config('services.marketplace.key'),
            'date' => now(),
        ]);

        if ($response->successful()) {
            return $response->json()['data'];
        }
    }

    public function fetch($id)
    {
        $response = Http::get("https://sample-market.despatchcloud.uk/api/orders/{$id}", [
            'api_key' => config('services.marketplace.key'),
        ]);
        
        if ($response->failed()) {
            throw new Exception;
        }

        return $this->store($response->json());
    }

    public function update(Order $order)
    {
        $response = Http::post("https://sample-market.despatchcloud.uk/api/orders/" . $order->id, [
            'api_key' => config('services.marketplace.key'),
            'type' => 'approved'
        ]);

        if ($response->failed()) {
            throw new Exception;
        }

        return $order->setApproved();
    }

    public function store(array $order)
    {
        Customer::create(
            $order['customer']
        );

        Address::create(
            $order['shipping_address']
        );

        Address::create(
            $order['billing_address']
        );

        foreach ($order['order_items'] as $orderItem) {
            Product::create($orderItem['product']);
        }

        Order::create(
            collect($order)->filter(function ($item, $key) {
                return $key !== 'customer'
                    && $key !== 'billing_address'
                    && $key !== 'shipping_address'
                    && $key !== 'order_items';
            })->toArray()
        );

        $orderItems = [];
        foreach ($order['order_items'] as $orderItem) {
            unset($orderItem['product']);
            $orderItems[] = OrderItem::create($orderItem);
        }

        return Order::find($order['id']);
    }
}
