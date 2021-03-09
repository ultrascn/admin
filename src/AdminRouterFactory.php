<?php

	namespace UltraScn\Admin;

	use Nette;
	use Nette\Application\Helpers;
	use Nette\Application\Routers\Route;
	use Nette\Application\Routers\RouteList;
	use Nette\Utils\Strings;


	class AdminRouterFactory
	{
		/** @var string */
		private $adminPrefix;

		/** @var array<string, string> */
		private $packages;

		/** @var string|NULL */
		private $defaultPackage;

		/** @var string|NULL */
		private $appPresenter;


		/**
		 * @param  string $adminPrefix
		 * @param  array<string, string> $packages
		 * @param  string|NULL $defaultPackage
		 * @param  string|NULL $appPresenter
		 */
		public function __construct(
			$adminPrefix,
			array $packages,
			$defaultPackage = NULL,
			$appPresenter = NULL
		)
		{
			$this->adminPrefix = rtrim($adminPrefix, '/') . '/';
			$this->defaultPackage = $defaultPackage;
			$this->appPresenter = $appPresenter;

			foreach ($packages as $packageName => $packagePresenter) {
				list($modulePresenter, $action) = Helpers::splitName($packagePresenter);
				$action = $action !== '' ? $action : 'default';
				$this->packages[$packageName] = $modulePresenter . ':' . $action;
			}
		}


		/**
		 * @param  string $name
		 * @param  string $presenter
		 * @return void
		 */
		public function addPackage($name, $presenter)
		{
			if (isset($this->packages[$name])) {
				throw new InvalidArgumentException("Package '$name' already exists.");
			}

			$this->packages[$name] = $presenter;
		}


		/**
		 * @return Nette\Application\IRouter
		 */
		public function createRouter()
		{
			$router = new RouteList;

			if (!empty($this->packages)) {
				$router[] = new Route($this->adminPrefix . '[<package>/[<presenter>/[<action>/[<id>]]]]', [
					NULL => [
						Route::FILTER_IN => function (array $params) {
							$params['package'] = $params['package'] !== '' ? $params['package'] : NULL;
							$params['presenter'] = $params['presenter'] !== '' ? $params['presenter'] : NULL;
							$params['action'] = $params['action'] !== '' ? $params['action'] : NULL;
							$params['id'] = $params['id'] !== '' ? $params['id'] : NULL;

							if ($params['package'] === NULL) {
								if ($this->defaultPackage === NULL || !isset($this->packages[$this->defaultPackage])) {
									return NULL;
								}

								$params['package'] = $this->defaultPackage;
							}

							$packagePresenter = NULL;

							if (isset($this->packages[$params['package']])) {
								$packagePresenter = $this->packages[$params['package']];
							}

							if ($packagePresenter === NULL) {
								return NULL;
							}

							list($presenter, $action) = Helpers::splitName($packagePresenter);

							if (isset($params['presenter'])) {
								list($module, $presenter, $separator) = Helpers::splitName($presenter);
								$params['presenter'] = $module . $separator . $params['presenter'];

							} else {
								$params['presenter'] = $presenter;
							}

							if (!isset($params['action'])) {
								$params['action'] = $action;
							}


							unset($params['package']);
							return $params;
						},
						Route::FILTER_OUT => function (array $params) {
							if (isset($params['package'])) { // cizi routa
								return NULL;
							}

							if (!isset($params['presenter'])) {
								return NULL;
							}

							// try find exact match
							foreach ($this->packages as $packageName => $presenter) {
								list($presenter, $action) = Helpers::splitName($presenter);

								if ($presenter === $params['presenter']) { // exact match
									list($module, $presenter, $separator) = Helpers::splitName($presenter);
									$params['package'] = $packageName;
									$params['presenter'] = $presenter;

									if (isset($params['action']) && $params['action'] === $action) {
										unset($params['presenter']);
										unset($params['action']);
									}

									if ($params['package'] === $this->defaultPackage) {
										unset($params['package']);
									}

									return $params;
								}
							}

							// try find module match
							foreach ($this->packages as $packageName => $presenter) {
								list($presenter, $action) = Helpers::splitName($presenter);
								list($module, $presenter, $separator) = Helpers::splitName($presenter);

								if (Strings::startsWith($params['presenter'], $module . $separator)) {
									$params['package'] = $packageName;
									$params['presenter'] = Strings::substring($params['presenter'], Strings::length($module . $separator));

									if (isset($params['action']) && $params['action'] === $action) {
										unset($params['action']);
									}

									return $params;
								}
							}

							return NULL;
						},
					],
				]);
			}

			if ($this->appPresenter !== NULL) {
				list($presenter, $action) = Helpers::splitName($this->appPresenter);
				list($module, $presenter, $separator) = Helpers::splitName($presenter);
				$router[] = new Route($this->adminPrefix . '[<presenter>/[<action>/[<id>]]]', [
					'module' => $module,
					'presenter' => $presenter,
					'action' => $action !== '' ? $action : 'default',
				]);
			}

			return $router;
		}
	}
