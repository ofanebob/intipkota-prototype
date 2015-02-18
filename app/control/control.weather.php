<?php if ( !defined('BASEPATH')) header('Location:/404');
class WeatherControl extends GenTag
{

	public static function build($data=false)
	{
		if(is_array($data))
		{
			extract($data);

			$weather_icon = $data->current_observation->icon;
			$weather_name = $data->current_observation->weather;
			$weather_temp = intval($data->current_observation->temp_c);
			$weather_city = preg_replace('/((K|k)ota|(C|c)ity| )/','',$data->current_observation->display_location->city);

			$weather_suffix = is_night_day_bool() == 0 ? 'nt_' : '';

			$r = '
			<div class="icon-weather">

				<div class="heading-weather">
					<span class="forecast-city '.(strlen($weather_city) >= 15 ? 'small':'big').'">'.$weather_city.'</span>
					<span class="forecast-name">'.$weather_name.'</span>
				</div>

				<div class="body-weather" style="color:#'.convert_color_temp($weather_temp).'">
					<span class="forecast-icon wi wi-'.$weather_suffix . $weather_icon.'"></span>
					<span class="forecast-temp">'.$weather_temp.'<i class="wi wi-celsius"></i></span>
				</div>

			</div>
			';

			$forecastday = @$data->forecast->txt_forecast->forecastday;

			if( count($forecastday) > 0 ):
				$r .= '<div class="forecast-list clearfix no-overflow">';
				foreach($forecastday as $forecast):
					if($forecast->period % 2 === 0 AND $forecast->period != 0):
					$r .= '
							<div class="media noborder-last-bottom border-bottom border-solid border-1px border-smoke">
								<div class="pull-left">
									<font size="5" class="forecast-icon wi wi-'.$forecast->icon.'"></font> 
								</div>
								<div class="media-body">
									<h5 class="nomargin media-heading">'.$forecast->title.'</h5>
									<small class="break-word-all d-inline-block">'.$forecast->fcttext_metric.'</small>
								</div><br />
							</div>
					';
					endif;
				endforeach;
				$r .= '</div>';
			endif;

			return $r;
		}
	}
}
?>