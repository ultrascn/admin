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


		/**
		 * @param  string|int|\DateTimeInterface|\DateInterval $date
		 * @return string
		 */
		public static function date($date)
		{
			return Latte\Runtime\Filters::date($date, 'j.n.Y');
		}


		/**
		 * @param  string|int|\DateTimeInterface|\DateInterval $date
		 * @return string
		 */
		public static function datetime($date)
		{
			return Latte\Runtime\Filters::date($date, 'j.n.Y / H:i');
		}
	}
