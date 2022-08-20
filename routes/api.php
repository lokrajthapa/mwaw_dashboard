<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FlexCallController;
use App\Http\Controllers\FlexConferenceController;
use App\Http\Controllers\FlexRecordingController;
use App\Http\Controllers\FlexTaskController;
use App\Http\Controllers\FlexWebhookController;
use App\Http\Controllers\GmailWebhookController;
use App\Http\Controllers\IvrController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobNumberAlertController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/ivr', [IvrController::class, 'handle'])->name('ivr');

Route::get('/jobs/bySchedule', [JobController::class, 'bySchedule'])->name('jobs.bySchedule');
Route::post('sms/resend/{sms}', [SmsController::class, 'resendEmail'])->name('sms.resendEmail');

Route::post('flex/webhook/conference', [FlexWebhookController::class, 'handleConference'])
    ->name('flex.participantsWebhook');
Route::post('flex/webhook/recording', [FlexWebhookController::class, 'handleRecording'])
    ->name('flex.recordingsWebhook');


Route::post('flex/webhook', [FlexWebhookController::class, 'handle'])->name('flex.webhook');

Route::apiResource('/users', UserController::class);
Route::apiResource('/customers', CustomerController::class);
Route::apiResource('/jobs', JobController::class);
Route::apiResource('/statuses', StatusController::class);
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/subCategories', SubCategoryController::class);
Route::apiResource('/sms', SmsController::class);
Route::apiResource('/flexRecordings', FlexRecordingController::class);
Route::apiResource('/flexCalls', FlexCallController::class);
Route::apiResource('/flexTasks', FlexTaskController::class);
Route::apiResource('/flexConferences', FlexConferenceController::class);
Route::apiResource('/jobNumberAlerts', JobNumberAlertController::class);
Route::apiResource('/leads', LeadController::class);

Route::get('/vehicles/latestStatus', [VehicleController::class, 'latestStatus'])->name('vehicles.latestStatus');

Route::apiResource('/vehicles', VehicleController::class);

Route::post('/emails/programming/webhook', [GmailWebhookController::class, 'webhook']);

Route::post('/test-sms-client', function (Request $request) {
//    Log::debug('test sms client', $request->all());
    return response()->json(['message' => 'success']);
});
