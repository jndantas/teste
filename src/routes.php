<?php

use Simcify\Router;
use Simcify\Exceptions\Handler;
use Simcify\Middleware\Authenticate;
use Simcify\Middleware\RedirectIfAuthenticated;
use Pecee\Http\Middleware\BaseCsrfVerifier;

/**
 * ,------,
 * | NOTE | CSRF Tokens are checked on all PUT, POST and GET requests. It
 * '------' should be passed in a hidden field named "csrf-token" or a header
 *          (in the case of AJAX without credentials) called "X-CSRF-TOKEN"
 *  */ 
Router::csrfVerifier(new BaseCsrfVerifier());

// Router::group(['prefix' => '/signer'], function() {

    Router::group(['exceptionHandler' => Handler::class], function() {

        Router::group(['middleware' => Simcify\Middleware\Authenticate::class], function() {

            /**
             *  login Required pages
             **/ 

            // Dashboard
            Router::get('/', 'Dashboard@get');
            Router::get('/', 'Dashboard@get');

            // Notifications
            Router::get('/notifications', 'Notification@get');
            Router::post('/notifications/read', 'Notification@read');
            Router::post('/notifications/count', 'Notification@count');
            Router::post('/notifications/delete', 'Notification@delete');


            // Documents
            Router::get('/documents', 'Document@get');
            Router::get('/document/{docId}/download', 'Document@download', ['as' => 'docId']);
            Router::get('/document/{document_key}', 'Document@open');
            Router::post('/documents/sign', 'Document@sign');
            Router::post('/documents/send', 'Document@send');
            Router::post('/documents/fetch', 'Document@fetch');
            Router::post('/documents/delete', 'Document@delete');
            Router::post('/documents/restore', 'Document@restore');
            Router::post('/documents/convert', 'Document@convert');
            Router::post('/documents/protect', 'Document@protect');
            Router::post('/documents/replace', 'Document@replace');
            Router::post('/documents/relocate', 'Document@relocate');
            Router::post('/documents/duplicate', 'Document@duplicate');
            Router::post('/documents/upload/file', 'Document@uploadfile');
            Router::post('/documents/update/file', 'Document@updatefile');
            Router::post('/documents/update/file/access', 'Document@updatefileaccess');
            Router::post('/documents/update/file/acess/view', 'Document@updatefileaccessview');
            Router::post('/documents/delete/file', 'Document@deletefile');
            Router::post('/documents/create/folder', 'Document@createfolder');
            Router::post('/documents/update/folder', 'Document@updatefolder');
            Router::post('/documents/update/folder/access', 'Document@updatefolderaccess');
            Router::post('/documents/update/folder/access/view', 'Document@updatefolderaccessview');
            Router::post('/documents/update/folder/protect', 'Document@updatefolderprotect');
            Router::post('/documents/update/folder/protect/view', 'Document@updatefolderprotectview');
            Router::post('/documents/delete/folder', 'Document@deletefolder');
            Router::post('/documents/import/dropbox', 'Document@dropboximport');
            Router::post('/documents/import/googledrive', 'Document@googledriveimport');

            // Templates
            Router::get('/templates', 'Template@get');
            Router::post('/templates/fetch', 'Template@fetch');
            Router::post('/templates/create', 'Template@create');
            Router::post('/templates/upload/file', 'Template@uploadfile');
            Router::post('/templates/import/dropbox', 'Template@dropboximport');
            Router::post('/templates/import/googledrive', 'Template@googledriveimport');

            // Chat
            Router::post('/chat/post', 'Chat@post');
            Router::post('/chat/fetch', 'Chat@fetch');

            // Fields
            Router::post('/field/save', 'Field@save');
            Router::post('/field/delete', 'Field@delete');

            // Requests
            Router::get('/requests', 'Request@get');
            Router::post('/requests/send', 'Request@send');
            Router::post('/requests/delete', 'Request@delete');
            Router::post('/requests/cancel', 'Request@cancel');
            Router::post('/requests/remind', 'Request@remind');
            Router::post('/requests/decline', 'Request@decline');

            // Chat
            Router::post('/signature/save', 'Signature@save');
            Router::post('/signature/save/upload', 'Signature@upload');
            Router::post('/signature/save/draw', 'Signature@draw');

            // Team
            Router::get('/team', 'Team@get');
            Router::post('/team/create', 'Team@create');
            Router::post('/team/update', 'Team@update');
            Router::post('/team/update/view', 'Team@updateview');
            Router::post('/team/delete', 'Team@delete');

            // Departments
            Router::get('/departments', 'Department@get');
            Router::post('/departments/create', 'Department@create');
            Router::post('/departments/update', 'Department@update');
            Router::post('/departments/update/view', 'Department@updateview');
            Router::post('/departments/delete', 'Department@delete');

            // customers
            Router::get('/customers', 'Customer@get');
            Router::post('/customers/create', 'Customer@create');
            Router::post('/customers/update', 'Customer@update');
            Router::post('/customers/update/view', 'Customer@updateview');
            Router::post('/customers/delete', 'Customer@delete');

            // Companies
            Router::get('/companies', 'Company@get');
            Router::post('/companies/update', 'Company@update');
            Router::post('/companies/update/view', 'Company@updateview');
            Router::post('/companies/delete', 'Company@delete');

            // users
            Router::get('/users', 'User@get');
            Router::post('/users/create', 'User@create');
            Router::post('/users/update', 'User@update');
            Router::post('/users/update/view', 'User@updateview');
            Router::post('/users/delete', 'User@delete');

            // settings
            Router::get('/settings', 'Settings@get');
            Router::post('/settings/update/profile', 'Settings@updateprofile');
            Router::post('/settings/update/company', 'Settings@updatecompany');
            Router::post('/settings/update/system', 'Settings@updatesystem');
            Router::post('/settings/update/reminders', 'Settings@updatereminders');
            Router::post('/settings/update/password', 'Settings@updatepassword');

            // Auth
            Router::get('/signout', 'Auth@signout');
            

        });
            
        Router::group(['middleware' => Simcify\Middleware\RedirectIfAuthenticated::class], function() {

            /**
             * No login Required pages
             **/ 
            Router::get('/signin', 'Auth@get');
            Router::post('/signin/validate', 'Auth@signin');
            Router::post('/forgot', 'Auth@forgot');
            Router::get('/reset/{token}', 'Auth@getreset', ['as' => 'token']);
            Router::post('/reset', 'Auth@reset');
            Router::post('/signup', 'Auth@signup');
            Router::get('/view/{document_key}', 'Guest@open');
            Router::post('/guest/decline', 'Guest@decline');
            Router::post('/guest/sign', 'Guest@sign');

        });

        Router::get('/404', function() {
            response()->httpCode(404);
            echo view();
        });
    });

// });
