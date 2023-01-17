<?php

namespace Krzychu12350\Phpmetasploit;
use Goutte\Client;
//Laravel Framework
//require_once dirname(__DIR__) . '\vendor\autoload.php';
//require dirname(__DIR__) . '../../../autoload.php';
//require dirname(__DIR__) . "./vendor/autoload.php";
//require dirname(__DIR__) . '../../../autoload.php';
//require "vendor/autoload.php";
//require './vendor/autoload.php';
//Vanilla PHP
//require dirname(__DIR__) . '../../../autoload.php';
class MsfPayloadArrayScrapper
{
    public function getArraysPayloadsFromWebsite(): array
    {
        //dd(__DIR__ . "\\vendor\\autoload.php");
        $client = new Client();
        $crawler = $client->request('GET',
            'https://docs.rapid7.com/metasploit/standard-api-methods-reference');
        return array_values(array_unique(array_filter($crawler
            ->filter('.language-bash > .token-line > .code-line-content')
            ->each(function ($node) {
                $line = $node->text();
                if (str_contains($line, '[') and str_contains($line, '{'))
                    return $this->stringToArrayProcessor(str_replace("{", '"Options" ]', $line));
                elseif (!str_contains($line, '[') or str_contains($line, 'Bad'))
                    return false;
                else
                    return $this->stringToArrayProcessor($line);
            })), SORT_REGULAR));
    }

    private function stringToArrayProcessor($stringArray): array
    {
        $replacements = array(
            '0' => 'ConsoleID',
            'version\n' => 'Command',
            "ReadPointer ]" => 'InputCommand',
            'id\n' => 'InputCommand',
            "1.2.3.4" => "IpAddress",
            //"ps" => "InputCommand",

        );
        $singleArray = array();
        foreach (explode('"', $stringArray) as $key => $singleElement)
            if ($key % 2 != 0)
                $singleArray[] = strtr($singleElement, $replacements);

        return $singleArray;
    }
}
