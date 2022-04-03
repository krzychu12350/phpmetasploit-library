<?php

namespace Krzychu12350\Phpmetasploit;

use MessagePack\BufferUnpacker;
use MessagePack\Packer;

class Commands
{
    public function msfAuth(String $string)
    {
        return "Biggest Lord of Existence is:" . $string;
    }
    // ************ curl_post() ************ //
    public function curl_post($url, $port, $httpheader, $postfields): array
    {
        //debug("START Function curl_post()</br>");

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
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0');   // Webbot name
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

        //debug("END Function curl_post()</br>");
        return $return_array;
    }

// ************  msf_auth() ************ //
    public function msf_auth($ip, $username, $password)
    {
        //debug("START Function msf_auth()</br>");
        //$data = array(0=>1,1=>2,2=>3);
        $data = array("auth.login", $username, $password);

        //$msgpack_data = msgpack_pack($data);
        $packer = new Packer();
        $msgpack_data = $packer->pack($data);


        $url = "https://" . $ip . ":55553/api/1.0";
        $port = 55553;
        $httpheader = array("Host: RPC Server", "Content-Length: " . strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curl_post($url, $port, $httpheader, $postfields);


        //$msgunpack_data = msgpack_unpack($return_array['FILE']);

        //$msgunpack_data = MessagePack::unpack($return_array['FILE']);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();


        $token = $msgunpack_data["token"];

        //debug("END Function msf_auth()</br>");

        return $token;
    }
}
