<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
{
    /* @require memanggil file loader.php termasuk konfigurasi, dsb */
    require_once(dirname(__FILE__).'/loader.php');

    /* @condition jika nilai DEVMODE (developer mode) adalah true maka HTML dikompresi
       nilai DEVMODE bisa ditemukan di file metadata.json
    if(DEVMODE != true)
    {
        proto_html_compression_start();
    } */

    /* @var Mendefinisikan file PHP library (pihak ketiga) */
    $param_library = Libs::inc(APPRUNPATH.'/library.inc');
    Libs::load3rd($param_library);

    /* @require Memasukan library ajax system terpisah sesuai kebutuhan */
    require_once(LIBPATH.'/sys/ajax.handler.php');

    /* @set Header content type untuk format output HTML */
    @header( 'Content-Type: text/html; charset=utf-8' );
    @header( 'X-Robots-Tag: noindex' );
    
    AjaxHandler::nocache_headers();

    /**
     * @since v.2.0
     * Global parameter GET/POST method for AJAX Request function
     * Semua parameter di metode form atau XHTTPS akan dikirm ke AjaxPort::_Process()
     * Dengan parameter fungsi:
     * $method = untuk type pemanggilan class, fungsi & MVC
     * $params = data input/parameter ajax dalam bentuk array
     */
    if(isset($_POST['method']) AND isset($_POST['param']))
    {
        $method = isset($_POST['method']) ? $_POST['method'] : '';
        $params = isset($_POST['param']) ? $_POST['param'] : '';
        return AjaxHandler::_Process($method,$params);
    }
    elseif(isset($_GET['method']) AND isset($_GET['param']))
    {
        $method = isset($_GET['method']) ? $_GET['method'] : '';
        $params = isset($_GET['param']) ? $_GET['param'] : '';
        return AjaxHandler::_Process($method,$params);
    }
    else
    {
        /* Jika tidak ada GLOBAL method POST/GET maka akan di alihkan ke die() */
        header("HTTP/1.0 401 Error POST/GET");
        die('401 Error POST/GET');
    }
}
else
{
    /* Jika bukan XHTTP Request di alihkan ke die() */
    //header("HTTP/1.0 401 Error XHTTP");
    //die('404 Error XHTTP');
    header("Location:404");
}
?>