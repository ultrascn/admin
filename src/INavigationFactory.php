<?php

	namespace UltraScn\Admin;


	interface INavigationFactory
	{
		/**
		 * @param  int|string|NULL $userId
		 * @return \Inteve\Navigation\Navigation
		 */
		function create($userId);
	}
