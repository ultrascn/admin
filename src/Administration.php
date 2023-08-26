<?php

	namespace UltraScn\Admin;

	use CzProject\Assert\Assert;
	use Inteve\AssetsManager\AssetsManager;


	class Administration
	{
		/** @var string */
		private $title;

		/** @var string */
		private $homepagePresenter;

		/** @var string */
		private $signPresenter;

		/** @var string|NULL */
		private $signOutLink;

		/** @var AssetsManager */
		private $assetsManager;

		/** @var INavigationFactory */
		private $navigationFactory;

		/** @var string|NULL */
		private $favicon = 'favicon.ico';

		/** @var string|NULL */
		private $layoutFooterTemplate;


		/**
		 * @param  string $title
		 * @param  string $homepagePresenter
		 * @param  string $signPresenter
		 * @param  string|NULL $signOutLink
		 * @param  string|NULL $layoutFooterTemplate
		 */
		public function __construct(
			$title,
			$homepagePresenter,
			$signPresenter,
			$signOutLink,
			AssetsManager $assetsManager,
			INavigationFactory $navigationFactory,
			$layoutFooterTemplate = NULL
		)
		{
			Assert::string($title);
			Assert::string($homepagePresenter);
			Assert::string($signPresenter);
			Assert::stringOrNull($signOutLink);

			$this->title = $title;
			$this->homepagePresenter = ':' . ltrim($homepagePresenter, ':');
			$this->signPresenter = ':' . ltrim($signPresenter, ':');
			$this->signOutLink = $signOutLink !== NULL ? ':' . ltrim($signOutLink, ':') : NULL;
			$this->assetsManager = $assetsManager;
			$this->navigationFactory = $navigationFactory;
			$this->layoutFooterTemplate = $layoutFooterTemplate;
		}


		/**
		 * @return string
		 */
		public function getTitle()
		{
			return $this->title;
		}


		/**
		 * @return string
		 */
		public function getHomepagePresenter()
		{
			return $this->homepagePresenter;
		}


		/**
		 * @return string
		 */
		public function getSignPresenter()
		{
			return $this->signPresenter;
		}


		/**
		 * @return string|NULL
		 */
		public function getSignOutLink()
		{
			return $this->signOutLink;
		}


		/**
		 * @return bool
		 */
		public function hasSignOutLink()
		{
			return $this->signOutLink !== NULL;
		}


		/**
		 * @return AssetsManager
		 */
		public function getAssetsManager()
		{
			return $this->assetsManager;
		}


		/**
		 * @param  int|string|NULL $userId
		 * @return \Inteve\Navigation\Navigation
		 */
		public function createNavigation($userId)
		{
			return $this->navigationFactory->create($userId);
		}


		/**
		 * @param string|NULL $favicon
		 * @return void
		 */
		public function setFavicon($favicon)
		{
			$this->favicon = $favicon;
		}


		/**
		 * @return bool
		 */
		public function hasFavicon()
		{
			return $this->favicon !== NULL;
		}


		/**
		 * @return string|NULL
		 */
		public function getFavicon()
		{
			return $this->favicon;
		}


		/**
		 * @return bool
		 */
		public function hasLayoutFooterTemplate()
		{
			return $this->layoutFooterTemplate !== NULL;
		}


		/**
		 * @return string|NULL
		 */
		public function getLayoutFooterTemplate()
		{
			return $this->layoutFooterTemplate;
		}
	}
