<?php

namespace Drupal\Composer\Plugins;

use Composer\Script\Event;
use Composer\Semver\Comparator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

abstract class DrupalHandlerBase {

  public static $drush = 'vendor/bin/drush -r web';

  protected static function getDrupalRoot($project_root) {
    return $project_root . '/web';
  }

  public static function prepareFilesDirectories(Event $event) {
    $fs = new Filesystem();
    $root = static::getDrupalRoot(getcwd());

    $dirs = [
      'modules',
      'profiles',
      'themes',
      'libraries',
    ];

    // Required for unit testing
    foreach ($dirs as $dir) {
      if (!$fs->exists($root . '/'. $dir)) {
        $fs->mkdir($root . '/'. $dir);
        $fs->touch($root . '/'. $dir . '/.gitkeep');
      }
    }

    // Prepare the settings file for installation
    if ($fs->exists(getcwd() . '/settings/settings.php')) {
      $fs->copy(getcwd() . '/settings/settings.php', $root . '/sites/default/settings.php');
      $fs->chmod($root . '/sites/default/settings.php', 0666);
      $event->getIO()->write("Create a sites/default/settings.php file with chmod 0666");
    }

    // Prepare the settings file for installation
    if ($fs->exists(getcwd() . '/settings/settings.local.php')) {
      $fs->symlink(getcwd() . '/settings/settings.local.php', $root . '/sites/default/settings.local.php');
      $fs->chmod($root . '/sites/default/settings.local.php', 0666);
      $event->getIO()->write("Symlink a sites/default/settings.local.php file with chmod 0666");
    }

    // Prepare the services file for installation
    if ($fs->exists(getcwd() . '/settings/services.yml')) {
      $fs->copy(getcwd() . '/settings/services.yml', $root . '/sites/default/services.yml');
      $fs->chmod($root . '/sites/default/services.yml', 0666);
      $event->getIO()->write("Create a sites/default/services.yml file with chmod 0666");
    }

    // Prepare the settings file for installation
    if ($fs->exists(getcwd() . '/settings/development.services.yml')) {
      $fs->symlink(getcwd() . '/settings/development.services.yml', $root . '/sites/development.services.yml');
      $fs->chmod($root . '/sites/development.services.yml', 0666);
      $event->getIO()->write("Symlink a sites/development.services.yml file with chmod 0666");
    }

    // Create the files directory with chmod 0777
    if (!$fs->exists($root . '/sites/default/files')) {
      $oldmask = umask(0);
      $fs->mkdir($root . '/sites/default/files', 0777);
      umask($oldmask);
      $event->getIO()->write("Create a sites/default/files directory with chmod 0777");
    }
  }

  public static function devModulesManager(Event $event, $op, array $modules) {
    $io = $event->getIO();
    if ('dis' === $op) {
      $io->write("Dev modules : disable developpements modules");
    } else {
      $io->write("Dev modules : Enable developpements modules");
    }

    $module_enable_process = self::$drush . " {$op} ";
    foreach ($modules as $module) {
      $module_enable_process .= $module . ' ';
    }
    $process = new Process($module_enable_process . '-y');
    $process->run();
    $process->getOutput();
  }

  public static function getDrushConfig($input) {
    return Yaml::parse($input);
  }
}
