<?php

namespace Krzychu12350\Phpmetasploit;

use MessagePack\BufferUnpacker;
use MessagePack\Packer;

class MsfRpcClient
{
    public string $userPassword;
    public string $ssl;
    public string $userName;
    public string $ip;
    public int $port;
    public string $webServerURI;

    function __construct($userPassword,$ssl,$userName,$ip,$port,$webServerURI) {
        $this->userPassword  = $userPassword;
        //$this->ssl = $ssl;
        if($ssl == 'true') $this->ssl = "https://";
        if($ssl == 'false') $this->ssl = "http://";
        $this->userName = $userName;
        $this->ip = $ip;
        $this->port = $port;
        $this->webServerURI = $webServerURI;
    }


    // ************ curl_post() ************ //
    public function curl_post($url, $port, $httpheader, $postfields): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , $port);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postfields);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30000);    // Timeout
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0');   // Webbot name
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    // No verify certificate added
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No verify certificate

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string

        $return_array['FILE']   = curl_exec($ch);
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR']  = curl_error($ch);

        curl_close($ch);

        return $return_array;
    }
    /*
     * $ ./msfrpcd -h

   Usage: msfrpcd <options>

   OPTIONS:

       -P <opt>  Specify the password to access msfrpcd
       -S        Disable SSL on the RPC socket
       -U <opt>  Specify the username to access msfrpcd
       -a <opt>  Bind to this IP address
       -f        Run the daemon in the foreground
       -h        Help banner
       -n        Disable database
       -p <opt>  Bind to this port instead of 55553
       -u <opt>  URI for Web server
     */

    // ************  msf_auth() ************ //
    public function msfAuth()
    {
        $data = array("auth.login", $this->userName, $this->userPassword);

        $packer = new Packer();
        $msgpack_data = $packer->pack($data);

        $url = "$this->ssl" . $this->ip .":$this->port$this->webServerURI";
        $httpheader = array("Host: RPC Server", "Content-Length: " . strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curl_post($url, $this->port, $httpheader, $postfields);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();
        $generateToken = $msgunpack_data["token"];

        session_start();
        $_SESSION["token"] = $generateToken;
        //dd($_SESSION["token"]);

        return $generateToken;

    }

    // ************ msf_cmd() ************ //
    function msf_cmd($client_request)
    {
        $packer = new Packer();
        $msgpack_data = $packer->pack($client_request);

        $url = "$this->ssl" . $this->ip .":$this->port$this->webServerURI";
        $httpheader = array("Host: RPC Server", "Content-Length: ".strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curl_post($url, $this->port, $httpheader, $postfields);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();

        return $msgunpack_data;
    }
    public function msfConsoleCreate()
    {
        //$this->msf_auth();
        $client_request = array("module.exploits",$_SESSION["token"]);
        $server_write_response = $this->msf_cmd($client_request);
        return $server_write_response;
    }
    /*
    // ************  msf_create() ************ //
    public function msf_create($token,$ip)
    {
        $data = array("console.create", $token);

        $packer = new Packer();
        $msgpack_data = $packer->pack($data);

        $url = "$this->ssl" . $this->ip .":$this->port$this->webServerURI";

        $httpheader = array("Host: RPC Server", "Content-Length: " . strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curl_post($url, $this->port, $httpheader, $postfields);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();

        return $msgunpack_data;
    }
    */

    // ************ msf_console() ************ //
    function msf_console($ip, $token, $console_id, $cmd)
    {
        $client_request = array("console.write", $token, $console_id, $cmd . "\n");
        $server_write_response = $this->msf_cmd($ip, $client_request);
        do {

            $client_request = array("console.read", $token, $console_id);
            $server_read_response = $this->msf_cmd($ip, $client_request);
            //print $server_read_response["data"] . "</br>";
            //debug('$server_read_response["data"]: ' . $server_read_response["data"] . "</br>");
            //debug('$server_read_response["prompt"]: ' . $server_read_response["prompt"] . "</br>");
            //debug('$server_read_response["busy"]: ' . $server_read_response["busy"] . "</br>");

        } while($server_read_response["busy"] == true);

        return $server_read_response;
    }

    // ************ msf_execute() ************ //
    function msf_execute($ip, $token, $cmd)
    {
        $client_request = array("console.write", $token, '0', $cmd . "\n");
        $server_write_response = $this->msf_cmd($client_request);
        return $server_write_response;
    }

}
