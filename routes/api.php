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

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');
    
    Route::resource('images', 'ImagesController');
    Route::post('uploadFile/{id}', 'ImagesController@uploadFile');
    Route::get('getImagesByPatient/{patient_id}','ImagesController@getImagesByPatient');
    Route::get('getImagesSizeById/{Id}','ImagesController@getUsedStorage'); 

    Route::post('uploadAvatar/{id}', 'UsersController@uploadAvatar');
    Route::post('updatePassword/{password}', 'UsersController@updatePassword');

    Route::get('/user','UsersController@getUserInfo');   
    
    Route::get('get_CommentsByImageId/{id}','CommentsController@get_CommentsByImageId'); 
    Route::resource('comments', 'CommentsController');

    Route::resource('chats', 'ChatsController');
    Route::get('getAllDoctorsChats','ChatsController@getAllDoctorsChats');   
    Route::get('getAdminAndDoctorChats','ChatsController@getAdminAndDoctorChats');       

    Route::resource('company', 'CompanyController');
    Route::get('getCompanyValues','CompanyController@show');

    Route::resource('users', 'UsersController');
    Route::get('getUserValues','UsersController@edit');    
    Route::get('getDoctorlist','UsersController@getDoctors');
    Route::get('getRadiologistlist','UsersController@getRadiologist');
    Route::get('getDoctorDashboardData','UsersController@getDoctorDashboardData');
    
    Route::get('getPatientDashboardData','UsersController@getPatientDashboardData');    

    Route::get('getNotificationList/{profileName}','TrackerController@getNotificationList');    
    Route::post('markNotificationAsRead/{id}', 'TrackerController@markNotificationAsRead');

    Route::post('redemptPoint/{product_id}', 'PointsRedemptionController@redemptPoint');
    Route::get('getProductRedemptionHistory', 'PointsRedemptionController@getProductRedemptionHistory');    
    
    Route::resource('products', 'ProductsController');
    Route::get('getProductValues','ProductsController@edit');   
    Route::get('getProductList','ProductsController@getProducts');
    
     //dashboard
    Route::get('getDashboardInfo','HomeController@getDashboardInfo');
         
    Route::resource('patients', 'PatientsController');
    Route::get('getPatientsValues','PatientsController@edit');    
    Route::get('getPatientlist','PatientsController@getPatients');
    Route::get('getPatientsAndDoctors','PatientsController@getPatientsAndDoctors');    

    Route::resource('procedures', 'ProceduresController');
    Route::get('getProceduresValues','ProceduresController@edit');    
    Route::get('getProcedureslist','ProceduresController@getProcedures');
    Route::get('getProceduresByPatientAndDoctor/{patient_id}/{doctor_id?}','ProceduresController@getProceduresByPatientAndDoctor');

    Route::resource('points_levels', 'PointsLevelsController');
    Route::get('getPointsLevelslist','PointsLevelsController@getPointsLevels');
    Route::get('getPointsSummaryByDoctor','PointsLevelsController@getPointsSummaryByDoctor');
    Route::put('store_NewPoints','PointsLevelsController@store_NewPoints');
    Route::post('confirmCoupon/{id}', 'PointsLevelsController@confirmCoupon');
    Route::post('rejectCoupon/{id}', 'PointsLevelsController@rejectCoupon');


});

Route::group(['middleware' => 'guest:api'], function () {    
    Route::post('login', 'Auth\LoginController@login');
    //Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::post('password/reset_password','Auth\ResetPasswordController@reset_password'); 
});