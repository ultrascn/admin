<?php

	namespace UltraScn\Admin\DI;

	use Nette;


	class AdminExtension extends Nette\DI\CompilerExtension
	{
		const ASSETS_FLAG_CRITICAL = 'critical';


		/** @var array<string, mixed> */
		private $defaults = [
			'title' => 'Admin',
			'homepagePresenter' => NULL,
			'signPresenter' => NULL,
			'signOutLink' => NULL,
			'assets' => [
				'productionMode' => FALSE,
				'scripts' => [],
				'stylesheets' => [],
			],
		];


		public function loadConfiguration()
		{
			$this->validateConfig($this->defaults);
			$builder = $this->getContainerBuilder();

			$builder->addDefinition($this->prefix('administration'))
				->setFactory(\UltraScn\Admin\Administration::class, [
					'title' => $this->config['title'],
					'homepagePresenter' => $this->config['homepagePresenter'],
					'signPresenter' => $this->config['signPresenter'],
					'signOutLink' => $this->config['signOutLink'],
				]);

			$assetsManager = $builder->addDefinition($this->prefix('assetsManager'))
				->setFactory(\Inteve\AssetsManager\AssetsManager::class, [
					'productionMode' => $this->config['assets']['productionMode'],
				]);

			foreach ($this->config['assets']['scripts'] as $assetDefinition) {
				if (is_string($assetDefinition)) {
					$assetDefinition = [$assetDefinition];
				}

				$flags = isset($assetDefinition[2]) ? ((array) $assetDefinition[2]) : [];
				unset($assetDefinition[2]);

				$assetsManager->addSetup(in_array(self::ASSETS_FLAG_CRITICAL, $flags, TRUE) ? 'addCriticalScript' : 'addScript', $assetDefinition);
			}

			foreach ($this->config['assets']['stylesheets'] as $assetDefinition) {
				if (is_string($assetDefinition)) {
					$assetDefinition = [$assetDefinition];
				}

				$assetsManager->addSetup('addStylesheet', $assetDefinition);
			}

			$builder->addDefinition($this->prefix('formFactory'))
				->setFactory(\UltraScn\Admin\Forms\FormFactory::class);

			$builder->addDefinition($this->prefix('signFormFactory'))
				->setFactory(\UltraScn\Admin\Forms\SignFormFactory::class);
		}
	}
