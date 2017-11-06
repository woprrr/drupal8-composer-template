<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\IO\ConsoleIO;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

/**
 * HandlerBase offers convenience methods to manage Drupal instance with composer.
 *
 * @author Alexandre Mallet <woprrr.dev@gmail.com>
 */
abstract class DrupalHandlerBase {

  /**
   * Execute drush in vendor to execute on Drupal ROOT folder.
   *
   * @var string $drush
   */
  public static $drush = 'bin/drush';

  /**
   * Before running install/update
   *
   * Prepare required files/folders for an correct Drupal install .
   *
   * @param Event $event
   */
  public static function prepareFilesDirectories(Event $event) {
    $fs = new Filesystem();
    $io = $event->getIO();
    $root = static::getDrupalRootFolder(getcwd());

    foreach (['modules', 'profiles', 'themes', 'libraries'] as $dir) {
      if (!$fs->exists($root . '/'. $dir)) {
        $fs->mkdir($root . '/'. $dir);
        $fs->touch($root . '/'. $dir . '/.gitkeep');
      }
    }

    // Prepare the settings file for installation
    if (!$fs->exists($root . '/sites/default/settings.php') && $fs->exists(getcwd() . '/settings/settings.php')) {
      $fs->copy(getcwd() . '/settings/settings.php', $root . '/sites/default/settings.php');
      $fs->chmod($root . '/sites/default/settings.php', 0666);
      $io->write("* Create a `<info>sites/default/settings.php</info>` file with chmod 0666");
    }

    // Prepare the settings.local file for installation
    if (!$fs->exists($root . '/sites/default/settings.local.php') && $fs->exists(getcwd() . '/settings/settings.local.php')) {
      $fs->symlink(getcwd() . '/settings/settings.local.php', $root . '/sites/default/settings.local.php');
      $fs->chmod($root . '/sites/default/settings.local.php', 0666);
      $io->write("* Symlink a `<info>sites/default/settings.local.php</info>` file with chmod 0666");
    }

    // Prepare the services file for installation
    if (!$fs->exists($root . '/sites/default/services.yml') && $fs->exists(getcwd() . '/settings/services.yml')) {
      $fs->copy(getcwd() . '/settings/services.yml', $root . '/sites/default/services.yml');
      $fs->chmod($root . '/sites/default/services.yml', 0666);
      $io->write("* Create a `<info>sites/default/services.yml</info>` file with chmod 0666");
    }

    // Prepare the development.service file for installation
    if (!$fs->exists($root . '/sites/development.services.yml') && $fs->exists(getcwd() . '/settings/development.services.yml')) {
      $fs->symlink(getcwd() . '/settings/development.services.yml', $root . '/sites/development.services.yml');
      $fs->chmod($root . '/sites/development.services.yml', 0666);
      $io->write("* Symlink a `<info>sites/development.services.yml</info>` file with chmod 0666");
    }

    // Create the files directory with chmod 0777
    if (!$fs->exists($root . '/sites/default/files')) {
      $oldmask = umask(0);
      $fs->mkdir($root . '/sites/default/files', 0777);
      umask($oldmask);
      $io->write("* Create a `<info>sites/default/files directory</info>` with chmod 0777");
    }
    // New whitespace.
    $io->write("");
  }

  /**
   * Helper function to manage Drupal modules with drush.
   *
   * @param Event  $event
   * @param string $op      Opération needed can be `en` or `pm-uninstall` because Drupal 8 does not support `dis` alias.
   * @param array  $modules List of developpement module to exclude on config export/import.
   *
   * @throws \Symfony\Component\Process\Exception\ProcessFailedException
   */
  public static function devModulesManager(Event $event, $op, array $modules) {
    $io = $event->getIO();
    if ('pm-uninstall' === $op) {
      $io->write("<comment>Dev modules : Disable developpements modules.</comment>");
    } else {
      $io->write("<comment>Dev modules : Enable developpements modules.</comment>");
    }

    $module_enable_process = self::drush() . " {$op} ";
    foreach ($modules as $module) {
      $module_enable_process .= $module . ' ';
    }
    $process = new Process($module_enable_process . '-y');
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    self::writeDrushOutput($io, $process);

    // New whitespace.
    $io->write("");
  }

  /**
   * Helper function to manage Drupal modules with drush.
   *
   * @param string $input The YAML file to convert to a PHP value.
   *
   * @return mixed The YAML converted to a PHP value.
   */
  protected static function getDrushConfig($input) {
    return Yaml::parse($input);
  }

  /**
   * Retrieve the Drupal root folder path.
   *
   * @param string $project_root The current working directory.
   *
   * @return string The Drupal ROOT path.
   */
  protected static function getDrupalRootFolder($project_root) {
    return $project_root . '/web';
  }

  /**
   * Helper to use drush with root.
   *
   * @param string $project_root The current working directory.
   *
   * @return string The Drupal ROOT path.
   */
  protected static function drush() {
    return self::$drush . ' --root="' . getcwd() . '/web"';
  }

  /**
   * Return correct Output return by Drush process.
   *
   * ATM we have a strange "bug", when drush return on the Output
   * that is an ErrorOutput to view sucess/ok status message.
   *
   * @param ConsoleIO   $event
   * @param Process                 $process
   */
  protected static function writeDrushOutput(ConsoleIO $io, Process $process) {
    $io->write($process->getOutput());
    $io->write($process->getErrorOutput());
  }
}
