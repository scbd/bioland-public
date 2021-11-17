<?php

namespace Drupal\chm_common\Plugin\migrate\process;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin downloads remote file and saves it as Drupal file.
 *
 * @MigrateProcessPlugin(
 *   id = "import_managed_file"
 * )
 */
class FileImportAsManaged extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($data, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($data) || !is_array($data)) {
      return [];
    }
    $ret = [];
    foreach ($data as $source => $filename) {
      if (empty($source) || empty($filename)) {
        // TODO: Log error.
        continue;
      }
      $destination = $this->configuration['destination_dir'] . $filename;
      if (!$fid = $this->createFile($source, $destination, $migrate_executable)) {
        $migrate_executable->saveMessage("Could not save file at $source", MigrationInterface::MESSAGE_ERROR);
      }
      $ret[] = $fid;
    }
    return $ret;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return TRUE;
  }

  /**
   * Create or update an existing managed file.
   *
   * @param string $url
   *   Source URL.
   * @param string $destination
   *   Final destination URI.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   Migration source.
   *
   * @return int|null
   *   Return the save/updated FID.
   */
  protected function createFile($url, $destination, MigrateExecutableInterface $migrate_executable) {
    if (!empty($this->configuration['reuse_file'])) {
      $f_entity = \Drupal::entityQuery('file')->condition('uri', $destination)->execute();
      if (!empty($f_entity) && file_exists($destination)) {
        return reset($f_entity);
      }
    }

    if ($data = $this->download($url)) {
      $dest_dir = $this->configuration['destination_dir'];
      if (!file_prepare_directory($dest_dir, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
        $migrate_executable->saveMessage("$destination is not writable", MigrationInterface::MESSAGE_WARNING);
      }
      /** @var FileInterface $file */
      if ($file = file_save_data($data, $destination, FILE_EXISTS_REPLACE)) {
        return $file->id();
      }
    }
    return NULL;
  }

  /**
   * Wrapper around CURL to download files.
   *
   * @param string $url
   *   Source URL.
   * @param array $headers
   *   HTTP Headers.
   *
   * @return mixed|null
   *   File content.
   */
  protected function download($url, array $headers = []) {
    if (empty($url)) {
      return NULL;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // DANGER: Disable SSL certificate validation.
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);.
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_NOBODY, 0);
    $ret = curl_exec($ch);
    $info = curl_getinfo($ch);
    if ($info['http_code'] != 200) {
      $ret = NULL;
    }
    curl_close($ch);
    return $ret;
  }

}
