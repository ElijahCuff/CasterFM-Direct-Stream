<?php
// Allow Cross Origin Requests
cors();

// Get IP Address & Useragent 
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
$agent = $_SERVER['HTTP_USER_AGENT'];
$ip = $_SERVER['REMOTE_ADDR'];


// Get UID
if(hasParam("uid"))
{
   // ID Found, Build Widget URL
$id = $_GET["uid"];
$widget = "https%3A%2F%2Fwww.caster.fm%2Fwidgets%2Fem_player.php%3Fjsinit%3Dtrue%26uid%3D".$id."%26t%3Dc%3Dblue";
$url = urldecode($widget);
}
else
{
   // ID Not Found, Return ERROR in Json Format 
header("HTTP/1.0 300 Invalid Input");
$data = [ 'message' => 'Invalid Input', 'code' => 300 ];
$response = jsonify($data);
exit($response);
}


// Extract Authentication Token and Build Streaming URL
 $data = extractAuthSimple($url);
 $port = $data[0];
 $token = $data[1];
 $gen= "http://shaincast.caster.fm:".$port."/listen.mp3?".$token;
 $url = $gen;

// Start Streaming the Feed as Response 
stream($url);


function stream($url, $mime = "audio/mpeg", $bufferSize = 1024*1024)
{
    header('Content-type: '.$mime);
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    header('Keep-Alive: timeout='.(60*24).', max='.(60*48));
    $handle = fopen($url, 'rb');

    ob_start();
    $bytes = '';
     $stream = true;
    while ($stream) {
        $bytesLive = fread($handle, $bufferSize);
        $bytes = $bytesLive;
        echo $bytes;
        flush_buffers();
        }
   $status = fclose($handle);
   ob_end_flush(); 
}


function flush_buffers(){
    ob_flush();
    flush();
}

function extractAuthSimple($url)
{
   $html = getPage($url);
   $keyToken = "&port=";
   $keyEndChar = "&type";
   $keyStart = strpos($html, $keyToken)+strlen($keyToken);
   $keyEnd = strpos($html, $keyEndChar, $keyStart);
   $keyLength = $keyEnd-$keyStart;
   $keyExtract = substr($html,$keyStart,$keyLength);
   return explode("&auth=",$keyExtract);
}

function getPage($url, $asClient = true)
{
  $ch = curl_init();
  global $ip;
  global $agent;
   if($asClient)
     {
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"));
    curl_setopt ($ch, CURLOPT_USERAGENT, $agent);
     }
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec ($ch);
     curl_close ($ch);
   return $data;
}

function jsonify($dataArray)
{
header('Content-Type: application/json');
echo json_encode($dataArray);
}

function hasParam($param) 
{
   if (array_key_exists($param, $_POST))
    {
       return array_key_exists($param, $_POST);
    } else
    {
      return array_key_exists($param, $_GET);
    }
}

function cors()
{
if (isset($_SERVER['HTTP_ORIGIN'])) {

        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); 
    }
}

?>
