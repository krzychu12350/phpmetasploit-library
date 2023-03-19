<?php
declare(strict_types=1);


use Krzychu12350\Phpmetasploit\MsfRpcClient;
use PHPUnit\Framework\TestCase;


class AuthApiMethodsTest extends TestCase
{

    /** @test */
    public function should_return_the_array_with_result_equals_success(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $authApiMethods = new \Krzychu12350\Phpmetasploit\AuthApiMethods();
        $this->assertSame(['result' => 'success'], $authApiMethods->tokenAdd('9f4gt17wIdLviRYh0lAumtkRTr9GvVv4'));
    }

    /** @test */
    public function should_return_array_with_key_tokens(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $authApiMethods = new \Krzychu12350\Phpmetasploit\AuthApiMethods();
        $tokenList = $authApiMethods->tokenList();
        $this->assertArrayHasKey("tokens", $tokenList);

    }

    /** @test */
    public function should_login_and_logout_successfully(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        //$msfRpcClient->msfAuth();
        $authApiMethods = new \Krzychu12350\Phpmetasploit\AuthApiMethods();
        $authToken = $authApiMethods->login('user', 'pass123');
        //var_dump($authToken['token']);
        $logoutResponse = $authApiMethods->logout($authToken['token']);
        //var_dump($logoutResponse["result"]);
        //$tokenList = $authApiMethods->tokenList();
        //$this->assertArrayHasKey("tokens", $tokenList);
        $this->assertSame('success', $logoutResponse["result"]);
    }

    /** @test */
    public function should_generate_new_propely_auth_token(): void
    {
        new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $authApiMethods = new \Krzychu12350\Phpmetasploit\AuthApiMethods();
        $authToken = $authApiMethods->login('user', 'pass123');
        $generatedAuthToken = $authApiMethods->tokenGenerate();
        $authApiMethods->logout($authToken['token']);
        $authApiMethods->tokenRemove($generatedAuthToken['token']);

        $this->assertSame(32,  strlen($generatedAuthToken['token']));
    }
}
