<?php

	namespace UltraScn\Admin\Model;

	use Latte;
	use UltraScn;


	class Filters
	{
		public function __construct()
		{
			throw new UltraScn\Admin\StaticClassException;
		}


		public static function date($date)
		{
			return Latte\Runtime\Filters::date($date, 'j.n.Y');
		}


		public static function datetime($date)
		{
			return Latte\Runtime\Filters::date($date, 'j.n.Y / H:i');
		}
	}
