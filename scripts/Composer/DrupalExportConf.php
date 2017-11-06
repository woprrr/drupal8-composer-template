<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Export your Drupal active configuration into YAML files using CMI.
 *
 * @author Alexandre Mallet <woprrr.dev@gmail.com>
 */
class DrupalExportConf extends DrupalHandlerBase {

  /**
   * Execute all process commands needed for export Drupal configuration.
   *
   * These script disable developpements modules before configuration export,
   * and re-enable it after to ensure we don't export unneeded configuration YAML.
   *
   * @param Event $event
   *
   * @throws \Symfony\Component\Process\Exception\ProcessFailedException
   */
  public static function process(Event $event) {
    $step = 1;
    $io = $event->getIO();
    $dc = self::getDrushConfig(file_get_contents('app/Drupal/config/parameters.yml'));

    self::devModulesManager($event, 'pm-uninstall', $dc['parameters']['dev.modules']);

    if (isset($event->getArguments()[0])) {
      $io->write("<comment>#step {$step}.</comment> Configuration : <info>Exporting <warning>{$event->getArguments()[0]}</warning></info>.");
      $process = new Process(self::drush() . " cex {$event->getArguments()[0]} -y");
    } else {
      $io->write("<comment>#step {$step}.</comment> Configuration : <info>Exporting default (<warning>sync</warning>)</info>.");
      $process = new Process(self::drush() . " cex -y");
    }
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);

    self::devModulesManager($event, 'en', $dc['parameters']['dev.modules']);

  }
}
