<?php

	namespace UltraScn\Admin\DI;

	use UltraScn\Admin\AdminRouterFactory;
	use Nette\DI\ContainerBuilder;


	class AdminRouterDecorator
	{
		public function __construct()
		{
			throw new \UltraScn\Admin\StaticClassException('This is static class.');
		}


		/**
		 * @param  array<string, string> $packages
		 * @return void
		 */
		public static function registerPackages(ContainerBuilder $builder, array $packages)
		{
			$routerFactoryName = $builder->getByType(AdminRouterFactory::class);

			if ($routerFactoryName !== NULL) {
				$definition = $builder->getDefinition($routerFactoryName);

				foreach ($packages as $packageName => $packagePresenter) {
					$definition->addSetup('addPackage', [$packageName, $packagePresenter]);
				}
			}
		}
	}
