<?php

	namespace UltraScn\Admin\Forms;

	use Nette\Application\UI\Form;
	use Typro;


	class FormFactory implements IFormFactory
	{
		/**
		 * @return Form
		 */
		public function create()
		{
			$form = new Form;
			$form->onError[] = function ($form) {
				$errors = $form->getOwnErrors();

				if (empty($errors)) { // errors only for controls
					$form->addError('Formulář obsahuje chyby, opravte je prosím.');
				}
			};

			Typro\Bridges\NetteForms\FormConfigurator::configure($form);
			return $form;
		}
	}
