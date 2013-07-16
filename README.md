L4-Smarty-Menubuilder
=====================

A Menubuilder for Laravel 4, Twitter Bootstrap, Cartalyst/Sentry 2, and Dark/SmartyView.

Introduction
------------

In developing a number of projects using Laravel4, Sentry2, and Smarty, I needed a way to manage menus in the GUI. In the past, I've built menu tables in the database and menu editors, but in general I don't need that level of flexibility (or overhead).

So this time around, I was looking for a way to define my menus in code and generate menus automatically. What I came up with was this Menubuilder, which I am now rebuilding into a package which I will use in multiple projects.

**Note that this project is tailored _very_ closely to my requirements and may not be suitable for use outside of my environment.**

Pull requests are welcome. Please note that I am using git flow for my workflow. Don't know what that is? Check out [http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/](http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/).

Or have a look at one of these screen casts:

* [How to use a scalable Git branching model called git-flow](http://buildamodule.com/video/change-management-and-version-control-deploying-releases-features-and-fixes-with-git-how-to-use-a-scalable-git-branching-model-called-gitflow) (by Build a Module)
* [A short introduction to git-flow](http://vimeo.com/16018419) (by Mark Derricutt)
* [On the path with git-flow](http://codesherpas.com/screencasts/on_the_path_gitflow.mov) (by Dave Bock)

(The links above were found at [https://github.com/nvie/gitflow](https://github.com/nvie/gitflow)).

Usage
-----

### Installing

Add the appropriate line to your `composer.json` file. This package can be found [on packagist.org](https://packagist.org/packages/monstergfx/menubuilder).

Once you've run `composer.phar update` and downloaded the package, add it to your `app/config/app.php` file in two places:

* in the `providers` array:


    'providers' => array(
        // a whole bunch of other providers...
        'Monstergfx\Menubuilder\MenubuilderServiceProvider',
    ),


* in the `aliases` array:

    'aliases' => array(
        // a whole bunch of other aliases...
        'Menubuilder' => 'Monstergfx\Menubuilder\Facades\Menubuilder',
    ),

### Defining your menus in code

Somewhere in your code (it doesn't matter where), define a `menu` by inserting a comment like this:

    // @menu <menu text>|<menu icon>|>sort order>

or, in a docblock:

    /**
     * @menu <menu text>|<menu icon>|>sort order>
     */

For example,

    // @menu Administration|icon-star|5

The line above defines a menu with the title "Administration", a Twitter Bootstrap start icon, and a sort order of 5 (ie. it comes after 0, 1, 2, 3, and 4).

Menu items are inserted into your code in a similar way:

    // @menuitem <menu>|<item text>|<item icon>|<route>|<permissions>|<sort order>

or

     /**
      * @menuitem <menu>|<item text>|<item icon>|<route>|<permissions>|<sort order>
      */

For example,

    // @menuitem Administration|List Users|icon-user|list-users|user.list|3

This defines a menu item under the "Administration" menu with the text "List Users", a Twitter Bootstrap "user" icon, routes to "list-users", requires the "user.list" permission, and has a sort order of 3.

Taken together, the two examples above give a menu that looks something like this:

    +------------------+
    | * Administration |
    +------------------+
    | x List Users     |
    +------------------+

(where the 'x' is the user icon).

### Generating the menus

The menus are generated via an artisan command:

    php artisan menu:make

This command will scan your source files and extract all the `@menu` and `@menuitem` lines, parse them, and write a configuration file: `app/config/menus.php`.

This configuration will then be used at run time to generate menus.

### Using the menubuilder

The menubuild provides a method to generate the appropriate menus: `Menubuilder::build()`.

I use it in a default view composer to ensure that the menus are built automatically for each page:

    View::composer('*',function($view){
    	// add the menus (if any)
    	$view->with('menus', Menu::build());
    	// add other stuff here...
    });

The `build()` method returns an array that looks something like this:

    Array
    (
        [Administration] => Array
            (
                [icon] => icon-star
                [sort] => 5
                [items] => Array
                    (
                        [0] => Array
                            (
                                [text] => List Users
                                [icon] => icon-user
                                [route] => list-users
                                [permission] => user.list
                                [sort] => 1
                            )
                        [1] => Array
                            (
                                [text] => Add New User
                                [icon] => icon-user
                                [route] => create-user
                                [permission] => user.edit
                                [sort] => 1
                            )
                    )
            )
    )

This menu can be passed to a view to be rendered as you see fit.
