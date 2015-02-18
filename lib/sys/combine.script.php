<?php if ( ! defined('BASEPATH')) header('Location:/404');
/**
 * CombineScript Class
 * Menangani proses penggabungan script CSS/JS kedalam satu file dalam bentuk cache
 * @author Ofan Ebob
 * @since 2015 (v.1)
 * @copyright GNU & GPL license
 */
class CombineScript
{
	public static function start($args=false)
	{
		if(is_array($args))
		{
			$cache 	  = isset($args['cache']) ? $args['cache'] : true;
			$cachedir = BASEPATH . '/public/assets/cache';
			$cssdir   = BASEPATH . '/public/assets/css';
			$jsdir    = BASEPATH . '/public/assets/js';

			/* @switch mengambil base direktori sesuai type script */
			switch ($args['type'])
			{
				case 'css':
					$base = $cssdir;
					break;
				case 'javascript':
					$base = $jsdir;
					break;
				default:
					return null;
			}

			$type = $args['type'];
			$elements = explode(',', $args['files']);

			while(list(,$element) = each($elements))
			{
				$fileGets = $base.'/'.$element;

				if(file_exists($fileGets))
				{
					$script[] = file_get_contents($fileGets);
				}
			}

			$scripts = join("\n",$script);

			/* @var Script diberinama md5 untuk memudahkan penamaan saat penyimpanan */
			$fileMD5 = md5($args['files']);

			/* @var Menentukan extensi file sesuai jenis script */
			$fileNames = $fileMD5.'.'.basename($base);

			/* @var Mendefinisikan alamat/direktori & nama file untuk penyimpanan cache */
			$fileReady = $cachedir.'/'.$fileNames;

			if($cache == true)
			{
				/* @condition Jika file tersedia maka langsung dipanggil */
				if(file_exists($fileReady))
				{
					/**
					 * Catatan:
					 * Pembuatan nama file menggunakan md5 adalah
					 * berisi deretan nama file yg dikompres & dibuat cache
					 * Contoh sebelum dibuat md5 adalah:
					 * jquery.js, prortype.js, plugin.js, script.js (dan seterusnya)
					 */
					return DOMAINFIX.'/public/assets/cache/'.$fileNames;
				}
				else
				{
					/**
					 * Catatan:
					 * Jika file tidak terdapat di direktori
					 * Maka akan di return NULL dan system akan tetap menjalankan script berurutan
					 * Misal array('script.js', 'script2.js')
					 * Di browser: <script src="script.js" /> <script src="script2.js" /> (dan seterusnya)
					 *
					 * Informasi:
					 * Dalam prototype ini pemanggilan script di trigger dari file library html.generator.php
					 * dengan nama method GenTag::script() atau GenTag::css()
					 */

					//file_put_contents($fileReady, $scripts);

					if($fp = fopen($fileReady, 'wb'))
					{
						fwrite($fp, $scripts);
						fclose($fp);
					}

					return null;
				}
			}
		}
	}
}
?>