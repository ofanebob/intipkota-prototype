<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * Proto_HTML_Compression Class
 * Unwrapper HTML output
 * @author Ofan Ebob
 * @since 2014 (v.1)
 * @copyright GNU & GPL license
 */
class Proto_HTML_Compression
{
	// Settings
	protected $compress_css = true;
	protected $compress_js = true;
	protected $info_comment = true;
	protected $remove_comments = true;

	// Variables
	protected $html;

	public function __construct($html)
	{
		if (!empty($html))
		{
			$this->parseHTML($html);
		}
	}

	public function __toString()
	{
		return $this->html;
	}

	protected function bottomComment($raw, $compressed)
	{
		$raw = strlen($raw);
		$compressed = strlen($compressed);
		
		$savings = ($raw-$compressed) / $raw * 100;
		
		$savings = round($savings, 2);
		
		//return '<!-- HTML compressed, size saved '.$savings.'%. From '.$raw.' bytes, now '.$compressed.' bytes | Developed by @OfanEbob -->';
		return '<!-- '.$compressed.' bytes received | Developed by intipkota.com - @OfanEbob -->';
	}

	protected function minifyHTML($html)
	{
		mb_internal_encoding('UTF-8');
		header('Content-type: text/html; charset=UTF-8');
		
		$pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
		preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
		$overriding = false;
		$raw_tag = false;

		// Variable reused for output
		$html = '';

		foreach ($matches as $token)
		{
			$tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;
			
			$content = $token[0];
			
			if (is_null($tag))
			{
				if ( !empty($token['script']) )
				{
					$strip = $this->compress_js;
				}
				else if ( !empty($token['style']) )
				{
					$strip = $this->compress_css;
				}
				else if ($content == '<!--wp-html-compression no compression-->')
				{
					$overriding = !$overriding;
					
					// Don't print the comment
					continue;
				}
				else if ($this->remove_comments)
				{
					if (!$overriding && $raw_tag != 'textarea')
					{
						// Remove any HTML comments, except MSIE conditional comments
						$content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);
					}
				}
			}
			else{
				if ($tag == 'pre' || $tag == 'textarea')
				{
					$raw_tag = $tag;
				}
				else if ($tag == '/pre' || $tag == '/textarea')
				{
					$raw_tag = false;
				}
				else{
					if ($raw_tag || $overriding)
					{
						$strip = false;
					}
					else{
						$strip = true;
						
						// Remove any empty attributes, except:
						// action, alt, content, src
						$content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);
						
						// Remove any space before the end of self-closing XHTML tags
						// JavaScript excluded
						$content = str_replace(' />', '/>', $content);
					}
				}
			}
			
			if ($strip)
			{
				$content = $this->removeWhiteSpace($content);
			}
			
			$html .= $content;
		}
		
		return $html;
	}
		
	public function parseHTML($html)
	{
		$htmlCompressCache = PUBLICPATH.'/cache/html-'.md5($GLOBALS['CURRENTPAGE']);

		if( file_exists($htmlCompressCache) AND ( filemtime($htmlCompressCache) > strtotime('now') ) )
		{
			$this->html = @file_get_contents($htmlCompressCache);
		}
		else
		{
			/*if ($this->info_comment)
			{
				$this->html .= "\n" . $this->bottomComment($html, $this->html);
			}*/

			$this->html = $this->minifyHTML($html);
			
			if($fp = fopen($htmlCompressCache, 'wb'))
			{
				fwrite($fp, $this->html . '<!-- HTML Cache -->');
				fclose($fp);
			
				touch($htmlCompressCache, $GLOBALS['EXPIREPAGECACHE']);
			}
		}
	}
	
	protected function removeWhiteSpace($str)
	{
		$str = str_replace("\t", ' ', $str);
		$str = str_replace("\n",  '', $str);
		$str = str_replace("\r",  '', $str);
		
		while (stristr($str, '  '))
		{
			$str = str_replace('  ', ' ', $str);
		}
		
		return $str;
	}
}

function proto_html_compression_finish($html)
{
	return new Proto_HTML_Compression($html);
}

function proto_html_compression_start()
{
	ob_start('proto_html_compression_finish');
}
?>