<?php

use UltraScn\Admin\AdminRouterFactory;

require __DIR__ . '/../bootstrap.php';

$routerFactory = new AdminRouterFactory('admin', [
	'dashboard' => 'Cms:Admin:Dashboard:',
	'users' => 'CmsUsers:Admin:User:',
]);
$route = $routerFactory->createRouter();


// no match
test(function () use ($route) {
	testRouteIn($route, 'admin/', NULL);
});
