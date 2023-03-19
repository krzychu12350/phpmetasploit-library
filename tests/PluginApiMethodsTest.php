<?php
declare(strict_types=1);


use Krzychu12350\Phpmetasploit\MsfRpcClient;
use Krzychu12350\Phpmetasploit\PluginApiMethods;
use PHPUnit\Framework\TestCase;


class PluginApiMethodsTest extends TestCase
{

    /** @test */
    public function should_return_the_array_of_loaded_plugins(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $pluginsApiMethods = new PluginApiMethods();
        $this->assertArrayHasKey("plugins", $pluginsApiMethods->loaded());
    }

    /** @test */
/*
    public function should_successfully_load_a_plugin(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $pluginsApiMethods = new PluginApiMethods();
        $pluginsApiMethods->load("network_scanner", ["IPV6" => "true"]);

        $this->assertSame(['result' => 'success'], $responseData);
        //$pluginsApiMethods->unload("network_scanner");
    }
*/
    /** @test */
/*
    public function should_successfully_unload_a_plugin(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123','true','user',
            '127.0.0.1',55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $pluginsApiMethods = new PluginApiMethods();
        $pluginsApiMethods->load("network_scanner", ["IPV6" => "true"]);
        $this->assertSame(['result' => 'success'], $pluginsApiMethods->unload("sample"));
    }
*/
}
