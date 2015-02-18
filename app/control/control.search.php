<?php if ( !defined('BASEPATH')) header('Location:/404');
class SearchControl extends GenTag
{
	public static function html($param)
	{
		extract($param);

		$class = isset($param['class']) ? $param['class'] : 'col-xs-12 col-lg-6';
		
		$placeholder = empty($addholder) ? 'Mulai Cari Kota Disini (ex: Kuningan, Jawa Barat)' : 'Cari Kota | Pencarian saat ini: '.urldecode($addholder);
		$searchinput = empty($addholder) ? '' : $addholder;

		$r = '
		<div class="'.$class.' nopadding">
			<form action="'.$domain.'" class="navbar-form nopadding position-relative" role="search" data-turboform="">
				<div class="input-group">
					<input id="topsearchbox" value="'.urldecode($addholder).'" class="form-control" placeholder="'.$placeholder.'" name="cari" type="text" autocomplete="off">
					<div class="input-group-btn row-fluid">
						<div class="input-group-btn">
							<button class="btn btn-default" type="submit">
								<i class="glyphicon glyphicon-search"></i>
							</button>
						</div>
					</div> 
				</div>
			</form>
		</div>
		';

		return $r;
	}

	public static function auto_suggest($city=false, $limit=null)
	{
		if(is_string($city))
		{
			$limit = is_null($limit) ? 5 : $limit;

			$arr_db = array('tbl'=>'city_cache','row'=>'*','prm'=>"WHERE cache_data LIKE '%$city%' ORDER by cache_data LIMIT $limit");

			$sql = AccessCRUD($arr_db,'read');

			if($sql->num_rows > 0)
			{
				$Results = '<ul class="taglist-flat">';

				while($suggest = $sql->fetch_assoc())
				{
					$Results .= '<li id="'.preg_replace('/city-/','',$suggest['cache_name']).'" class="suggest_search bg-primary-hover noborder-last-bottom border-smoke border-solid border-1px border-bottom">';
					$Results .= '<span class="selectCity cursor-pointer inner-side d-inline-block">';
					$Results .= $suggest['cache_data'];
					$Results .= '</span></li>';
					//$Results[] = array('id'=>preg_replace('/city-/','',$suggest['cache_name']),'city'=>$suggest['cache_data']);
				}

				$Results .= '</ul>';
			}
			else
			{
				$Results = '0';
			}

			return $Results;
		}
	}
}
?>