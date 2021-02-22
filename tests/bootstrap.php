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
