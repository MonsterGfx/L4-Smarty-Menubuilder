<?php namespace Monstergfx\Menubuilder;

class Menubuilder {

	/**
	 * A (private) instance of the app container, initialized via injection
	 */
	private $app = null;

	/**
	 * The class constructor
	 *
	 * @param Illuminate\Foundation\Application $application
	 * The application container
	 */
	public function __construct(\Illuminate\Foundation\Application $application)
	{
		// save the application container instance
		$this->app = $application;
	}

	/**
	 * Build the menu array structure based on the menus configuration file
	 * and the currently logged in user.
	 *
	 * @return array
	 */
	public function build()
	{

		// references to objects that will be needed
		$sentry = $this->app['sentry'];
		$config = $this->app['config'];

		// and one more that's useful for development
		$kint = $this->app['kint'];

		// get the current user
		$user = $sentry->getUser();
		// if nobody's logged in, return an empty array
		if(!$user)
			return array();

		// get the menu structure from the config
		$menus = $config->get('menus.menus');

		// step through the menus & items
		foreach(array_keys($menus) as $m)
		{
			$items = $menus[$m]['items'];
			$permitted_items = array();
			foreach($items as $i)
			{
				if($user->hasAccess('admin') || $user->hasAccess($i['permission']))
					$permitted_items[] = $i;
			}
			$menus[$m]['items'] = $permitted_items;
		}

		// step through the top level menus
		foreach(array_keys($menus) as $m)
		{
			// if the current menu is empty, remove it
			if(count($menus[$m]['items'])==0)
				unset($menus[$m]);
		}

$kint->dump($menus); die;
		// return the result
		return $menus;
	}

}
