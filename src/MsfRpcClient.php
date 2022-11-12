<?php

namespace Krzychu12350\Phpmetasploit;

use MessagePack\BufferUnpacker;
use MessagePack\Packer;
use Nette\PhpGenerator as PhpGenerator;
use function PHPUnit\Framework\directoryExists;

//require dirname(__DIR__) . '\vendor\autoload.php';

class MsfRpcClient
{

    //TO DO: zapisać te argumenty do sesji i stworzyć z tego osobną klasą
    /*
     * Zrobić ogolna klase abstracyjna te ktore cos robia
     * Ta klasa abstrakcyjna ma jeszcze jakas plole ktore ma pole jeszcze innej klasy
     *  najpieerw stworzyc klase conntector ktora bedzie trzymala 'polaczenie' i dane autoryzacyjne
     * w kazdej klasie bede mogl sie do tego pola odniesc a inicjalizacje podaje w konstruktorze
     * getterem pobrac poloczenie i wrzucic do constructora
     *
     * biore getterem connection 1 i 2 wywoluje getConnection albo uzyc tego polaczenia
     * to co jest wspolne do klasy abstrackja klasy abstrakcyjnej np. MsfRpcClient i kolejna Connection
     * w konstrukturze klsay MsfRpcClient 'wpinam' obiekt klasy Connection
     */
    private string $ssl;
    private string $userName;
    private string $ip;
    private int $port;
    private string $webServerURI;
    private string $userPassword;
    private string $token;

    public function __construct($userPassword,$ssl,$userName,$ip,$port, $webServerURI)
    {
        MsfConnector::setUserPassword($userPassword);
        MsfConnector::setSsl($ssl);
        MsfConnector::setUserName($userName);
        MsfConnector::setIp($ip);
        MsfConnector::setPort($port);
        MsfConnector::setWebServerURI($webServerURI);

        $this->userPassword = $userPassword;
        if ($ssl == 'true') {
            $this->ssl = "https://";

        }
        if ($ssl == 'false') {
            $this->ssl = "http://";

        }
        $this->userName = $userName;
        $this->ip = $ip;
        $this->port = $port;
        $this->webServerURI = $webServerURI;
    }

    /**
     * @return string
     */
    private function getUserPassword(): string
    {
        return $this->userPassword;
    }

    /**
     * @param string $ssl
     */
    private function setUserPassword(string $userPassword): void
    {
        $this->userPassword = $userPassword;
    }

    /**
     * @return string
     */
    private function getSsl(): string
    {
        return $this->ssl;
    }

    /**
     * @param string $ssl
     */
    private function setSsl(string $ssl): void
    {
        $this->ssl = $ssl;
    }

    /**
     * @return string
     */
    private function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    /*
    private function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }
    */
    /**
     * @return string
     */
    private function getIp(): int
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    private function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    private function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    private function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    private function getWebServerURI(): string
    {
        return $this->webServerURI;
    }

    /**
     * @param string $webServerURI
     */
    private function setWebServerURI(string $webServerURI): void
    {
        $this->webServerURI = $webServerURI;
    }

    /**
     * @return string
     */
    private function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    private function setToken(string $token): void
    {
        $this->token = $token;
    }


    //TO DO: setter z tokenem


    /*
    public function __set($name, $value) {
        $this->$connectionData[$userName] = $name;
    }

    public function __get($name) {
        if(!array_key_exists($name, $this->connectionData)) {
            return null;
        }
    }
    public function getData() {
        return $this->connectionData;
    }
    */


    // ************ curlPost() ************ //

    public function msfAuth()
    {
        $data = array("auth.login", $this->userName, $this->userPassword);

        $packer = new Packer();
        $msgpack_data = $packer->pack($data);

        $url = "$this->ssl" . $this->ip . ":$this->port$this->webServerURI";
        $httpheader = array("Host: RPC Server", "Content-Length: " . strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curlPost($url, $this->port, $httpheader, $postfields);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();
        $generatedToken = $msgunpack_data["token"];

        //$this->setToken($generateToken);
        MsfConnector::setToken($generatedToken);

        //Generating API Methods from array
        //$this->createApiMethods();

        return $generatedToken;
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

    private function curlPost($url, $port, $httpheader, $postfields): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, $port);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30000);    // Timeout
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0');   // Webbot name
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    // No verify certificate added
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No verify certificate

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string

        $return_array['FILE'] = curl_exec($ch);
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR'] = curl_error($ch);

        curl_close($ch);

        return $return_array;
    }

    // ************ msf_cmd() ************ //

    private static function createApiMethods()
    {
        //if (!directoryExists('methods'))
        //    mkdir(dirname(__FILE__) . 'methods', 0777, true);
        //13 metod, które trzeba ręcznie napisać
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
            ["core.add_module_path", "<token>", "<Path>"],
            ["core.module_stats", "<token>"],
            ["core.reload_modules", "<token>"],
            ["core.save", "<token>"],
            ["core.setg", "<token>", "<OptionName>", "<OptionValue>"],
            ["core.unsetg", "<token>", "<OptionName>"],
            ["core.thread_list", "<token>"],
            ["core.thread_kill", "<token>", "<ThreadID>"],
            ["core.version", "<token>"],
            ["core.stop", "<token>"],

            //-----------------------Console-----------------------
            ["console.create", "<token>"],
            ["console.destroy", "<token>", "ConsoleID"],
            ["console.list", "<token>"],
            //[ "console.write", "<token>", "0", "version\n"],
            //[ "console.read", "<token>", "0"],
            ["console.session_detach", "<token>", "ConsoleID"],
            ["console.session_kill", "<token>", "ConsoleID"],
            ["console.tabs", "<token>", "ConsoleID", "InputLine"],

            //-----------------------Jobs-----------------------
            ["job.list", "<token>"],
            ["job.info", "<token>", "JobID"],
            // nie może być w jednym pliku bo metoda stop juz jest z core grupy metod
            ["job.stop", "<token>", "JobID"],

            //-----------------------Modules-----------------------
            [ "module.exploits", "<token>" ],
            [ "module.auxiliary", "<token>" ],
            [ "module.post", "<token>" ],
            [ "module.payloads", "<token>" ],
            [ "module.encoders", "<token>" ],
            [ "module.nops", "<token>" ],
            [ "module.info", "<token>", "ModuleType", "ModuleName" ],
            [ "module.options", "<token>", "ModuleType", "ModuleName" ],
            [ "module.compatible_payloads", "<token>", "ModuleName" ],
            //[ "module.target_compatible_payloads", "<token>", "ModuleName", 1 ],
            [ "module.compatible_sessions", "<token>", "ModuleName" ],
            //[ "module.encode", "<token>", "Data", "EncoderModule", ["Option1" => "Value1", "Option2" => "Value2"]],
            //[ "module.execute", "<token>", "ModuleType", "ModuleName", [ "RHOST" => "1.2.3.4", "RPORT" => "80"]],
            // Wyrzuca błąd przy nawiasach klamrowych może byc jedynie zagnieżdzona jeszcze tablica
            //[ "module.execute", "<token>", "ModuleType", "ModuleName", {"LHOST" => "4.3.2.1", "LPORT" => "4444"}],

            //-----------------------Plugins-----------------------
            //[ "plugin.load", "<token>", "PluginName", ["Option1" => "Value1", "Option2" => "Value2"]],
            ["plugin.unload", "<token>", "PluginName"],
            ["plugin.loaded", "<token>"],
            //-----------------------Sessions-----------------------
            ["session.list", "<token>"],
            ["session.stop", "<token>", "SessionID"],
            ["session.shell_read", "<token>", "SessionID", "ReadPointer"],
            //[ "session.shell_write", "<token>", "SessionID", "id\n" ],
            //[ "session.meterpreter_write", "<token>", "SessionID", "ps" ],
            ["session.meterpreter_read", "<token>", "SessionID"],
            //[ "session.meterpreter_run_single", "<token>", "SessionID", "ps" ],
            //["session.meterpreter_script","<token>","SessionID","scriptname"],
            ["session.meterpreter_session_detach", "<token>", "SessionID"],
            //[ "session.meterpreter_session_detach", "<token>", "SessionID" ],
            ["session.meterpreter_tabs", "<token>", "SessionID", "InputLine"],
            ["session.compatible_modules", "<token>", "SessionID"],
            //[ "session.shell_upgrade", "<token>", "SessionID", "1.2.3.4", 4444 ],
            ["session.ring_clear", "<token>", "SessionID"],
            ["session.ring_last", "<token>", "SessionID"],
            //[ "session.ring_put", "<token>", "SessionID", "id\n" ],

        ];


        $msfPayloadArrayScrapper = new MsfPayloadArrayScrapper();
        $apiMethods = $msfPayloadArrayScrapper->getArraysPayloadsFromWebsite();
        //dd($apiMethods);
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

        //dd($filesArray);

        foreach ($filesArray as $methodsGroup) {

            $file = new PhpGenerator\PhpFile;
            $file->addComment('This file is auto-generated.');
            $file->setStrictTypes(); // adds declare(strict_types=1)

            $namespace = $file->addNamespace('Krzychu12350\Phpmetasploit');

            //$methodsGroup = ucwords('core');
            $className = ucwords($methodsGroup) . 'ApiMethods';

            $class = $namespace->addClass($className);
            $class->setExtends(MsfRpcClient::class);

            $class->addProperty('token')
                ->setType('string')
                ->setPrivate();

            $class->addMethod('__construct')
                ->setBody('parent::__construct
                (MsfConnector::getUserPassword(),
                MsfConnector::getSsl(), MsfConnector::getUserName(),
                MsfConnector::getIp(), MsfConnector::getPort(),
                MsfConnector::getWebServerURI());
                $this->token = MsfConnector::getToken();
                ');


            // improve generating method' name for method like core.add_module_path
            // generating method name from $apiMethods nested array
            for ($i = 0; $i <= count($apiMethods) - 1; $i++) {

                //generating methods belongs to specific file
                //dd(strtok($apiMethods[$i][0], '.'), $methodsGroup);
                if (strtok($apiMethods[$i][0], '.') == $methodsGroup) {

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
                            if (str_contains($value, "token")){
                                $elem = '$this->' . lcfirst(trim($value, '<>'));
                            }
                            $requestArray[] = $elem;
                        } else {
                            if (str_contains($value, ".")) $requestArray[] = '"' . $value . '"';
                            else {
                                $requestArray[] = "$" . lcfirst($value);
                                $method->addComment("@param " . lcfirst($value));
                            }

                        }
                    }

                    $requestArray = implode(', ', $requestArray);
                    //dd($requestArray);

                    //dd($requestArray);

                    // END processing nested array to create client_request arrays

                    $method->setBody('
                        $clientRequest = [' . $requestArray . '];' . "\n" . 'return $this->msfRequest($clientRequest);
                        ');

                    for ($j = 1; $j <= count(current($apiMethods)); $j++) {
                        //dd($apiMethods[$i][$j]);
                        if (isset($apiMethods[$i][$j]) && $apiMethods[$i][$j] != '<token>')
                            $method->addParameter(lcfirst(trim($apiMethods[$i][$j], '<>')));
                    }
                }

            }



            /*
            if ($className == "ConsoleApiMethods") {
                $method = $class->addMethod('write')
                    ->setBody('
                        $clientRequest = ["console.write", $this->token, $consoleId, $content];'
                        . "\n" . 'return $this->msfRequest($clientRequest);');
                $method->addParameter("consoleId");
                $method->addParameter("content");

                $method = $class->addMethod('read')
                    ->setBody('
                        $clientRequest = ["console.read", $this->token, $consoleId];'
                        . "\n" . 'return $this->msfRequest($clientRequest);');
                $method->addParameter("consoleId");
            }
            if ($className == "ModuleApiMethods") {
                //[ "module.encode", "<token>", "Data", "EncoderModule", ["Option1" => "Value1", "Option2" => "Value2"]],
                $method = $class->addMethod('encode')
                    ->setBody('
                        $clientRequest = [$token, $data, $encoderModule, $optionsArray];'
                        . "\n" . 'return $this->msfRequest($clientRequest);');
                $method->addParameter("token");
                $method->addParameter("data");
                $method->addParameter("encoderModule");
                $method->addParameter("optionsArray");

                $method = $class->addMethod('execute')
                    ->setBody('
                        $clientRequest = [$token, $moduleType, $moduleName,$optionsArray];'
                        . "\n" . 'return $this->msfRequest($clientRequest);');
                $method->addParameter("token");
                $method->addParameter("moduleType");
                $method->addParameter("moduleName");
                $method->addParameter("optionsArray");
                //[ "module.execute", "<token>", "ModuleType", "ModuleName", [ "RHOST" => "1.2.3.4", "RPORT" => "80"]],
                //dd($method);

                // "module.target_compatible_payloads", "<token>", "ModuleName", 1 ],
                $method = $class->addMethod('targetCompatiblePayloads')
                    ->setBody('
                        $clientRequest = [$token, $moduleName, $target];'
                        . "\n" . 'return $this->msfRequest($clientRequest);');
                $method->addParameter("token");
                $method->addParameter("moduleName");
                $method->addParameter("target");

            }
            */

            //file_put_contents("E:\\phpmetasploit\\package\\phpmetasploit\src\\".$className.'.php', $file);
            file_put_contents(dirname(__FILE__) . '\\'. $className . '.php', $file);

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


    //Genereting methods with

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

        } while ($server_read_response["busy"] == true);

        return $server_read_response;
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

    protected function msfRequest($client_request)
    {
        $packer = new Packer();
        $msgpack_data = $packer->pack($client_request);

        $url = "$this->ssl" . $this->ip . ":$this->port$this->webServerURI";
        $httpheader = array("Host: RPC Server", "Content-Length: " . strlen($msgpack_data), "Content-Type: binary/message-pack");
        $postfields = $msgpack_data;
        $return_array = $this->curlPost($url, $this->port, $httpheader, $postfields);

        $unpacker = new BufferUnpacker();
        $unpacker->reset($return_array['FILE']);
        $msgunpack_data = $unpacker->unpack();

        return $msgunpack_data;
    }

    // ************ msf_execute() ************ //

    function msf_execute($ip, $token, $cmd)
    {
        $client_request = array("console.write", $token, '0', $cmd . "\n");
        $server_write_response = $this->msfRequest($client_request);
        return $server_write_response;
    }

}
