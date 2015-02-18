<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * GenTag Class
 * HTML tag generator for html, meta, link stylesheet & script
 * @author Ofan Ebob
 * @since 2014 (v.1)
 */
class GenTag
{
	/** @method Default print function for script/CSS output return */
	private static function _print($s)
	{
		if(!empty($s)) echo $s;
	}

	/** @method generate CSS tags with array definition */
	public static function css($a,$b=false)
	{
		if(is_array($a))
		{
			if($b=='combine')
			{
				if(defined('DEVMODE') AND DEVMODE == false)
				{
					return self::_buildCSSCombine($a);
				}
				else
				{
					return self::cssLoop($a,$b);
				}
			}
			else
			{
				return self::cssLoop($a,$b);
			}
		}
	}

	private static function cssLoop($a,$b)
	{
		if (count($a) == count($a, COUNT_RECURSIVE)) 
		{
			return self::_buildCSS($a);
		}
		else
		{
			foreach($a as $name => $value)
			{
				$css[] = self::_buildCSS($value);
			}
			return join("\n",$css)."\n\r";
		}
	}

	/** @method generate CSS tags with array definition & combining it with cache */
	private static function _buildCSSCombine($a)
	{
		if(method_exists('CombineScript', 'start') == false AND 
			is_callable(array('CombineScript','start')) == false
		)
		{
			require_once(LIBPATH.'/sys/combine.script.php');
		}

		foreach($a as $name => $value)
		{
			$script[] = preg_replace(Regex::get('css_file'), "$1.min.css",$value['href']);
		}
		
		$combine = CombineScript::start(array('type'=>'css','files'=>join(',',$script)));

		if($combine == null)
		{
			return self::css($a);
		}
		else
		{
			return "<link media=\"all\" rel=\"stylesheet\" href=\"$combine\" />\n";
		}
	}

	/** @method Build CSS passing function after access self::css() */
	private static function _buildCSS($a)
	{
		$print = isset($a['print']) ? $a['print'] : false;
		$rel = isset($a['rel']) ? $a['rel'] : 'stylesheet';
		$media = isset($a['media']) ? $a['media'] : 'all';
		$css_dir = '/public/assets/css/';
		$path = isset($a['path']) ? $a['path'] : DOMAINFIX.$css_dir;
		$href = isset($a['href']) ? $a['href'] : null;

		if($href !== null)
		{
			if(file_exists(BASEPATH.$css_dir.$href))
			{
				if(preg_match('/(\-|\.)min\./',$href) == false)
				{
					$href = DEVMODE == true ? $href : preg_replace(Regex::get('css_file'), "$1.min.css", $href);
				}

				$return = '<link media="'.$media.'" rel="'.$rel.'" href="'.$path.$href.'" />';
				return $print == true ? self::_print($return) : $return;
			}
		}
	}

	public static function script($a,$b=false)
	{
		if(is_array($a))
		{
			if($b == 'variable')
			{
				return self::_buildScriptVariable($a);
			}
			elseif($b == 'combine')
			{
				if(defined('DEVMODE') AND DEVMODE == false)
				{
					return self::_buildScriptCombine($a);
				}
				else
				{
					return self::scriptLoop($a,$b);
				}
			}
			else
			{
				return self::scriptLoop($a,$b);
			}
		}
	}

	private static function scriptLoop($a,$b)
	{
		if (count($a) == count($a, COUNT_RECURSIVE)) 
		{
			return self::_buildScript($a);
		}
		else
		{
			foreach($a as $name => $value)
			{
				$script[] = self::_buildScript($value);
			}

			return join("\n",$script)."\r";
		}
	}

	private static function _buildScriptCombine($a)
	{
		if(method_exists('CombineScript', 'start') == false AND 
			is_callable(array('CombineScript','start')) == false
		)
		{
			require_once(LIBPATH.'/sys/combine.script.php');
		}

		foreach($a as $name => $value)
		{
			$script[] = preg_replace(Regex::get('js_file'), "$1.min.js",$value['src']);
		}
		
		$combine = CombineScript::start(array('type'=>'javascript','files'=>join(',',$script)));

		if($combine == null)
		{
			return self::script($a);
		}
		else
		{
			return "<script type=\"text/javascript\" src=\"$combine\"></script>\n";
		}
	}

	private static function _buildScriptVariable($a)
	{
		if(count($a) > 1)
		{
			foreach($a as $name => $value)
			{
				$script[] = "$name = $value";
			}

			$script_print = 'var '.join(",",$script).';';
		}
		else
		{
			foreach($a as $name => $value)
			{
				$script[] = "var $name = $value;";
			}

			$script_print = $script[0];
		}
		
		$return = "<script>\r\n/* <![CDATA[ */\r\n".$script_print."\r\n/* ]]> */\r</script>\n";
		return $return;
	}

	private static function _buildScript($a)
	{
		$print = isset($a['print']) ? $a['print'] : false;
		$type = isset($a['type']) ? $a['type'] : 'text/javascript';
		$js_dir = '/public/assets/js/';
		$path = isset($a['path']) ? $a['path'] : DOMAINFIX.$js_dir;
		$src = isset($a['src']) ? $a['src'] : null;

		if($path == 'dynamic')
		{
			$return = '<script type="'.$type.'" src="'.$src.'"></script>';
			return $print == true ? self::_print($return) : $return;
		}
		else
		{
			if($src !== null)
			{
				if(file_exists(BASEPATH.$js_dir.$src))
				{
					if(preg_match('/(\-|\.)min\./',$src) == false)
					{
						$src = DEVMODE == true ? $src : preg_replace(Regex::get('js_file'), "$1.min.js", $src);
					}

					$return = '<script type="'.$type.'" src="'.$path.$src.'"></script>';
					return $print == true ? self::_print($return) : $return;
				}
			}
		}
	}

	public static function meta($args=false)
	{
		if(is_array($args))
		{
			if (count($args) == count($args, COUNT_RECURSIVE)) 
			{
				return self::_buildMeta($args);
			}
			else
			{
				foreach($args as $name => $a)
				{
					$meta[] = self::_buildMeta($name,$a);
				}
				return join("\n",$meta)."\r";
			}
		}
	}

	private static function _buildMeta($name,$a)
	{
		$print = isset($a['print']) ? $a['print'] : false;
		$content = isset($a['content']) ? $a['content'] : false;

		$return = '<meta content="'.$content.'" name="'.$name.'" />';
		return $print == true ? self::_print($return) : $return;
	}

	public static function html($args=false)
	{
		$args = is_array($args) ? $args : null;

		if($args != null)
		{
			extract($args);
			$title = isset($args['t']) ? $t : '';
			$content = isset($args['c']) ? $c : '';
			$datapage = isset($args['dp']) ? $dp : '';
			$embed = isset($args['e']) ? $e : '';
			$footer = isset($args['f']) ? $f : '';
			$embedfoot = isset($args['ef']) ? $ef : '';
			$meta = isset($args['m']) ? $m : '';
		}

		include(TEMPLATEHTML);
	}

	public static function template($args=false)
	{
		if(is_array($args))
		{
			extract($args);
			
			$meta = isset($args['meta']) ? $meta : '';

			$file_template = TEMPLATEPATH.'/'.$publictheme.'.php';
			
			if(file_exists($file_template))
			{
				include($file_template);

				$embed = isset($args['embed']) ? is_array($embed) ? implode('',$embed) : $embed : '';
				$datapage = isset($args['datapage']) ? $datapage : '';
				$embedfoot = isset($args['embedfoot']) ? is_array($embedfoot) ? implode('',$embedfoot) : $embedfoot : '';

				return self::html(array('c'=>$content,'t'=>$title,'e'=>$embed,'f'=>$footer,'ef'=>$embedfoot,'m'=>$meta,'dp'=>$datapage));
			}
			else
			{
				header("Location:404");
			}
		}
		else
		{
			return '<div class="alert alert-danger">Invalid Template Parameter</div>';
		}
	}

	public static function ErrorPage($args=false)
	{
		if(is_array($args))
		{
			extract($args);

			$webname = isset($args['webname']) ? $webname : '';
			$navbar = isset($args['navbar']) ? $navbar : '';
			$embed = isset($args['embed']) ? $embed : '';
			$param_theme = isset($args['param_theme']) ? $param_theme : '';
			$code = isset($args['code']) ? $args['code'] : '404';

			$param_theme = array('homepage'=>DOMAINFIX,
								 'webname'=>$webname,
								 'navbar'=>$navbar,
								 );

			self::template(
					array('publictheme'=>$code,
						  'embed'=>$embed,
						  'param_theme'=>$param_theme
					)
			);
		}
		else
		{
			return '<div class="alert alert-danger">Invalid Template Parameter</div>';
		}
	}

	public static function alertTag($param=false)
	{
		if(is_array($param))
		{
			$param['message'] = isset($param['message']) ? $param['message'] : 'Error: Terjadi kesalahan';
			$param['type'] = isset($param['type']) ? $param['type'] : 'danger';
			$param['class'] = isset($param['class']) ? $param['class'] : 'marginspace-top';

			$html = '<div class="alert alert-{TYPE} {CLASS}">{MESSAGE}</div>';
			$html = str_replace('{TYPE}', $param['type'], $html);
			$html = str_replace('{MESSAGE}', $param['message'], $html);
			$html = str_replace('{CLASS}', $param['class'], $html);
			$Render = $html;
			return $Render;
		}
	}

	public static function panelTag($param=false)
	{
		if(is_array($param))
		{
			$param['message'] = isset($param['message']) ? $param['message'] : 'Error: Terjadi kesalahan';
			$param['title'] = isset($param['title']) ? $param['title'] : 'Info';
			$param['type'] = isset($param['type']) ? $param['type'] : 'info';
			$param['footer'] = isset($param['footer']) ? $param['footer'] : '';
			$param['class'] = isset($param['class']) ? $param['class'] : 'marginspace-top';

			$html = '<div class="panel panel-{TYPE} {CLASS}">
					<h5 class="nomargin panel-heading">{TITLE}</h5>
					<div class="panel-body">{MESSAGE}</div>
					<div class="panel-footer">{FOOTER}</div>
					</div>';
					
			$html = str_replace('{TYPE}', $param['type'], $html);
			$html = str_replace('{TITLE}', $param['title'], $html);
			$html = str_replace('{MESSAGE}', $param['message'], $html);
			$html = str_replace('{CLASS}', $param['class'], $html);
			$html = str_replace('{FOOTER}', $param['footer'], $html);
			$Render = $html;
			return $Render;
		}
	}
}
?>