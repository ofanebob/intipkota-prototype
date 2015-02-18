<?php if ( !defined('BASEPATH')) header('Location:/404');
class WikiModule extends Wikipedia
{
	public static function get($param=null)
	{
		try
		{
			$data = array(	'state'=>'id',
							'parameter'=>$param,
							'rvprop'=>'content',
							'expire_cache'=>strtotime('+1 Years'),
							'type_save'=>'file'
							);
			
			$wiki = Wikipedia::retrive_wiki($data);

			return $wiki;
		}
		catch(WikipediaException $e)
		{
			//return $e->getMessage();
			return null;
		}
	}

	public static function html($param=null)
	{
		$wiki = self::get($param);

		if(is_object($wiki))
		{
			self::_Global($wiki);
			
			return self::build(array('data'=>$wiki));
		}
		else
		{
			//return '<div class="inner-side"><div class="alert alert-danger">Wiki '.$wiki.'</div></div>';
			return $wiki;
		}
	}

	private static function build($data=false)
	{
		if(is_array($data))
		{
			extract($data);

			$asterik = "*";
			$indexpageids = $data->query->pageids[0];
			$wikitext = $data->query->pages->$indexpageids->revisions[0]->$asterik;
			$wikititle = $data->query->pages->$indexpageids->title;

			$r = '
				<h2 class="inner-separator border-bottom border-solid border-primary border-3px">
					<i class="v-align-top text-primary glyphicon glyphicon-briefcase"></i> Tentang '.$wikititle.'
				</h2>

				<div id="wiki-page" class="overflow-y page-scroll nomargin text-justify border-right border-solid border-smoke border-1px">
					<div class="inner-side">'.$wikitext.'</div>
				</div>
			';
			return $r;
		}
	}

	public static function _Global($wiki)
	{
		$GLOBALS['CURRENTWIKI'] = htmlentities("");
	}
}