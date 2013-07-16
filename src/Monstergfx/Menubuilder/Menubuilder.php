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

echo "howdy!"; die;
	}









}
