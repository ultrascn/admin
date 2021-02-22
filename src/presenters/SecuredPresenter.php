<?php

	namespace UltraScn\Admin\Presenters;

	use Inteve\Navigation\BreadcrumbsControl;
	use UltraScn\Admin\Components\MenuControl;


	abstract class SecuredPresenter extends BasePresenter
	{
		use \Inteve\Application\TSecured;


		protected function startup()
		{
			parent::startup();
			$this->checkUserRelogin($this->administration->getSignPresenter(), NULL, 'Byl jste odhlášen z důvodu neaktivity.');
		}


		/**
		 * @return BreadcrumbsControl
		 */
		protected function createComponentBreadcrumbs()
		{
			$navigation = $this->getNavigation();

			if ($navigation->getCurrentPage() === NULL) {
				$currentPresenterName = $this->getName();

				foreach ($navigation->getPages() as $pageId => $page) {
					if (MenuControl::extractPresenterName($page) === $currentPresenterName) {
						$navigation->setCurrentPage($pageId);
						break;
					}
				}
			}

			return new BreadcrumbsControl($navigation);
		}


		/**
		 * @return MenuControl
		 */
		protected function createComponentNavigationMain()
		{
			return new MenuControl($this->administration, $this->getNavigation(), MenuControl::TYPE_MAIN_MENU);
		}


		/**
		 * @return MenuControl
		 */
		protected function createComponentNavigationSub()
		{
			return new MenuControl($this->administration, $this->getNavigation(), MenuControl::TYPE_SUB_MENU);
		}
	}
