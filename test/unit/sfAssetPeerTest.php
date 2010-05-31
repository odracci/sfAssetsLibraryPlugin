<?php

$app = "frontend";
include(dirname(__FILE__).'/../../../../test/bootstrap/functional.php');
include($configuration->getSymfonyLibDir().'/vendor/lime/lime.php');
$folderProvider = new sfAssetsFolderProvider();
$assetProvider = new sfAssetsProvider();

$databaseManager = new sfDatabaseManager($configuration);
$con = Propel::getConnection();

$con->beginTransaction();
try
{
  // prepare test environment
  $folderProvider->doDeleteAll();
  $assetProvider->doDeleteAll();
  sfConfig::set('app_sfAssetsLibrary_upload_dir', 'medias');
  $f = new sfAssetFolder();
  $f->setName(sfConfig::get('app_sfAssetsLibrary_upload_dir'));
  sfAssetFolderPeer::createRoot($f);
  $f->save();
  
  $t = new lime_test(5, new lime_output_color());
  $t->diag('sfAssetPeer');

  $t->is($assetProvider->retrieveFromUrl(sfAssetFolderPeer::retrieveRoot()->getRelativePath() . '/filename.jpg'), null, '$assetProvider->retrieveFromUrl() returns null when a URL is not found');
  $t->is($assetProvider->exists(sfAssetFolderPeer::retrieveRoot()->getId(), 'filename.jpg'), false, '$assetProvider->exists() returns false when an asset is not found');
  
  $sfAsset = new sfAsset();
  $sfAsset->setsfAssetFolder(sfAssetFolderPeer::retrieveRoot());
  $sfAsset->setFilename('filename.jpg');
  $sfAsset->save($con);
  $t->is($assetProvider->retrieveFromUrl(sfAssetFolderPeer::retrieveRoot()->getRelativePath() . '/filename.jpg')->getId(), $sfAsset->getId(), '$assetProvider->retrieveFromUrl() finds an asset from its URL');
  $t->is($assetProvider->retrieveFromUrl($sfAsset->getUrl())->getId(), $sfAsset->getId(), '$assetProvider->retrieveFromUrl() finds an asset from the result of `getUrl()`');
  $t->is($assetProvider->exists(sfAssetFolderPeer::retrieveRoot()->getId(), 'filename.jpg'), true, '$assetProvider->exists() returns true when an asset is found');
}
catch (Exception $e)
{
  echo $e->getMessage();
}

// reset DB
$con->rollBack();