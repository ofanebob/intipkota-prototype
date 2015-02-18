<?php if ( !defined('BASEPATH')) header('Location:/404');
class NewsControl extends GenTag
{
	public static function build($var=false)
	{
		if(is_array($var))
		{
			extract($var);

			$regex = Regex::get('news_image');

			$r = '<div class="taglist-flat">';
			foreach ($rss->item as $item):
				
				$guid = preg_replace('/(.*)((cluster)?\=(f|ht)t(p|ps)\:)(.*)/', "$6", (htmlSpecialChars($item->guid)));
				$fixTitle = htmlSpecialChars($item->title);

				$date = @$item->timestamp ? (date("j F, Y", (int) $item->timestamp)) : '';

				$r .= '<div class="media noborder-last-bottom border-smoke border-bottom border-solid border-1px">';

					$r .= '
					<div class="pull-left">
					<a href="'.$guid.'" target="_blank" class="quick-news-preview d-inline-block w-100cent">
					';
					
					$img = DOMAINFIX.'/public/images/no-image-80x80.jpg';

					if(preg_match($regex, $item->description))
					{
						$img = preg_replace($regex, "$3", $item->description);
					}
					
					$r .= '<img src="'.$img.'" class="thumbnail waitForImages" data-holder-rendered="true" style="width: 80px; height: 80px;" alt="80x80" />';

					$r .= '
					</a>
					</div><div class="media-body">
					<h5 class="media-heading">
						<a href="'.$guid.'" target="_blank" class="quick-news-preview d-inline-block w-100cent">
						'.$fixTitle.'
						</a>
					</h5>
					<div class="marginspace-bottom text-gray important">
						<small>'.$date.'</small>
					</div>
					</div>
					</div>';

			endforeach;
			$r .= '</div>';

			return $r;
		}
	}
}
?>