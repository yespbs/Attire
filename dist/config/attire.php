<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Theme Path
|--------------------------------------------------------------------------
|
| Path to your attire themes folder. Typically, it will be within your 
| application path.
|
*/
$config['theme_path'] = APPPATH.'themes/';

/*
|--------------------------------------------------------------------------
| Assets Path (required write permissions)
|--------------------------------------------------------------------------
|
| Path to your assets/cache folder. 
|
| Typical scenarios: 
|	- outside your application directory 
\	- inside your public directory.
|
*/
$config['assets_path'] = FCPATH.'assets/';

/*
|--------------------------------------------------------------------------
| Twig-Codeigniter Functions
|--------------------------------------------------------------------------
|
| Allows to add Codeigniter functionality in Twig Environment that come 
| from other libraries or helpers. 
| 
| Example:
|
| 	$config['functions'] = array(
|		'base_url' => function($path = ""){ 
|			return base_url($path); 
|		},
| 	);
| 
| Call the functions in Twig environment:
|		
|	{{base_url('foo_fighters')}}
| 
| Remember to load the library or helper bafore the render method
| in your controller.
|
*/
$config['functions'] = array();

/*
|--------------------------------------------------------------------------
| Twig Global Vars 
|--------------------------------------------------------------------------
|
| Global variables can be registered in the Twig environment. Same as 
| declare a function:
|
| $config['global_vars'] = array(
| 	'some' => 'hello world',
| );
|
| Call the functions in Twig environment:
|		
|	{{ some }}
|
*/
$config['global_vars'] = array();

/*
|--------------------------------------------------------------------------
| Sprockets-PHP - Assets Paths
|--------------------------------------------------------------------------
|
| The asset paths are divided by "modules" to be as flexible as it can.
|
| You have 2 keys in each modules: 
|	- Directories: which list directories where the Pipeline must search 
|		files.
|	- Prefixes: which will append the path for the extension to the 
|		directory (ie a js file will get javascripts/ appended to its paths).
|	
| Set the external directories where the pipeline can find the bower 
| components, composer packages, etc.
|
*/

$config['pipeline_paths'] = array(
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
			'bower_components/'
		)
	)
);