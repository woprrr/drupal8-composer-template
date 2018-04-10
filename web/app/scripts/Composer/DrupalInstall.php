<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Take ability to manage fresh installation of Drupal instance with configuration.
 *
 * @author Alexandre Mallet <woprrr.dev@gmail.com>
 */
class DrupalInstall extends DrupalHandlerBase {

  /**
   * Execute all process commands needed by Drupal fresh installation.
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
    $site_params = $params['parameters'];
    $dev_modules = $params['parameters']['dev.modules'];
    $step++;

    // New whitespace.
    $io->write("");

    $io->write("<info>#step {$step}.</info> Drupal install : Site install");
    // Now we need to change permission of this file before re-install.
    $fs = new Filesystem();
    $fs->chmod(static::getDrupalRootFolder(getcwd()) . '/sites/default/settings.php', 0666);

    $process = new Process(self::drush() . " si {$site_params['site.profile']} --site-name='{$site_params['site.name']}' --account-name={$site_params['admin.account.name']} --account-pass={$site_params['admin.account.password']} --account-mail={$site_params['admin.account.mail']} --locale={$site_params['site.language.locale']} -y");
    $process->setTimeout('1200');
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);
    $step++;

    // New whitespace.
    $io->write("");

    $io->write("<info>#step {$step}.</info> Drupal install : Clear Caches");
    $process = new Process(self::drush() . ' cr');
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);
    $step++;

    if (!empty($site_params['site.language.uuid'])) {
      $io->write("<info>#step {$step}.</info> Drupal install : Force language uuid");
      $process = new Process(self::drush() . " cset language.entity.{$site_params['site.language.locale']} uuid {$site_params['site.language.uuid']} -y");
      $process->run();

      if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
      }

      self::writeDrushOutput($io, $process);
      $step++;
    }

    // New whitespace.
    $io->write("");

    if (isset($event->getArguments()[0])) {
      $io->write("<info>#step {$step}.</info> Drupal Configuration : Import {$event->getArguments()[0]}.");
      $process = new Process(self::drush() . " cim {$event->getArguments()[0]} --quiet -y");
    } else {
      $io->write("<info>#step {$step}.</info> Drupal Configuration : Import default (<warning>sync</warning>).");
      $process = new Process(self::drush() . " cim --quiet -y");
    }
    $process->run();

    self::writeDrushOutput($io, $process);
    $step++;

    // New whitespace.
    $io->write("");

    $io->write("<info>#step {$step}.</info> Dev modules : Enable developpements modules.");

    self::devModulesManager($event, 'en', $dev_modules);
    $step++;

    $io->write("<info>#step {$step}. settings.local permissions</info>");
    $fs->chmod(self::getDrupalRootFolder(getcwd()) . "/sites/default/settings.local.php", 0666);
    $io->write("* Update `<info>sites/default/settings.local.php</info>` file with chmod 0666");
  }
}
