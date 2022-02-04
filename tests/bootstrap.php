<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Nette2xMocks.php';

Tester\Environment::setup();


function test($cb)
{
	$cb();
}


function renderControl(\Nette\Application\UI\Control $control)
{
	ob_start();
	$control->render();
	return ob_get_clean();
}


/**
 * @param  string $url
 * @param  string|NULL $expectedPresenterName
 */
function testRouteIn(
	Nette\Application\IRouter $route,
	$url,
	$expectedPresenter,
	array $expectedParameters = [],
	$expectedUrl = NULL
)
{
	$url = new Nette\Http\UrlScript("http://example.com$url");
	$url->setScriptPath('/');

	$httpRequest = new Nette\Http\Request($url);
	$request = $route->match($httpRequest);

	if ($request) { // match
		$params = $request->getParameters();
		ksort($params);
		ksort($expectedParameters);
		Tester\Assert::same($expectedPresenter, $request->getPresenterName());
		Tester\Assert::same($expectedParameters, $params);

		if ($expectedUrl !== NULL) {
			$result = $route->constructUrl($request, $url);
			$result = strncmp($result, 'http://example.com', 18) ? $result : substr($result, 18);
			Tester\Assert::same($expectedUrl, $result);
		}

	} else {
		Tester\Assert::null($expectedPresenter);
	}
}


/**
 * @param  string $url
 * @param  string|NULL $expectedPresenterName
 */
function testRouteOut(
	Nette\Application\IRouter $route,
	$presenter,
	array $parameters = [],
	$expectedUrl
)
{
	$refUrl = new Nette\Http\UrlScript("http://example.com");
	$refUrl->setScriptPath('/');

	$appRequest = new \Nette\Application\Request($presenter, NULL, $parameters);
	$url = $route->constructUrl($appRequest, $refUrl);
	$url = strncmp($url, 'http://example.com', 18) ? $url : substr($url, 18);
	Tester\Assert::same($expectedUrl, $url);
}


function prepareTempDir()
{
	static $dirs = [];

	@mkdir(__DIR__ . '/temp/');  # @ - directory may already exist

	$tempDir = __DIR__ . '/temp/' . getmypid();

	if (!isset($dirs[$tempDir])) {
		Tester\Helpers::purge($tempDir);
		$dirs[$tempDir] = TRUE;
	}

	return $tempDir;
}
