<?php

	namespace UltraScn\Admin\Forms;

	use Nette;


	interface ISignFormFactory
	{
		/** @return Nette\Forms\Form */
		function create(callable $onSuccess);
	}
