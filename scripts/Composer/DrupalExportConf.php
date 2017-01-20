<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class DrupalExportConf
{
  public static $drush = 'vendor/bin/drush -r web';

  public static function process(Event $event)
  {
    $drush_yml = Yaml::parse(file_get_contents('settings/drush.config.yml'));
    self::devModulesManager('dis', $drush_yml);

    if (isset($event->getArguments()[0])) {
      echo sprintf("\n\n#step 1. Configuration : Exporting {$event->getArguments()[0]}.");
      $process = new Process(self::$drush . " cim {$event->getArguments()[0]} --quiet -y");
    } else {
      echo sprintf("\n\n#step 1. Configuration : Exporting default (sync).");
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

    self::devModulesManager('en', $drush_yml);

  }

  public static function devModulesManager($op, $drush_yml) {
    if ('dis' === $op) {
      echo sprintf("\n\nDev modules : disable developpements modules");
    } else {
      echo sprintf("\n\nDev modules : Enable developpements modules");
    }
    $modules = $drush_yml['parameters']['dev.modules'];
    $module_enable_process = self::$drush . " {$op} ";
    foreach ($modules as $module) {
      $module_enable_process .= $module . ' ';
    }
    $process = new Process($module_enable_process . '-y');
    $process->run();
  }
}
