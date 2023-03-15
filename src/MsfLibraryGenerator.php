<?php

namespace Krzychu12350\Phpmetasploit;

use Exception;
use Nette\PhpGenerator as PhpGenerator;

class MsfLibraryGenerator
{
    public static function generateLibrary()
    {
        $msfPayloadArrayScrapper = new MsfPayloadArrayScrapper();
        $apiMethods = $msfPayloadArrayScrapper->getArraysPayloadsFromWebsite();

        for ($i = 0; $i <= count($apiMethods) - 1; $i++) {
            $filesArray[] = strtok($apiMethods[$i][0], '.');
            $filesArray = array_unique($filesArray);
        }

        foreach ($filesArray as $methodsGroup) {
            $file = new PhpGenerator\PhpFile;
            $file->addComment('This is auto-generated class using Nette PHP Generator');
            $file->setStrictTypes();
            $namespace = $file->addNamespace('Krzychu12350\Phpmetasploit');
            $namespace->addUse(Exception::class);
            $className = ucwords($methodsGroup) . 'ApiMethods';
            $class = $namespace->addClass($className);
            $class->setExtends(MsfRpcClient::class);
            $class->addComment("@author Krzysztof Karaś");
            $class->addComment("@access public");
            $class->addMethod('__construct')
                ->setBody('parent::__construct
                (MsfConnector::getUserPassword(),
                MsfConnector::getSsl(), MsfConnector::getUserName(),
                MsfConnector::getIp(), MsfConnector::getPort(),
                MsfConnector::getWebServerURI());
                $this->token = MsfConnector::getToken();
                ');
            for ($i = 0; $i <= count($apiMethods) - 1; $i++) {
                if (strtok($apiMethods[$i][0], '.') == $methodsGroup) {
                    $substr = substr($apiMethods[$i][0], strpos($apiMethods[$i][0], ".") + 1);
                    $methodName = lcfirst(str_replace("_", "", join('_',
                        array_map('ucfirst', explode('_', $substr)))));
                    $method = $class->addMethod($methodName);
                    $requestArray = array();
                    foreach ($apiMethods[$i] as $value) {
                        if (str_contains($value, "<") || str_contains($value, ">")) {
                            $elem = '$' . lcfirst(trim($value, '<>'));
                            if (str_contains($value, "token")) {
                                $elem = '$this->' . lcfirst(trim($value, '<>'));
                            }
                            $requestArray[] = $elem;
                        } else {
                            if (str_contains($value, ".")) $requestArray[] = '"' . $value . '"';
                            else {
                                if ($methodsGroup == 'console' && lcfirst($value) == 'inputCommand')
                                    $requestArray[] = "$" . lcfirst($value) . ' ."\n"';
                                else $requestArray[] = "$" . lcfirst($value);
                                $method->addComment("@param $" . lcfirst($value));
                            }

                        }
                    }
                    $requestArray = implode(', ', $requestArray);
                    $method->setBody('$responseData = $this->msfRequest([' . $requestArray . ']);' . "\n" .
                        'if (array_key_exists("result", $responseData) && $responseData["result"] === "failure")' . "\n\t" .
                        'throw new Exception("Unprocessable Content", 422);' . "\n" .
                        'if (array_key_exists("error", $responseData))' . "\n\t" .
                        'throw new Exception($responseData["error_message"], 400);'
                        . "\n" . 'else return $responseData;
                        ');
                    $method->addComment("@throws Exception");
                    $method->addComment("@return array");
                    $method->addComment("@access public");
                    $method->setReturnType('array');

                    //dd($apiMethods);
                    for ($j = 1; $j <= count(current($apiMethods)) + 1; $j++) {
                        //dd($apiMethods[$i][$j]);
                        if (isset($apiMethods[$i][$j]) && $apiMethods[$i][$j] != '<token>')
                            //var_dump($apiMethods[$i][$j]);
                            $method->addParameter(lcfirst(trim($apiMethods[$i][$j], '<>')));
                    }
                }
            }
            file_put_contents(dirname(__FILE__) . '/' . $className . '.php', $file);
        }
    }
}
