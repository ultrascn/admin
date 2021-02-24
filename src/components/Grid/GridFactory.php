<?php

	namespace UltraScn\Admin\Components;

	use Inteve;
	use Inteve\DataGrid;


	class GridFactory
	{
		use \Nette\SmartObject;


		/**
		 * @return DataGrid\DataGrid
		 */
		public function create(DataGrid\IDataSource $dataSource)
		{
			$grid = new Inteve\DataGrid\DataGrid($dataSource);
			$grid->setItemsOnPage(20, TRUE);
			return $grid;
		}
	}
