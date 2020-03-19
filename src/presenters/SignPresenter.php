<?php

	namespace UltraScn\Admin\Presenters;

	use UltraScn\Admin\Forms;


	abstract class SignPresenter extends BasePresenter
	{
		/** @var string @persistent */
		public $backlink = '';

		/** @var Forms\ISignFormFactory @inject */
		public $signFormFactory;


		public function actionIn()
		{
			if ($this->getUser()->isLoggedIn()) {
				$this->redirect($this->administration->getHomepagePresenter());
			}
		}


		public function renderIn()
		{
			$this->template->setFile(__DIR__ . '/templates/Sign/in.latte');
		}


		public function actionOut()
		{
			$this->user->logout(TRUE);
			$this->flashSuccess('Odhlášení proběhlo v pořádku.');
			$this->redirect($this->administration->getHomepagePresenter());
		}


		protected function createComponentSignForm()
		{
			$form = $this->signFormFactory->create(function () {
				$this->flashSuccess('Byl jste přihlášen.');
				$this->restoreRequest($this->backlink);
				$this->redirect($this->administration->getHomepagePresenter());
			});
			return $form;
		}
	}
