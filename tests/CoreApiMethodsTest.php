<?php
declare(strict_types=1);


use Krzychu12350\Phpmetasploit\CoreApiMethods;
use Krzychu12350\Phpmetasploit\MsfRpcClient;
use PHPUnit\Framework\TestCase;


class CoreApiMethodsTest extends TestCase
{
    /** @test */
    public function must_return_the_array_with_three_array_keys(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $coreApiMethods = new CoreApiMethods();
        $responseArray = $coreApiMethods->version();
        $this->assertSame(3, count($responseArray));
    }

    /** @test */
    public function should_properly_set_global_variable(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $coreApiMethods = new CoreApiMethods();
        $responseArray = $coreApiMethods->setg('IP', '8.8.8.8');
        $this->assertSame(['result' => 'success'], $responseArray);
    }
    /** @test */
    public function should_properly_set_and_unset_global_variable(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $coreApiMethods = new CoreApiMethods();
        $responseArray = $coreApiMethods->setg('IP', '8.8.8.8');
        $responseArray = $coreApiMethods->unsetg('IP');
        $this->assertSame(['result' => 'success'], $responseArray);
    }

}
