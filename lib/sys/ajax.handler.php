<?php
/**
 * @author ofanebob
 * @copyright 2014 - 2015
 * Fungsi AJAX Handler
 * Penyederhanaan code dengan mengganti beberapa fungsi standar kedalam MVC Library
 * Fungsi dasar berasal dari project ticket tracker (2013) yang dimodifikasi dari kode wordpress
 * Kemudian di tambahkan beberapa fungsi error handler dan dynamic return untuk format response
 * -----------------------------------------------------------------------------------------------------
 * Standar penggunaan file ajax.php harus dengan jQuery/JavaScript module XHR, Response
 * Beberapa parameter yang menjadi rumus baku adalah:
 * method = berupa object json berisi metode pada MVC
 * params = nilai parameter untuk fungsi yg dipanggil
 * Kedua parameter harus dalam bentuk array
 */
class AjaxHandler
{
    /**
     * @since v.2.0
     * Menangani AJAX Proses tahap kedua setelah self::_Process()
     * Lebih mendetail karena akan memisahkan nilai dari parameter sesuai kebutuhan
     * Yaitu memisahkan nama Class, nama Function dan isi/nilai Parameter fungsi berupa data Array()
     * Kemudian akan di kirim ke fungsi return_format() jika berhasil
     * atau dibuat header() error response
     */
    private static function _ResponseHandler($method,$params)
    {
        /* Cek ketersediaan nama Class */
        if(class_exists($method[0]))
        {
            /* Cek apakah fungsi di dalam Class bisa digunakan & dipanggil dengan fungsi lain */
            if(method_exists($method[0], $method[1]) && is_callable(array($method[0], $method[1])))
            {
                /* Menggunakan call_user_func_array untuk memanggil fungsi dalam Class beserta parameter yg dibutuhkan
                 * Nama Class dan Function masuk kedalam array()
                 * Parameter Function masuk kedalam array, variable $a_params adalah hasil pengolahan dari array_push()
                 */
                $response = call_user_func_array(array($method[0], $method[1]), $params);

                if(is_array($response))
                {
                    echo json_encode($response);
                }
                elseif(is_object($response))
                {
                    $responseObject = ObjectToArray($response);
                    echo json_encode($responseObject);
                }
                elseif(json_decode($response)) {
                    echo $response;
                }
                elseif(is_string($response))
                {
                    echo $response;
                }
                else
                {
                    header("HTTP/1.0 401 Error Format Response");
                    die('401 Error Format Response');
                }

            }
            else
            {
                header("HTTP/1.0 401 Error Method Class");
                die('401 Error Method Class');
            }
        }
        else
        {
            /* Fungsi untuk memeriksa ketersediaan function tanpa Class */
            if(function_exists($method[1]))
            {
                $response = call_user_func_array($method[1], $params);

                if(is_array($response))
                {
                    echo json_encode($response);
                }
                elseif(is_object($response))
                {
                    $responseObject = ObjectToArray($response);
                    echo json_encode($responseObject);
                }
                elseif(json_decode($response)) {
                    echo $response;
                }
                elseif(is_string($response))
                {
                    echo $response;
                }
                else
                {
                    header("HTTP/1.0 401 Error Format Response");
                    die('401 Error Format Response');
                }
            }
            /* Jika Function tidak ada maka respon akan dikirm ke die() */
            else
            {
                header("HTTP/1.0 401 Error Method Function");
                die('401 Error Method Function');
            }
        }
    }


    /**
     * @since v.2.0
     * Menangani AJAX Proses tahap kedua setelah GLOBAL Method
     * Kemudian akan di kirim ke fungsi responseHandler() jika berhasil
     * atau dikirim ke fungsi ajaxError() jika gagal
     */
    public static function _Process($method,$params)
    {
        /* Menetapkan variable $mode berdasakan isi dari variable $class
         * Jika nilai pada variable $class kosong maka array() index pertama berisi string kosong 
         */
        $method = json_decode($method, true);

        $mode = isset($method['m']) ? $method['m'] : null;
        $class = isset($method['c']) ? $method['c'] : null;
        $function = isset($method['f']) ? $method['f'] : null;

        if($mode == null AND $function == null)
        {
            header("HTTP/1.0 401 Error Param");
            die('401 Error Param');
        }
        else
        {
            if($class == null)
            {
                $instanceMethod = array('',$function);
            }
            else
            {
                /* Jika nilai varibale $class tidak kosong maka array() index 1 berisi nilai $class & index 2 berisi $function */
                $Rules = $class.ucfirst($mode);
                $instanceMethod = array($Rules,$function);
            }

            $params = is_object($params) ? 
                        ObjectToArray($params) : is_array($params) ? 
                            $params : json_decode($params, true);
            
            if($params == false)
            {
                header("HTTP/1.0 401 Error Param");
                die('401 Error Param');
            }
            else
            {
                return self::_ResponseHandler($instanceMethod,$params);
            }
        }
    }


	/**
	 * @since v.1.0
	 * get_nocache_headers()
	 * Mengatasi cache pada header yang dikirm kepada client browser melalui ajax
	 * Fungsi ini digunakan untuk nocache_headers()
	 * @return $headers
	 */ 
	public static function get_nocache_headers()
	{
		$headers = array(
			'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
			'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
			'Pragma' => 'no-cache',
		);

		$headers['Last-Modified'] = false;
		return $headers;
	}


	/**
	 * @since v.1.0
	 * nocache_headers()
	 * Mengatasi cache pada header yang dikirm kepada client browser melalui ajax
	 * @return headers()
	 */ 
	public static function nocache_headers()
	{
		$headers = self::get_nocache_headers();

		unset($headers['Last-Modified']);

		if(function_exists('header_remove'))
		{
			@header_remove('Last-Modified');
		}
		else
		{
			foreach(headers_list() as $header)
			{
				if(0 === stripos($header, 'Last-Modified' ))
				{
					$headers['Last-Modified'] = '';
					break;
				}
			}
		}

		foreach( $headers as $name => $field_value )
			@header("{$name}: {$field_value}");
	}
}
?>