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
		$navigation->addPage('/', 'Dashboard', 'Cms:Admin:Dashboard:');

		$navigation->addPage('settings/website', 'Websites', ':CmsSites:Admin:Website:');
		$navigation->addPage('settings/users', 'Users', 'CmsUsers:Admin:User:');
		$navigation->addPage('settings', 'Settings', 'CmsUsers:Admin:User:');
		$navigation->addPage('settings/change-password', 'Change password', 'CmsUsers:Admin:ChangePassword:');

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
	NavigationHelper::trySelectCurrentPage($navigation, 'CmsUsers:Admin:UserRole');

	Assert::same(implode("\n", [
		'<div class="navigation">',
		'	<div class="navigation__inner">',
		'			<a href="#presenter=:Cms:Admin:Dashboard:" class="navigation__item">Dashboard</a>',
		'			<a href="#presenter=:CmsUsers:Admin:User:" class="navigation__item navigation__item--active">Settings</a>',
		'		<a href="#presenter=:Admin:Sign:out" class="navigation__item">Odhlásit se</a>',
		'	</div>',
		'</div>',
		'',
	]), renderControl($presenter['menu']));
});
