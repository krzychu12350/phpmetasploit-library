<?php
declare(strict_types=1);


use Krzychu12350\Phpmetasploit\JobApiMethods;
use Krzychu12350\Phpmetasploit\ModuleApiMethods;
use Krzychu12350\Phpmetasploit\MsfRpcClient;
use PHPUnit\Framework\TestCase;


class JobApiMethodsTest extends TestCase
{

    /** @test */
    public function should_return_array_of_jobs(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $jobApiMethods = new JobApiMethods();
        $moduleApiMethods = new ModuleApiMethods();
        $responseData = $moduleApiMethods->execute("auxiliary",
            "auxiliary/scanner/portscan/tcp", ["RHOSTS" =>"192.168.1.1-100", "PORTS" => "100-200"]);
        $jobsList = $jobApiMethods->list();
        $this->assertIsArray($jobsList);
    }

    /** @test */
    public function should_create_and_stop_job(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $jobApiMethods = new JobApiMethods();
        $moduleApiMethods = new ModuleApiMethods();
        $executingModuleResponseData = $moduleApiMethods->execute("auxiliary",
            "auxiliary/scanner/portscan/tcp", ["RHOSTS" =>"192.168.1.1-100", "PORTS" => "100-200"]);
        $responseData = $jobApiMethods->stop($executingModuleResponseData['job_id']);
        $this->assertSame(['result' => 'success'], $responseData);
    }

    /** @test */
    public function should_return_job_info(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $jobApiMethods = new JobApiMethods();
        $moduleApiMethods = new ModuleApiMethods();
        $executingModuleResponseData = $moduleApiMethods->execute("auxiliary",
            "auxiliary/scanner/portscan/tcp", ["RHOSTS" =>"192.168.1.1-100", "PORTS" => "100-200"]);
        $responseData = $jobApiMethods->info($executingModuleResponseData['job_id']);
        $this->assertIsArray($responseData);
    }
}
