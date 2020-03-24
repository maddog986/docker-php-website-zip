<?php

/**
 * function to zip files and folders
 *
 * @author	Drew Gauderman
 * @version	v1.0.0	Tuesday, March 24th, 2020.
 * @global
 * @param	string	$source
 * @param	string	$destination
 * @param	string	$prependPath
 * @return	void
 */
function zipIt($source = './', $destination = './website.zip', $prependPath = '')
{
  // make sure the zip extension is enabled
  if (!extension_loaded('zip')) {
    throw new Exception('extension zip must be loaded!');
  }

  // zip up folder/file check
  $source = realpath($source);
  if (!is_dir($source) && !is_file($source)) {
    throw new Exception('source must be a valid path to a folder or file!');
  }

  // start ZipArchive
  $zip = new ZipArchive();

  // create or overwrite the zip file
  if ($zip->open($destination, ZIPARCHIVE::CREATE || ZIPARCHIVE::OVERWRITE) === true) {
    // convert to realpath once file is created
    $destination = realpath($destination);

    // if folder, use recursive to grab all the contents
    if (is_dir($source)) {
      $iterator = new RecursiveDirectoryIterator($source);
      $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
      $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

      // go through each file
      foreach ($files as $path) {
        echo "path: $path<br>";

        // file full path
        $path = realpath($path);

        // zip entire directory
        if (is_dir($path)) {
          $zip->addEmptyDir($prependPath . str_replace($source . DIRECTORY_SEPARATOR, '', $path . DIRECTORY_SEPARATOR));
        }
        // zip file (but not the main zip file)
        elseif (is_file($path) && $path !== $destination) {
          $zip->addFile($path, $prependPath . str_replace($source . DIRECTORY_SEPARATOR, '', $path));
        }
      }
    } else {
      // add file to the zip
      $zip->addFile($source, $prependPath . basename($source));
    }
  }

  // close ZipArchive
  $zip->close();
}

// file locations
$folderBackup = './testfolder';
$databaseBackup = './database.sql';
$backupZip = './backup.zip';

// mysql server details
$databaseServer = 'localhost';
$databaseUsername = 'tester';
$databasePassword = 'Tester0!';
$databaseName = 'test_database';

// dump the database to .sql file. "(*) 2>&1" helps to get any error messages.
$response = exec("(mysqldump --user=$databaseUsername --password='$databasePassword' --host=\"$databaseServer\" $databaseName --result-file=$databaseBackup) 2>&1");
// echo "response: $response";

// zip up the test folder
zipIt($folderBackup, $backupZip);

echo "Done.";
