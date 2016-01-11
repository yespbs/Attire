---
layout: page
title: Docs
fulltitle: Documentation
permalink: /docs/
description: ""
menu:
  - setup: 
      Installation       : "#installation"
      Getting Started    : "#getting-started"
      Installing a theme : "#installing-a-theme"
      Adding Views       : "#adding-views"
  - user guide:
      Creating themes  	 : "#creating-themes"
      Twig Environment 	 : "#twig-environment"
      Sprockets Pipeline : "#sprockets-pipeline"
  - get involved:
      Contributions    	 : "#contributions"
---

Installation
------------

**With Composer:**

	composer require dsv/attire 

Now we need to set the environment where all your templates are stored properly.

**Autoloading composer**

Enabling this setting in **application/config/config.php** will tell CodeIgniter to look for a Composer package auto-loader script.

{% highlight PHP startinline %}
$config['composer_autoload'] = TRUE;
{% endhighlight %}

Or if you have your vendor/ folder located somewhere else, you can opt to set a specific path as well:

{% highlight PHP startinline %}
$config['composer_autoload'] = '/path/to/vendor/autoload.php';
{% endhighlight %}

**Config File (optional)**

Attire use one config file to retrieve configuration preferences. Copy the **dist/config/attire.php** file inside your config folder:

	+-APPPATH/
	| | +-config/
	| | | +-attire.php

**folder structure**

Create this folder structure inside your CodeIgniter application:

	+-APPPATH/
	| +-themes/
	+-FCPATH 
	| +-assets/

Where:

* ```APPPATH``` is Codeigniter's principal folder, where all your controllers, models and views are placed.
* ```FCPATH``` is Codeigniter's secured installation folder, where your ```index.php``` file is placed (normally outside the application folder).

Also you can override the default structure, check the [config guide](#Config_guide) for more details.

**Assets permissions**

Set the ```assets``` folder with writable permissions.

	sudo chmod -R 777 assets/

---

Getting started
---------------

Like the [Twig basics](http://twig.sensiolabs.org/doc/api.html), Attire uses a central object called the environment. Instances of this class are used to store the configuration and extensions, and are used to load templates from the file system or other locations.

This is the simplest way to configure Attire to load templates for your application:

{% highlight PHP startinline %}
$this->load->library('attire/attire'); 

$this->attire->set_loader('filesystem');
$this->attire->set_environment(array(
	'cache' => '/path/to/compilation_cache',
));
{% endhighlight %}

This will create a template environment with the default settings and a loader that looks up the templates in the ```/path/to/templates/``` folder. 

##Installing a theme

Attire supports theme instances that are quick start points for you to kick off your next project. So instead of create a central environment, a way more simple to start using Attire is calling a theme instance with a layout:

**Bootstrap + Attire**

Install the Bootstrap example theme with composer:

	composer require dsv/attire-theme-bootstrap

Bootstrap theme includes some example layout structures. 

{% highlight PHP startinline %}
$this->attire->set_theme('bootstrap');
$this->attire->set_layout('jumbotron');
{% endhighlight %}

Chaining method also supported:

{% highlight PHP startinline %}
$this->attire->set_theme('bootstrap')->set_layout('jumbotron');
{% endhighlight %}

Finally display the theme with the render method.

{% highlight PHP startinline %}
$this->attire->render();
{% endhighlight %}

Full example:

{% highlight PHP startinline %}
class Welcome extends CI_Controller 
{
	public function index()
	{	
		$this->load->library('attire/attire');

		$this->attire
			->set_theme('bootstrap')
			->set_layout('jumbotron')
			->render();
	}
}
{% endhighlight %}

Preview:

<div class="row" style="margin-bottom:30px;">
	<div class="col-sm-12">
		<img class="img-responsive img-thumbnail" src="{{ '/assets/img/hello_world.png' | prepend: site.baseurl }}">
	</div>
</div>

Now you can use the [Bootstrap](http://getbootstrap.com/) responsive framework in your application.

---

Adding views
------------

So far we've only displayed the default template and layout. You can add views to this layout using the ```add_view``` method.

{% highlight PHP startinline %}
$this->attire->add_view($view, $params);
{% endhighlight %}

Where: 

* ```$view``` is the view file path relative to ```VIEWPATH``` folder. 
* ```$params``` is an array of variables used inside the view interface. 

It's exactly like the Codeigniter's method: 

{% highlight PHP startinline %}
$this->load->view($view, $params);
{% endhighlight %}

**Example**

Create a view ```foo.php``` inside the ```VIEWPATH``` folder:

{% highlight HTML startinline %}
<!-- application/views/foo.php -->
<h2>Header</h2>
<p>paragraph<p>
{% endhighlight %}

Next add the view without specifing the folder app and the extension:

{% highlight PHP startinline %}
class Welcome extends CI_Controller 
{
	public function index()
	{	
		$this->load->library('attire/attire');
		
		$this->attire
			->set_theme('bootstrap')
			->add_layout('jumbotron')
			->add_view('foo')
			->render();	
	}
}
{% endhighlight %}

Preview:

<div class="row" style="margin-bottom:30px;">
	<div class="col-sm-12">
		<img class="img-responsive img-thumbnail" src="{{ '/assets/img/add_view.png' | prepend: site.baseurl }}">
	</div>
</div>

Also you can add as many views as you want using the same function multiple times before the render method. 

{% highlight PHP startinline %}
class Welcome extends CI_Controller 
{
	public function index()
	{	
		$this->load->library('attire/attire');

		$this->attire
			->set_theme('bootstrap')
			->set_layout('jumbotron')
			->add_view('foo')
			->add_view('fighters')
			->render();	
	}
}
{% endhighlight %}

**Example 2**

Implement the same theme and layout structure for all your controller method's: 

{% highlight PHP startinline %}
class Welcome extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('attire/attire');

		$this->attire->set_theme('bootstrap')->add_layout('jumbotron');
	}
	
	public function index()
	{	
		$this->attire->add_view('foo')->render();	
	}

	public function other()
	{
		$this->attire->add_view('fighters')->render();		
	}
}
{% endhighlight %}

Or you can change your view path:

{% highlight PHP startinline %}
class Welcome extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
	$this->load->library('attire/attire');

	$this->attire->set_theme('bootstrap')->add_layout('jumbotron');
    }

	public function index()
	{
	    $this->attire
	    	->add_path('/outside/viewpath/','some')
	    	->add_view('@some/foo')
	    	->render();
	}
}
{% endhighlight %}
---

Creating themes
---------------

**Folder structure**

Create a new folder structure inside the theme folder:

	+-theme/
	| +-new_theme/
	| | +-assets (all your theme asset files needed)
	| | | +- stylesheets/* 
	| | | +- javascripts/*
	| | | +- fonts/*
	| | | +- images/*
	| | +-layout
	| | | +-<new_layout>.twig
	| | +- master.twig

**The master theme**

This is the default template used in every **Attire** theme instance:

{% highlight HTML %}
{% raw %}
<!DOCTYPE html>
<head lang="en">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	{% block head %}
		<title>{% block title %}{% endblock %}</title>
	{% endblock %}
	<link rel="stylesheet" href="{{ pipeline.css }}">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->	
</head>
<body>
	{% block content %}{% endblock %}
	
	<script src="{{ pipeline.js }}"></script>
</body>
</html>
{% endraw %}
{% endhighlight %}

Copy this structure into your new ```master.twig``` file. Use it as a basic template and create something unique.

**Layout**

Same as the ```master.twig```, copy the ```layouts/new_layout.twig``` default template: 

{% highlight HTML %}
{% raw %}
{% extends "master.twig" %}

{% block content %}
	{% for view,params in views %}
		{% include view with params %}
	{% endfor %}
{% endblock %}
{% endraw %}
{% endhighlight %}

Anything can be a layout, check the [twig extends docs](http://twig.sensiolabs.org/doc/tags/extends.html).

**Rendering the theme**

Set the new theme and structure, add the views and load it before sending the output to the browser.

{% highlight PHP startinline %}
class Welcome extends CI_Controller 
{
	public function index()
	{	
		$this->load->library('attire/attire');
		
		$this->attire
			->set_theme('new_theme')
			->set_layout('new_layout')
			->add_view('welcome_message')
			->render();	
	}
}
{% endhighlight %}

Notice that you only need to specify the name of the template (without the extension `*.twig`).

---

Twig Environment
----------------

Attire is flexible enough for all your needs, even the most complex ones.

**Globals**

A global variable it's available in all the views used in the template:

{% highlight PHP startinline %}
$this->attire->add_global('text', new Text());
{% endhighlight %}

Then you can use it as follows:

{% highlight HTML %}
{{ text.lipsum(40) }}
{% endhighlight %}

**Filters**

Attire implements ```Twig_SimpleFilter``` objects. This is usefull when you're integrating third-party libraries, helpers or libraries that needed inside the views.

{% highlight PHP startinline %}
// Closure function
$this->attire->add_filter('rot13',function ($string) {
    return str_rot13($string);
}));

// Or a simple php function
$this->attire->add_filter('rot13','str_rot13')


// Or a class method
$this->attire->add_filter('rot13',array('SomeClass', 'rot13Filter'));
{% endhighlight %}

The first argument passed is the name of the *filter* and the second it's the *closure function*. Inside the view you can call the filter:

{% highlight HTML %}
{% raw %}
{{ 'Twig'|rot13 }}
{# output Gjvt #}
{% endraw %}
{% endhighlight %}

When called by Twig, the PHP executable on the left side receives the filter (before the vertical bar) as the first argument and the extra arguments passed to the filter (within parentheses) as an extra argument.

**Functions**

Functions are defined in the exact same way as **filters**, except for the options preserves_safety and pre_escapr.

{% highlight PHP startinline %}
$this->attire->add_function('function_name', closure_function);
{% endhighlight %}

References:

* [Closure Functions](http://php.net/manual/en/functions.anonymous.php)

**Example**

Create a new function inside your controller's method:

{% highlight PHP startinline %}
$this->attire->add_function('foo_bar', function(){return "foo";});
{% endhighlight %}

Now you can call the function:

{% highlight HTML %}
{% raw %}
<p>{{foo_bar()}}</p>
{% endraw %}
{% endhighlight %}

---

Sprockets Pipeline
------------------

The Attire Asset Pipeline will read your main file (usually ```application.js``` or ```application.css```), read directives, and apply filters for all the files. 

**Example**

{% highlight HTML %}
/**
 * (see the "directive syntax" section below)
 *= require jquery
 *= require bootstrap.min
 *= require lib/inputs/{text,password}
 *= require_directory lib/loaders
 */
{% endhighlight %}

**Asset Paths**

The asset paths are divided by "modules" to be as flexible as it can:

{% highlight PHP startinline %}
array(
	'template' => array(
		'directories' => array(
			'%theme%/assets/',
			'_shared/assets/'
		),
		'prefixes' => array(
			'js' => 'javascripts',
			'css' => 'stylesheets',
			'img' => 'images',
			'font' => 'fonts'
		)
	),
	'external' => array(
		'directories' => array(
			'bower_components/',
			'vendor/components/'
		)
	)
);
{% endhighlight %}

Each module have 2 keys: 

* Directories: which list directories where the Pipeline must search files.
* Prefixes: which will append the path for the extension to the directory.

For example, if we run the **render** method, the pipeline will try to find the following files:

	path/to/application/themes/%theme%/assets/javascripts/application.js
	path/to/application/themes/_shared/assets/javascripts/application.js
	path/to/bower_components/application.js
	path/to/vendor/components/application.js

Where:

* **%theme%** being replaced by the library.

This asset manager allows to use a Rails-like ```javascripts/``` directory for js file gracefully, also supports //= require jquery/dist/jquery to find ```path/to/bower_components/jquery/dist/jquery.js```

Only the "meaningful" extension matters (using a whitelist).

	/**
	 * for example
	 *= require datatables/js/jquery.dataTables
	 * will find correctly the file named
	 * "path/to/bower_components/datatables/js/jquery.dataTables.js.coffee"
	 */

---

Contributions
-------------

The Attire project welcomes and depends on contributions from all developers in the **Codeigniter community**. 

Contributions can be made in a number of ways, a few examples are:

* Code patches via pull requests
* Documentation improvements
* Bug reports and patch reviews
* Creating a theme

**Reporting an Issue**

Please include as much detail as you can. Let us know your platform and Attire/CodeIgniter version. If the problem is visual (for example a theme or design issue) please add a screenshot and if you get an error please include the the full error and traceback.

**Submitting Pull Requests**

Once you are happy with your changes or you are ready for some feedback, push it to your fork and send a pull request. For a change to be accepted it will most likely need to have tests and documentation if it is a new feature.

**Creating a theme**

Check the [User Guide]({{ '/docs/#creating-themes' | prepend: site.baseurl }}) that supports this subject and once you are ready for some feedback let us know with an issue or email. Please include all your personal information so that we can make the appropriate acknowledgments on this page.

---
