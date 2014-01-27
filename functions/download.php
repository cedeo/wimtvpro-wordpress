<?php
/**
 * Created by JetBrains PhpStorm.
 * User: walter
 * Date: 21/01/14
 * Time: 17.07
 * To change this template use File | Settings | File Templates.
 */

define('CHUNK_SIZE', 1024*1024); // Size (in bytes) of tiles chunk

// Read a file and display its content chunk by chunk
function chunk($ch, $str) {
    print($str);
    ob_flush();
    flush();
    return strlen($str);
}

function readfile_chunked($file, $size, $username, $password) {

    function get_chunk($file, $start, $end, $auth){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $auth);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RANGE, $start.'-'.$end);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'chunk');
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    $auth = $username . ':' . $password;
    $i = 0;
    while($i<=$size){
        //Output the chunk
        get_chunk($file,(($i==0)?$i:$i+1),((($i+CHUNK_SIZE)>$size)?$size:$i+CHUNK_SIZE), $auth);
        $i = ($i+CHUNK_SIZE);
    }
    ob_end_clean();
}

function wimtvpro_download() {
    $code = explode("download/", request_path());
    if (count($code)>1) {
        $host_id = $code[1];
        $info = apiDownload($host_id);
        $headers = $info->headers;
        $opts = getApi();
        $username = $opts->username;
        $password = $opts->password;

        $response_headers = array('content-type', 'content-disposition', 'accept-ranges', 'content-length');

        foreach ($response_headers as $header) {
            header($header . ": " . $headers[$header]);
            //print_r($header . ": " . $headers[$header] . "\n");
        }
        header('Connection: close');

        $file = $info->request->uri;
        $size = $headers['content-length'];

        readfile_chunked($file, $size, $username, $password);
    } else {
        echo ("File not found");
    }
    die();
}