<?php
declare(strict_types=1);


use Krzychu12350\Phpmetasploit\MsfRpcClient;
use Krzychu12350\Phpmetasploit\SessionApiMethods;
use PHPUnit\Framework\TestCase;

class SessionApiMethodsTest extends TestCase
{

    /** @test */
    public function should_return_a_array_with_sessions_data(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $sessionApiMethods = new SessionApiMethods();
        //var_dump($sessionApiMethods->list());
        $this->assertIsArray($sessionApiMethods->list());
    }

    /** @test */
    public function should_return_an_exception_with_message_unknown_session(): void
    {
        $this->expectException(Exception::class);
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $sessionApiMethods = new SessionApiMethods();
        $test = $sessionApiMethods->stop('9999');
    }

    /** @test */
    public function should_return_compatibile_modules(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $sessionApiMethods = new SessionApiMethods();
        $this->assertArrayHasKey("modules",  $sessionApiMethods->compatibleModules("1"));
    }
}
