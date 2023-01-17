<?php

namespace Krzychu12350\Phpmetasploit;

class MsfConnector
{
    private static string $ssl;
    private static string $userName;
    private static string $ip;
    private static int $port;
    private static string $webServerURI;
    private static string $userPassword;
    private static string $token;

    /**
     * @return string
     */
    public static function getSsl(): string
    {
        return self::$ssl;
    }

    /**
     * @param string $ssl
     */
    public static function setSsl(string $ssl): void
    {
        self::$ssl = $ssl;
    }

    /**
     * @return string
     */
    public static function getUserName(): string
    {
        return self::$userName;
    }

    /**
     * @param string $userName
     */
    public static function setUserName(string $userName): void
    {
        self::$userName = $userName;
    }

    /**
     * @return string
     */
    public static function getIp(): string
    {
        return self::$ip;
    }

    /**
     * @param string $ip
     */
    public static function setIp(string $ip): void
    {
        self::$ip = $ip;
    }

    /**
     * @return int
     */
    public static function getPort(): int
    {
        return self::$port;
    }

    /**
     * @param int $port
     */
    public static function setPort(int $port): void
    {
        self::$port = $port;
    }

    /**
     * @return string
     */
    public static function getWebServerURI(): string
    {
        return self::$webServerURI;
    }

    /**
     * @param string $webServerURI
     */
    public static function setWebServerURI(string $webServerURI): void
    {
        self::$webServerURI = $webServerURI;
    }

    /**
     * @return string
     */
    public static function getUserPassword(): string
    {
        return self::$userPassword;
    }

    /**
     * @param string $userPassword
     */
    public static function setUserPassword(string $userPassword): void
    {
        self::$userPassword = $userPassword;
    }

    /**
     * @return string
     */
    /*
    public static function getToken(): string
    {
        return self::$token;
    }
    */
    /**
     * @param string $token
     */
    /*
    public static function setToken(string $token): void
    {
        self::$token = $token;
    }
    */
}
