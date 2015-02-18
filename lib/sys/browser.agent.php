<?php
class Browser
{
    public static $is_mobile;

	public static function is_mobile()
	{
        if( isset(self::$is_mobile) )
            return self::$is_mobile;

        if( empty($_SERVER['HTTP_USER_AGENT']) )
        {
            self::$is_mobile = false;
        } 
        elseif( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false )
        {
            self::$is_mobile = true;
        }
        else
        {
            self::$is_mobile = false;
        }

        return self::$is_mobile;
	}


    public static function get_ip()
    {  
        if( function_exists( 'apache_request_headers' ) )
        {
            $headers = apache_request_headers();
        }
        else
        {
            $headers = $_SERVER;
        }
        
        if( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
        {
            $the_ip = $headers['X-Forwarded-For'];
        }
        elseif( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
        {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
        }

        return $the_ip;
    }


    /**
     * @since v.1.0
     * statusCode()
     * Membuat standar code response untuk ajax JSON
     * Diambil dari twitter API kemudian dibuat Class custom di sini
     * @return $status
     */ 
    public static function statusCode($code='',$lang='id')
    {
        switch ($code)
        {
            case 200:
                $status = $lang == 'id' ? 'Berhasil' : 'Successfull';
                break;
            case 201:
                $status = $lang == 'id' ? 'Sumber Berhasil Dibuat' : 'Resource created';
                break;
            case 202:
                $status = $lang == 'id' ? 'Proses Diterima' : 'Accepted for processing';
                break;
            case 301:
                $status =  $lang == 'id' ? 'Dialihkan' : 'Redirect';
                break;
            case 302:
                $status =  $lang == 'id' ? 'Ditemukan' : 'Found';
                break;
            case 304:
                $status =  $lang == 'id' ? 'Tidak Ada Data' : 'No Data';
                break;
            case 400:
                $status =  $lang == 'id' ? 'Permintaan Gagal' : 'Failed Request';
                break;
            case 401:
                $status =  $lang == 'id' ? 'Tidak Bisa di Otorisasi' : 'Authentication Failed';
                break;
            case 403:
                $status =  $lang == 'id' ? 'Masuk Kawasan Terlarang' : 'Forbidden';
                break;
            case 404:
                $status =  $lang == 'id' ? 'Tidak Tersedia' : 'Not Exists';
                break;
            case 406:
                $status =  $lang == 'id' ? 'Akses Ditolak' : 'Access Denied';
                break;
            case 410:
                $status =  $lang == 'id' ? 'Permintaan Hilang' : 'Request Lost';
                break;
            case 420:
                $status =  $lang == 'id' ? 'Jangan Tergesa-gesa' : 'Do not rush';
                break;
            case 422:
                $status =  $lang == 'id' ? 'Data Tidak Diproses' : 'Can\'t process data';
                break;
            case 429:
                $status =  $lang == 'id' ? 'Terlalu Banyak Permintaan' : 'Too much requests';
                break;
            case 500:
                $status =  $lang == 'id' ? 'Server Bermasalah' : 'Disruption server';
                break;
            case 502:
                $status =  $lang == 'id' ? 'Salah Saluran' : 'Wrong way';
                break;
            case 503:
                $status =  $lang == 'id' ? 'Layanan Tidak Tersedia' : 'Undefined Services';
                break;
            case 504:
                $status =  $lang == 'id' ? 'Tenggang Saluran Habis' : 'Timeout requests';
                break;
            
            default:
                $status =  $lang == 'id' ? 'Malfungsi' : 'Cheating!';
                break;
        }
        return $status;
    }
}
?>