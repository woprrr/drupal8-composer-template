<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class DrupalExportConf extends DrupalHandlerBase
{
  public static $drush = 'vendor/bin/drush -r web';

  public static function process(Event $event)
  {
    $step = 1;
    $io = $event->getIO();
    $dc = self::getDrushConfig(file_get_contents('settings/drush.config.yml'));

    self::devModulesManager($event, 'dis', $dc['parameters']['dev.modules']);

    if (isset($event->getArguments()[0])) {
      $io->write("#step {$step}. Configuration : Exporting {$event->getArguments()[0]}.");
      $process = new Process(self::$drush . " cim {$event->getArguments()[0]} --quiet -y");
    } else {
      $io->write("#step {$step}. Configuration : Exporting default (sync).");
      $process = new Process(self::$drush . " cim --quiet -y");
    }
    $process->run();
    echo $process->getOutput();

    if (isset($event->getArguments()[0])) {
      $process = new Process(self::$drush . " cex {$event->getArguments()[0]} -y");
    } else {
      $process = new Process(self::$drush . " cex -y");
    }
    $process->run();
    echo $process->getOutput();

    self::devModulesManager($event, 'en', $dc['parameters']['dev.modules']);

  }
}
