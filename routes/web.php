<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('login');
});
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/login-history', 'HomeController@loginHistory')->name('loginHistory');
Route::group(['middleware' => 'auth'], function() {

	Route::group(['prefix' => 'manage_project'], function(){
		Route::get('projectIndex','ManageProjectController@projectIndex')->name('projectIndex');
		Route::get('addProject','ManageProjectController@addProject')->name('addProject');
		Route::get('editProject/{id}','ManageProjectController@editProject')->name('editProject');
		Route::get('deleteProjectmilestone/{id}','ManageProjectController@deleteProjectmilestone')->name('deleteProjectmilestone');
		Route::post('addProjectSubmit','ManageProjectController@addProjectSubmit')->name('addProjectSubmit');
		Route::post('editProjectSubmit','ManageProjectController@editProjectSubmit')->name('editProjectSubmit');
		Route::post('bedroomsDelete','ManageProjectController@bedroomsDelete')->name('bedroomsDelete');
		Route::post('deleteProject','ManageProjectController@deleteProject')->name('deleteProject');
		Route::post('datatableManageProject','ManageProjectController@datatableManageProject')->name('datatableManageProject');
		Route::post('moveToReadyProject','ManageProjectController@moveToReadyProject')->name('moveToReadyProject');
		Route::post('moveToSoldOutProject','ManageProjectController@moveToSoldOutProject')->name('moveToSoldOutProject');

		Route::get('previewProject/{id}','ManageProjectController@previewProject')->name('previewProject');
		Route::post('addProjectNote','ManageProjectController@addProjectNote')->name('addProjectNote');
		Route::post('deleteProjectNotes','ManageProjectController@deleteProjectNotes')->name('deleteProjectNotes');
		Route::post('addProjectDocument','ManageProjectController@addProjectDocument')->name('addProjectDocument');
		Route::post('deleteProjectDocuments','ManageProjectController@deleteProjectDocuments')->name('deleteProjectDocuments');
		Route::post('deleteProjectAttachments','ManageProjectController@deleteProjectAttachments')->name('deleteProjectAttachments');

		Route::post('addProjectReminder','ManageProjectController@addProjectReminder')->name('addProjectReminder');
		Route::post('getProjectReminder','ManageProjectController@getProjectReminder')->name('getProjectReminder');
		Route::post('changeProjectReminderStatus','ManageProjectController@changeProjectReminderStatus')->name('changeProjectReminderStatus');

		Route::post('addAssignProject','ManageProjectController@addAssignProject')->name('addAssignProject');
	});

	Route::group(['prefix' => 'manage_ready_project'], function(){
		Route::get('readyProjectIndex','ManageProjectController@readyProjectIndex')->name('readyProjectIndex');
		Route::get('addReadyProject','ManageProjectController@addProject')->name('addReadyProject');
		Route::get('editReadyProject/{id}','ManageProjectController@editProject')->name('editReadyProject');
		Route::get('previewReadyProject/{id}','ManageProjectController@previewProject')->name('previewReadyProject');
	});

	Route::group(['prefix' => 'manage_sold_out_project'], function(){
		Route::get('soldOutProjectIndex','ManageProjectController@soldOutProjectIndex')->name('soldOutProjectIndex');
		Route::get('addSoldOutProject','ManageProjectController@addProject')->name('addSoldOutProject');
		Route::get('editSoldOutProject/{id}','ManageProjectController@editProject')->name('editSoldOutProject');
		Route::get('previewSoldOutProject/{id}','ManageProjectController@previewProject')->name('previewSoldOutProject');
	});

	Route::group(['prefix' => 'manage_overdue_project'], function(){
		Route::get('overdueProjectIndex','ManageProjectController@overdueProjectIndex')->name('overdueProjectIndex');
		Route::get('editOverdueProject/{id}','ManageProjectController@editProject')->name('editOverdueProject');
		Route::get('previewOverdueProject/{id}','ManageProjectController@previewProject')->name('previewOverdueProject');
	});

/*********************************  Agent  *********************************/
Route::get('/manage_agent','AgentController@list_agent')->name('manage_agent');
Route::group(['prefix' => '/manage_agent'], function(){
	Route::get('/add_agent', 'AgentController@add_agent')->name('add_agent');

	Route::post('/submit_add_agent', 'AgentController@craete_agent')->name('submit_add_agent');

	Route::get('/delete_agent/{id}', 'AgentController@delete_agent')->name('delete_agent');
	Route::get('/edit_agent/{id}', 'AgentController@edit_agent')->name('edit_agent');
	Route::post('/submit_edit_agent/{id}', 'AgentController@update_agent')->name('submit_edit_agent');
});

/*********************************  User = Developer  *********************************/
Route::get('/manage-User','UserController@listUser')->name('manage-user')->middleware('read_developer');
Route::get('/pending-user','UserController@pendingUser')->name('pending-user')->middleware('read_developer');
Route::group(['prefix' => '/manage-user'], function(){
	Route::get('/add-user', 'UserController@addUser')->name('add-user')->middleware('add_developer');
	Route::post('/submit-add-user', 'UserController@craeteUser')->name('submit-add-user')->middleware('add_developer');

	Route::get('/delete-user/{id}', 'UserController@deleteUser')->name('delete-user')->middleware('delete_developer');

	Route::get('/edit-user/{id}', 'UserController@edituser')->name('edit-user')->middleware('edit_developer');
	Route::post('/submit-edit-user', 'UserController@updateUser')->name('submit-edit-user')->middleware('edit_developer');

	Route::get('preview-user/{id}','UserController@previewuser')->name('preview-user')->middleware('read_developer');;

	Route::get('delete-contact/{id}','UserController@deletecontact')->name('delete-contact')->middleware('delete_developer');
	Route::post('attachment-post','UserController@attachmentpost')->name('attachment-post')->middleware('add_developer');
	Route::get('removeattachment','UserController@removeattachment')->name('removeattachment')->middleware('delete_developer');

	Route::post('datatableDeveloperList','UserController@datatableDeveloperList')->name('datatableDeveloperList');
	Route::post('deleteDeveloper','UserController@deleteDeveloper')->name('deleteDeveloper');
	Route::post('datatableDeveloperProjectList','UserController@datatableDeveloperProjectList')->name('datatableDeveloperProjectList');
	Route::post('addDeveloperNote','UserController@addDeveloperNote')->name('addDeveloperNote');
	Route::post('deleteDeveloperNotes','UserController@deleteDeveloperNotes')->name('deleteDeveloperNotes');
	Route::post('getDeveloperNote','UserController@getDeveloperNote')->name('getDeveloperNote');

});
Route::group(['prefix' => '/manage-listing'], function(){
	Route::get('/search','ListingController@search')->name('search-list-developer');
    Route::get('/pendingsearch','ListingController@pendingsearch')->name('pending-search-list');
});

/*********************************  Unit  *********************************/
Route::get('manage_listings','ManageController@managelistings')->name('manage_listings')->middleware('read_unit');
Route::post('datatableManageListings','ManageController@datatableManageListings')->name('datatableManageListings');
Route::get('update_flag_status','ManageController@updateFlag')->name('update_flag_status');

Route::get('add-view-unit','ManageController@addviewunit')->name('add-view-unit')->middleware('add_unit');
Route::post('add-unit','ManageController@submitunit')->name('add-unit')->middleware('add_unit');

Route::get('edit-unit/{id}','ManageController@editunit')->name('edit-unit')->middleware('edit_unit');
Route::post('submit-edit-unit','ManageController@updateunit')->name('submit-edit-unit')->middleware('edit_unit');

Route::get('copy-unit/{id}','ManageController@copyunit')->name('copy-unit')->middleware('add_unit');
Route::post('submit-copy-unit','ManageController@submitunit')->name('submit-copy-unit')->middleware('add_unit');

Route::get('delete-unit','ManageController@deleteunit')->name('delete-unit')->middleware('delete_unit');

Route::get('delete-unit-milestone/{id}','ManageController@delete_unit_milestone')->name('delete-unit-milestone')->middleware('delete_unit');
Route::get('search-list','ManageController@searchlist')->name('search-list');

Route::post('unit-attachment-post','ManageController@unitattachmentpost')->name('unit-attachment-post')->middleware('add_unit');
Route::get('unit-removeattachment','ManageController@unitremoveattachment')->name('unit-removeattachment')->middleware('delete_unit');

/*** unit-status**/
Route::group(['prefix' => '/manage-unit-status'], function(){
	Route::get('change_ready_status','ManageController@setUnitStatus')->name('add_ready_status')->middleware('edit_unit');
	Route::get('ready_unit_list','ManageController@readyUnitList')->name('ready_unit_list')->middleware('read_unit');
	Route::get('add-view-unit','ManageController@addviewunit')->name('add_ready_unit')->middleware('add_unit');

	Route::get('edit-unit/{id}','ManageController@editunit')->name('ready-edit-unit')->middleware('edit_unit');
	Route::get('copy-unit/{id}','ManageController@copyunit')->name('ready-copy-unit')->middleware('add_unit');
	Route::get('preview-unit/{id}','ManageController@previewunit')->name('ready-preview-unit')->middleware('read_unit');
});

/********** Sold Out Unit **********/
Route::group(['prefix' => '/manage-soldout-unit'], function(){
	Route::get('sold_out_unit_list','ManageController@soldOutUnitList')->name('sold_out_unit_list')->middleware('read_unit');
	Route::get('change_sold_out_status','ManageController@soldoutsetStatus')->name('add_sold_out_status')->middleware('edit_unit');

	Route::get('edit-unit/{id}','ManageController@editunit')->name('sold-out-edit-unit')->middleware('edit_unit');
	Route::get('copy-unit/{id}','ManageController@copyunit')->name('sold-out-copy-unit')->middleware('add_unit');
	Route::get('preview-unit/{id}','ManageController@previewunit')->name('sold-out-preview-unit')->middleware('read_unit');
});


/** out dated unit */
Route::group(['prefix' => '/manage-outdated-unit'], function(){
	Route::get('change_ready_status','ReminderController@setUnitStatus')->name('add_status_change')->middleware('edit_unit');
	Route::get('outdated_unit_list','ManageController@outdatedUnit')->name('outdated_unit_list')->middleware('read_unit');

	Route::get('chenge_ready_status','ManageController@readysetUnitStatus')->name('ready_add_ready_status')->middleware('edit_unit');


	Route::get('edit-unit/{id}','ManageController@editunit')->name('outdated-edit-unit')->middleware('edit_unit');
	Route::get('copy-unit/{id}','ManageController@copyunit')->name('outdated-copy-unit')->middleware('add_unit');
	Route::get('preview-unit/{id}','ManageController@previewunit')->name('outdated-preview-unit')->middleware('read_unit');
});


Route::get('change_reminder_status','ReminderController@update')->name('add_reminder_status')->middleware('edit_unit');
/*********************************  Milestones  *********************************/
Route::get('add-milestones','MilestonesController@addviewmilestones')->name('add-milestones')->middleware('read_milestone');
Route::post('listmilestonesDatatable','MilestonesController@listmilestonesDatatable')->name('listmilestonesDatatable');
Route::post('add-milestones','MilestonesController@submitmilestones')->name('add-milestones')->middleware('add_milestone');
Route::post('delete-milestones/{id?}','MilestonesController@deletemilestones')->name('delete-milestones')->middleware('delete_milestone');




/*********************************  Web View unit  *********************************/
Route::get('preview-unit/{id}','ManageController@previewunit')->name('preview-unit')->middleware('read_unit');



// /*********************************  Features  *********************************/
// Route::get('/manage-features','FeaturesController@listfeatures')->name('manage-features');
// Route::get('/view-features','FeaturesController@viewfeatures')->name('view-features');
// Route::get('/add-features', 'FeaturesController@add_features')->name('add-features');
// Route::post('/submit-add-features', 'FeaturesController@add_features')->name('submit-add-features');
// Route::get('/delete-features/{id}', 'FeaturesController@delete_Features')->name('delete-features');


/*********************************  Features  *********************************/
Route::get('/manage-features','FeaturesController@listfeatures')->name('manage-features')->middleware('read_features');
Route::post('listfeaturesDatatable','FeaturesController@listfeaturesDatatable')->name('listfeaturesDatatable');
Route::post('/submit-add-features', 'FeaturesController@add_features')->name('submit-add-features')->middleware('add_features');
Route::post('/delete-features/{id?}', 'FeaturesController@delete_Features')->name('delete-features')->middleware('delete_features');

/*********************************  Community  *********************************/
Route::get('/manage-community','ManageController@managecommunity')->name('manage-community')->middleware('read_community');
Route::post('/add-community','ManageController@addcommunity')->name('add-community')->middleware('add_community');
Route::post('managecommunityDatatable','ManageController@managecommunityDatatable')->name('managecommunityDatatable');
Route::post('/delete-community/{id?}','ManageController@deletecommunity')->name('delete-community')->middleware('delete_community');

Route::get('/getcommunity','ManageController@getcommunity')->name('get-community');
Route::get('/add-other-community','ManageController@addcommunity')->name('add-other-community');
Route::get('get-community-list','ManageController@getcommunityList')->name('get-community-list');
Route::get('/get-edit-community','ManageController@geteditcommunity')->name('get-edit-community');


/*********************************  Sub Community  *********************************/
Route::get('/manage-subcommunity','ManageController@managesubcommunity')->name('manage-subcommunity')->middleware('read_community');
Route::post('managesubcommunityDatatable','ManageController@managesubcommunityDatatable')->name('managesubcommunityDatatable');
Route::post('/add-other-subcommunity','ManageController@addSubcommunity')->name('add-other-subcommunity')->middleware('add_community');
Route::post('/delete-subcommunity/{id?}','ManageController@deletesubcommunity')->name('delete-subcommunity')->middleware('delete_community');

Route::post('/getSubcommunity','ManageController@getSubcommunity')->name('get-subcommunity');


/* -------------------------- Manage Note Route----------------------------*/
Route::post('/add-note','ManageController@add_note')->name('add-note')->middleware('add_unit');
Route::get('/remove-note','ManageController@remove_note')->name('remove-note')->middleware('delete_unit');
Route::post('getUnitNoteList','ManageController@getUnitNoteList')->name('getUnitNoteList');


/* -------------------------- Manage Lead Clients ----------------------------*/
	Route::group(['prefix' => '/manage-lead-clients'], function(){
		Route::get('lead_index','LeadClientController@index')->name('lead_index');
		Route::get('lead_create','LeadClientController@create')->name('lead_create');
		Route::post('lead_store','LeadClientController@store')->name('lead_store');
        Route::post('lead-note','LeadClientController@add_note')->name('lead-note');
        Route::get('lead_edit/{id}','LeadClientController@edit')->name('lead_edit');
		Route::get('view_lead/{id}','LeadClientController@show')->name('view_lead');
        Route::get('remove-lead-note','LeadClientController@remove_note')->name('remove-lead-note');
		Route::post('lead_update','LeadClientController@update')->name('lead_update');
		Route::get('lead_destroy/{id}','LeadClientController@destroy')->name('lead_destroy');
        Route::post('store-reminder','LeadClientController@storeReminder')->name('store-reminder');
        Route::get('change_reminder_status','LeadClientController@updateReminder')->name('update_reminder_status');
	});

	Route::group(['prefix'=> '/manage-reminder'], function(){
		Route::resource('reminder','ReminderController');
	});

	Route::get('excel/{id}','LeadClientController@export')->name('lead_report');
});
Route::get('view-project/{id}','ManageController@viewproject')->name('view-unit');
Route::post('/mail/{id}','ManageController@mail')->name('mail-send');
