<?php

namespace Krzychu12350\Phpmetasploit;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PreFileDownloadEvent;

class TemplateInstallerPlugin implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        var_dump("dddddddddddddddddddd");
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {

    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            /*
            PluginEvents::PRE_FILE_DOWNLOAD => array(
                array('onPreFileDownload', 0)
            ),
            */
            'post-autoload-dump' => 'Krzychu12350\Phpmetasploit\MsfRpcClient::createApiMethods',
        );
    }

    public function onPreFileDownload(PreFileDownloadEvent $event)
    {
        $protocol = parse_url($event->getProcessedUrl(), PHP_URL_SCHEME);
        /*
        if ($protocol === 's3') {
            // ...
        }
        */
    }
}
