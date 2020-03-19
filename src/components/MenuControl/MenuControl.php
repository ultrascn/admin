<?php

	namespace UltraScn\Admin\Components;

	use Inteve\Navigation;
	use Nette\Utils\Strings;
	use UltraScn\Admin\Administration;


	class MenuControl extends \Nette\Application\UI\Control
	{
		const TYPE_MAIN_MENU = 0;
		const TYPE_SUB_MENU = 1;

		/** @var Administration */
		private $administration;

		/** @var int */
		private $type;


		public function __construct(Administration $administration, $type)
		{
			$this->administration = $administration;
			$this->type = $type;

			if ($type !== self::TYPE_MAIN_MENU && $type !== self::TYPE_SUB_MENU) {
				throw new \UltraScn\Admin\InvalidArgumentException("Invalid type '$type'.");
			}
		}


		/**
		 * @return void
		 */
		public function render()
		{
			$items = [];
			$subTreeLevel = $this->type === self::TYPE_MAIN_MENU ? 0 : 1;
			$navigation = $this->administration->getNavigation();
			$usages = [];
			// TODO: pro submenu musime podle aktualniho presenteru vybrat odpovidajici subtree

			foreach ($navigation->getPages() as $pageId => $page) {
				if (!$page->hasLink()) {
					continue;
				}

				$pageLevel = substr_count($pageId, '/');

				if ($subTreeLevel === $pageLevel) {
					$presenterName = self::extractPresenterName($page);
					$moduleName = $this->extractModuleName($presenterName);

					if ($moduleName !== NULL) {
						if (!isset($usages[$moduleName])) {
							$usages[$moduleName] = 0;
						}

						$usages[$moduleName]++;
					}

					$items[] = [
						'page' => $page,
						'presenterName' => $presenterName,
						'moduleName' => $moduleName,
						'active' => FALSE,
					];
				}
			}

			$currentPresenterName = $this->getPresenter()->getName();
			$currentModuleName = $this->extractModuleName($currentPresenterName);

			foreach ($items as &$item) {
				$moduleName = $item['moduleName'];

				if ($moduleName === NULL || $usages[$moduleName] > 1) {
					$item['active'] = $currentPresenterName === $item['presenterName'];

				} else {
					$item['active'] = $currentModuleName === $moduleName;
				}
			}

			$template = $this->createTemplate();
			$template->items = $items;
			$template->showSignOutLink = $this->type === self::TYPE_MAIN_MENU;
			$template->administration = $this->administration;
			$template->linkGenerator = new Navigation\DefaultLinkGenerator($this->getPresenter(), $template->basePath);
			$template->render(__DIR__ . '/navigation.latte');
		}


		/**
		 * @param  string|NULL
		 * @return string|NULL
		 */
		private function extractModuleName($presenterName)
		{
			if ($presenterName === NULL) {
				return NULL;
			}

			$pos = strrpos($presenterName, ':');
			return $pos !== FALSE ? substr($presenterName, 0, $pos) : NULL;
		}


		/**
		 * @return string|NULL
		 */
		public static function extractPresenterName(Navigation\NavigationItem $item)
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
	}
