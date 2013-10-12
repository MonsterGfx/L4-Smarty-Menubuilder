<?php namespace Monstergfx\Menubuilder\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MenubuilderCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'menubuilder:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Make the menu structure from the tags in the application code.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 *
	 * @todo make this code more resistant to errors in menu syntax
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->info("Generating menus.");

		// instantiate an array to hold the results
		$menus = array();

		// perform an 'exec' to get the menu specs
		$raw_lines = array();

		$command = 'find ./app ./workbench \( -name storage -prune \) -o -name "*.php" -exec grep -PHn "^\s*(\*|//)\s+@menu\s+" {} 2>/dev/null \;';
		exec($command, $raw_lines);

		// lines will be of the form
		// 		./app/libraries/Menu.php:45: * @menu Processing|icon-star|1

		// parse them!
		foreach($raw_lines as $line)
		{
			// extract the file name & line number
			$n = strpos($line,':');
			if($n===false)
			{
				$this->error("Cannot parse line: '{$line}'");
				continue;
			}
			$file = substr($line,0, $n);
			$line = substr($line, $n+1);

			$n = strpos($line,':');
			if($n===false)
			{
				$this->error("Cannot parse line: '{$line}'");
				continue;
			}
			$line_number = substr($line,0, $n);
			$line = trim(substr($line, $n+1));

			// now parse the menu specification
			$menu = array();
			$x = preg_match('/^((\*|\/\/)\s+@menu\s+)([a-z ]+)\|([a-z-]+)\|([0-9]+)$/i', $line, $menu);
			if(!$x)
			{
				$this->error("Invalid menu specification. File: $file, Line: $line_number");
				continue;
			}

			// if we get here, then parsing was successful
			$menus[$menu[3]] = array(
				'icon' => $menu[4],
				'sort' => $menu[5],
				'items' => array(),
			);

		}

		// sort the menus
		uksort($menus,function($a, $b){ return $a['sort']<$b['sort']; });


		// perform an 'exec' to get the menuitem specifications
		$raw_lines = array();
		$command = 'find ./app ./workbench \( -name storage -prune \) -o -name "*.php" -exec grep -PHn "^\s*(\*|//)\s+@menuitem " {} 2>/dev/null \;';
		exec($command, $raw_lines);


		// lines will be of the form
		// 		./app/controllers/AccountController.php:370:	 * @menuitem Administration|List Users|icon-user|list-users|user.list|1

		// parse them!
		foreach($raw_lines as $line)
		{
			// extract the file name & line number
			$n = strpos($line,':');
			if($n===false)
			{
				$this->error("Cannot parse line: '{$line}'");
				continue;
			}
			$file = substr($line,0, $n);
			$line = substr($line, $n+1);
			$n = strpos($line,':');
			if($n===false)
			{
				$this->error("Cannot parse line: '{$line}'");
				continue;
			}
			$line_number = substr($line,0, $n);
			$line = trim(substr($line, $n+1));

			// now parse the menu specification
			$item = array();
			$x = preg_match('/^((\*|\/\/)\s+@menuitem\s+)([a-z0-9 ]+)\|([a-z0-9 ]+)\|([a-z-.]+)\|([a-z-.]+)\|([a-z-.]+)\|([0-9]+)$/i', $line, $item);
			if(!$x)
			{
				$this->error("Invalid menu specification. File: $file, Line: $line_number");
				continue;
			}
			// if we get here, then parsing was successful
			$menus[$item[3]]['items'][] = array(
				'text' => $item[4],
				'icon' => $item[5],
				'route' => $item[6],
				'permission' => $item[7],
				'sort' => $item[8],
			);

		}

		// sort the list of menu items
		foreach(array_keys($menus)as $k)
			usort($menus[$k]['items'], function($a, $b){ return $a['sort']>$b['sort']; });

		// write the menu configuration file
		$code = var_export($menus, true);

		$written = file_put_contents(app_path().'/config/menus.php',
			<<<PHPCODE
<?php

/**
 * The menu structure for the application
 *
 * Note that the code below is automatically generated from the source by
 * running
 *
 * 		php artisan menu:make
 *
 * ANY CHANGES WILL BE OVERWRITTEN THE NEXT TIME THAT COMMAND IS RUN.
 *
 * Menus are defined by adding comment tags to the code. Here is the syntax:
 *
 * For top-level menus, the syntax is:
 *
 * 		(at)menu <menu text>|<menu icon>|>sort order>
 *
 * For example,
 *
 * 		(at)menu Administration|icon-star|5
 *
 * will generate an "Administration" menu with the icon-star icon to the left,
 * appearing to the right of menus with sort orders 0 through 4.
 *
 * For menu items, the syntax is:
 *
 *		(at)menuitem <menu>|<item text>|<item icon>|<route>|<permissions>|<sort order>
 *
 * For example,
 *
 *		(at)menuitem Administration|List Users|icon-user|list-users|user.list|3
 *
 * will generate a menu item under the "Administration" menu with the text "List
 * Users". It will have the icon-user icon to the left of the text and it will
 * appear below items with sort orders 0 through 3.
 *
 */

return array(

	'menus' =>

{$code},

);

PHPCODE
		);

		$this->info("Menu generation complete.");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
