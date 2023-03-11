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


class PluginInstaller implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    public static function getSubscribedEvents()
    {
        return [
            'post-package-install' => 'onPostPackageInstallOrUpdate',
            'post-package-update' => 'onPostPackageInstallOrUpdate'
        ];
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        //var_dump("dddddddddddddddddddd");
        /*
        if (!file_exists(dirname(__FILE__) . '\\methods'))
            mkdir(dirname(__FILE__) . '\\methods', 0777, true);
        */
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {

    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public function onPostPackageInstallOrUpdate(PackageEvent $event)
    {
        //$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir') . '/';

        /** @var InstallOperation $item */

        $metasploitLibraryGenerator = new MsfLibraryGenerator();
        $metasploitLibraryGenerator::generateLibrary();
    }
}
