<?php

use Inteve\AssetsManager\AssetsManager;
use Inteve\Navigation\Navigation;
use Nette\Configurator;
use Tester\Assert;
use UltraScn\Admin\Administration;
use UltraScn\Admin\AdminRouterFactory;
use UltraScn\Admin\INavigationFactory;

require __DIR__ . '/../bootstrap.php';
define('TEMP_DIR', prepareTempDir());
define('WWW_DIR', TEMP_DIR . '/www');
@mkdir(WWW_DIR, 0777, TRUE);

class MyNavigationFactory implements INavigationFactory
{
	public function create($userId)
	{
		$navigation = new Navigation;
		$navigation->addPage('/', 'Homepage');
		return $navigation;
	}
}

/**
 * @param  \Inteve\AssetsManager\AssetFile[] $files
 * @return string[]
 */
function extractPaths(array $files)
{
	$res = [];

	foreach ($files as $file) {
		$res[] = $file->getPath();
	}

	return $res;
}

test(function () {
	$configurator = new Configurator;
	$configurator->setTempDirectory(TEMP_DIR);
	$configurator->addParameters([
		'wwwDir' => WWW_DIR,
	]);
	$configurator->addConfig(__DIR__ . '/config/AdminExtension.1.neon');

	/** @var \Nette\DI\Container $container */
	$container = $configurator->createContainer();
	Assert::true($container instanceof \Nette\DI\Container);

	// Administration
	$administration = $container->getByType(Administration::class);
	Assert::true($administration instanceof Administration);
	Assert::same('MyAdmin', $administration->getTitle());
	Assert::same(':My:Homepage:', $administration->getHomepagePresenter());
	Assert::same(':My:Sign:in', $administration->getSignPresenter());
	Assert::same(':My:Sign:out', $administration->getSignOutLink());

	// router
	$routerFactory = $container->getByType(AdminRouterFactory::class);
	Assert::true($routerFactory instanceof AdminRouterFactory);

	// assets manager
	$assetsManager = $container->getByType(AssetsManager::class, FALSE);
	Assert::null($assetsManager);

	$assetsManager = $container->getService('admin.assetsManager');
	Assert::true($assetsManager instanceof AssetsManager);

	Assert::same([
		'path/to/ultrascn/admin/styles.less',
	], extractPaths($assetsManager->getStylesheets()));

	Assert::same([
		'path/to/netteForms.js',
	], extractPaths($assetsManager->getScripts()));

	Assert::same([
		'path/to/less.js',
	], extractPaths($assetsManager->getCriticalScripts()));

});
