<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Krzychu12350\Phpmetasploit\MsfRpcClient;

class MsfRpcClientTests extends TestCase
{

    /**
     * @test for checking of returned token value by msfAuth method (getting of a token)
     */
    public function mustReturnTheSameLength(): void
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

    /**
     * @test for checking of logout token
     */
    public function shouldReturnTheArrayWithResultEqualsSuccess(): void
    {
        $userPassword = 'pass123';
        $ssl = 'true';
        $userName = 'user';
        $ip = '127.0.0.1';
        $port = 55553;
        $webServerURI = '/api/1.0';
        $msfRpcClient = new MsfRpcClient($userPassword,$ssl,$userName,$ip,$port, $webServerURI);
        $token = $msfRpcClient->msfAuth();
        //print_r($msfRpcClient->msfRequest(["auth.logout", $token, $token]));
        $this->assertSame(['result' => 'success'], $msfRpcClient->msfRequest(["auth.logout", $token, $token]));
    }
}
