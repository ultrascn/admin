<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Nette2xMocks.php';

Tester\Environment::setup();


/**
 * @return void
 */
function test(callable $cb)
{
	$cb();
}


/**
 * @return string
 */
function renderControl(\Nette\Application\UI\Control $control)
{
	if (!method_exists($control, 'render')) {
		throw new \RuntimeException('Control has not render() method.');
	}

	ob_start();
	$control->render();
	return (string) ob_get_clean();
}


/**
 * @param  string $url
 * @param  string|NULL $expectedPresenter
 * @param  array<string, mixed> $expectedParameters
 * @param  string|NULL $expectedUrl
 * @return void
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

			if (is_string($result)) {
				$result = strncmp($result, 'http://example.com', 18) ? $result : substr($result, 18);
			}

			Tester\Assert::same($expectedUrl, $result);
		}

	} else {
		Tester\Assert::null($expectedPresenter);
	}
}


/**
 * @param  string $presenter
 * @param  array<string, mixed> $parameters
 * @param  string|NULL $expectedUrl
 * @return void
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

	if (is_string($url)) {
		$url = strncmp($url, 'http://example.com', 18) ? $url : substr($url, 18);
	}

	Tester\Assert::same($expectedUrl, $url);
}


/**
 * @return string
 */
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
