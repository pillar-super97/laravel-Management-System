<?php





//Route::get('/', 'HomeController@index');

Route::resource('json-report', 'JsonReportController');

Route::get('/', 'Auth\LoginController@showLoginForm')->name('auth.login');


Route::get('filterlist', FilterList::class);


Route::get('get-state-list','AjaxController@getStateList');
Route::get('get-city-list','AjaxController@getCityList');
Route::get('get-crew-leader-list','AjaxController@getCrewLeader');

Route::get('updateFlaggedTimesheet', 'Admin\EmployeesController@updateFlaggedTimesheet')->name('updateFlaggedTimesheet');
//Route::get('importEmployees1', 'Admin\EmployeesController@importEmployees1')->name('importEmployees1');

Route::get('lesson/{course_id}/{slug}', ['uses' => 'LessonsController@show', 'as' => 'lessons.show']);
Route::post('lesson/{slug}/test', ['uses' => 'LessonsController@test', 'as' => 'lessons.test']);

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('auth.login');
Route::post('login', 'Auth\LoginController@login')->name('auth.login');
Route::post('logout', 'Auth\LoginController@logout')->name('auth.logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('auth.register');
Route::post('register', 'Auth\RegisterController@register')->name('auth.register');

Route::match(['get', 'post'],'users/invite/{uuid}', ['uses' =>'Auth\RegisterController@invitation', 'as' => 'accept_invitation']);
   


// Change Password Routes...
Route::get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
Route::patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');

Route::get('testcron', 'Admin\TimesheetsController@testcron');
Route::get('export_cost_center_to_kronos', 'Admin\StoresController@export_cost_center_to_kronos');
Route::get('export_schedules_to_kronos', 'Admin\EmployeesController@export_schedules_to_kronos');
Route::get('sync_schedules_to_kronos', 'Admin\EmployeesController@sync_schedules_to_kronos');
Route::get('importEmployees', 'Admin\EmployeesController@importEmployees')->name('importEmployees');
Route::get('import_time_entries', 'Admin\TimesheetsController@import_time_entries')->name('timesheets.import_time_entries');
Route::get('import_time_entries_manually', 'Admin\TimesheetsController@import_time_entries_manually')->name('timesheets.import_time_entries_manually');
Route::get('import_inventory_evaluation', 'Admin\TimesheetsController@import_inventory_evaluation')->name('timesheets.import_inventory_evaluation');
Route::get('import_gap_report', 'Admin\TimesheetsController@import_gap_report')->name('timesheets.import_gap_report');
Route::get('export_timesheet_to_kronos', 'Admin\TimesheetsController@export_to_kronos');
Route::get('admin/get-timeentries-status', 'Admin\TimesheetsController@timeentries_status');
Route::get('calculate_benchmark', 'Admin\EmployeesController@calculate_benchmark');
Route::get('calculate_old_benchmark', 'Admin\EmployeesController@calculate_old_benchmark');
Route::get('validate_mdb', 'Admin\EventsController@validateMdb');
Route::get('import_from_mdb', 'Admin\EventsController@importFromMdb');
Route::get('unscheduled_stores_notification', 'Admin\NotificationsController@unscheduled_stores');

Route::get('calculate_vehicle_travel/{from}/{to}', 'Admin\TimesheetsController@calculate_vehicle_travel');

Route::get('export_to_kronos_manually', 'Admin\TimesheetsController@export_to_kronos_manually');

Route::group(['middleware' => ['admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::get('/log/{channel}', 'Admin\LogController')->name('log');
    Route::get('/home', 'Admin\DashboardController@index');
    
    Route::resource('permissions', 'Admin\PermissionsController');
    Route::post('permissions_mass_destroy', ['uses' => 'Admin\PermissionsController@massDestroy', 'as' => 'permissions.mass_destroy']);
    Route::post('permissions_restore/{id}', ['uses' => 'Admin\PermissionsController@restore', 'as' => 'permissions.restore']);
    Route::delete('permissions_perma_del/{id}', ['uses' => 'Admin\PermissionsController@perma_del', 'as' => 'permissions.perma_del']);
    
    Route::resource('roles', 'Admin\RolesController');
    Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);
    Route::post('roles_restore/{id}', ['uses' => 'Admin\RolesController@restore', 'as' => 'roles.restore']);
    Route::delete('roles_perma_del/{id}', ['uses' => 'Admin\RolesController@perma_del', 'as' => 'roles.perma_del']);
    
    Route::match(['get', 'post'], 'users/invite/client', ['uses' =>'Admin\UsersController@getClients', 'as'=> 'users.invite.select_client']);
    Route::match(['get', 'post'],'users/invite/client/{id}', ['uses' =>'Admin\UsersController@inviteClient', 'as' => 'users.invite_client']);
    
    Route::resource('users', 'Admin\UsersController');
    
    Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
    Route::post('users_restore/{id}', ['uses' => 'Admin\UsersController@restore', 'as' => 'users.restore']);
    Route::delete('users_perma_del/{id}', ['uses' => 'Admin\UsersController@perma_del', 'as' => 'users.perma_del']);
    
    Route::resource('lessons', 'Admin\LessonsController');
    Route::post('lessons_mass_destroy', ['uses' => 'Admin\LessonsController@massDestroy', 'as' => 'lessons.mass_destroy']);
    Route::post('lessons_restore/{id}', ['uses' => 'Admin\LessonsController@restore', 'as' => 'lessons.restore']);
    Route::delete('lessons_perma_del/{id}', ['uses' => 'Admin\LessonsController@perma_del', 'as' => 'lessons.perma_del']);
    
    Route::resource('associations', 'Admin\AssociationsController');
    Route::post('associations_mass_destroy', ['uses' => 'Admin\AssociationsController@massDestroy', 'as' => 'associations.mass_destroy']);
    Route::post('associations_restore/{id}', ['uses' => 'Admin\AssociationsController@restore', 'as' => 'associations.restore']);
    Route::delete('associations_perma_del/{id}', ['uses' => 'Admin\AssociationsController@perma_del', 'as' => 'associations.perma_del']);
    
    Route::resource('clients', 'Admin\ClientsController');



    Route::post('clients_mass_destroy', ['uses' => 'Admin\ClientsController@massDestroy', 'as' => 'clients.mass_destroy']);
    Route::post('clients_restore/{id}', ['uses' => 'Admin\ClientsController@restore', 'as' => 'clients.restore']);
    Route::delete('clients_perma_del/{id}', ['uses' => 'Admin\ClientsController@perma_del', 'as' => 'clients.perma_del']);
    Route::post('getClientByAssociation', 'Admin\ClientsController@getClientByAssociation')->name('clients.getClientByAssociation');


    



    
    Route::resource('divisions', 'Admin\DivisionsController');
    Route::post('divisions_mass_destroy', ['uses' => 'Admin\DivisionsController@massDestroy', 'as' => 'divisions.mass_destroy']);
    Route::post('divisions_restore/{id}', ['uses' => 'Admin\DivisionsController@restore', 'as' => 'divisions.restore']);
    Route::delete('divisions_perma_del/{id}', ['uses' => 'Admin\DivisionsController@perma_del', 'as' => 'divisions.perma_del']);
    
    Route::resource('districts', 'Admin\DistrictsController');
    Route::post('districts_mass_destroy', ['uses' => 'Admin\DistrictsController@massDestroy', 'as' => 'districts.mass_destroy']);
    Route::post('districts_restore/{id}', ['uses' => 'Admin\DistrictsController@restore', 'as' => 'districts.restore']);
    Route::delete('districts_perma_del/{id}', ['uses' => 'Admin\DistrictsController@perma_del', 'as' => 'districts.perma_del']);
    //Route::post('getdistrictautofilldata', 'Admin\DistrictsController@getdistrictautofilldata')->name('districts.getdistrictautofilldata');
    //Route::post('getDivisionByClient', ['uses' => 'Admin\DistrictsController@getDivisionByClient', 'as' => 'districts.getDivisionByClient']);
    Route::post('getDivisionByClient', 'Admin\DistrictsController@getDivisionByClient')->name('districts.getDivisionByClient');
    
    Route::resource('areas', 'Admin\AreasController');
    Route::post('areas_mass_destroy', ['uses' => 'Admin\AreasController@massDestroy', 'as' => 'areas.mass_destroy']);
    Route::post('areas_restore/{id}', ['uses' => 'Admin\AreasController@restore', 'as' => 'areas.restore']);
    Route::delete('areas_perma_del/{id}', ['uses' => 'Admin\AreasController@perma_del', 'as' => 'areas.perma_del']);
    
    Route::resource('areas.jsa', 'Admin\JSAController');
    Route::post('mass_destroy', ['uses' => 'Admin\JSAController@massDestroy', 'as' => 'areas.jsa.mass_destroy']);
    Route::post('jsa_restore/{area_id}/{id}', ['uses' => 'Admin\JSAController@restore', 'as' => 'areas.jsa.restore']);
    Route::delete('jsa_perma_del/{area_id}/{id}', ['uses' => 'Admin\JSAController@perma_del', 'as' => 'areas.jsa.perma_del']);
    
    Route::resource('stores', 'Admin\StoresController');
    Route::post('stores_mass_destroy', ['uses' => 'Admin\StoresController@massDestroy', 'as' => 'stores.mass_destroy']);
    Route::post('stores_restore/{id}', ['uses' => 'Admin\StoresController@restore', 'as' => 'stores.restore']);
    Route::delete('stores_perma_del/{id}', ['uses' => 'Admin\StoresController@perma_del', 'as' => 'stores.perma_del']);
    Route::post('getDistrictByDivision', 'Admin\StoresController@getDistrictByDivision')->name('stores.getDistrictByDivision');
    Route::post('getJSAByArea', 'Admin\StoresController@getJSAByArea')->name('stores.getJSAByArea');
    Route::post('stores/get_store_list_by_page', ['uses' => 'Admin\StoresController@get_store_list_by_page', 'as' => 'stores.get_store_list_by_page']);
    
    Route::resource('blackoutdates', 'Admin\BlackoutdatesController');
    Route::post('blackoutdates/get_list_by_page', ['uses' => 'Admin\BlackoutdatesController@get_list_by_page', 'as' => 'blackoutdates.get_list_by_page']);
    Route::post('blackoutdates/validateBlackoutdateImportExcel', ['uses' => 'Admin\BlackoutdatesController@validateBlackoutdateImportExcel', 'as' => 'blackoutdates.validateBlackoutdateImportExcel']);
    
    Route::resource('mileages', 'Admin\MileagesController');
    Route::post('mileages_mass_destroy', ['uses' => 'Admin\MileagesController@massDestroy', 'as' => 'mileages.mass_destroy']);
    Route::post('mileages_restore/{id}', ['uses' => 'Admin\MileagesController@restore', 'as' => 'mileages.restore']);
    Route::delete('mileages_perma_del/{id}', ['uses' => 'Admin\MileagesController@perma_del', 'as' => 'mileages.perma_del']);
    Route::post('getAreaByStore', 'Admin\MileagesController@getAreaByStore')->name('mileages.getAreaByStore');
    Route::post('getJsaByArea', 'Admin\MileagesController@getJsaByArea')->name('mileages.getJsaByArea');
    
    Route::post('calculateDistance', 'Admin\MileagesController@calculateDistance')->name('mileages.calculateDistance');
    //added new routes for inactive events on October 25, 2021
    Route::get('events/inactive', ['uses' => 'Admin\EventsController@getDeleted', 'as' => 'events.get_inactive']);
    Route::resource('events', 'Admin\EventsController');
    Route::post('events_mass_destroy', ['uses' => 'Admin\EventsController@massDestroy', 'as' => 'events.mass_destroy']);
    Route::post('events_restore/{id}', ['uses' => 'Admin\EventsController@restore', 'as' => 'events.restore']);
    Route::delete('events_perma_del/{id}', ['uses' => 'Admin\EventsController@perma_del', 'as' => 'events.perma_del']);
    Route::post('events-feedback', ['uses' => 'Admin\EventsController@feedback', 'as' => 'events.feedback']);
    Route::post('events-qc', ['uses' => 'Admin\EventsController@qc', 'as' => 'events.qc']);
    Route::post('events-precall', ['uses' => 'Admin\EventsController@precall', 'as' => 'events.precall']);
    Route::get('events/get_event_feedback_data/{id}', ['uses' => 'Admin\EventsController@get_event_feedback_data', 'as' => 'events.get_event_feedback_data']);
    Route::post('save_event_data', ['uses' => 'Admin\EventsController@savecalendarevent', 'as' => 'events.savecalendarevent']);
    
    Route::get('events/get_list', ['uses' => 'Admin\EventsController@get_list', 'as' => 'events.get_list']);
    
    Route::post('events/get_event_list_by_page', ['uses' => 'Admin\EventsController@get_event_list_by_page', 'as' => 'events.get_event_list_by_page']);
    Route::post('events/get_prior_event_list_by_page', ['uses' => 'Admin\EventsController@get_prior_event_list_by_page', 'as' => 'events.get_prior_event_list_by_page']);
    
    Route::get('events/getprecallcomment/{event_id}', ['uses' => 'Admin\EventsController@getprecallcomment', 'as' => 'events.getprecallcomment']);
    Route::get('events/getqccomment/{event_id}', ['uses' => 'Admin\EventsController@getqccomment', 'as' => 'events.getqccomment']);
    Route::get('events/showstoreevents/{store_id}', ['uses' => 'Admin\EventsController@showstoreevents', 'as' => 'events.showstoreevents']);
    Route::post('events/getEventDetailsByID', ['uses' => 'Admin\EventsController@getEventDetailsByID', 'as' => 'events.getEventDetailsByID']);
    Route::post('events/updateEventDetailsByID', ['uses' => 'Admin\EventsController@updateEventDetailsByID', 'as' => 'events.updateEventDetailsByID']);
    Route::get('prior-events', ['uses' => 'Admin\EventsController@getCompletedEvents', 'as' => 'events.completedevents']);
    Route::get('events/upload_timesheet_mdb/{event_id}', ['uses' => 'Admin\EventsController@upload_timesheet_mdb', 'as' => 'events.upload_timesheet_mdb']);
    Route::post('events/uploadmdb', ['uses' => 'Admin\EventsController@uploadmdb', 'as' => 'events.uploadmdb']);
    Route::get('import-events', ['uses' => 'Admin\EventsController@importevents', 'as' => 'events.import']);
    Route::post('import-events', ['uses' => 'Admin\EventsController@importevents', 'as' => 'events.import']);
    Route::post('events/validateEventImportExcel', ['uses' => 'Admin\EventsController@validateEventImportExcel', 'as' => 'events.validateEventImportExcel']);
    Route::get('events/pendingeventlist/{event_id}', ['uses' => 'Admin\EventsController@pendingEventList', 'as' => 'events.pendingEventList']);
    
    Route::get('import-invoice', ['uses' => 'Admin\EventsController@importinvoice', 'as' => 'events.invoice']);
    Route::post('import-invoice', ['uses' => 'Admin\EventsController@importinvoice', 'as' => 'events.invoice']);
    Route::post('events/validateEventInvoiceImportExcel', ['uses' => 'Admin\EventsController@validateEventInvoiceImportExcel', 'as' => 'events.validateEventInvoiceImportExcel']);
    
    Route::get('import-lodging', ['uses' => 'Admin\EventsController@importlodging', 'as' => 'events.lodging']);
    Route::post('import-lodging', ['uses' => 'Admin\EventsController@importlodging', 'as' => 'events.lodging']);
    Route::post('events/validateEventLodgingImportExcel', ['uses' => 'Admin\EventsController@validateEventLodgingImportExcel', 'as' => 'events.validateEventLodgingImportExcel']);
    
    Route::get('import-meal', ['uses' => 'Admin\EventsController@importmeal', 'as' => 'events.meal']);
    Route::post('import-meal', ['uses' => 'Admin\EventsController@importmeal', 'as' => 'events.meal']);
    Route::post('events/validateEventMealImportExcel', ['uses' => 'Admin\EventsController@validateEventMealImportExcel', 'as' => 'events.validateEventMealImportExcel']);
    
    Route::post('calculate_additional_area_distance', ['uses' => 'Admin\EventsController@calculate_additional_area_distance', 'as' => 'events.calculate_additional_area_distance']);
    Route::post('events/validateSchedule', ['uses' => 'Admin\EventsController@validateSchedule', 'as' => 'events.validateSchedule']);
    
    Route::post('fullcalendar','Admin\EventsController@get_list');
    Route::post('fullcalendar/add_calendar_event',['uses' => 'Admin\EventsController@add_calendar_event','as'=>'events.add_calendar_event']);
    Route::post('fullcalendar/edit_event','Admin\EventsController@edit_event');
    Route::post('fullcalendar/delete_event','Admin\EventsController@delete_event');
    
    //Route::get('events/get_event_feedback_data/{id}', ['uses' => 'Admin\EventsController@get_event_feedback_data', 'as' => 'events.get_event_feedback_data']);
    Route::get('schedule-event/{event_id}',['uses' => 'Admin\EventsController@schedule_event','as' => 'events.schedule-event']);
    Route::get('view-schedule-event/{event_id}',['uses' => 'Admin\EventsController@view_schedule_event','as' => 'events.view-schedule-event']);
    Route::post('save_schedule_event',['uses' => 'Admin\EventsController@save_schedule_event','as' => 'events.save_schedule_event']);
    
    Route::resource('employees', 'Admin\EmployeesController');
    Route::post('employees_mass_destroy', ['uses' => 'Admin\EmployeesController@massDestroy', 'as' => 'employees.mass_destroy']);
    Route::post('employees_restore/{id}', ['uses' => 'Admin\EmployeesController@restore', 'as' => 'employees.restore']);
    Route::delete('employees_perma_del/{id}', ['uses' => 'Admin\EmployeesController@perma_del', 'as' => 'employees.perma_del']);

    Route::post('/spatie/media/upload', 'Admin\SpatieMediaController@create')->name('media.upload');
    Route::post('/spatie/media/remove', 'Admin\SpatieMediaController@destroy')->name('media.remove');
    
    Route::resource('cities', 'Admin\CitiesController');
    //Route::post('mileages_mass_destroy', ['uses' => 'Admin\MileagesController@massDestroy', 'as' => 'mileages.mass_destroy']);
    //Route::post('mileages_restore/{id}', ['uses' => 'Admin\MileagesController@restore', 'as' => 'mileages.restore']);
    //Route::delete('mileages_perma_del/{id}', ['uses' => 'Admin\MileagesController@perma_del', 'as' => 'mileages.perma_del']);
    
    
    Route::get('approval/{id}',['uses' => 'Admin\TimesheetsController@approvalWindow', 'as' => 'timesheets.approval']);
    Route::get('employee-other-events/{emp_id}/{event_date}/{timesheet_id}',['uses' => 'Admin\TimesheetsController@employee_other_events', 'as' => 'timesheets.employee_other_events']);
    Route::get('gap-time/{emp_id}/{timesheet_id}',['uses' => 'Admin\TimesheetsController@gap_time', 'as' => 'timesheets.gap_time']);
    Route::post('approve/{id}', 'Admin\TimesheetsController@approve')->name('timesheets.approve');
    Route::post('caldrivetime', ['uses' => 'Admin\TimesheetsController@caldrivetime', 'as' => 'timesheets.caldrivetime']);
    Route::get('approved-timesheet', ['uses' => 'Admin\TimesheetsController@approved', 'as' => 'timesheets.approved']);
    Route::get('rejected-timesheet', ['uses' => 'Admin\TimesheetsController@rejected_timesheets', 'as' => 'timesheets.rejected']);
    Route::get('restore-timesheet/{id}', ['uses' => 'Admin\TimesheetsController@restore_timesheets', 'as' => 'timesheets.restore']);
    Route::get('submitted-timesheet', ['uses' => 'Admin\TimesheetsController@submitted_timesheets', 'as' => 'timesheets.submitted']);

    Route::post('timesheets/indexexcused', ['uses' => 'Admin\TimesheetsController@indexexcused', 'as' => 'timesheets.indexexcused']);
    Route::put('excused-timesheet/scUpdate/{id}','Admin\TimesheetsController@scUpdate');
    Route::get('import_list', ['uses' => 'Admin\TimesheetsController@import_list', 'as' => 'timesheets.import_list']);
    
    Route::post('callunchgap', ['uses' => 'Admin\TimesheetsController@callunchgap', 'as' => 'timesheets.callunchgap']);
    
    Route::get('import_time_entries_date_wise/{date}', 'Admin\TimesheetsController@import_time_entries_date_wise')->name('timesheets.import_time_entries_date_wise');
    Route::get('import_store_historical_data/{store_id}', 'Admin\TimesheetsController@import_store_historical_data')->name('timesheets.import_store_historical_data');
    Route::get('timesheet_data_by_id', 'Admin\TimesheetsController@timesheet_data_by_id')->name('timesheets.timesheet_data_by_id');
    Route::get('reset_kronos_queue', 'Admin\TimesheetsController@reset_kronos_queue');
    
    
    Route::get('reset_schedule_kronos_queue', 'Admin\EmployeesController@reset_schedule_kronos_queue');
    Route::get('cost_center_to_kronos', 'Admin\StoresController@cost_center_to_kronos');
    
    Route::post('employee_schedule_area_wise',['uses' => 'Admin\EventsController@employee_schedule_area_wise','as'=>'events.employee_schedule_area_wise']);
    Route::post('kronos_queue_status', 'Admin\TimesheetsController@kronos_queue_status');
    Route::post('get_mini_schedule', 'Admin\EventsController@get_mini_schedule')->name('events.get_mini_schedule');
    Route::get('export_event_info', 'Admin\EventsController@export_event_info');
    Route::post('timesheet_submitted_check', 'Admin\TimesheetsController@timesheet_submitted_check');
    Route::post('copy_event_schedule', ['uses' => 'Admin\EventsController@copy_event_schedule', 'as' => 'events.copy_event_schedule']);
    Route::get('reports/weekly-projected-hours', 'Admin\WeeklyProjectedHours')->name('reports.weekly_projected_hours');
    
    Route::resource('reports', 'Admin\ReportsController');
    Route::post('reports_mass_destroy', ['uses' => 'Admin\ReportsController@massDestroy', 'as' => 'reports.mass_destroy']);
    Route::post('reports_restore/{id}', ['uses' => 'Admin\ReportsController@restore', 'as' => 'reports.restore']);
    Route::delete('reports_perma_del/{id}', ['uses' => 'Admin\ReportsController@perma_del', 'as' => 'reports.perma_del']);
    Route::get('employee_export', 'Admin\EventsController@employee_export');
    Route::get('view_crystal_report/{id}',['uses'=>'Admin\ReportsController@view_crystal_report','as'=>'reports.view_crystal_report']);
    Route::get('reports/assignusers/{id}', ['uses'=>'Admin\ReportsController@assignusers','as'=>'reports.assignusers']);
    Route::post('reports/saveassignedusers', ['uses' => 'Admin\ReportsController@saveassignedusers', 'as' => 'reports.saveassignedusers']);
    Route::post('event_schedule_extra_area',['uses' => 'Admin\EventsController@event_schedule_extra_area','as'=>'events.event_schedule_extra_area']);
    Route::get('unscheduled-stores', ['uses'=>'Admin\ReportsController@unscheduled_store_list_view','as'=>'reports.unscheduled_store_list_view']);
    Route::post('reports/unscheduled_store_list', ['uses'=>'Admin\ReportsController@unscheduled_store_list','as'=>'reports.unscheduled_store_list']);
    //Scheduled Vs excused employees
    Route::resource('excusedemployee', 'Admin\Excusedemployee');

    Route::get('excused-employee', ['uses'=>'Admin\ExcusedemployeeController@index','as'=>'excusedemployee.index']);

    Route::get('timesheets/excluded-absent-employees', 'Admin\AbsentEmployeeController@excludedAbsentEmployees');
    Route::post('timesheets/excluded-absent-employees', 'Admin\AbsentEmployeeController@getExcludedAbsentEmployees');

    Route::post('timesheets/absent-employees', 'Admin\AbsentEmployeeController@getAbsentEmployees');
    Route::get('timesheets/absent-employees', 'Admin\AbsentEmployeeController@index')->name('timesheets.absent_employees');;

    Route::post('timesheets/exclude-absent-employee', 'Admin\AbsentEmployeeController@excludeAbsentEmployee');
    

    Route::resource('timesheets', 'Admin\TimesheetsController');

    


    //Json data uploader
    Route::get('import', ['uses'=>'Admin\ImportController@index','as'=>'import.index']);
    Route::post('import','Admin\ImportController@import');
    Route::get('upload-json', ['uses'=>'Admin\JsonUploaderController@create','as'=>'json_data.create']);
    Route::get('read-data/{jsonId}', ['uses'=>'Admin\JsonUploaderController@appendData','as'=>'json_data.read']);
    Route::post('store-uploaded-json', ['uses'=>'Admin\JsonUploaderController@store','as'=>'json_data.store']);
    Route::delete('json-del/{id}', ['uses' => 'Admin\JsonUploaderController@destroy', 'as' => 'json_data.json_del']);
});




Route::get('event-reports/{id}', ['uses' => 'Admin\ClientsController@eventReports', 'as' => 'clients.client_reports']);
Route::get('area_report/{id}', ['uses' => 'Admin\ClientsController@downloadAreaReport']);   
Route::get('category_report/{id}', ['uses' => 'Admin\ClientsController@downloadCategoryReport']);   
Route::get('location_report/{id}', ['uses' => 'Admin\ClientsController@downloadLocationReport']);
Route::get('consolidation_report/{id}', ['uses' => 'Admin\ClientsController@downloadConsolidationReport']);
//Route::get('timesheet_report/{id}', ['uses' => 'Admin\ClientsController@downloadTimeSheetReport']);

