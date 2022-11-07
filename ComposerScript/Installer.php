<?php

namespace ComposerScript;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

use ComposerScript\FileGenerator;

class Installer
{
    public static function postUpdate(PackageEvent $event)
    {
        $packageName = $event->getOperation()
            ->getPackage()
            ->getName();
        echo $packageName. "Tesssssssstttttttttt";
        // do stuff
    }

    public static function postPackageUpdate(Event $event)
    {
        /*
        $packageName = $event->getOperation()
            ->getPackage()
            ->getName();
        echo "$packageName\n";
        */
        $fileGenerator = new FileGenerator();
        $fileGenerator->createApiMethods();
        var_dump(get_class_methods($fileGenerator));
        echo "Tesssssssstttttttttt";
        // do stuff
    }

    public static function warmCache(Event $event)
    {
        // make cache toasty
        echo "Tesssssssstttttttttt";
    }
}
