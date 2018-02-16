<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Yaml\Yaml;

/**
 * Take ability to manage updates of Drupal instance with configuration.
 *
 * @author Alexandre Mallet <woprrr.dev@gmail.com>
 */
class DrupalUpdate extends DrupalHandlerBase {

  /**
   * Execute all process commands needed by Drupal Update.
   *
   * These script assume all syncronization of drupal configuration.
   *
   * @param Event $event
   *
   * @throws \Symfony\Component\Process\Exception\ProcessFailedException
   */
  public static function process(Event $event) {
    $fs = new Filesystem();
    $io = $event->getIO();
    $step = 1;

    $io->write("<info>#step {$step}.</info> Settings : Prepare directories");
    self::prepareFilesDirectories($event);

    $params = self::getDrushConfig(file_get_contents('app/Drupal/config/parameters.yml'));
    $dev_modules = $params['parameters']['dev.modules'];

    self::devModulesManager($event, 'pm-uninstall', $dev_modules);
    $step++;

    // New whitespace.
    $io->write("");

    $io->write("<info>#step {$step}.</info> Mode maintenance ON");
    $process = new Process(self::drush() . " state-set system.maintenance_mode 1");
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);
    $step++;
    // New whitespace.
    $io->write("");

    $io->write("<info>#step {$step}.</info> Drupal Update");
    $process = new Process(self::drush() . " updb -y");
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);
    $step++;
    // New whitespace.
    $io->write("");

    if (isset($event->getArguments()[0])) {
      $io->write("<info>#step {$step}.</info> Configuration : Update {$event->getArguments()[0]}.");
      $process = new Process(self::drush() . " cim {$event->getArguments()[0]} --quiet -y");
    } else {
      $io->write("<info>#step {$step}.</info> Configuration : Import default (sync).");
      $process = new Process(self::drush() . " cim --quiet -y");
    }
    $process->run();

    self::writeDrushOutput($io, $process);
    $step++;
    // New whitespace.
    $io->write("");

    $io->write("<info>#step {$step}.</info> Drupal Entity update");
    $process = new Process(self::drush() . " entup -y");
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);
    $step++;
    // New whitespace.
    $io->write("");

    $io->write("<info>#step {$step}.</info> Mode maintenance OFF");
    $process = new Process(self::drush() . " state-set system.maintenance_mode 0");
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);
    $step++;
    // New whitespace.
    $io->write("");

    $io->write("<info>#step {$step}.</info> Clear Caches");
    $process = new Process(self::drush() . ' cr');
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);
    $step++;
    // New whitespace.
    $io->write("");

    self::devModulesManager($event, 'en', $dev_modules);

    $io->write("<info>#step {$step}.</info> settings.local permissions");
    $fs->chmod(self::getDrupalRootFolder(getcwd()) . "/sites/default/settings.local.php", 0666);
    $io->write("* Update `<info>sites/default/settings.local.php</info>` file with chmod 0666");
  }

}
