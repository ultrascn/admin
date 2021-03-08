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
			$moduleToPage = [];
			$presenterToPage = [];

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

					if (!isset($moduleToPage[$moduleName]) || $moduleToPage[$moduleName]->getLevel() < $page->getLevel()) {
						$moduleToPage[$moduleName] = $page;
					}
				}

				if (!isset($presenterToPage[$presenterName]) || $presenterToPage[$presenterName]->getLevel() < $page->getLevel()) {
					$presenterToPage[$presenterName] = $page;
				}

				$items[] = [
					'pageId' => $pageId,
					'presenterName' => $presenterName,
					'moduleName' => $moduleName,
					'rank' => 0,
				];
			}

			if (isset($presenterToPage[$currentPresenterName])) { // exact match
				$navigation->setCurrentPage($presenterToPage[$currentPresenterName]->getId());

			} else {
				$currentModuleName = self::extractModuleName($currentPresenterName);

				if ($currentModuleName !== NULL && isset($moduleToPage[$currentModuleName])) {
					$navigation->setCurrentPage($moduleToPage[$currentModuleName]->getId());
				}
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
