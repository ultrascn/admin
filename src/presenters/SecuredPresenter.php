<?php

	namespace UltraScn\Admin\Presenters;

	use Inteve\Navigation\BreadcrumbsControl;
	use Inteve\Navigation\MenuControl;
	use UltraScn\Admin\Components\MenuControlFactory;


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
			return new BreadcrumbsControl($this->getNavigation());
		}


		/**
		 * @return MenuControl
		 */
		protected function createComponentNavigationMain()
		{
			$menuControlFactory = new MenuControlFactory;
			return $menuControlFactory->createMainMenu($this->administration, $this->getNavigation());
		}


		/**
		 * @return MenuControl
		 */
		protected function createComponentNavigationSub()
		{
			$menuControlFactory = new MenuControlFactory;
			return $menuControlFactory->createSubMenu($this->administration, $this->getNavigation());
		}
	}
