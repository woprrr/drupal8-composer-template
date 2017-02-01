<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Yaml\Yaml;

class DrupalInstall extends DrupalHandlerBase
{

  public static function process(Event $event)
  {
    $io = $event->getIO();
    $step = 1;

    $io->write("#step {$step}. Settings : Prepare directories");
    self::prepareFilesDirectories($event);

    $dc = self::getDrushConfig(file_get_contents('settings/drush.config.yml'));
    $step++;

    $io->write("#step {$step}. Site-install : Drupal install");

    $process = new Process(self::$drush . " si {$dc['parameters']['site.parameters']['profile']} --site-name={$dc['parameters']['site.parameters']['name']} --account-name={$dc['parameters']['site.parameters']['admin.account']['name']} --account-pass={$dc['parameters']['site.parameters']['admin.account']['password']} --account-mail={$dc['parameters']['site.parameters']['admin.account']['mail']} --locale={$dc['parameters']['site.parameters']['locale']} -y");
    $process->setTimeout('1200');
    $process->run();

    echo $process->getOutput();
    $step++;

    $io->write("#step {$step}. Site-install : Clear Caches");
    $process = new Process(self::$drush . ' cr');
    $process->run();

    echo $process->getOutput();
    $step++;

    if (!empty($dc['parameters']['site.parameters']['language']['uuid'])) {
      $io->write("#step {$step}. Site-install : Force language uuid");
      $process = new Process(self::$drush . " cset language.entity.{$dc['parameters']['site.parameters']['language']['locale']} uuid {$dc['parameters']['site.parameters']['language']['uuid']} -y");
      $process->run();
      echo $process->getOutput();
      $step++;
    }


    if (isset($event->getArguments()[0])) {
      $io->write("#step {$step}. Configuration : Import {$event->getArguments()[0]}.");
      $process = new Process(self::$drush . " cim {$event->getArguments()[0]} --quiet -y");
    } else {
      $io->write("#step {$step}. Configuration : Import default (sync).");
      $process = new Process(self::$drush . " cim --quiet -y");
    }
    $process->run();

    echo $process->getOutput();
    $step++;

    $io->write("#step {$step}. Dev modules : Enable developpements modules");

    self::devModulesManager($event, 'en', $dc['parameters']['dev.modules']);
    $step++;

    $io->write("#step {$step}. settings.local permissions");
    $process = new Process("chmod +w web/sites/default/settings.local.php" . '-y');
    $process->run();
  }

}
