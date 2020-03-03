<?php

use Illuminate\Http\Request;

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
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
    
    Route::resource('images', 'ImagesController');
    Route::post('uploadFile/{id}', 'ImagesController@uploadFile');
    Route::post('uploadAvatar/{id}', 'ImagesController@uploadAvatar');

    Route::get('/user','UsersController@getUserInfo');   

    Route::resource('company', 'CompanyController');
    Route::get('getCompanyValues','CompanyController@show');

    Route::resource('users', 'UsersController');
    Route::get('getUserValues','UsersController@edit');    
    Route::get('getDoctorlist','UsersController@getDoctors');
    Route::get('getRadiologistlist','UsersController@getRadiologist');
    Route::get('getDoctorDashboardData','UsersController@getDoctorDashboardData');
    
     //dashboard
    Route::get('getDashboardInfo','HomeController@getDashboardInfo');

    Route::get('getImagesByProcedure/{procedure_id}','ImagesController@getImagesByProcedure');    
    
    Route::resource('patients', 'PatientsController');
    Route::get('getPatientsValues','PatientsController@edit');    
    Route::get('getPatientlist','PatientsController@getPatients');

    Route::resource('procedures', 'ProceduresController');
    Route::get('getProceduresValues','ProceduresController@edit');    
    Route::get('getProcedureslist','ProceduresController@getProcedures');
    Route::get('getProceduresByPatientAndDoctor/{patient_id}','ProceduresController@getProceduresByPatientAndDoctor');

    Route::resource('points_levels', 'PointsLevelsController');
    Route::get('getPointsLevelslist','PointsLevelsController@getPointsLevels');
    
    
    //Route::get('getInventoryReports/{process_type}/{product_id}', 'InventoryController@getInventoryReports');

});

Route::group(['middleware' => 'guest:api'], function () {    
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});