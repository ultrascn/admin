<?php

use Inteve\Navigation\Navigation;
use Tester\Assert;
use UltraScn\Admin\Model\NavigationHelper;

require __DIR__ . '/../bootstrap.php';


function createNavigation()
{
	$navigation = new Navigation;
	$navigation->addPage('/', 'Dashboard', 'Admin:Dashboard:');
	$navigation->addPage('eshop/products', 'Products', 'Admin:Products:');
	$navigation->addPage('eshop', 'Eshop', 'Admin:Products:list');
	$navigation->addPage('eshop/orders', 'Orders', 'Admin:Orders:');
	$navigation->addPage('settings', 'Settings', 'Core:Settings:default');
	$navigation->addPage('settings/users', 'Users', 'UserPackage:Admin:Users:default');
	$navigation->addPage('settings/shops', 'Shops', 'Eshop:Shops:default');

	return $navigation;
}


function selectCurrentPage(Navigation $navigation, $currentPresenter)
{
	NavigationHelper::trySelectCurrentPage($navigation, $currentPresenter);
	$currentPage = $navigation->getCurrentPage();
	$navigation->setCurrentPage(NULL);
	return $currentPage;
}


test(function () {
	$navigation = createNavigation();
	Assert::null($navigation->getCurrentPage());

	Assert::same('', selectCurrentPage($navigation, 'Admin:Dashboard'));

	Assert::same('eshop/products', selectCurrentPage($navigation, 'Admin:Products'));

	Assert::same('eshop/orders', selectCurrentPage($navigation, 'Admin:Orders'));

	Assert::same('settings', selectCurrentPage($navigation, 'Core:Settings'));

	Assert::same('settings/users', selectCurrentPage($navigation, 'UserPackage:Admin:Users'));
	Assert::same('settings/users', selectCurrentPage($navigation, 'UserPackage:Admin:UserRoles'));

	Assert::same('settings/shops', selectCurrentPage($navigation, 'Eshop:Shops'));
});
