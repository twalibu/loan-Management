<?php

    // Landing Page
    Route::get('/', 'WelcomeController@show');
    Route::post('/contact', 'WelcomeController@contact');

    // Dashboard
    Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@tenant']);
    
/* ACL */
    // Authorization
    Route::get('/login', ['as' => 'auth.login.form', 'uses' => 'Acl\Tenant\SessionController@getLogin']);
    Route::post('/login', ['as' => 'auth.login.attempt', 'uses' => 'Acl\Tenant\SessionController@postLogin']);
    Route::get('/logout', ['as' => 'auth.logout', 'uses' => 'Acl\Tenant\SessionController@getLogout']);

    // Activation
    Route::get('activate/{code}', ['as' => 'auth.activation.attempt', 'uses' => 'Acl\Tenant\RegistrationController@getActivate']);
    Route::get('resend', ['as' => 'auth.activation.request', 'uses' => 'Acl\Tenant\RegistrationController@getResend']);
    Route::post('resend', ['as' => 'auth.activation.resend', 'uses' => 'Acl\Tenant\RegistrationController@postResend']);

    // Password Reset
    Route::get('password/reset/{code}', ['as' => 'auth.password.reset.form', 'uses' => 'Acl\Tenant\PasswordController@getReset']);
    Route::post('password/reset/{code}', ['as' => 'auth.password.reset.attempt', 'uses' => 'Acl\Tenant\PasswordController@postReset']);
    Route::get('password/reset', ['as' => 'auth.password.request.form', 'uses' => 'Acl\Tenant\PasswordController@getRequest']);
    Route::post('password/reset', ['as' => 'auth.password.request.attempt', 'uses' => 'Acl\Tenant\PasswordController@postRequest']);

    // Users
    Route::resource('users', 'Acl\Tenant\UserController');

    // Roles
    Route::resource('roles', 'Acl\Tenant\RoleController');
/* End of ACL */

/* Settings */
    // Tenant Sales
    Route::resource('sales', 'TenantSaleController');
    
    // Loan Types
    Route::resource('loanTypes', 'LoanTypesController');

    // Overdue Penalts
    Route::resource('loanOverdues', 'LoanOverdueController');

    // Payment Methods
    Route::resource('paymentMethods', 'PayMentMethodController');

    // Staff Types
    Route::resource('types', 'TypeController');

    // Schedule
    Route::resource('schedules', 'TenantScheduleController');
/* End of Settings*/

/* Tenant Routes */
    // Clients
    Route::resource('clients', 'ClientController');

    // Staff
    Route::resource('staff', 'StaffController');

    // Offices
    Route::resource('offices', 'OfficeController');

    // Loans
    Route::resource('loans', 'LoanController');

    // Payments
    Route::resource('payments', 'LoanPaymentController');
    Route::get('pay/{loan}', ['as' => 'pay.loan', 'uses' => 'LoanPaymentController@fromloan']);
    Route::get('overwrite/{loan}', ['as' => 'pay.overwrite', 'uses' => 'LoanPaymentController@overwrite']);
    Route::post('overwrite', ['as' => 'payment.overwrite', 'uses' => 'LoanPaymentController@overwritepost']);

    /* SMS */
    Route::get('communications', ['uses' => 'CommunicationController@tenant']);
    Route::post('sendClients', ['uses' => 'CommunicationController@sendClients']);
    Route::post('sendStaff', ['uses' => 'CommunicationController@sendStaff']);  
/* End of Tenant Routes */

/* Tools */
    // Reports
    Route::get('tools/summary', ['uses' => 'ToolsController@summary']);
    Route::post('tools/report/generate', ['uses' => 'ToolsController@generateReport']);

    // SMS Reports
    Route::get('tools/smsReports', ['uses' => 'ToolsController@smsReports']);
    
    // Loan Contract
    Route::get('contract/{loan}', ['as' => 'contract.loan', 'uses' => 'LoanContractController@index']);
    
    // Today's Transactions
    Route::get('today/payments', 'TodayController@payments');
    
    // Armotisation
    Route::post('projection', 'ArmotisationController@projection');
    Route::post('armotisation', 'ArmotisationController@calculate');

    // Fast Track
    Route::resource('fastTrack', 'FastTrackController');

    // Bulk SMS
    Route::resource('bulk', 'BulkController');
    Route::resource('contacts', 'ContactController');
    Route::get('bulks/add/{id}', ['as' => 'bulks.add', 'uses' => 'BulkController@add']);
    Route::post('bulks/post/add/{id}', ['as' => 'bulks.post.add', 'uses' => 'BulkController@postAdd']);
    Route::post('bulks/send', ['as' => 'bulks.send', 'uses' => 'CommunicationController@sendBulk']);
    Route::post('group/send', ['as' => 'group.send', 'uses' => 'CommunicationController@sendGroup']);
/* End of Tools */

/* Super Admin Routes */
Route::group(['prefix' => 'admin'], function () {
    /* Dashboard */
	Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'DashboardController@admin']);

	/* Subscriptions */
	Route::resource('subscriptions', 'SubscriptionController');

    /* Tools */
    Route::get('smsBalanceCheck', ['uses' => 'AdminToolsController@sms']);
    Route::get('subscriptionCheck', ['uses' => 'AdminToolsController@subscription']);
    
	/* Communications */
	Route::get('communications', ['uses' => 'CommunicationController@admin']);
	Route::post('send', ['uses' => 'CommunicationController@postAdmin']);

	/* Tenants */
	Route::resource('tenants', 'TenantController');
    Route::get('tenant/subscription/{id}', ['as' => 'tenant.subscription', 'uses' => 'TenantSubscriptionController@edit']);
    Route::post('tenant/subscription/{id}', ['as' => 'tenant.subscription.update', 'uses' => 'TenantSubscriptionController@update']);
    Route::get('tenant/topup/{id}', ['as' => 'tenant.balance.topup', 'uses' => 'TenantSMSTopUpController@topUp']);
    Route::post('tenant/topup/{id}', ['as' => 'tenant.balance.posttopup', 'uses' => 'TenantSMSTopUpController@postTopUp']);
	Route::get('tenant/admin/{id}', ['as' => 'tenant.admin', 'uses' => 'TenantController@admin']);
	Route::post('tenant/admin', ['as' => 'tenant.add.admin', 'uses' => 'TenantController@postAdmin']);
	Route::delete('tenant/admin/{id}', ['as' => 'tenant.admin', 'uses' => 'TenantController@destroyAdmin']);
	Route::get('tenant/contact/{id}', ['as' => 'tenant.contact', 'uses' => 'TenantController@contact']);
	Route::post('tenant/contact', ['as' => 'tenant.add.contact', 'uses' => 'TenantController@postContact']);
	Route::delete('tenant/contact/{id}', ['as' => 'tenant.contact', 'uses' => 'TenantController@destroyContact']);

	// Sentinel Session Routes
    Route::get('login', ['as' => 'sentinel.login', 'uses' => 'Acl\Admin\SessionController@create']);
    Route::get('logout', ['as' => 'sentinel.logout', 'uses' => 'Acl\Admin\SessionController@destroy']);
    Route::get('sessions/create', ['as' => 'sentinel.session.create', 'uses' => 'Acl\Admin\SessionController@create']);
    Route::post('sessions/store', ['as' => 'sentinel.session.store', 'uses' => 'Acl\Admin\SessionController@store']);
    Route::delete('sessions/destroy', ['as' => 'sentinel.session.destroy', 'uses' => 'Acl\Admin\SessionController@destroy']);

    // Sentinel Registration
    Route::get('register', ['as' => 'sentinel.register.form', 'uses' => 'Acl\Admin\RegistrationController@registration']);
    Route::post('register', ['as' => 'sentinel.register.user', 'uses' => 'Acl\Admin\RegistrationController@register']);
    Route::get('users/activate/{hash}/{code}', ['as' => 'sentinel.activate', 'uses' => 'Acl\Admin\RegistrationController@activate']);
    Route::get('reactivate', ['as' => 'sentinel.reactivate.form', 'uses' => 'Acl\Admin\RegistrationController@resendActivationForm']);
    Route::post('reactivate', ['as' => 'sentinel.reactivate.send', 'uses' => 'Acl\Admin\RegistrationController@resendActivation']);
    Route::get('forgot', ['as' => 'sentinel.forgot.form', 'uses' => 'Acl\Admin\RegistrationController@forgotPasswordForm']);
    Route::post('forgot', ['as' => 'sentinel.reset.request', 'uses' => 'Acl\Admin\RegistrationController@sendResetPasswordEmail']);
    Route::get('reset/{hash}/{code}', ['as' => 'sentinel.reset.form', 'uses' => 'Acl\Admin\RegistrationController@passwordResetForm']);
    Route::post('reset/{hash}/{code}', ['as' => 'sentinel.reset.password', 'uses' => 'Acl\Admin\RegistrationController@resetPassword']);

    // Sentinel Profile
    Route::get('profile', ['as' => 'sentinel.profile.show', 'uses' => 'Acl\Admin\ProfileController@show']);
    Route::get('profile/edit', ['as' => 'sentinel.profile.edit', 'uses' => 'Acl\Admin\ProfileController@edit']);
    Route::put('profile', ['as' => 'sentinel.profile.update', 'uses' => 'Acl\Admin\ProfileController@update']);
    Route::post('profile/password', ['as' => 'sentinel.profile.password', 'uses' => 'Acl\Admin\ProfileController@changePassword']);

    // Sentinel Users
    Route::get('users', ['as' => 'sentinel.users.index', 'uses' => 'Acl\Admin\UserController@index']);
    Route::get('users/create', ['as' => 'sentinel.users.create', 'uses' => 'Acl\Admin\UserController@create']);
    Route::post('users', ['as' => 'sentinel.users.store', 'uses' => 'Acl\Admin\UserController@store']);
    Route::get('users/{hash}', ['as' => 'sentinel.users.show', 'uses' => 'Acl\Admin\UserController@show']);
    Route::get('users/{hash}/edit', ['as' => 'sentinel.users.edit', 'uses' => 'Acl\Admin\UserController@edit']);
    Route::post('users/{hash}/password', ['as' => 'sentinel.password.change', 'uses' => 'Acl\Admin\UserController@changePassword']);
    Route::post('users/{hash}/memberships', ['as' => 'sentinel.users.memberships', 'uses' => 'Acl\Admin\UserController@updateGroupMemberships']);
    Route::put('users/{hash}', ['as' => 'sentinel.users.update', 'uses' => 'Acl\Admin\UserController@update']);
    Route::delete('users/{hash}', ['as' => 'sentinel.users.destroy', 'uses' => 'Acl\Admin\UserController@destroy']);
    Route::get('users/{hash}/suspend', ['as' => 'sentinel.users.suspend', 'uses' => 'Acl\Admin\UserController@suspend']);
    Route::get('users/{hash}/unsuspend', ['as' => 'sentinel.users.unsuspend', 'uses' => 'Acl\Admin\UserController@unsuspend']);
    Route::get('users/{hash}/ban', ['as' => 'sentinel.users.ban', 'uses' => 'Acl\Admin\UserController@ban']);
    Route::get('users/{hash}/unban', ['as' => 'sentinel.users.unban', 'uses' => 'Acl\Admin\UserController@unban']);

    // Sentinel Groups
    Route::get('groups', ['as' => 'sentinel.groups.index', 'uses' => 'Acl\Admin\GroupController@index']);
    Route::get('groups/create', ['as' => 'sentinel.groups.create', 'uses' => 'Acl\Admin\GroupController@create']);
    Route::post('groups', ['as' => 'sentinel.groups.store', 'uses' => 'Acl\Admin\GroupController@store']);
    Route::get('groups/{hash}', ['as' => 'sentinel.groups.show', 'uses' => 'Acl\Admin\GroupController@show']);
    Route::get('groups/{hash}/edit', ['as' => 'sentinel.groups.edit', 'uses' => 'Acl\Admin\GroupController@edit']);
    Route::put('groups/{hash}', ['as' => 'sentinel.groups.update', 'uses' => 'Acl\Admin\GroupController@update']);
    Route::delete('groups/{hash}', ['as' => 'sentinel.groups.destroy', 'uses' => 'Acl\Admin\GroupController@destroy']);
});
