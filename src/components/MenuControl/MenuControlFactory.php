<?php

	namespace UltraScn\Admin\Components;

	use Inteve\Navigation;
	use UltraScn\Admin\Administration;


	class MenuControlFactory
	{
		/**
		 * @return Navigation\MenuControl
		 */
		public function createMainMenu(
			Administration $administration,
			Navigation\Navigation $navigation
		)
		{
			$menuControl = $this->create($administration, $navigation, TRUE);
			$menuControl->setSubLevel(0);
			return $menuControl;
		}


		/**
		 * @return Navigation\MenuControl
		 */
		public function createSubMenu(
			Administration $administration,
			Navigation\Navigation $navigation
		)
		{
			$menuControl = $this->create($administration, $navigation, FALSE);
			$menuControl->setSubLevel(1);
			$menuControl->setLevelLimit(NULL);
			return $menuControl;
		}


		/**
		 * @param  bool $showSignOutLink
		 * @return Navigation\MenuControl
		 */
		private function create(
			Administration $administration,
			Navigation\Navigation $navigation,
			$showSignOutLink
		)
		{
			$menuControl = new Navigation\MenuControl($navigation);
			$menuControl->setTemplateFile(
				__DIR__ . '/navigation.latte',
				[
					'showSignOutLink' => $showSignOutLink,
					'administration' => $administration,
				]
			);

			return $menuControl;
		}
	}
