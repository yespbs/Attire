---
layout: page
title: Docs
fulltitle: Documentation
permalink: /docs/
description: ""
menu:
  - setup: 
      Installation     : "#installation"
      Environment      : "#environment"
      Getting Started  : "#getting-started"
      Adding Views     : "#adding-views"
  - user guide:
      Creating themes  : "#creating-themes"
      Twig Environment : "#twig-environment"
  - get involved:
      Contributions    : "#contributions"
---

Installation
------------

**With Composer:**

	composer require dsv/attire 

Environment
-----------

Now we need to set the environment where all your templates are stored properly.

**Autoloading composer**

Enabling this setting in **application/config/config.php** will tell CodeIgniter to look for a Composer package auto-loader script.

{% highlight PHP startinline %}
$config['composer_autoload'] = 'vendor/autoload.php';
{% endhighlight %}

**Config File (optional)**

Attire use one config file to retrieve configuration preferences. Copy the **dist/config/attire.php** file inside your config directory:

	+-APPPATH/
	| | +-config/
	| | | +-attire.php

**Directory structure**

Create this directory structure inside your CodeIgniter application:

	+-APPPATH/
	| +-themes/
	+-FCPATH 
	| +-assets/
	| | +-css/
	| | +-js/

Notes:

* ```APPPATH``` is Codeigniter's principal directory, where all your controllers, models and views are placed.
* ```FCPATH``` is Codeigniter's secured installation directory, where your ```index.php``` file is placed (normally outside the application directory).

Also you can override the default structure, check the [config guide](#Config_guide) for more details.

**Assets permissions**

Set the ```assets``` directory with writable permissions.

**Theme example structure**

Install the Bootstrap example theme with composer:

	composer require dsv/attire-theme-bootstrap

**Before we continue**

Let's take a moment to review the initial project that we created and also the library that is already included.

<div class="row" style="margin-bottom:30px;">
	<div class="col-sm-12">
		<img class="img-responsive img-thumbnail" src="{{ '/assets/img/take_a_look.png' | prepend: site.baseurl }}">
	</div>
</div>

---

Getting started
---------------

Load the library in your controller:

{% highlight PHP startinline %}
$this->load->library('attire/attire'); 
{% endhighlight %}

Next set the theme and layout:

{% highlight PHP startinline %}
$this->attire->set_theme('bootstrap');
$this->attire->set_layout('jumbotron');
{% endhighlight %}

Bootstrap theme includes some example layout structures. 

Chaining method also supported:

{% highlight PHP startinline %}
$this->attire->set_theme('bootstrap')->set_layout('jumbotron');
{% endhighlight %}

Finally display the theme with the render method.

{% highlight PHP startinline %}
$this->attire->render();
{% endhighlight %}

**Example**

{% highlight PHP startinline %}
class Welcome extends CI_Controller 
{
	public function index()
	{	
		$this->load->library('attire/attire');
		$this->attire->set_theme('bootstrap')->set_layout('jumbotron');
		$this->attire->render();
	}
}
{% endhighlight %}

Preview:

<div class="row" style="margin-bottom:30px;">
	<div class="col-sm-12">
		<img class="img-responsive img-thumbnail" src="{{ '/assets/img/hello_world.png' | prepend: site.baseurl }}">
	</div>
</div>

This is the current output of the render method. Now you can use the [Bootstrap](http://getbootstrap.com/)  responsive framework in your application.

---

Adding views
------------

So far we've only displayed the default template and layout. You can add views to this layout using the ```add_view``` method.

{% highlight PHP startinline %}
$this->attire->add_view($view, $params);
{% endhighlight %}

Where: 

* ```$view``` is the view file path. 
* ```$params``` is an array of variables used inside the view interface. 

It's exactly like the Codeigniter's method: 

{% highlight PHP startinline %}
$this->load->view($view, $params);
{% endhighlight %}

**Example**

Create a view ```foo.php``` inside the ```VIEWPATH``` directory:

{% highlight HTML startinline %}
<!-- application/views/foo.php -->
<h2>Header</h2>
<p>paragraph<p>
{% endhighlight %}

Next add the view without specifing the directory app and the extension:

{% highlight PHP startinline %}
class Welcome extends CI_Controller 
{
	public function index()
	{	
		$this->load->library('attire/attire');
		$this->attire->set_theme('bootstrap')->add_layout('jumbotron');
		$this->attire->add_view('foo');
		$this->attire->render();	
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

		$this->attire->set_theme('bootstrap')
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
	    $this->attire->add_path('/outside/viewpath/','some');
	    $this->attire->add_view('@some/foo')->render();
	}
}
{% endhighlight %}
---

Creating themes
---------------

**Directory structure**

Create a new directory structure inside the theme directory:

	+-theme/
	| +-new_theme/
	| | +-assets (all your theme asset files needed)
	| | | +- css/* 
	| | | +- js/*
	| | +-layout
	| | | +-new_layout.twig
	| | +- theme.twig

**The master theme**

This is the default template used in every **Attire** theme instance:

{% highlight HTML %}
{% raw %}
<!DOCTYPE html>
<html>
	<head>
		{% block head %}
			<title>{% block title %}{% endblock %} - {{app_fullname|title}}</title>
		{% endblock %}
		{% block stylesheets %}
			{% stylesheets 'css/*' filter='cssrewrite' %}
			<link href="{{ base_url('assets/' ~ asset_url) }}" type="text/css" rel="stylesheet" />
			{% endstylesheets %}		
		{% endblock %}
	</head>
	<body>
		{% block content %}{% endblock %}
		<div id="footer">
			{% block footer %}{% endblock %}
		</div>
		{% block javascripts %}
			{% javascripts 'js/*' %}
			<script src="{{ base_url('assets/' ~ asset_url) }}"></script>
			{% endjavascripts %}
		{% endblock %}
	</body>
</html>
{% endraw %}
{% endhighlight %}

Copy this structure into your new **theme.twig** file. Use it as a basic template and create something unique.

**Layout**

Same as ```theme.twig```, copy the ```layouts/new_layout.twig``` default template: 

{% highlight HTML %}
{% raw %}
{% extends "theme.twig" %}
{% block title %}{{'new_layout'|capitalize}}{% endblock %}

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
		$this->attire->set_theme('new_theme')->set_layout('new_layout');
		$this->attire->add_view('welcome_message')->render();	
	}
}
{% endhighlight %}

Notice that you only need to specify the name of the template (without the extension `*.twig`).

---

Twig Environment
----------------

Twig is flexible enough for all your needs, even the most complex ones. Attire implements Twig tags, filters and functions with ease thanks to for his open architecture.

**Globals**

A global variable it's available in all the views used in the template:

{% highlight PHP startinline %}
$this->attire->add_global('text', new Text());
{% endhighlight %}

Then you can use it as follows:

{% highlight PHP startinline %}
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
