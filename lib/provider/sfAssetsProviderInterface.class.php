<?php
interface sfAssetsProviderInterface {
	public static function clearInstancePool();

	public static function exists($folderId, $filename);

	public static function retrieveFromUrl($url);

	public static function getPager(array $params, $sort, $page, $size);

	public static function doDeleteAll();

	public static function retrieveByPk($id);
}