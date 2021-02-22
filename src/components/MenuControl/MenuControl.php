<?php

	namespace UltraScn\Admin\Components;

	use Inteve\Navigation;
	use UltraScn\Admin\Administration;


	class MenuControl extends \Nette\Application\UI\Control
	{
		const TYPE_MAIN_MENU = 0;
		const TYPE_SUB_MENU = 1;

		/** @var Administration */
		private $administration;

		/** @var Navigation\Navigation */
		private $navigation;

		/** @var int */
		private $type;


		/**
		 * @param int $type
		 */
		public function __construct(
			Administration $administration,
			Navigation\Navigation $navigation,
			$type
		)
		{
			$this->administration = $administration;
			$this->navigation = $navigation;
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
			$navigation = $this->navigation;
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

			$presenter = $this->getPresenter();
			assert($presenter !== NULL);

			$currentPresenterName = $presenter->getName();
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
			$template->linkGenerator = new Navigation\DefaultLinkGenerator($presenter, $template->basePath);
			assert($template instanceof \Nette\Bridges\ApplicationLatte\Template);
			$template->render(__DIR__ . '/navigation.latte');
		}


		/**
		 * @param  string|NULL $presenterName
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
	}
