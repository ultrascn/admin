<?php

	namespace UltraScn\Admin\Forms;

	use Nette;
	use Nette\Application\UI\Form;
	use Nette\Security\IAuthenticator;
	use UltraScn\Admin\Model;


	class SignFormFactory implements ISignFormFactory
	{
		/** @var Model\Authenticator */
		private $authenticator;

		/** @var Nette\Security\User */
		private $user;

		/** @var FormFactory */
		private $formFactory;


		public function __construct(
			IAuthenticator $authenticator,
			Nette\Security\User $user,
			FormFactory $formFactory
		)
		{
			$this->authenticator = $authenticator;
			$this->user = $user;
			$this->formFactory = $formFactory;
		}


		public function create(callable $onSuccess)
		{
			$form = $this->formFactory->create();
			$form->addText('username', 'Uživatelské jméno')
				->setRequired('Zadejte prosím svoje uživatelské jméno.');

			$form->addPassword('password', 'Heslo')
				->setRequired('Prosím zadejte svoje heslo.');

			$form->addSubmit('send', 'Přihlásit se');

			$form->onSuccess[] = function ($form, $values) use ($onSuccess) {
				try {
					$identity = $this->authenticator->authenticate([$values->username, $values->password]);
					$this->user->login($identity);

				} catch (Nette\Security\AuthenticationException $e) {
					$msg = 'Neplatné přihlašovací jméno nebo heslo.';
					$form->addError($msg);
					return;
				}

				$onSuccess();
			};

			return $form;
		}
	}
