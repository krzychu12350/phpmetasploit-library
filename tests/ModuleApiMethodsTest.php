<?php
declare(strict_types=1);


use Krzychu12350\Phpmetasploit\ModuleApiMethods;
use Krzychu12350\Phpmetasploit\MsfRpcClient;
use PHPUnit\Framework\TestCase;


class ModuleApiMethodsTest extends TestCase
{

    /** @test */
    public function should_return_a_array_with_exploits(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $moduleApiMethods = new ModuleApiMethods();
        $this->assertArrayHasKey("modules", $moduleApiMethods->exploits());
    }

    /** @test */
    public function should_return_a_array_with_payloads(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $moduleApiMethods = new ModuleApiMethods();
        $this->assertArrayHasKey("modules", $moduleApiMethods->payloads());
    }

    /** @test */
    public function should_return_the_same_module_filename(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $moduleApiMethods = new ModuleApiMethods();
        $moduleInfo = $moduleApiMethods->info("exploit","exploit/multi/handler");
        $this->assertEquals("exploit/multi/handler", $moduleInfo['fullname']);
    }

    /** @test */
    public function should_successfully_run_a_module(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $moduleApiMethods = new ModuleApiMethods();
        $responseData = $moduleApiMethods->execute("auxiliary", "auxiliary/scanner/portscan/tcp",
            ["RHOSTS" =>"192.168.1.1-100", "PORTS" => "100-200"]);
        $this->assertArrayHasKey("job_id", $responseData);
    }
}
