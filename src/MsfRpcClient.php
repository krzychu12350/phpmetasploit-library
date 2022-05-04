<?php

namespace Krzychu12350\Phpmetasploit;

use JetBrains\PhpStorm\Pure;
use MessagePack\BufferUnpacker;
use MessagePack\Packer;
use Nette\PhpGenerator as PhpGenerator;


class MsfRpcClient
{

    public string $userPassword;
    public string $ssl;
    public string $userName;
    public string $ip;
    public int $port;
    public string $webServerURI;

    function __construct($userPassword,$ssl,$userName,$ip,$port,$webServerURI) {
        $this->userPassword  = $userPassword;
        //$this->ssl = $ssl;
        if($ssl == 'true') $this->ssl = "https://";
        if($ssl == 'false') $this->ssl = "http://";
        $this->userName = $userName;
        $this->ip = $ip;
        $this->port = $port;
        $this->webServerURI = $webServerURI;
    }


    // ************ curlPost() ************ //
    public function curlPost($url, $port, $httpheader, $postfields): array {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , $port);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postfields);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30000);    // Timeout
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0');   // Webbot name
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    // No verify certificate added
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No verify certificate

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string

        $return_array['FILE']   = curl_exec($ch);
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR']  = curl_error($ch);

        curl_close($ch);

        return $return_array;
    }
    /*
     * $ ./msfrpcd -h

   Usage: msfrpcd <options>

   OPTIONS:

       -P <opt>  Specify the password to access msfrpcd
       -S        Disable SSL on the RPC socket
       -U <opt>  Specify the username to access msfrpcd
       -a <opt>  Bind to this IP address
       -f        Run the daemon in the foreground
       -h        Help banner
       -n        Disable database
       -p <opt>  Bind to this port instead of 55553
       -u <opt>  URI for Web server
     */

    // ************  msf_auth() ************ //
    public function msfAuth() {
        $data = array("auth.login", $this->userName, $this->userPassword);

        $packer = new Packer();
        $msgpack_data = $packer->pack($data);

        $url = "$this->ssl" . $this->ip .":$this->port$this->webServerURI";
        $httpheader = array("Host: RPC Server", "Content-Length: " . strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curlPost($url, $this->port, $httpheader, $postfields);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();
        $generateToken = $msgunpack_data["token"];

        session_start();
        $_SESSION["token"] = $generateToken;
        //dd($_SESSION["token"]);

        //Generating API Methods from array
        $this->createApiMethods();

        return $generateToken;
    }

    // ************ msf_cmd() ************ //
    public function msfRequest($client_request) {
        $packer = new Packer();
        $msgpack_data = $packer->pack($client_request);

        $url = "$this->ssl" . $this->ip .":$this->port$this->webServerURI";
        $httpheader = array("Host: RPC Server", "Content-Length: ".strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curlPost($url, $this->port, $httpheader, $postfields);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();

        return $msgunpack_data;
    }


    //Genereting methods with
    public function createApiMethods() {

        //-----------------------Core-----------------------
        $apiMethods = [
            //-----------------------Authentication-----------------------
            [ "auth.login", "MyUserName", "MyPassword"],
            [ "auth.logout", "<token>", "<LogoutToken>"],
            [ "auth.token_add", "<token>", "<NewToken>"],
            [ "auth.token_generate", "<token>"],
            [ "auth.token_list", "<token>"],
            [ "auth.token_remove", "<token>", "<TokenToBeRemoved>"],
            //-----------------------Core-----------------------
            [ "core.add_module_path", "<token>", "<Path>"],
            [ "core.module_stats", "<token>"],
            [ "core.reload_modules", "<token>"],
            [ "core.save", "<token>" ],
            [ "core.setg", "<token>", "<OptionName>", "<OptionValue>"],
            [ "core.unsetg", "<token>", "<OptionName>" ],
            [ "core.thread_list", "<token>"],
            [ "core.thread_kill", "<token>", "<ThreadID>"],
            [ "core.version", "<token>"],
            [ "core.stop", "<token>"],

            /*
            //-----------------------Console-----------------------
            [ "console.create", "<token>"],
            [ "console.destroy", "<token>", "ConsoleID"],
            [ "console.list", "<token>"],
            [ "console.write", "<token>", "0", "version\n"],
            [ "console.read", "<token>", "0"],
            [ "console.session_detach", "<token>", "ConsoleID"],
            [ "console.session_kill", "<token>", "ConsoleID"],
            [ "console.tabs", "<token>", "ConsoleID", "InputLine"]
            */
            //-----------------------Jobs-----------------------
            [ "job.list", "<token>"],
            [ "job.info", "<token>", "JobID"],
            // nie może być w jednym pliku bo metoda stop juz jest z core grupy metod
            [ "job.stop", "<token>", "JobID"],

        ];

        //TO DO - Passing arguments to dynamically creating methods
        /*
        $token ='efqeeqe355egeg';

        $arrayOfCoreMethods = array(
            'aFunctionName' => array($_SESSION["token"],'second','third'),
            //'version' => ['core.version', $token],
            'version' => [ "core.version", "<token>"],
            'module_stats($token)' => [ "core.module_stats", "<token>"]
            //'module_stats($token)' => [ $arg1, $_SESSION["token"]]
        );

        $test = new GroupMethodsTemplate($arrayOfCoreMethods);
        dd($test, $test->module_stats('erhhr'));

        return $test;
         */

        //Searching unique methods group names

        for ($i = 0; $i <= count($apiMethods) - 1; $i++) {
            $filesArray[] = strtok($apiMethods[$i][0], '.');
            $filesArray = array_unique($filesArray);
        }
        //dd($fileArray);

        foreach($filesArray as $methodsGroup) {

            $file = new PhpGenerator\PhpFile;
            $file->addComment('This file is auto-generated.');
            $file->setStrictTypes(); // adds declare(strict_types=1)

            $namespace = $file->addNamespace('Krzychu12350\Phpmetasploit');

            //$methodsGroup = ucwords('core');
            $className = ucwords($methodsGroup) . 'ApiMethods';

            $class = $namespace->addClass($className);
            $class->setExtends(MsfRpcClient::class);

            // improve generating method' name for method like core.add_module_path
            // generating method name from $apiMethods nested array
            for ($i = 0; $i <= count($apiMethods) - 1; $i++) {

                //generating methods belongs to specific file
                //dd(strtok($apiMethods[$i][0], '.'), $methodsGroup);
                if(strtok($apiMethods[$i][0], '.') == $methodsGroup) {

                    $substr1 = substr($apiMethods[$i][0], strpos($apiMethods[$i][0], ".") + 1);
                    if (str_contains($substr1, '_'))
                        $substr1 = explode('_', $substr1)[0] . ucwords(explode('_', $substr1)[1]);
                    $method = $class->addMethod($substr1);


                    //dd($substr1, $substr2);
                    //dd($methods);
                    //lcfirst(trim($apiMethods[$i][2], '<>'))

                    //$requestArray = implode('", "', [ $apiMethods[$i][0], '$token' , '$path']);

                    //$requestArray = [ $apiMethods[$i][0], $path ];
                    //dd($requestArray);
                    //$requestArray = '["' . $apiMethods[$i][0] .'"]';

                    // processing nested array to create client_request arrays

                    //that's works
                    $requestArray = array();
                    foreach ($apiMethods[$i] as $value) {
                        if (str_contains($value, "<") || str_contains($value, ">")) {
                            $elem = '$' . lcfirst(trim($value, '<>'));
                            $requestArray[] = $elem;
                        } else {
                            $requestArray[] = '"' . $value . '"';
                        }
                    }

                    $requestArray = implode(', ', $requestArray);

                    //dd($requestArray);

                    // END processing nested array to create client_request arrays

                    $method->setBody('
                        $clientRequest = [' . $requestArray . '];' . "\n" . 'return $this->msfRequest($clientRequest);
                        ');

                    for ($j = 1; $j <= count(current($apiMethods)); $j++) {

                        //dd($apiMethods[$i][$j])
                        if (isset($apiMethods[$i][$j]))
                            $method->addParameter(lcfirst(trim($apiMethods[$i][$j], '<>')));
                    }
                }
            }

            //file_put_contents("E:\\phpmetasploit\\package\\phpmetasploit\src\\".$className.'.php', $file);
            file_put_contents(dirname(__FILE__) . '\\' . $className . '.php', $file);

            //dd(dirname(__FILE__));

            //spl_autoload_register($this->my_autoloader($className));
            //$test = new Test();
            //spl_autoload_register('custom_autoloader');


            //$printer = new PhpGenerator\Printer;
            //echo $printer->printNamespace($namespace);
            // = fopen($className, "w") or die("Unable to create file!");
            //fwrite($myfile, $class);
            //($myfile);
            //return $class;
        }

    }

    /*
    public function msfConsoleCreate()
    {
        //$this->msfAuth();
        $client_request = array("core.thread_list",$_SESSION["token"]);
        $server_write_response = $this->msfRequest($client_request);
        return $server_write_response;
    }
    /*
    // ************  msf_create() ************ //
    public function msf_create($token,$ip)
    {
        $data = array("console.create", $token);

        $packer = new Packer();
        $msgpack_data = $packer->pack($data);

        $url = "$this->ssl" . $this->ip .":$this->port$this->webServerURI";

        $httpheader = array("Host: RPC Server", "Content-Length: " . strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curlPost($url, $this->port, $httpheader, $postfields);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();

        return $msgunpack_data;
    }
    */

    // ************ msf_console() ************ //
    function msf_console($ip, $token, $console_id, $cmd)
    {
        $client_request = array("console.write", $token, $console_id, $cmd . "\n");
        $server_write_response = $this->msfRequest($ip, $client_request);
        do {

            $client_request = array("console.read", $token, $console_id);
            $server_read_response = $this->msfRequest($ip, $client_request);
            //print $server_read_response["data"] . "</br>";
            //debug('$server_read_response["data"]: ' . $server_read_response["data"] . "</br>");
            //debug('$server_read_response["prompt"]: ' . $server_read_response["prompt"] . "</br>");
            //debug('$server_read_response["busy"]: ' . $server_read_response["busy"] . "</br>");

        } while($server_read_response["busy"] == true);

        return $server_read_response;
    }

    // ************ msf_execute() ************ //
    function msf_execute($ip, $token, $cmd)
    {
        $client_request = array("console.write", $token, '0', $cmd . "\n");
        $server_write_response = $this->msfRequest($client_request);
        return $server_write_response;
    }

}
