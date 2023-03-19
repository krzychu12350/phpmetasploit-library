<?php
declare(strict_types=1);


use Krzychu12350\Phpmetasploit\ConsoleApiMethods;
use Krzychu12350\Phpmetasploit\MsfRpcClient;
use PHPUnit\Framework\TestCase;


class ConsoleApiMethodsTest extends TestCase
{

    /** @test */
    public function should_return_a_array_with_console_id(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123', 'true', 'user', '127.0.0.1', 55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $consoleApiMethods = new ConsoleApiMethods();
        $this->assertArrayHasKey("id", $consoleApiMethods->create());
    }

    /** @test */
    public function should_return_a_list_of_arrays(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123', 'true', 'user', '127.0.0.1', 55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $consoleApiMethods = new ConsoleApiMethods();
        $consolesList = $consoleApiMethods->list();
        $this->assertArrayHasKey("consoles", $consolesList);
    }

    /** @test */
    public function should_successfully_destroy_a_console(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123', 'true', 'user', '127.0.0.1', 55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $consoleApiMethods = new ConsoleApiMethods();
        $consoleData = $consoleApiMethods->create();
        $this->assertSame(['result' => 'success'], $consoleApiMethods->destroy($consoleData['id']));
    }

    /** @test */
    public function must_write_into_a_console(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123', 'true', 'user', '127.0.0.1', 55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $consoleApiMethods = new ConsoleApiMethods();
        $consoleData = $consoleApiMethods->create();
        $this->assertArrayHasKey("wrote", $consoleApiMethods->write($consoleData['id'], "version"));
    }

    /** @test */
    public function must_read_from_a_console(): void
    {
        $msfRpcClient = new MsfRpcClient('pass123', 'true', 'user', '127.0.0.1', 55553, '/api/1.0');
        $msfRpcClient->msfAuth();
        $consoleApiMethods = new ConsoleApiMethods();
        $consoleData = $consoleApiMethods->create();
        $consoleApiMethods->write($consoleData['id'], "version");
        $responseData = $consoleApiMethods->read($consoleData['id'],);
        $this->assertArrayHasKey("data", $responseData);
    }

}
