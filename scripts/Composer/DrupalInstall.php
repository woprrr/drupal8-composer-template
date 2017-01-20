<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Yaml\Yaml;

class DrupalInstall
{
  public static $drush = 'vendor/bin/drush -r web';

  public static function process(Event $event)
  {
    echo sprintf("\n\n#step 1. Settings : Prepare directories");
    self::prepareDirectories($event);

    $drush_yml = Yaml::parse(file_get_contents('settings/drush.config.yml'));

    echo sprintf("\n\n#step 2. Site-install : Drupal install");

    $process = new Process(self::$drush . " site-install -y --account-name={$drush_yml['parameters']['admin.account']['name']} --account-pass={$drush_yml['parameters']['admin.account']['password']} --account-mail={$drush_yml['parameters']['admin.account']['mail']} --locale={$drush_yml['parameters']['site.parameters']['locale']} {$drush_yml['parameters']['site.parameters']['profile']}");
    $process->setTimeout('1200');
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    echo $process->getOutput();

    echo sprintf("\n\n#step 3. Site-install : Clear Caches");
    $process = new Process(self::$drush . ' cr');
    $process->run();

    echo $process->getOutput();

    if (!empty($drush_yml['parameters']['site.parameters']['language']['uuid'])) {
      echo sprintf("\n\n#step 4. Site-install : Force language uuid");
      $process = new Process(self::$drush . " cset language.entity.{$drush_yml['parameters']['site.parameters']['language']['locale']} uuid {$drush_yml['parameters']['site.parameters']['language']['uuid']} -y");
      $process->run();
      echo $process->getOutput();
    }


    if (isset($event->getArguments()[0])) {
      echo sprintf("\n\n#step 5. Configuration : Import {$event->getArguments()[0]}.");
      $process = new Process(self::$drush . " cim {$event->getArguments()[0]} --quiet -y");
    } else {
      echo sprintf("\n\n#step 5. Configuration : Import default (sync).");
      $process = new Process(self::$drush . " cim --quiet -y");
    }
    $process->run();

    echo $process->getOutput();

    echo sprintf("\n\n#step 6. Dev modules : Enable developpements modules");
    $modules = $drush_yml['parameters']['dev.modules'];
    $module_enable_process = self::$drush . ' en ';
    foreach ($modules as $module) {
      $module_enable_process .= $module . ' ';
    }
    $process = new Process($module_enable_process . '-y');
    $process->run();

    echo $process->getOutput();

    echo sprintf("\n\n#step 7. settings.local permissions");
    $process = new Process("chmod +w web/sites/default/settings.local.php" . '-y');
    $process->run();
  }

  public static function prepareDirectories(Event $event)
  {
    $prepare_directories_process = new Process('cp settings/settings.php web/sites/default/settings.php');
    $prepare_directories_process->run();

    if (!$prepare_directories_process->isSuccessful()) {
      if (!file_exists('web/sites/default/settings.php')) {
        throw new ProcessFailedException($prepare_directories_process);
      }
    }

    echo sprintf("\nssettings.php are correctly copied");

    $prepare_directories_process = new Process('cp settings/services.yml web/sites/default/services.yml');
    $prepare_directories_process->run();

    if (!$prepare_directories_process->isSuccessful()) {
      if (!file_exists('web/sites/default/services.yml')) {
        throw new ProcessFailedException($prepare_directories_process);
      }
    }

    echo sprintf("\nservices.yml are correctly copied");

    $prepare_directories_process = new Process('cp settings/settings.local.php web/sites/default/settings.local.php');
    $prepare_directories_process->run();

    if (!$prepare_directories_process->isSuccessful()) {
      if (!file_exists('web/sites/default/settings.local.php')) {
        throw new ProcessFailedException($prepare_directories_process);
      }
    }

    echo sprintf("\nsettings.local.php are correctly symlnc");

    $prepare_directories_process = new Process('cp settings/development.services.yml web/sites/development.services.yml');
    $prepare_directories_process->run();

    if (!$prepare_directories_process->isSuccessful()) {
      if (!file_exists('web/sites/development.services.yml')) {
        throw new ProcessFailedException($prepare_directories_process);
      }
    }

    echo sprintf("\ndevelopment.services.yml are correctly symlnc");

  }
}
