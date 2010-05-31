<?php

/**
 *
 * @author rick
 */
interface sfAssetsFolderProviderInterface {
	public static function clearInstancePool();
	public static function retrieveByPk($id);
	public static function doDeleteAll();
	public static function createRoot($node);

	/**
	 * Recursively creates parent folders
	 *
	 * @param string $path
	 * @return sfAssetFolder
	 */
	public static function createFromPath($path);

	/**
	 * Retrieves folder by relative path
	 *
	 * @param string $path
	 * @param string $separator
	 * @return sfAssetFolder
	 */
	public static function retrieveByPath($path, $separator);

	/**
	 * Gives an options array with all folders
	 *
	 * @param bool $includeRoot
	 * @return array options array
	 */
	public static function getAllPaths($includeRoot);

	/**
	 * @param  string $folder
	 * @return array
	 */
	public static function getAllNonDescendantsPaths($folder);

//	/**
//	 * @param  string   $folder
//	 * @return Criteria
//	 */
//	public static function getAllNonDescendantsPathsCriteria($folder);
//
//	/**
//	 * get a criteria for all folders except one
//	 * @param  sfAssetFolder $folder folder to exclude
//	 * @return Criteria
//	 */
//	public static function getAllPathsButOneCriteria($folder);

	/**
	 * sort dirs by name
	 * @param  array $dirs
	 * @return array
	 */
	public static function sortByName(array $dirs);

	/**
	 * sort dirs by date
	 * @param  array $dirs
	 * @return array
	 */
	public static function sortByDate(array $dirs);

	/**
	 * Sanitize path
	 *
	 * @param string $path
	 * @return string
	 */
	public static function cleanPath($path);

	/**
	 * Calculate total size of files
	 * @param  array   $files
	 * @return integer
	 */
	public static function countFilesSize($files);
}