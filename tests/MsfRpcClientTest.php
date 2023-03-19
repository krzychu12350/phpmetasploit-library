<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Krzychu12350\Phpmetasploit\MsfRpcClient;

class MsfRpcClientTest extends TestCase
{

    /**
     * @test for checking of returned token value by msfAuth method (getting of a token)
     */
    public function must_return_the_same_length(): void
    {
        $userPassword = 'pass123';
        $ssl = 'true';
        $userName = 'user';
        $ip = '127.0.0.1';
        $port = 55553;
        $webServerURI = '/api/1.0';
        $msfRpcClient = new MsfRpcClient($userPassword,$ssl,$userName,$ip,$port, $webServerURI);
        $this->assertSame(32, strlen($msfRpcClient->msfAuth()));
    }
}
