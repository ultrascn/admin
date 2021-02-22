<?php

	namespace UltraScn\Admin\Presenters;

	use Nette;
	use UltraScn\Admin\Administration;
	use UltraScn\Admin\Model;


	abstract class BasePresenter extends Nette\Application\UI\Presenter
	{
		use \Inteve\Application\TFlashMessages;

		/** @var Administration @inject */
		public $administration;

		/** @var \Inteve\Navigation\Navigation */
		private $navigation;


		/**
		 * @return string[]
		 */
		public function formatLayoutTemplateFiles()
		{
			return [
				__DIR__ . '/templates/@layout.latte',
			];
		}


		protected function beforeRender()
		{
			parent::beforeRender();
			assert($this->template instanceof Nette\Bridges\ApplicationLatte\Template);
			$this->template->addFilter('date', [Model\Filters::class, 'date']);
			$this->template->addFilter('datetime', [Model\Filters::class, 'datetime']);
			$this->template->administration = $this->administration;
		}


		/**
		 * @return \Inteve\Navigation\Navigation
		 */
		protected function getNavigation()
		{
			if ($this->navigation === NULL) {
				$this->navigation = $this->administration->createNavigation($this->user->isLoggedIn() ? $this->user->id : NULL);
			}

			return $this->navigation;
		}
	}
