<?php

use UltraScn\Admin\AdminRouterFactory;

require __DIR__ . '/../bootstrap.php';

$routerFactory = new AdminRouterFactory('admin', [
	'dashboard' => 'Cms:Admin:Dashboard:',
	// 'sign' => 'App:Admin:Sign:in',
	'users' => 'CmsUsers:Admin:User:',
], 'dashboard', 'App:Admin:Homepage:');
$route = $routerFactory->createRouter();


// no match
test(function () use ($route) {
	testRouteIn($route, 'front/', NULL);
});


// dashboard
test(function () use ($route) {
	testRouteIn($route, '/admin/', 'Cms:Admin:Dashboard', [
		'id' => NULL,
		'action' => 'default',
	], '/admin/');

	testRouteIn($route, '/admin/dashboard/', 'Cms:Admin:Dashboard', [
		'id' => NULL,
		'action' => 'default',
	], '/admin/');
});


// sign presenter
test(function () use ($route) {
	testRouteIn($route, '/admin/sign/', 'App:Admin:Sign', [
		'id' => NULL,
		'action' => 'default',
	], '/admin/sign/');

	testRouteIn($route, '/admin/sign/in/', 'App:Admin:Sign', [
		'id' => NULL,
		'action' => 'in',
	], '/admin/sign/in/');

	testRouteIn($route, '/admin/sign/out/', 'App:Admin:Sign', [
		'id' => NULL,
		'action' => 'out',
	], '/admin/sign/out/');
});


// module presenter
test(function () use ($route) {
	testRouteIn($route, '/admin/users/', 'CmsUsers:Admin:User', [
		'id' => NULL,
		'action' => 'default',
	], '/admin/users/');

	testRouteIn($route, '/admin/users/user/edit/10', 'CmsUsers:Admin:User', [
		'id' => '10',
		'action' => 'edit',
	], '/admin/users/user/edit/10');

	testRouteIn($route, '/admin/users/user/default/1?do=delete', 'CmsUsers:Admin:User', [
		'id' => '1',
		'action' => 'default',
		'do' => 'delete'
	], '/admin/users/user/default/1?do=delete');

	testRouteIn($route, '/admin/users/user-role/', 'CmsUsers:Admin:UserRole', [
		'id' => NULL,
		'action' => 'default',
	], '/admin/users/user-role/');

	testRouteIn($route, '/admin/users/user-role/default/1?do=delete', 'CmsUsers:Admin:UserRole', [
		'id' => '1',
		'action' => 'default',
		'do' => 'delete',
	], '/admin/users/user-role/default/1?do=delete');
});


// app presenter
test(function () use ($route) {
	testRouteIn($route, '/admin/non-exists', 'App:Admin:NonExists', [
		'id' => NULL,
		'action' => 'default',
	], '/admin/non-exists/');

	testRouteIn($route, '/admin/some-app/delete', 'App:Admin:SomeApp', [
		'id' => NULL,
		'action' => 'delete',
	], '/admin/some-app/delete/');

	testRouteIn($route, '/admin/some-app/edit/10', 'App:Admin:SomeApp', [
		'id' => '10',
		'action' => 'edit',
	], '/admin/some-app/edit/10');
});
