<?php

	namespace UltraScn\Admin\Model;

	use Inteve\Navigation;


	class NavigationHelper
	{
		public function __construct()
		{
			throw new \UltraScn\Admin\StaticClassException;
		}


		/**
		 * @param  string|NULL $currentPresenterName absolute, without action and ':'
		 * @return void
		 */
		public static function trySelectCurrentPage(Navigation\Navigation $navigation, $currentPresenterName)
		{
			if ($currentPresenterName === NULL) {
				return;
			}

			if ($navigation->getCurrentPage() !== NULL) { // already selected
				return;
			}

			$items = [];
			$usages = [];

			foreach ($navigation->getPages() as $pageId => $page) {
				if (!$page->hasLink()) {
					continue;
				}

				$presenterName = self::extractPresenterName($page);
				$moduleName = self::extractModuleName($presenterName);

				if ($moduleName !== NULL) {
					if (!isset($usages[$moduleName])) {
						$usages[$moduleName] = 0;
					}

					$usages[$moduleName]++;
				}

				$items[] = [
					'pageId' => $pageId,
					'presenterName' => $presenterName,
					'moduleName' => $moduleName,
					'rank' => 0,
				];
			}

			$currentModuleName = self::extractModuleName($currentPresenterName);
			$candidate = NULL;
			$candidateLevel = NULL;

			foreach ($items as $item) {
				$moduleName = $item['moduleName'];
				$matched = FALSE;

				if ($moduleName === NULL || $usages[$moduleName] > 1) {
					$matched = $currentPresenterName === $item['presenterName'];

				} else {
					$matched = $currentModuleName === $moduleName;
				}

				if ($matched) {
					if ($candidate === NULL) {
						$candidate = $item['pageId'];
						$candidateLevel = Navigation\Helpers::getPageLevel($candidate);

					} else { /** @phpstan-ignore-line */
						$myLevel = Navigation\Helpers::getPageLevel($item['pageId']);

						if ($myLevel > $candidateLevel) {
							$candidate = $item['pageId'];
							$candidateLevel = $myLevel;
						}
					}
					break;
				}
			}

			if ($candidate !== NULL) {
				$navigation->setCurrentPage($candidate);
			}
		}


		/**
		 * @return string|NULL
		 */
		public static function extractPresenterName(Navigation\NavigationPage $item)
		{
			$link = $item->getLink();

			if ($link === NULL || !($link instanceof Navigation\NetteLink)) {
				return NULL;
			}

			$destination = ltrim($link->getDestination(), ':');
			$pos = strrpos($destination, ':');

			if ($pos === FALSE) { // 'this' or action name
				return NULL;
			}

			return substr($destination, 0, $pos);
		}


		/**
		 * @param  string|NULL $presenterName
		 * @return string|NULL
		 */
		public static function extractModuleName($presenterName)
		{
			if ($presenterName === NULL) {
				return NULL;
			}

			$pos = strrpos($presenterName, ':');
			return $pos !== FALSE ? substr($presenterName, 0, $pos) : NULL;
		}
	}
