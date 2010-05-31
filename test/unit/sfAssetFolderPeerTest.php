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
  $folderProvider->createRoot($f);
  $f->save();
  
  // run the test
  $t = new lime_test(13, new lime_output_color());
  $t->diag('sfAssetFolderPeer');
  
  $sfAssetFolder = $folderProvider->retrieveByPath(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media'));
  $t->ok($sfAssetFolder->isRoot(), 'retrieveByPath() retrieves root from app_sfAssetsLibrary_upload_dir string');

  $sfAssetFolder = $folderProvider->retrieveByPath();
  $t->ok($sfAssetFolder->isRoot(), 'retrieveByPath() retrieves root from empty string');
  
  $sfAssetFolder = $folderProvider->createFromPath(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media').'/simple');
  $t->isa_ok($sfAssetFolder, 'sfAssetFolder', 'createFromPath() creates a sfAssetFolder from simple string');
  $t->isa_ok($sfAssetFolder->retrieveParent(), 'sfAssetFolder', 'createFromPath() from simple string has a parent');
  $t->ok($sfAssetFolder->retrieveParent()->isRoot(), 'createFromPath() creates a root child from simple string');

  $sfAssetFolder2 = $folderProvider->createFromPath(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media').'/simple/subfolder');
  $t->isa_ok($sfAssetFolder2, 'sfAssetFolder', 'createFromPath() creates a sfAssetFolder from simple string');
  $t->is($sfAssetFolder2->retrieveParent()->getId(), $sfAssetFolder->getId(), 'createFromPath() from simple string parent is correct');

  $sfAssetFolder = $folderProvider->createFromPath(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media').'/second/subfolder');
  $t->ok($sfAssetFolder instanceof sfAssetFolder, 'createFromPath() creates a sfAssetFolder from simple string');
  $t->ok($sfAssetFolder->retrieveParent() instanceof sfAssetFolder, 'createFromPath() from composed string has a parent');
  $t->ok($sfAssetFolder->retrieveParent()->retrieveParent()->isRoot(), 'createFromPath() creates a root child from composed string');
  
  $sfAssetFolder = $folderProvider->createFromPath('third/subfolder');
  $t->ok($sfAssetFolder instanceof sfAssetFolder, 'createFromPath() creates a sfAssetFolder from simple string');
  $t->ok($sfAssetFolder->retrieveParent() instanceof sfAssetFolder, 'createFromPath() from composed string has a parent');
  $t->ok($sfAssetFolder->retrieveParent()->retrieveParent()->isRoot(), 'createFromPath() creates a root child from composed string');
}
catch (Exception $e)
{
  // do nothing
}

// reset DB
$con->rollBack();

