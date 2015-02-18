<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * CacheHandler Class
 * Menangani proses penyimpanan cache berupa file atau database
 * Metode caching dibagi menjadi dua jenis:
 * 1. Type save cache File (berupa json/xml/text)
 * 2. Type save cache Database (MySQL)
 *
 * Berisi metode dari implementasi CRUD, yaitu:
 * Create (save cache), Read (check cache), Update (merge dengan save cache), Delete (drop cache)
 *
 * @author Ofan Ebob
 * @since 2014 (v.1)
 * @copyright GNU & GPL license
 */
class CacheHandler extends Method
{
	protected static $_args;
	protected static $_cache_expire;
	protected static $_cache_prefix;
	protected static $_cache_name;
	protected static $_cache_id;

	const CACHE_DIR = 'public/cache';

	function __construct()
	{
		/* @objectVariable Beberapa nilai untuk kontruksi */
		$this->_cache_expire = strtotime('+1 Day');
		$this->_cache_prefix = 'prototype';
		$this->_cache_id = md5(date('Y-m-d h:i:s'));
	}


	/**
	 * check()
	 * Fungsi akses publik untuk memeriksa ketersediaan cache
 	 * @return booelan
	 */
	public static function check($param=false)
	{
		if(is_array($param))
		{
			$param['prefix'] = isset($param['prefix']) ? $param['prefix'] : 'data';
			$param['query'] = isset($param['query']) ? $param['query'] : null;
			$param['suffix'] = isset($param['suffix']) ? $param['suffix'] : 'json';
			$param['table'] = isset($param['table']) ? $param['table'] : 'app_cache';
			$param['type_save'] = isset($param['type_save']) ? $param['type_save'] : 'file';

			$idFile = $param['prefix'].'-'.md5($param['query']);
			$fileReady = defined('APPDATAPATH') ? APPDATAPATH.'/cache/'.$idFile :
						$param['absolute_path'].'/'.$idFile;

			$fileReady = $fileReady.'.'.$param['suffix'];

			switch($param['type_save'])
			{
				case 'file':
				$data = file_exists($fileReady);
				break;
				case 'database':
				$prm_read = "WHERE cache_name='{$idFile}'";
				$data_read = array('tbl'=>$param['table'],'row'=>'*','prm'=>$prm_read);
				$sql_read = AccessCRUD($data_read,'read');
				$data = $sql_read == false ? false : $sql_read->num_rows > 0 ? true : false;
				break;
				default:
				$data = false;
			}

			if($data)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}


	/**
	 * save()
	 * Fungsi akses publik setelah semua proses permintaan data
 	 * @throws CacheHandlerException
 	 * @return $save
	 */
	public static function save($args=false)
	{
		/* @var Memberikan nilai default $_args pada konstruksi */
		self::$_args = is_array($args) ? $args : null;

		/* @var Mengembalikan nilai default $_args dari konstruksi
		   ke variable $argum difungsi save */
		$argum = self::$_args;

		if($argum == null)
		{
			/* @throw Jika nilai parameter $argum NULL atau tidak didefinisikan */
			throw new CacheHandlerException('Unknown Parameter Cache.');
		}
		else
		{
			/* @var Menentukan nilai cache_prefix default jika index tidak ada di $argum */
			$cache_prefix = isset($argum['cache_prefix']) ? $argum['cache_prefix'] : self::$_cache_prefix;

			/* @var Menentukan nilai cache_id default jika index tidak ada di $argum */
			$cache_id = isset($argum['cache_id']) ? $argum['cache_id'] : self::$_cache_id;
			//$cache_id = preg_replace('/ /','',$cache_id);

			/* @var Menentukan nilai type_save default jika index tidak ada di $argum */
			$type_save = isset($argum['type_save']) ? $argum['type_save'] : 'file';

			/* @var Menentukan nilai cache_expire untuk konstruktor */
			self::$_cache_expire = isset($argum['cache_expire']) ? $argum['cache_expire'] : self::$_cache_expire;

			/* @var Menentukan nilai cache_name untuk konstruktor */
			self::$_cache_name = $cache_prefix.'-'.md5(strtolower($cache_id));

			/* @switch Membuat batasan jenis penyimpanan cache dalam bentuk file atau database */
			switch($type_save)
			{
				case 'file':
				$save = self::_CacheToFile();
				break;
				case 'database':
				$save = self::_CacheToDatabase();
				break;
				default:
				$save = null;
			}

			if($save == null)
			{
				/* @throw Pesan error jika penyimpanan cache gagal */
				throw new CacheHandlerException('Error Save Cache.');
			}
			else
			{
				return $save;
			}
		}
	}


	/**
	 * _CacheToFile()
	 * Fungsi menangani penyimpanan cache berupa file
 	 * @return $cache OR NULL
	 */
	protected static function _CacheToFile()
	{
		/* @var Menentukan nilai default $argum dari nilai $_args di konstruksi */
		$argum = self::$_args;

		/* @cond Jika nilai $argum NULL maka dikembalikan ke nilai $argum */
		if($argum == null)
		{
			return $argum;
		}
		else
		{
			/* @var Menentukan nilai cache_name dari nilai $_cache_name di konstruksi */
			$cache_name = self::$_cache_name;

			/* @var Menentukan nilai cache_expire dari nilai $_cache_expire di konstruksi */
			$cache_expire = self::$_cache_expire;

			/* @var Menentukan nilai basepath jika BASEPATH tidak di define di konfigurasi */
			$basepath = defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../';
			$cache_dir = isset($argum['cache_dir']) ? $argum['cache_dir'] : $basepath.'/'.self::CACHE_DIR;
			$formatted = isset($argum['format']) ? $argum['format'] : 'json';

			$stored = $cache_dir.'/'.$cache_name.'.'.$formatted;

			/* @cond Buat file cache baru jika file tidak ditemukan di direktori */
			if( file_exists($stored) AND ( filemtime($stored) > strtotime('now') ) )
			{
				/* @return Nilai data dari lokal file cache */
				return @file_get_contents($stored);
			}
			else
			{
				/* @var Menentukan nilai default method jika tidak ada di index $argum */
				$method = isset($argum['method']) ? $argum['method'] : null;

				/* @var Menentukan nilai cache_data method jika tidak ada di index $argum */
				$cache_data = isset($argum['data']) ? $argum['data'] : null;

				/* @cond Beberapa pemeriksaan untuk memastikan parameter yg valid
				 *
				 * Diantaranya:
				 * $method Tidak boleh false
				 * $cache_data Tidak boleh false
				 * $method harus ada di library yg di muat di konfigurasi
				 * $method harus bisa dipanggil dimana saja (bukan private/protected)
				 * $method harus berupa array
				 * $method harus berisi fungsi
				 */
				if(is_null($cache_data))
				{
					/* @return Jika nilai kondisi benar maka output bernilai NULL */
					return null;
				}
				else
				{
					if(is_null($method))
					{
						$cache = null;
					}
					elseif($method == false)
					{
						$cache = $cache_data;
					}
					else
					{
						/* @Jika lolos pemeriksaan maka $method akan dipanggil menggunakan
						   call_user_func_array() termasuk pendefinisian parameternya */
						$cache = parent::call($method, $cache_data);
					}

					/* @cond Jika hasil data API false maka di return null */
					if($cache == null)
					{
						/* @cond Jika proses cURL gagal maka sistem kembali mengambil data lokal */
						if( file_exists($stored) )
						{
							/* @return Nilai data dari lokal file cache */
							return @file_get_contents($stored);
						}
						else
						{
							/* @return Jika file cache tidak ditemukan maka nilai NULL */
							return null;
						}
					}
					else
					{
						/* @compile Buat file cache */

						//@file_put_contents($stored, $cache);

						if($fopen = fopen($stored, 'wb'))
						{
							fwrite($fopen, $cache);
							fclose($fopen);
						}

						/* @PHP rubah meta time nya */
						touch($stored, $cache_expire);

						/* @return Definisikan nilai $cache */
						return $cache;
					}
				}
			}
		}
	}


	/**
	 * _CacheToDatabase()
	 * Fungsi menangani penyimpanan cache berupa MySQL atau Database
 	 * @return $cache OR NULL
	 */
	protected static function _CacheToDatabase()
	{
		$argum = self::$_args;

		if($argum == null)
		{
			return $argum;
		}
		else
		{
			$retrive_cache = isset($argum['retrive']) ? $argum['retrive'] : true;

			$base64 = isset($argum['base64']) ? $argum['base64'] : true;

			$serialize = isset($argum['serialize']) ? $argum['serialize'] : true;

			$method = isset($argum['method']) ? $argum['method'] : null;

			$method_data = isset($argum['data']) ? $argum['data'] : null;

			$table_cache = isset($argum['table_cache']) ? $argum['table_cache'] : 'app_cache';

			$cache_name = self::$_cache_name;

			$cache_expire = self::$_cache_expire;

			$prm_read = "WHERE cache_name='{$cache_name}'";
			$data_read = array('tbl'=>$table_cache,'row'=>'*','prm'=>$prm_read);
			$sql_read = AccessCRUD($data_read,'read');

			// Cek ketersediaan cache di database
			if( $sql_read!=false )
			{
				if( $sql_read->num_rows > 0 )
				{
					$sql_array = $sql_read->fetch_array();
					
					if( $sql_array['cache_expire'] < strtotime('now') )
					{
						if(is_null($method_data))
						{
							return null;
							//die('Error Data Method Cache');
						}
						else
						{
							if($method == false)
							{
								$cache_update = $method_data;
							}
							else
							{
								$cache_update = parent::call($method, $method_data);
							}

							if($cache_update == null)
							{
								return null;
								//die('Error Call User Func');
							}
							else
							{
								if(is_array($cache_update))
								{
									$cache_result = $cache_update;
								}
								elseif(is_object($cache_update))
								{
									$cache_result = ObjectToArray($cache_update);
								}
								else
								{
									$cache_result = is_null(json_decode($cache_update)) ? 
													$cache_update : json_decode($cache_update);
								}

								$cache_serialize = $serialize == true ? serialize($cache_result) : $cache_result;
								$cache_data = $base64 == true ? base64_encode($cache_serialize) : $cache_serialize;

								$prm_update = array('cache_data'=>$cache_data,'cache_expire'=>$cache_expire);

								$data_update = array('tbl'=>$table_cache,'prm'=>$prm_update,'con'=>$prm_read);
								
								$update = AccessCRUD($data_update,'update');

								if($retrive_cache == true)
								{
									if($update > 0)
									{
										return $cache_update;
									}
									else
									{
										if( $sql_read->num_rows > 0 )
										{
											$cacheDB = $base64 == true ? base64_decode($sql_array['cache_data']) : $sql_array['cache_data'];
											return $serialize == true ? unserialize($cacheDB) : $cacheDB;												
										}
										else
										{
											return null;
											//die('Error Update Cache');
										}
									}
								}
								else
								{
									return true;
								}
							}
						}
					}
					else
					{
						if($retrive_cache == true)
						{
							$cacheDB = $base64 == true ? base64_decode($sql_array['cache_data']) : $sql_array['cache_data'];
							return $serialize == true ? unserialize($cacheDB) : $cacheDB;
						}
						else
						{
							return true;
						}
					}
				}
				else
				{
					if(is_null($method_data))
					{
						return null;
						//die('Error Method Data');
					}
					else
					{
						if($method == false)
						{
							$cache_create = $method_data;
						}
						else
						{
							$cache_create = parent::call($method, $method_data);
						}

						// Jika hasil data API false maka di return null
						if($cache_create == null)
						{
							return null;
							//die('Error Call User Func');
						}
						else
						{
							if(is_array($cache_create))
							{
								$cache_result = $cache_create;
							}
							elseif(is_object($cache_create))
							{
								$cache_result = ObjectToArray($cache_create);
							}
							else
							{
									$cache_result = is_null(json_decode($cache_create)) ? 
													$cache_create : json_decode($cache_create);
							}

							$cache_serialize = $serialize == true ? serialize($cache_result) : $cache_result;
							$cache_data = $base64 == true ? base64_encode($cache_serialize) : $cache_serialize;

							$prm_save = array('cache_name'=>$cache_name,'cache_data'=>$cache_data,'cache_expire'=>$cache_expire);
							
							$data_save = array('tbl'=>$table_cache,'prm'=>$prm_save);
							
							$create = AccessCRUD($data_save,'create');

							if($create > 0)
							{
								return $cache_create;
							}
							else
							{
								return null;
								//die('Error Save Cache');
							}
						}
					}
				}
			}
		}
	}


	/**
	 * drop()
	 * Fungsi akses publik untuk menghapus data cache
 	 * @throws CacheHandlerException
 	 * @return $cache
	 */
	public static function drop($args=false)
	{
		$argum = self::$_args;

		if($argum == null)
		{
			throw new CacheHandlerException('Unknown Parameter Cache.');
		}
		else
		{
			$cache_name = self::$_cache_name;
			switch($type_save)
			{
				case 'file':
				$save = self::_DropCacheFile();
				break;
				case 'database':
				$save = self::_DropCacheDatabase();
				break;
				default:
				$save = null;
			}

			if($save == null)
			{
				throw new CacheHandlerException('Failed Drop Cache.');
			}
			else
			{
				return true;
			}
		}
	}


	/**
	 * _DropCacheFile()
	 * Fungsi menangani penghapusan cache berupa file
 	 * @return true OR null
	 */
	protected static function _DropCacheFile()
	{
		$cache_name = self::$_cache_name;

		$basepath = defined('BASEPATH') ? BASEPATH : self::BASE_PATH;
		$cache_dir = $basepath.'/'.self::CACHE_DIR;
		$stored = $cache_dir.'/'.$cache_name.'.json';

		if( file_exists($stored) AND ( filemtime($stored) < strtotime('now') ) )
		{
			unlink($stored);
			return true;
		}
		else{
			return null;
		}
	}


	/**
	 * _DropCacheFile()
	 * Fungsi menangani penghapusan cache berupa MySQL atau Database
 	 * @return true OR null
	 */
	protected static function _DropCacheDatabase()
	{
		$argum = self::$_args;

		if($argum == null)
		{
			return $argum;
		}
		else
		{
			$table_cache = isset($argum['table_cache']) ? $argum['table_cache'] : 'app_cache';

			$cache_name = self::$_cache_name;
			$prm = "WHERE cache_name='{$cache_name}'";

			$data = array('tbl'=>$table_cache,'con'=>$prm);
			$drop = AccessCRUD($data,'update');

			if($drop == false)
			{
				return null;
			}
			else
			{
				return true;
			}
		}
	}
}

/**
 * CacheHandlerException extends
 */
class CacheHandlerException extends Exception{}
?>