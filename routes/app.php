<?php

// Dashboard
Route::get('/dashboard', 'DashboardController@dashboard');
Route::get('/dashboard/admin', 'DashboardController@admin')->middleware('admin_only');
Route::get('/dashboard/manager', 'DashboardController@manager')->middleware('manager_only');
Route::get('/dashboard/employee', 'DashboardController@employee');
Route::get('/dashboard/tasks', 'DashboardController@tasks');

// Settings
Route::get('/settings', 'SettingsController@edit')->name('settings');
Route::post('/settings', 'SettingsController@update')->name('settings.update');
Route::post('/settings/password', 'ChangePasswordController@update')->name('settings.password.update');

// API
Route::get('/settings/api', 'ApiController@show');
Route::post('/settings/api/regenerate', 'ApiController@regenerate');

// Client
Route::get('/clients', 'ClientController@index')->name('clients.index');
Route::get('/clients/{type?}', 'ClientController@index')->where('type', 'old|paused|deactivated|internal|all');
Route::get('/clients/create', 'ClientController@create')->middleware('can:create,App\Repositories\Client\Client');
Route::post('/clients', 'ClientController@store')->middleware('can:create,App\Repositories\Client\Client');
Route::get('/clients/{client}', 'ClientController@show')->middleware('can:view,client')->name('client.show');

Route::get('/clients/{client}/contacts', 'ClientController@showContacts')->name('client.contacts.index');
Route::get('/clients/{client}/contacts/create', 'ContactController@create')->name('client.contacts.create');
Route::post('/clients/{client}/contacts/{contact}/remove', 'ClientController@unlinkContact')->name('client.contacts.remove');
Route::get('/clients/{client}/contacts/{contact}/edit', 'ClientController@editContact')->name('client.contact.edit');
Route::get('/clients/{client}/contacts/link', 'ClientController@linkContact')->name('client.contacts.link');
Route::post('/clients/{client}/contacts/link', 'ClientController@storeLinkContact')->name('client.contacts.link.store');
Route::get('/clients/{client}/contacts/{contact}/phones/create', 'ContactPhoneController@create')->name('client.contact.phone.create');
Route::post('/clients/{client}/contacts/{contact}/phones', 'ContactPhoneController@store')->name('client.contact.phone.store');
Route::post('/clients/{client}/contacts/{contact}/phone/{phone}/primary', 'ContactPhoneController@setPrimary')->name('contacts.phone.primary');
Route::delete('/clients/{client}/contacts/{contact}/phone/{phone}', 'ContactPhoneController@delete')->name('client.contact.phone.delete');
Route::get('/clients/{client}/contacts/{contact}/emails/create', 'ContactEmailController@create')->name('client.contact.email.create');
Route::post('/clients/{client}/contacts/{contact}/email', 'ContactEmailController@store')->name('client.contact.email.store');
Route::post('/clients/{client}/contacts/{contact}/email/{email}/primary', 'ContactEmailController@setPrimary')->name('client.contact.email.primary');
Route::delete('/clients/{client}/contacts/{contact}/email/{email}', 'ContactEmailController@delete')->name('client.contact.email.delete');

Route::get('/clients/{client}/tickets', 'ClientController@showTickets')->middleware('can:view,client');
Route::get('/clients/{client}/tickets/{ticketId}', 'ClientController@viewTicket')->middleware('can:view,client')->name('client.ticket.comments');
Route::post('/clients/{client}/note', 'ClientController@updateNote')->middleware('can:view,client');
Route::get('/clients/{client}/notes', 'ClientController@showNotes')->middleware('can:view,client');
Route::post('/clients/{client}/risk', 'ClientController@updateRisk')->middleware('can:view,client');
Route::get('/clients/{client}/edit', 'ClientController@edit')->middleware('can:update,client');
Route::post('/clients/{client}', 'ClientController@update')->middleware('can:update,client');
Route::get('/clients/{client}/tasks/completed', 'ClientController@completed')->middleware('can:view,client');
Route::post('/clients/{client}/add_complaint', 'ClientController@addComplaint')->middleware('can:view,client');
Route::post('/clients/{client}/remove_complaint', 'ClientController@removeComplaint')->middleware('admin_only');
Route::get('/clients/{client}/notifications', 'ClientController@showNotifications');
Route::get('/clients/{client}/flags', 'ClientController@showFlags');

// Reports
Route::get('/reports/rating', 'RatingController@index');
Route::get('/reports/tasks/all', 'ReportController@task');
Route::get('/reports/tasks/overdue', 'ReportController@overdueTask');
Route::get('/reports/clients/overdue', 'ReportController@overdueTaskPerClient');
Route::get('/reports/capacity', 'ReportController@index')->middleware('customer_service');
Route::get('/reports/overdue', 'OverdueReasonController@report')->middleware('admin_only');
Route::get('/reports/filter/overdue', 'OverdueReasonController@reportReasonWithFilters')->middleware('admin_only');
Route::get('/reports/overdue/without_reason', 'OverdueReasonController@reportWithoutReason')->middleware('admin_only');
Route::get('/reports/overdue/{user}', 'OverdueReasonController@reportReason')->middleware('admin_only');
Route::get('/reports/rating/{rating}/review', 'RatingController@review')->name('ratings.review');
Route::get('/reports/it', 'ItController@issuesList')->name('reports.it.github_issues');
Route::get('/reports/it/unmatched', 'ItController@unmatchedTimeEntries')->name('reports.it.unmatched_time');
Route::get('/reports/it/time-entities/{id}/assign', 'ItController@assignTimeEntityForm')->name('it.timeEntity.assign.form');
Route::post('/reports/it/time-entities/{id}/disregard', 'ItController@disregardTimeEntity')->name('it.timeEntity.disregard');
Route::post('/reports/it/time-entities/{id}/assign', 'ItController@assignTimeEntity')->name('it.timeEntity.assign');
Route::get('/reports/it/github-issues', 'ItController@githubIssuesList')->name('it.githubIssues.list');
Route::get('/reports/it/github-issues/{githubIssue}/time-entries', 'ItController@githubIssueTimeEntries')->name('it.githubIssues.time_entries');

Route::get('/reports/{report}', 'ReportController@show')->name('report.show');

// Task
Route::get('/clients/{client}/tasks/create', 'TaskController@create');
Route::get('/clients/{client}/tasks/create-custom', 'TaskController@createCustom');
Route::post('/clients/{client}/tasks', 'TaskController@store');
Route::post('/clients/{client}/tasks/custom', 'TaskController@storeCustom');
Route::get('/tasks/{task}', 'TaskController@show')->middleware('can:view,task')->name('tasks');
Route::get('/tasks/{task}/edit', 'TaskController@edit')->middleware('can:update,task');
Route::post('/tasks/{task}', 'TaskController@update')->middleware('can:update,task');
Route::get('/tasks/{task}/review', 'TaskController@reviewChanges')->middleware('can:complete,task')->name('tasks.review.show');
Route::post('/tasks/{task}/review', 'TaskController@acceptReviewedChanges')->middleware('can:complete,task')->name('tasks.review.submit');
Route::delete('/tasks/{task}', 'TaskController@destroy')->middleware('can:delete,task');
Route::post('/tasks/{task}/completed', 'TaskController@completed')->middleware('can:complete,task')->name('tasks.completed');
Route::get('/tasks/{task}/reopen', 'ReopenController@form');
Route::post('/tasks/{task}/reopen', 'ReopenController@submit');
Route::get('/tasks/{task}/regenerate', 'TaskController@showRegenerateForm')->middleware('can:view,task');
Route::post('/tasks/{task}/regenerate', 'TaskController@regenerate')->middleware('can:view,task');
Route::post('/tasks/{task}/comments', 'CommentController@store');
Route::post('/tasks/{task}/review/comments', 'CommentController@reviewStore');
Route::get('/overdue', 'TaskController@showOverdue')->name('tasks.show.overdue');
Route::post('/overdue/{task}', 'TaskController@createOverdue')->name('tasks.create.overdue');
Route::post('/overdue/{task}/completed', 'TaskController@completedOverdue')->name('tasks.completed.overdue');

// Task Subtasks
Route::get('/subtasks/{subtask}', 'SubtaskController@show')->middleware('can:view,subtask');
Route::get('/subtasks/{subtask}/review', 'SubtaskController@reviewChanges')->middleware('can:complete,subtask')->name('tasks.subtask.review.show');
Route::post('/subtasks/{subtask}/review', 'SubtaskController@acceptReviewedChanges')->middleware('can:complete,subtask')->name('tasks.subtask.review.submit');
Route::post('/subtasks/{subtask}/completed', 'SubtaskController@completed')->middleware('can:complete,subtask')->name('tasks.subtasks.completed');
Route::get('/subtasks/{subtask}/completed', 'SubtaskController@renderCompletionPage')->middleware('can:complete,subtask')->name('tasks.subtasks.complete');
Route::get('/subtasks/{subtask}/reopen', 'ReopenSubtaskController@form');
Route::post('/subtasks/{subtask}/reopen', 'ReopenSubtaskController@submit');

// Template
Route::get('/templates', 'TemplateController@index')->name('templates.index');
Route::get('/templates/create', 'TemplateController@create')->name('templates.create');
Route::post('/templates', 'TemplateController@store');
Route::get('/templates/{template}', 'TemplateController@show')->name('templates.show');
Route::get('/templates/{template}/edit', 'TemplateController@edit')->name('templates.edit');
Route::post('/templates/{template}', 'TemplateController@update');
Route::post('/templates/{template}/duplicate', 'TemplateController@duplicate');
Route::post('/templates/{template}/deactivate', 'TemplateController@deactivate');

// Contracts
Route::get('/clients/{client}/contracts/create/{type}', 'ContractController@create')->name('clients.contracts.create')->where('type', 'simple|advanced');
Route::post('/contracts/preview', 'ContractController@preview')->name('contracts.preview');
Route::post('/contracts', 'ContractController@store')->name('contracts.store');
Route::get('/contracts/{contract}', 'ContractController@show')->name('contracts.show');
Route::post('/contracts/{contract}/terminate', 'ContractController@terminate')->name('contracts.terminate');

// Template Subtasks
Route::get('/templates/{template}/subtasks/create', 'TemplateSubtaskController@create');
Route::post('/templates/{template}/subtasks', 'TemplateSubtaskController@store');
Route::get('/template-subtasks/{templateSubtask}', 'TemplateSubtaskController@show');
Route::get('/template-subtasks/{templateSubtask}/edit', 'TemplateSubtaskController@edit');
Route::post('/template-subtasks/{templateSubtask}', 'TemplateSubtaskController@update');
Route::get('/template-subtasks/{templateSubtask}/deactivate', 'TemplateSubtaskController@showDeactivationSettings');
Route::post('/template-subtasks/{templateSubtask}/deactivate', 'TemplateSubtaskController@deactivate');
Route::post('/templates/{template}/subtasks/sort', 'TemplateSubtaskController@sort')->name('templates.subtasks.sort');

// Contacts
Route::post('/contacts', 'ContactController@store')->name('contacts.store');
Route::get('/contacts/{contract}', 'ContactController@show')->name('contacts.show');
Route::post('/contacts/{contract}', 'ContactController@update')->name('contacts.update');
Route::post('/contacts/{contact}/deactivate', 'ContactController@deactivate')->name('contacts.deactivate');


// Ajax requests
Route::post('/ajax/client/{clientId}/updateRisk', 'AjaxController@changeClientRiskStatus');
Route::post('/ajax/client/{clientId}/createFolder', 'AjaxController@createClientFolder');
Route::post('/ajax/client/{clientId}/deleteFolder', 'AjaxController@deleteClientFolder');
Route::post('/ajax/client/{clientId}/deleteFile', 'AjaxController@deleteFile');
Route::get('/ajax/get-template/', 'AjaxController@getTemplate')->middleware('admin_only');
Route::post('/ajax/clients/{clientId}/files', 'AjaxController@storeClientFile');

// Files
Route::get('/filevault/{id}/download', 'FilesController@fileVaultDownload')->name('filevault.url');
Route::post('/files/update', 'FilesController@updateFile');
Route::get('/files/{path?}/{fileName}', 'FilesController@download')->where('path', '(.*)');

// User
Route::middleware(['admin_only'])->group(function () {
    Route::get('/users', 'UserController@index');
    Route::get('/users/{type?}', 'UserController@index')->where('type', 'deactivated');
    Route::get('/users/create', 'UserController@create');
    Route::post('/users', 'UserController@store');
    Route::get('/users/{user}', 'UserController@show')->name('user.show');
    Route::get('/users/{user}/edit', 'UserController@edit');
    Route::get('/users/{user}/week-tasks/{week}', 'UserController@weekTasks');
    Route::post('/users/{user}', 'UserController@update');
    Route::post('/users/{user}/activate', 'UserController@activate');
    Route::post('/users/{user}/deactivate', 'UserController@deactivate');
});
Route::get('/users/{user}/out-of-office', 'UserController@createOutOfOffice')->name('users.ooo.create');
Route::post('/users/{user}/out-of-office/tasks', 'UserController@showOooTasks')->name('users.ooo.tasks');
Route::post('/users/{user}/out-of-office', 'UserController@storeOutOfOffice')->name('users.ooo.store');
Route::post('/users/{user}/out-of-office/delete', 'UserController@removeOoo')->name('users.ooo.remove');
Route::post('/users/{user}/out-of-office/end', 'UserController@endCurrentOoo')->name('users.ooo.end');

// User workload
Route::get('/users/{user}/workload', 'UserWorkloadController@edit')->name('user.workload.edit');
Route::post('/users/{user}/workload', 'UserWorkloadController@update')->name('user.workload.update');

Route::delete('/invitations/{invitation}', 'InvitationController@destroy')->middleware('admin_only');

// Faq
Route::get('/faq/{faq}', 'FaqController@show');
Route::get('/faq-categories/{faqCategory}', 'FaqCategoryController@show');
Route::middleware(['admin_only'])->group(function () {
    Route::get('/faq/{faq}/edit', 'FaqController@edit');
    Route::post('/faq/{faq}', 'FaqController@update');
    Route::delete('/faq/{faq}', 'FaqController@destroy');
    Route::post('/faq/{faq}/move', 'FaqController@move');
    Route::post('/faq', 'FaqController@store');
    Route::get('/system_settings/faq-categories/faq/create', 'FaqController@create');
    Route::get('/system_settings/faq-categories', 'FaqCategoryController@index');
    Route::get('/system_settings/faq-categories/create', 'FaqCategoryController@create');
    Route::post('/system_settings/faq-categories', 'FaqCategoryController@store');
    Route::get('/system_settings/faq-categories/{faqCategory}/edit', 'FaqCategoryController@edit');
    Route::delete('/faq-categories/{faqCategory}', 'FaqCategoryController@destroy');
    Route::post('/faq-categories/{faqCategory}', 'FaqCategoryController@update');
    Route::post('/faq-categories/{faqCategory}/move', 'FaqCategoryController@move');
});

// System Settings


// Overdue
Route::get('/system_settings/overdue', 'OverdueReasonController@index')->middleware('admin_only');
Route::get('/system_settings/overdue/create', 'OverdueReasonController@create')->middleware('admin_only');
Route::post('/system_settings/overdue', 'OverdueReasonController@store')->middleware('admin_only');
Route::get('/system_settings/overdue/{reason}/edit', 'OverdueReasonController@edit')->middleware('admin_only');
Route::post('/system_settings/overdue/{reason}', 'OverdueReasonController@update')->middleware('admin_only');
Route::delete('/system_settings/overdue/{reason}', 'OverdueReasonController@delete')->middleware('admin_only');
Route::post('/system_settings/overdue/{reason}/move', 'OverdueReasonController@move')->middleware('admin_only');

Route::get('/system_settings/users_information', 'InformationController@index')->name('settings.information.index');
Route::get('/system_settings/users_information/create', 'InformationController@create')->name('settings.information.create');
Route::get('/system_settings/users_information/{information}', 'InformationController@show')->name('settings.information.show');
Route::post('/system_settings/users_information', 'InformationController@store')->name('settings.information.store');
Route::get('/system_settings/users_information/{information}/edit', 'InformationController@edit')->name('settings.information.edit');
Route::post('/system_settings/users_information/{information}', 'InformationController@update')->name('settings.information.update');
Route::delete('/system_settings/users_information/{information}', 'InformationController@destroy')->name('settings.information.destroy');

Route::get('/system_settings/contacts', 'ContactController@index')->name('contacts.index');

// System general options/settings
Route::get('/system_settings/options', 'OptionsController@index')->name('settings.options.index');
Route::post('/system_settings/options/{option}', 'OptionsController@update')->name('settings.options.update');

// List all information to accept / Zendesk & Harvest Accept
Route::get('/list_information', 'ListInformationController@index')->name('information.list');
Route::get('/zendesk', 'ListInformationController@zendesk')->name('information.zendesk');
Route::get('/harvest', 'ListInformationController@harvest')->name('information.harvest');

// User information
Route::get('/information', 'UserInformationController@index')->name('information.index');
Route::post('/information', 'UserInformationController@store')->name('information.store');
Route::get('/information/{information}', 'UserInformationController@show')->name('information.show');

//Phone
Route::get('/system_settings/phone_system', 'PhoneSystemController@index')->name('settings.phone.index');
Route::post('/system_settings/phone_system', 'PhoneSystemController@update')->name('settings.phone.update');
Route::get('/system_settings/phone_call_logs', 'PhoneSystemController@callLogs')->name('settings.phone.logs.index')->middleware('customer_service');

//Folder structure
Route::get('/system_settings/folder-structure', 'FoldersStructureController@index')->middleware('admin_only');
Route::get('/system_settings/folder-structure/{parentId}/add/', 'FoldersStructureController@addFolder')->name('folderstructure.create')->middleware('admin_only');
Route::post('/system_settings/folder-structure/add', 'FoldersStructureController@saveFolder')->name('folderstraucture.folder.save')->middleware('admin_only');
Route::post('/system_settings/folder-structure/delete', 'FoldersStructureController@deleteFolder')->name('folderstraucture.folder.delete')->middleware('admin_only');

// Library
Route::get('/library', 'LibraryController@index')->name('library.index');
Route::get('/library/{id}/download', 'LibraryController@downloadFile')->name('library.file.download');

// Library settings
Route::get('/system_settings/library', 'LibraryController@indexSettings')->name('settings.library.folders.index')->middleware('admin_only');
Route::post('/system_settings/library', 'LibraryController@uploadFiles')->name('settings.library.files.upload')->middleware('admin_only');
Route::get('/system_settings/library-folders/{parentId}/add', 'LibraryController@addFolder')->name('settings.library.folder.create')->middleware('admin_only');
Route::post('/system_settings/library-folders/add', 'LibraryController@saveFolder')->name('settings.library.folder.save')->middleware('admin_only');
Route::post('/system_settings/library/deleteFile', 'LibraryController@deleteFile')->name('settings.library.file.delete')->middleware('admin_only');
Route::post('/library/folder/delete', 'LibraryController@deleteFolder')->name('settings.library.folder.delete')->middleware('admin_only');

// User flagging
Route::get('/system_settings/flags', 'FlagsController@index')->name('settings.flags.index');
Route::get('/system_settings/flags/create', 'FlagsController@create')->name('settings.flags.create');
Route::post('/system_settings/flags', 'FlagsController@store')->name('settings.flags.store');
Route::get('/system_settings/flags/{flag}/edit', 'FlagsController@edit')->name('settings.flags.edit');
Route::patch('/system_settings/flags/{flag}', 'FlagsController@update')->name('settings.flags.update');
Route::delete('/system_settings/flags/{flag}', 'FlagsController@destroy')->name('settings.flags.destroy');

// Custom SMS
Route::get('/system_settings/custom-sms', 'SMSController@index')->name('settings.sms.index');
Route::get('/system_settings/custom-sms/create', 'SMSController@create')->name('settings.sms.create');
Route::post('/system_settings/custom-sms/create', 'SMSController@store')->name('settings.sms.store');

/// Out Of Office reasons
Route::get('/system_settings/ooo-reasons', 'OooController@index')->name('settings.ooo.index');
Route::get('/system_settings/ooo-reasons/create', 'OooController@create')->name('settings.ooo.create');
Route::post('/system_settings/ooo-reasons', 'OooController@store')->name('settings.ooo.store');
Route::get('/system_settings/ooo-reasons/{reason}/edit', 'OooController@edit')->name('settings.ooo.edit');
Route::post('/system_settings/ooo-reasons/{reason}', 'OooController@update')->name('settings.ooo.update');

// Flag user
Route::get('/flag_user/{user}/create', 'FlagUserController@create')->name('flag.user.create');
Route::post('/flag_user/{user}', 'FlagUserController@store')->name('flag.user.store');
Route::delete('/flag_user/{flag}/{user}', 'FlagUserController@destroy')->name('flag.user.destroy');

// Template groups
Route::prefix('/system_settings')->group(function () {
    Route::resource('/groups', 'GroupController');

    // Template Group User
    Route::get('/groups/{group}/users/create', 'GroupUserController@create')->name('groups.users.create');
    Route::post('/groups/{group}/users', 'GroupUserController@store')->name('groups.users');
    Route::delete('/groups/{group}/users/{user}', 'GroupUserController@destroy')->name('groups.users.destroy');

    // Systems
    Route::resource('systems', 'SystemController');

    // Review settings
    Route::get('/review/settings', 'ReviewsController@showSettings')->name('review.settings.show');
    Route::post('/review/settings', 'ReviewsController@saveSettings')->name('review.settings.save');
});

// Task review process
Route::get('/reviews/pending', 'ReviewsController@pending')->middleware('can:view,App\Repositories\Review\Review')->name('reviews.list.pending');
Route::get('/reviews/completed', 'ReviewsController@completed')->middleware('can:view,App\Repositories\Review\Review')->name('reviews.list.completed');
Route::get('/reviews/{review}', 'ReviewsController@show')->middleware('can:view,App\Repositories\Review\Review')->name('reviews.show');
Route::get('/reviews/{review}/tasks/{tasks}', 'ReviewsController@reviewTask')->middleware('can:view,App\Repositories\Review\Review')->name('reviews.review.task');
Route::get('/reviews/{review}/task/{userCompletedTask}/subtask/{userCompletedSubtask}', 'ReviewsController@reviewSubtask')->middleware('can:view,App\Repositories\Review\Review')->name('reviews.review.subtask');
Route::post('/reviews/{review}/reviewed', 'ReviewsController@markReviewed')->middleware('can:update,App\Repositories\Review\Review')->name('reviews.update');
Route::post('/reviews/{review}/approve/task/{userCompletedTask}', 'ReviewsController@approveTask')->middleware('can:update,App\Repositories\Review\Review')->name('reviews.approve.task');
Route::post('/reviews/{review}/approve/subtask/{userCompletedSubtask}', 'ReviewsController@approveSubtask')->middleware('can:update,App\Repositories\Review\Review')->name('reviews.approve.subtask');
Route::post('/reviews/{review}/decline/task/{userCompletedTask}', 'ReviewsController@declineTask')->middleware('can:update,App\Repositories\Review\Review')->name('reviews.decline.task');
Route::post('/reviews/{review}/decline/subtask/{userCompletedSubtask}', 'ReviewsController@declineSubtask')->middleware('can:update,App\Repositories\Review\Review')->name('reviews.decline.subtask');

// Fetch users who belongs to a template
Route::get('/templates/{template}/users', 'TemplateController@users');

// Template Groups
Route::get('/templates/{template}/groups/create', 'GroupTemplateController@create')->name('groups.templates.create');
Route::post('/templates/{template}/groups', 'GroupTemplateController@store')->name('groups.templates');
Route::delete('/templates/{template}/groups/{group}', 'GroupTemplateController@destroy')->name('groups.templates.destroy');

// Processed templates
Route::get('/system_settings/templates/notifications/pending', 'ProcessedTemplatesController@indexPending')->middleware('can:view,App\Repositories\ProcessedNotification\ProcessedNotification')->name('templates.notifications.processed.pending');
Route::get('/system_settings/templates/notifications/declined', 'ProcessedTemplatesController@indexDeclined')->middleware('can:view,App\Repositories\ProcessedNotification\ProcessedNotification')->name('templates.notifications.processed.declined');
Route::get('/system_settings/templates/notifications/{processedNotification}', 'ProcessedTemplatesController@show')->middleware('can:view,App\Repositories\ProcessedNotification\ProcessedNotification')->name('templates.notifications.show');
Route::post('/processed_templates/approve', 'ProcessedTemplatesController@approve')->middleware('can:update,App\Repositories\ProcessedNotification\ProcessedNotification')->name('templates.notifications.processed.approve');
Route::post('/processed_templates/decline', 'ProcessedTemplatesController@decline')->middleware('can:update,App\Repositories\ProcessedNotification\ProcessedNotification')->name('templates.notifications.processed.decline');

// Template overdue reasons
Route::get('/templates/{template}/overdue-reasons/create', 'TemplateOverdueReasonController@create')->name('template.overdue-reason.create');
Route::post('/templates/{template}/overdue-reasons/create', 'TemplateOverdueReasonController@store')->name('template.overdue-reason.store');
Route::get('/templates/{template}/overdue-reasons/{templateOverdueReason}', 'TemplateOverdueReasonController@show')->name('template.overdue-reason.show');
Route::post('/templates/{template}/overdue-reasons/{templateOverdueReason}', 'TemplateOverdueReasonController@update')->name('template.overdue-reason.update');
Route::delete('/templates/{template}/overdue-reasons/{templateOverdueReason}', 'TemplateOverdueReasonController@destroy')->name('template.overdue-reason.destroy');

// Template notifications
Route::get('/templates/{template}/notifications/create', 'TemplateNotificationsController@create')->name('templates.notifications.create');
Route::post('/templates/{template}/notifications', 'TemplateNotificationsController@store')->name('templates.notifications.store');
Route::get('/templates/{template}/notifications/{notification}/edit', 'TemplateNotificationsController@edit')->name('templates.notifications.edit');
Route::get('/templates/{template}/notifications/{notification}', 'TemplateNotificationsController@show')->name('templates.notifications.show');
Route::put('/templates/{template}/notifications/{notification}', 'TemplateNotificationsController@update')->name('templates.notifications.update');
Route::delete('/templates/{template}/notifications/{templateNotification}', 'TemplateNotificationsController@destroy')->name('templates.notifications.destroy');
Route::get('/notifications/{notification}/variables', 'TemplateNotificationsController@getTemplateVariables')->name('templates.notifications.variables');

// Email Templates settings
Route::get('/system_settings/email_templates/folder/{id}', 'EmailTemplateController@detail')->name('email_templates.detail');
Route::get('/system_settings/email_templates/search', 'EmailTemplateController@search')->name('email_templates.search');
Route::get('/system_settings/email_templates/showall', 'EmailTemplateController@showall')->name('email_templates.showall');
Route::get('/system_settings/email_templates/send/{id}', 'EmailTemplateController@send')->name('email_templates.send');
Route::get('/email_templates/{emailTemplate}', 'EmailTemplateController@getTemplateVars');
Route::get('/system_settings/email_templates/add/{folderId}', 'EmailTemplateController@add')->name('email_templates.add');
Route::post('/system_settings/email_templates/create-folder', 'EmailTemplateController@createFolder')->name('email_templates.create-folder');
Route::resource('/system_settings/email_templates', 'EmailTemplateController');

// Rating Templates settings
Route::resource('/system_settings/rating_templates', 'RatingTemplateController');

// Notification log
Route::get('/notification/{notification}', 'NotifierLogController@show');

// Froala WYSIWYG editor routes
Route::post('/editor/image', 'EditorController@storeImage')->name('editor.image.store');
Route::get('/editor/images/{fileName}', 'EditorController@getPrivateImage')->name('editor.file.get');

Route::get('/documentation', 'DocumentationPagesController@index')->name('documentation.pages.index')->middleware('admin_only');
Route::get('/documentation/create', 'DocumentationPagesController@create')->name('documentation.pages.create')->middleware('admin_only');
Route::post('/documentation', 'DocumentationPagesController@store')->name('documentation.pages.store')->middleware('admin_only');
Route::get('/documentation/{documentationPage}/edit', 'DocumentationPagesController@edit')->name('documentation.pages.edit')->middleware('admin_only');
Route::put('/documentation/{documentationPage}', 'DocumentationPagesController@update')->name('documentation.pages.update')->middleware('admin_only');
Route::delete('/documentation/{id}', 'DocumentationPagesController@destroy')->name('documentation.pages.destroy')->middleware('admin_only');
