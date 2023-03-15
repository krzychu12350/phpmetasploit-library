<?php

namespace Krzychu12350\Phpmetasploit;

use MessagePack\BufferUnpacker;
use MessagePack\Exception\InsufficientDataException;
use MessagePack\Packer;

class MsfRpcClient
{

    public string $userPassword;
    protected ?string $token = null;
    private string $ssl;
    private string $userName;
    private string $ip;
    private int $port;
    private string $webServerURI;

    public function __construct($userPassword, $ssl, $userName, $ip, $port, $webServerURI)
    {
        MsfConnector::setUserPassword($userPassword);
        MsfConnector::setSsl($ssl);
        MsfConnector::setUserName($userName);
        MsfConnector::setIp($ip);
        MsfConnector::setPort($port);
        MsfConnector::setWebServerURI($webServerURI);
        $this->userPassword = $userPassword;
        if ($ssl == 'true') $this->ssl = "https://";
        if ($ssl == 'false') $this->ssl = "http://";
        $this->userName = $userName;
        $this->ip = $ip;
        $this->port = $port;
        $this->webServerURI = $webServerURI;
    }


    public function msfAuth()
    {
        $responseData = $this->msfRequest(array("auth.login", $this->userName, $this->userPassword));
        if (!array_key_exists('token', $responseData)) {
            throw new InsufficientDataException("Check the MSFRPCD utility/MSGRPC plugin
                settings or enter the correct username and password ", 500);
        }
        $generatedToken = $responseData["token"];
        MsfConnector::setToken($generatedToken);

        return $generatedToken;
    }

    public function msfRequest($client_request)
    {
        try {
            $packer = new Packer();
            $msgpack_data = $packer->pack($client_request);

            $url = "$this->ssl" . $this->ip . ":$this->port$this->webServerURI";
            $httpheader = array("Host: RPC Server", "Content-Length: " . strlen($msgpack_data), "Content-Type: binary/message-pack");
            $postfields = $msgpack_data;
            $return_array = $this->curlPost($url, $this->port, $httpheader, $postfields);

            $unpacker = new BufferUnpacker();
            $unpacker->reset($return_array['FILE']);
            $msgunpack_data = $unpacker->unpack();

            return $msgunpack_data;
        } catch (InsufficientDataException $e) {
            throw new InsufficientDataException("Check the MSFRPCD utility/MSGRPC plugin settings", 500);
        }
    }

    private function curlPost($url, $port, $httpheader, $postfields): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, $port);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30000);    // Timeout
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    // No verify certificate added
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No verify certificate
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
        $return_array['FILE'] = curl_exec($ch);
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR'] = curl_error($ch);
        curl_close($ch);

        return $return_array;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    protected function getUserPassword(): string
    {
        return $this->userPassword;
    }

    /**
     * @param string $userName
     */

    private function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @param string $ssl
     */
    private function setUserPassword(string $userPassword): void
    {
        $this->userPassword = $userPassword;
    }

    /**
     * @return string
     */
    private function getSsl(): string
    {
        return $this->ssl;
    }

    /**
     * @param string $ssl
     */
    private function setSsl(string $ssl): void
    {
        $this->ssl = $ssl;
    }

    /**
     * @return string
     */
    private function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    private function getIp(): int
    {
        return $this->ip;
    }


    /**
     * @param string $ip
     */
    private function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    private function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    private function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    private function getWebServerURI(): string
    {
        return $this->webServerURI;
    }


    /**
     * @param string $webServerURI
     */
    private function setWebServerURI(string $webServerURI): void
    {
        $this->webServerURI = $webServerURI;
    }


    /**
     * @return string
     */
    private function getToken(): string
    {
        return $this->token;
    }
}
