<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Yaml\Yaml;

class DrupalUpdate extends DrupalHandlerBase
{
  public static $drush = 'vendor/bin/drush -r web';

  public static function process(Event $event)
  {

    $io = $event->getIO();
    $step = 0;
    $io->write("#step {$step}. Settings : Prepare directories");
    self::prepareFilesDirectories($event);

    $dc = Yaml::parse(file_get_contents('settings/drush.config.yml'));

    self::devModulesManager($event, 'dis', $dc);
    $step++;

    $io->write("#step {$step}. Mode maintenance ON");
    $process = new Process(self::$drush . " state-set system.maintenance_mode 1");
    $process->run();
    echo $process->getOutput();
    $step++;

    $io->write("#step {$step}. Drupal Update");
    $process = new Process(self::$drush . " updb -y");
    $process->run();
    echo $process->getOutput();
    $step++;

    if (isset($event->getArguments()[0])) {
      $io->write("#step {$step}. Configuration : Update {$event->getArguments()[0]}.");
      $process = new Process(self::$drush . " cim {$event->getArguments()[0]} --quiet -y");
    } else {
      $io->write("#step {$step}. Configuration : Import default (sync).");
      $process = new Process(self::$drush . " cim --quiet -y");
    }
    $process->run();
    echo $process->getOutput();
    $step++;

    $io->write("#step {$step}. Drupal Entity update");
    $process = new Process(self::$drush . " entup -y");
    $process->run();
    echo $process->getOutput();
    $step++;

    $io->write("#step {$step}. Mode maintenance OFF");
    $process = new Process(self::$drush . " state-set system.maintenance_mode 1");
    $process->run();
    echo $process->getOutput();
    $step++;

    $io->write("#step {$step}. Clear Caches");
    $process = new Process(self::$drush . ' cr');
    $process->run();
    echo $process->getOutput();
    $step++;

    self::devModulesManager($event, 'en', $dc['parameters']['dev.modules']);

    $io->write("#step {$step}. settings.local permissions");
    $process = new Process("chmod +w web/sites/default/settings.local.php" . '-y');
    $process->run();
  }

}
