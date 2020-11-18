<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//pubisher Initialization
Route::get('publisherinit', 'PublisherController@initialization');
Route::get('publisherserve', 'PublisherController@publisherServe');




Route::get('userdetails', 'UserController@userdetails');
Route::get('regions', 'RegionController@index');

//retail
Route::post('retailorder', 'RetailOrderController@store');
Route::get('orders', 'RetailOrderController@index');

Route::post('agentstore', 'RetailController@agentstore');

Route::patch('login', 'UserController@update');
Route::post('storeuser', 'UserController@storeUser');
Route::post('storeagent', 'AgentController@store');
Route::post('storeretail', 'RetailController@store');
Route::post('storegroup', 'ClassgroupController@store');
Route::get('classgroups', 'ClassgroupController@index');
Route::post('series', 'SeriesController@store');
Route::get('publisher_series', 'SeriesController@publisherSeries');

Route::get('store_allitems', 'StoreController@getAllProducts');


Route::get('learnerstages', 'AdminController@showAllLearnerStage');
Route::get('checkToken', 'UserController@validToken');
Route::post('storelearnerstage', 'AdminController@storeLearnerStage');

Route::get('publishers', 'UserController@index');
Route::get('pendingpublishers', 'UserController@pendingUsers');
Route::post('respondpendingpublisher', 'UserController@fixUser');
Route::get('agents', 'AgentController@index');

Route::get('partners', 'UserController@partners');
Route::get('approvedpartners', 'UserController@approvedPartners');


//
Route::post('userrequest', 'UserController@userRequest');
Route::post('respondrequest', 'UserController@respondRequest');

//publisher





Route::apiResources([
    'subject' => 'SubjectController',
    'item' => 'ItemController',
    'paymentmethod' => 'PaymentMethodController',

]);
Route::group(['middleware'=>'auth:agent_api'],function(){

    Route::get('agentstock', 'AgentController@stocks');
    Route::get('retailerdetails/{id}', 'RetailController@retaildetails');
    Route::post('agentsalecalculator', 'AgentController@saleCalculator');
    Route::post('agentcreatesale', 'AgentController@createSale');
    Route::apiResources([
        'retailer' => 'RetailController',
        'retailpayment' => 'RetailPaymentController',
    ]);
});




Route::group(['middleware'=>'auth:api'],function(){
    Route::post('calculatesale', 'PublisherController@saleCalculator');
    Route::get('publisheritems', 'ItemController@publisherItem');
    Route::post('createsale', 'PublisherController@createSale');
    Route::post('agentpayments', 'AgentPaymentController@AgentPayments');
    Route::get('agentdetails/{id}', 'PublisherController@agentDetails');
    Route::apiResources([
        'subject' => 'SubjectController',
        'item' => 'ItemController',
        'publisherstock' => 'PublisherStockController',
        'agentpayment' => 'AgentPaymentController',
        'transact' => 'TransactController',
    ]);
});

