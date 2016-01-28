<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * @package   CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license   http://opensource.org/licenses/MIT	MIT License
 * @link      http://codeigniter.com
 * @since     Version 1.0.0
 */

/**
 * CodeIgniter Attire
 *
 * Templating with this class is done by layering the standard CI view system and extending 
 * it with Sprockets-PHP (pipeline asset management). The basic idea is that for every single 
 * CI view there are individual CSS, Javascript and View files that correlate to it and 
 * this structure is conected with the Twig Engine.
 *
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Libraries
 * @author     David Sosa Valdes
 * @link       https://github.com/davidsosavaldes/Attire
 */

class Attire
{
	/**
	 * Theme directory path
	 * 
	 * @var string
	 */
	protected $theme_path = NULL;

	/**
	 * Assets directory path
	 * 
	 * @var string
	 */
	protected $assets_path = NULL;

	/**
	 * Sprockets-PHP pipeline paths
	 * 
	 * @var array
	 */
	protected $pipeline_paths = array();

	/**
	 * Twig valid extensions
	 * 
	 * @var array
	 */
	protected $extensions = array();

	/**
	 * Twig files extension
	 * 
	 * @var string
	 */
	protected $file_extension = '.twig';	

	/**
	 * Twig Environment options
	 * 
	 * @var array
	 */
	protected $environment_options = array();

	/**
	 * Twig external functions 
	 * 
	 * @var array
	 */
	protected $functions = array();

	/**
	 * Twig global variables
	 * 
	 * @var array
	 */
	protected $global_vars = array();

	/**
	 * Enable the Twig built-in autoloader
	 * 
	 * @var boolean
	 */
	protected $auto_register = FALSE;

	/**
	 * Default theme 
	 * 
	 * @var string
	 */
	protected $theme = NULL;

	/**
	 * Default master template (without extension)
	 * 
	 * @var string
	 */
	protected $template = 'master';

	/**
	 * Twig Loader
	 * 
	 * @var object
	 */
	protected $_loader = NULL;

	/**
	 * Twig Environment
	 * 
	 * @var object
	 */
	protected $_environment = NULL;	

	/**
	 * Default layout (without extension)
	 * 
	 * @var string
	 */
	protected $_layout = NULL;

	/**
	 * Stored views with their params
	 * 
	 * @var array
	 */
	protected $_views = array();

	/**
	 * Sprockets-PHP cache directory base path
	 * 
	 * @var string
	 */
	protected $_cache_base = NULL;

	/**
	 * Sprockets-PHP Pipeline manifest
	 * @var string
	 */
	protected $_manifest = 'theme';

	/**
	 * CI Instance
	 * 
	 * @var object
	 */
	protected $_CI;

	/**
	 * Class constructor.
	 * 
	 * @param array $config | library params
	 */
	public function __construct(array $config = array())
	{
		// Load CI Instance
        $this->_CI =& get_instance();
        // Set the default Twig environment options
        $this->environment_options = array(
			'charset'             => 'utf-8',
			'base_template_class' => 'Twig_Template',
			'cache'               => FALSE,
			'auto_reload'         => FALSE,
			'strict_variables'    => FALSE,
			'autoescape'          => TRUE	
		);
		// Set the default Twig extensions
		$this->extensions = array(
			'core'      => 'Twig_Extension_Core',
			'escaper'   => 'Twig_Extension_Escaper',
			'sandbox'   => 'Twig_Extension_Sandbox',
			'profiler'  => 'Twig_Extension_Profiler',
			'optimizer' => 'Twig_Extension_Optimizer'
		);
		// Set the default pipeline assets paths
		$this->pipeline_paths = array(
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
					'vendor/bower/',
					'vendor/components/'
				)
			)
		);
        // Set the user params
        $this->_set($config);
        // If not set the library paths, set it
        empty($this->theme_path)  && $this->theme_path = APPPATH.'themes/';
        empty($this->assets_path) && $this->assets_path = FCPATH.'cache/';
        // Add trailing slash if not set
        $this->theme_path  = rtrim($this->theme_path, '/').'/';
        $this->assets_path = rtrim($this->assets_path, '/').'/';
        // Set an absolute path to all pipeline assets:
        array_walk($this->pipeline_paths['template']['directories'], function(&$path){ 
        	$path = $this->theme_path.rtrim($path,'/').'/';
        });
        // Also append the default library assets path
        array_push($this->pipeline_paths['template']['directories'], 
        	APPPATH.'libraries/attire/dist/template/assets'
        );
        // Set pipeline cache path (required)
        $this->pipeline_paths['CACHE_DIRECTORY'] = $this->assets_path;
        $this->_cache_base = basename($this->assets_path).'/';
        // Set if we need the Twig built-in autoloader
        if ($this->auto_register !== FALSE) 
        {
        	Twig_Autoloader::register();
        }
	}

	/**
	 * Set all posible attrs in constructor.
	 *
	 * @param array $config | class config options
	 */
	private function _set(array $config)
	{
		foreach ($config as $key => $val)
		{
			switch (strtolower($key)) 
			{
				case 'functions':
				case 'environment_options':
				case 'extensions':
				case 'pipeline_paths':
					$this->{$key} = array_merge($this->{$key}, (array) $val);
					break;

				default:
					$this->{$key} = $val;
					break;
			}
		}
	}

	/**
	 * Add Twig Functions
	 *
	 * The functions can be called to generate content. The functions are called by his name 
	 * and can have arguments.
	 * 
	 * @param mixed $name  	  | Name of the function
	 * @param mixed $function | Variable function
	 */
	public function add_function($name, $function = NULL)
	{
		try
		{
			if (! $this->_environment instanceof Twig_Environment) 
			{
				throw new Exception("Twig_Environment isn't set correctly.");
			}
			//Let's try to make a function of first arg.
			(! is_callable($function)) && $function = $name;
			//Finally let's try to add the function
			$this->_environment->addFunction(new Twig_SimpleFunction($name, $function));
		}
		catch (Exception $e) 
		{
			show_error($e->getMessage(),404, 'Attire::add_function()');
		}
		return $this;
	}

	/**
	 * Add multiple Twig Functions
	 *
	 * The functions can be called to generate content. The functions are called by his name 
	 * and can have arguments
	 * 
	 * @param array $functions | Set of functions
	 */
	public function add_functions(array $functions)
	{
		try
		{
			if (! $this->_environment instanceof Twig_Environment) 
			{
				throw new Exception("Twig_Environment isn't set correctly.");
			}
			foreach ($functions as $name => $function) 
			{
				//Let's try to make a function of first arg.
				(! is_callable($function)) && $function = $name;
				//Finally let's try to add the function
				$this->_environment->addFunction(new Twig_SimpleFunction($name, $function));			
			}
		}
		catch (Exception $e) 
		{
			show_error($e->getMessage(),404, 'Attire::add_function()');
		}
		return $this;
	}	

	/**
	 * Add an available Twig Extension to the environment.
	 * 
	 * @param string $name   | Shortname available extension
	 * @param mix    $params | Extension params
	 */
	public function add_extension($shortname, $params = NULL)
	{
		try 
		{
			if (! $this->_environment instanceof Twig_Environment) 
			{
				throw new Exception("Twig_Environment isn't set correctly.");
			}
			elseif (! in_array($shortname, array_keys($this->extensions))) 
			{
				throw new Exception(
					"<p>Error Processing Extension Request: '{$shortname}' as shortname.</p>"
					."<p>Available are: </p>"
					."<table style='padding-left:20px;'>"
					."<thead><th>Shortname</th><th>Class</th></thead>"
					.implode('<tr>', array_map(
						function ($v, $k) { 
							return sprintf("<td>%s</td> <td>%s</td></tr>", $k, $v); 
						},
						$this->extensions, 
						array_keys($this->extensions)
					))
					."</table>");
			}
			$extension = $this->extensions[$shortname];
			$this->_environment->addExtension(new $extension($params));
		}
		catch (Exception $e) 
		{
			show_error($e->getMessage(),404, 'Attire::add_extension()');
		}
		return $this;		
	}

	/**
	 * Set a new valid file extension.
	 * 
	 * @param string $file_extension | New file posible extension
	 */
	public function set_file_extension($file_extension)
	{
		try 
		{
			if (! preg_match('/^.*\.(twig|php|php.twig|html|html.twig)$/i', $file_extension)) 
			{
				throw new Exception("Extension '{$file_extension}' is not valid.");
			}
		} 
		catch (Exception $e)
		{
			show_error($e->getMessage(), 404, 'Attire:set_file_extension()');		
		}
		$this->file_extension = $file_extension;
		return $this;
	}	

	/**
	 * Set the Twig Loader.
	 * 
	 * The loaders are responsible for loading templates from a resource.
	 * 
	 * @param mixed  $value | Twig Loader first param 
	 * @param string $type  | The current type of Twig Loader
	 */
	public function set_loader($type, $value = NULL)
	{
		/**
		 * @todo Twig_Loader Chain
		 */	
		try {
			//Check the loader case
			switch (strtolower($type)) 
			{
				case 'filesystem':
					$template_dirs = array(APPPATH.'libraries/attire/dist/template');
					if (isset($this->theme)) 
					{
						$template_dirs[] = $this->theme_path.$this->theme;
					}
					foreach ((array) $value as $theme_name) 
					{
						$template_dirs[] = $this->theme_path.$theme_name;
					}
					$this->_loader = new Twig_Loader_Filesystem($template_dirs);
					break;	

				case 'array':
					if (! is_array($value)) 
					{
						throw new Exception("Set an array structure as second param.");
					}
					else
					{
						$this->_loader = new Twig_Loader_Array($value);
					}
					break;	

				default:
					$this->_loader = new Twig_Loader_String();
					break;
			}			
		} 
		catch (Exception $e) 
		{
			show_error($e->getMessage(),404, 'Attire::set_loader()');
		}
		return $this;
	}

	/**
	 * Set Twig environment
	 * 
	 * @param array $params | Environment options
	 */
	public function set_environment(array $options)
	{
		try 
		{
			if (! $this->_loader instanceof Twig_LoaderInterface) 
			{
				throw new Exception("Twig_LoaderInterface isn't set correctly.");
			}
			$this->_environment = new Twig_Environment(
				$this->_loader, 
				array_merge($this->environment_options, $options)
			);
		} 
		catch (Exception $e) 
		{
			show_error($e->getMessage(), 404, 'Attire::set_environment()');
		}
		return $this;
	}

	/**
	 * Set the theme instance 
	 * 
	 * @param string $name   | Theme name
	 * @param mix    $params | Environment options
	 */
	public function set_theme($name, array $options = array())
	{
		//If not set the theme, set it
		$this->theme !== $name && $this->theme = $name;
		//Replace the string format with the theme name
		array_walk_recursive($this->pipeline_paths, function(&$path){
			$path = str_replace('%theme%', $this->theme, $path);
		});
		return $this->set_loader('filesystem')
			 		->set_environment($options);
	}

	/**
	 * Add a layout in Twig
	 *
	 * Layout views are rendered in the output in the order they added.
	 * 
	 * @param string $type   | filename
	 * @param array  $params | child view params
	 */
	public function set_layout($name, array $params = array())
	{
		$this->_layout = array('layouts/'.$name, $params);
		return $this;
	}

	/**
	 * Set Twig Lexer
	 *
	 * Change the default Twig Lexer syntax, depends of available lexer declared
	 * in the class.
	 * 
	 * @param array $lexer | Twig Lexer
	 */
	public function set_lexer(array $new_lexer = array())
	{
		try 
		{
			if (! $this->_environment instanceof Twig_Environment) 
			{
				throw new Exception("Twig_Environment isn't set correctly.");
			}
			$lexer_instance = new Twig_Lexer($this->_environment, $new_lexer);
			$this->_environment->setLexer($lexer_instance);
		} 
		catch (Exception $e) 
		{
			show_error($e->getMessage(), 404, 'Attire::set_lexer()');
		}
		return $this;	
	}


	/**
	 * Add CI View file
	 *
	 * Every view file added (in the order they added) is rendered at last.
	 * 
	 * @param string $view   | Filename
	 * @param array  $params | View params
	 */
	public function add_view($view, array $params = array())
	{
		$file = $view.$this->file_extension;
		$key = (! strpos($view, '@'))? "@VIEWPATH/".$file : $file;
		$this->_views[$key] = $params; 
		return $this;
	}

	/**
	 * Prepend or append Twig Loader global path
	 * 
	 * @param string  $path      | Relative path
	 * @param string  $namespace | Space name without the '@' symbol
	 * @param boolean $prepend   | Enable prepend mode
	 */
	public function add_path($path, $namespace = '__main__', $prepend = FALSE)
	{
		try 
		{
			if (! $this->_loader instanceof Twig_Loader_Filesystem) 
			{
				throw new Exception("Set the Twig_Loader as Filesystem.");
			}
			($prepend !== FALSE)
				? $this->_loader->prependPath($path, $namespace)
				: $this->_loader->addPath($path, $namespace);
		} 
		catch (Exception $e) 
		{
			show_error($e->getMessage(), 404, 'Attire::add_path()');	
		}
		return $this;
	}

	/**
	 * Prepend Twig Loader global path
	 * 
	 * @param string  $path      | Relative path
	 * @param string  $namespace | Space name without the '@' symbol
	 */
	public function prepend_path($path, $namespace = '__main__')
	{
		return $this->add_path($path, $namespace, TRUE);
	}

	/**
	 * Add global params in Twig
	 * 
	 * @param string $name  | Name
	 * @param mixed  $value | Stored value
	 */
	public function add_global($name, $value = NULL)
	{
		try 
		{
			if (! is_string($name)) 
			{
				throw new Exception("Set global first param as string.");
			}
			if (! $this->_environment instanceof Twig_Environment) 
			{
				throw new Exception("Twig_Environment isn't set correctly.");
			}
			$this->_environment->addGlobal($name, $value);
		} 
		catch (Exception $e) 
		{
			show_error($e->getMessage(), 404, 'Attire::add_global()');
		}
		return $this;
	}	

	/**
	 * Add multiple global params in Twig
	 * 
	 * @param array $globals | Set of global vars
	 */
	public function add_globals(array $globals)
	{
		try 
		{
			if (! $this->_environment instanceof Twig_Environment) 
			{
				throw new Exception("Twig_Environment isn't set correctly.");
			}
			foreach ($globals as $name => $value) 
			{
				$this->_environment->addGlobal($name, $value);
			}
		} 
		catch (Exception $e) 
		{
			show_error($e->getMessage(), 404, 'Attire::add_global()');
		}
		return $this;
	}

	/**
	 * Set Sprockets-PHP Pipeline Manifest 
	 * 
	 * @param string $file_path | Path without extension
	 */
	public function set_manifest($file_path = '')
	{
		$this->_manifest = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_path);
		return $this;
	}	

	/**
	 * Render method
	 * 
	 * @param array $params | Params passed to the template as Twig variables.
	 */
	public function render(array $params = array())
	{
		try 
		{
			$this->_CI->benchmark->mark('Attire Render Time_start');
			if (! is_writable($this->pipeline_paths['CACHE_DIRECTORY'])) 
			{
				throw new Exception("Error Processing Request");
			}
			// Create Sprockets pipeline instance
			$pipeline = new Sprockets\Pipeline($this->pipeline_paths);
			/**
			 * Set the pipeline cache params
			 * @todo Set pipeline cache dynamic options
			 */
			$vars    = array();
			$options = array(
				'manifest' => $this->_manifest
			);
			// Set the pipeline cache instances
			$css_cache = new Sprockets\Cache($pipeline, 'css', $vars, $options);
			$js_cache  = new Sprockets\Cache($pipeline, 'js', $vars, $options);
			// Store the cache paths
			$this->global_vars['pipeline']['css'] = $css_cache;
			$this->global_vars['pipeline']['js'] = $js_cache;
			array_walk_recursive($this->global_vars['pipeline'], function(&$cache){
				$cache = '/'.$this->_cache_base.basename((string) $cache);
			});
			//Set additional stored config functions and global vars
			$this->add_functions($this->functions);
			$this->add_globals($this->global_vars);

			if ($this->_loader instanceof Twig_Loader_Filesystem) 
			{	
				$this->_loader->prependPath(VIEWPATH, 'VIEWPATH');

				if (empty($this->_layout)) 
				{
					$master = $this->template;
				}
				else
				{
					list($master, $params) = $this->_layout;
				}
				$master.= $this->file_extension;
				$params['views'] = $this->_views;				
				
				$template = $this->_environment->loadTemplate($master);
				echo $template->render($params); 	
			}
			else
			{
				/**
				 * @todo set another render method
				 */
				throw new Exception("Another render method currently not supported.");
			}
			$this->_CI->benchmark->mark('Attire Render Time_end');
		} 
		catch (Exception $e) 
		{
			show_error($e->getMessage(), 404, 'Attire::render()');			
		}
	}
}

/* End of file Attire.php */
/* Location: ./application/libraries/attire/Attire.php */
