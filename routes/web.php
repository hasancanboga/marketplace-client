<?php

use App\Services\OrderClient;
use App\Jobs\SaveOrderDetails;
use App\Jobs\UpdateOrderType;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    // Artisan::call('migrate:fresh');
    // $client = new OrderClient;
    // $order = $client->fetch(468250);
    // dump($order);
    // $order = $client->update($order);
    // dump($order);
});

Route::get('/', function () {

    // Artisan::call('migrate:fresh');

    // if (Queue::size() > 50) {
    //     return;
    // }
    // $orderClient = new OrderClient;

    // $orders = $orderClient->fetchAll();

    // foreach ($orders as $order) {
    //     SaveOrderDetails::dispatch($order['id']);
    // }

    return view('welcome');
});
