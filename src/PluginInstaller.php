<?php

namespace Krzychu12350\Phpmetasploit;

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PostFileDownloadEvent;
use Composer\Plugin\PreFileDownloadEvent;
use Nette\PhpGenerator as PhpGenerator;

class PluginInstaller implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        //var_dump("dddddddddddddddddddd");
        mkdir(dirname(__FILE__) . '\\methods', 0777, true);
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {

    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            'post-package-install' => 'onPostPackageInstallOrUpdate',
            'post-package-update' => 'onPostPackageInstallOrUpdate'
        ];
    }

    public function onPostPackageInstallOrUpdate(PackageEvent $event)
    {
        //$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir') . '/';

        /** @var InstallOperation $item */
        $this->createApiMethods();
        /*
        foreach ($event->getOperations() as $item) {

            $packageInstalled = $item->getPackage()->getName();
            // do any thing with the package name like `laravel/laravel`
            //You can now edit the composer.json file

            echo $vendorDir . $packageInstalled . '/composer.json';

        }
        */

    }

    public function autoGenerateFiles(PostFileDownloadEvent $event)
    {
        $protocol = parse_url($event->getName(), PHP_URL_SCHEME);
        /*
        if ($protocol === 's3') {
            // ...
        }
        */
        var_dump($event->getName());

    }

    // ************ msf_cmd() ************ //

    public function createApiMethods()
    {


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

            //file_put_contents(dirname(__FILE__) . '\\' . $className . '.php', $file);
            file_put_contents(dirname(__FILE__) . '\\methods\\' . $className . '.php', $file);

        }
    }
}
