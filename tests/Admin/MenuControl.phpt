<?php

use Inteve\AssetsManager\AssetsManager;
use UltraScn\Admin\Administration;
use UltraScn\Admin\INavigationFactory;
use UltraScn\Admin\Components\MenuControlFactory;
use Inteve\Navigation\Navigation;
use Tester\Assert;
use UltraScn\Admin\Model\NavigationHelper;

require __DIR__ . '/../bootstrap.php';


class NavigationFactory implements INavigationFactory
{
	public function create($userId)
	{
		$navigation = new Navigation;
		$navigation->addPage('/', 'Dashboard', 'Admin:Dashboard:');
		$navigation->addPage('eshop', 'Eshop', 'Admin:Products:');
		$navigation->addPage('eshop/products', 'Products', 'Admin:Products:');
		$navigation->addPage('eshop/orders', 'Orders', 'Admin:Orders:');
		$navigation->addPage('settings', 'Settings', 'Core:Settings:default');
		$navigation->addPage('settings/users', 'Users', 'Core:Users:default');
		$navigation->addPage('settings/shops', 'Shops', 'Eshop:Shops:default');

		return $navigation;
	}
}


function createAdministration()
{
	return new Administration(
		'Admin',
		'Admin:Dashboard:',
		'Admin:Sign:in',
		'Admin:Sign:out',
		new AssetsManager(TRUE),
		new NavigationFactory
	);
}


test(function () {
	$administration = createAdministration();
	$navigation = $administration->createNavigation(1);
	$menuControlFactory = new MenuControlFactory;

	$presenter = new MockPresenter;
	$presenter['menu'] = $menuControlFactory->createMainMenu($administration, $navigation);
	$navigation->setCurrentPage(NULL);

	Assert::same(implode("\n", [
		'<div class="navigation">',
		'	<div class="navigation__inner">',
		'			<a href="#presenter=:Admin:Dashboard:" class="navigation__item">Dashboard</a>',
		'			<a href="#presenter=:Admin:Products:" class="navigation__item">Eshop</a>',
		'			<a href="#presenter=:Core:Settings:default" class="navigation__item">Settings</a>',
		'		<a href="#presenter=:Admin:Sign:out" class="navigation__item">Odhlásit se</a>',
		'	</div>',
		'</div>',
		'',
	]), renderControl($presenter['menu']));

	$navigation->setCurrentPage(NULL);
	NavigationHelper::trySelectCurrentPage($navigation, 'Admin:Orders');

	Assert::same(implode("\n", [
		'<div class="navigation">',
		'	<div class="navigation__inner">',
		'			<a href="#presenter=:Admin:Dashboard:" class="navigation__item">Dashboard</a>',
		'			<a href="#presenter=:Admin:Products:" class="navigation__item navigation__item--active">Eshop</a>',
		'			<a href="#presenter=:Core:Settings:default" class="navigation__item">Settings</a>',
		'		<a href="#presenter=:Admin:Sign:out" class="navigation__item">Odhlásit se</a>',
		'	</div>',
		'</div>',
		'',
	]), renderControl($presenter['menu']));
});


test(function () {
	$administration = createAdministration();
	$navigation = $administration->createNavigation(1);
	$menuControlFactory = new MenuControlFactory;

	$presenter = new MockPresenter;
	$presenter['menu'] = $menuControlFactory->createSubMenu($administration, $navigation);
	$navigation->setCurrentPage(NULL);

	Assert::same('', renderControl($presenter['menu']));

	$navigation->setCurrentPage(NULL);
	NavigationHelper::trySelectCurrentPage($navigation, 'Admin:Orders');

	Assert::same(implode("\n", [
		'<div class="navigation">',
		'	<div class="navigation__inner">',
		'			<a href="#presenter=:Admin:Products:" class="navigation__item">Products</a>',
		'			<a href="#presenter=:Admin:Orders:" class="navigation__item navigation__item--active">Orders</a>',
		'	</div>',
		'</div>',
		'',
	]), renderControl($presenter['menu']));
});
