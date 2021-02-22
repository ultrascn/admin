<?php

	namespace UltraScn\Admin\Components;

	use Inteve\Navigation;
	use UltraScn\Admin\Administration;
	use UltraScn\Admin\Model\NavigationHelper;


	class MenuControl extends \Nette\Application\UI\Control
	{
		const TYPE_MAIN_MENU = 0;
		const TYPE_SUB_MENU = 1;

		/** @var Administration */
		private $administration;

		/** @var Navigation\Navigation */
		private $navigation;

		/** @var int */
		private $type;


		/**
		 * @param int $type
		 */
		public function __construct(
			Administration $administration,
			Navigation\Navigation $navigation,
			$type
		)
		{
			$this->administration = $administration;
			$this->navigation = $navigation;
			$this->type = $type;

			if ($type !== self::TYPE_MAIN_MENU && $type !== self::TYPE_SUB_MENU) {
				throw new \UltraScn\Admin\InvalidArgumentException("Invalid type '$type'.");
			}
		}


		/**
		 * @return void
		 */
		public function render()
		{
			$items = [];
			$subTreeLevel = $this->type === self::TYPE_MAIN_MENU ? 0 : 1;
			$navigation = $this->navigation;
			$usages = [];
			// TODO: pro submenu musime podle aktualniho presenteru vybrat odpovidajici subtree

			foreach ($navigation->getPages() as $pageId => $page) {
				if (!$page->hasLink()) {
					continue;
				}

				$pageLevel = substr_count($pageId, '/');

				if ($subTreeLevel === $pageLevel) {
					$items[] = [
						'page' => $page,
						'active' => $page->isHomepage()
							? $this->navigation->isPageCurrent($pageId)
							: $navigation->isPageActive($pageId),
					];
				}
			}

			$presenter = $this->getPresenter();
			assert($presenter !== NULL);

			$template = $this->createTemplate();
			$template->items = $items;
			$template->showSignOutLink = $this->type === self::TYPE_MAIN_MENU;
			$template->administration = $this->administration;
			$template->linkGenerator = new Navigation\DefaultLinkGenerator($presenter, $template->basePath);
			assert($template instanceof \Nette\Bridges\ApplicationLatte\Template);
			$template->render(__DIR__ . '/navigation.latte');
		}
	}
