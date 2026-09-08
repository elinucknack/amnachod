<?php
/**
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

use Slideshowck\CKPath;
use Slideshowck\CKFolder;
use Slideshowck\CKFile;
use Slideshowck\CKText;
use Slideshowck\CKFof;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class CKBrowse {

	static $isRestrictedUser = false;

	public static function getFileTypes($type) {
		$input = Slideshowck\CKFof::getApplication()->input;
		$type = $input->get('type', $type, 'string');

		switch ($type) {
			case 'video' :
				$filetypes = array('.mp4', '.ogv', '.webm', '.MP4', '.OGV', '.WEBM');
				break;
			case 'audio' :
				$filetypes = array('.mp3', '.ogg', '.MP3', '.OGG');
				break;
			case 'image' :
			default :
				$filetypes = array('.jpg', '.jpeg', '.png', '.gif', '.tiff', '.JPG', '.JPEG', '.PNG', '.GIF', '.TIFF', '.ico', '.webp', '.WEBP');
				break;
		}

		return $filetypes;
	}

	/*
	 * Get a list of folders and files 
	 */
	public static function getItemsList($type = 'image') {
		$input = Slideshowck\CKFof::getApplication()->input;

		$type = $input->get('type', $type, 'string');

		switch ($type) {
			case 'video' :
				$filetypes = array('.mp4', '.ogv', '.webm', '.MP4', '.OGV', '.WEBM');
				break;
			case 'audio' :
				$filetypes = array('.mp3', '.ogg', '.MP3', '.OGG');
				break;
			case 'image' :
			default :
				$filetypes = array('.jpg', '.jpeg', '.png', '.gif', '.tiff', '.JPG', '.JPEG', '.PNG', '.GIF', '.TIFF', '.ico', '.webp', '.WEBP');
				break;
		}

		$folder = $input->get('folder', '', 'string') ? '/' . trim($input->get('folder', '', 'string'), '/') : '/' . trim(\Joomla\CMS\Component\ComponentHelper::getParams('com_slideshowck')->get('imagespath', 'images/slideshowck'), '/');

		// makes replacement if specific user management is set
		if (stristr($folder, '$userid')) {
			self::$isRestrictedUser = true;
			$user = \Joomla\CMS\Factory::getUser();
			$folder = str_replace('$userid', 'user_' . $user->id, $folder);
			if (! file_exists(JPATH_SITE . '/' . $folder)) {
				mkdir(JPATH_SITE . '/' . $folder);
			}
		}

		// no folder filtering 
		if (\Joomla\CMS\Component\ComponentHelper::getParams('com_slideshowck')->get('imagespathexclusive', '0') == '0') {
		$folder = $input->get('folder', 'images', 'string');
		}

		$tree = new stdClass();

		// list the files in the root folder
		$fName = self::createFolderObj(JPATH_SITE . '/' . $folder, $tree, 1);
		$tree->$fName->files = self::getImagesInFolder(JPATH_SITE . '/' . $folder, implode('|', $filetypes));

		// look for all folder and files
		self::getSubfolder(JPATH_SITE . '/' . $folder, $tree, implode('|', $filetypes), 2);
		$tree = self::prepareList($tree);

		return $tree;
	}

	/* 
	 * List the subfolders and files according to the filter
	 */
	private static function getSubfolder($folder, &$tree, $filter, $level) {
		$folders = Slideshowck\CKFolder::folders($folder, '.', $recurse = false, $fullpath = true);
		natcasesort($folders);

		if (! count($folders)) return;

		foreach ($folders as $f) {
			$fName = self::createFolderObj($f, $tree, $level);

			// list all authorized files from the folder
			// self::getImagesInFolder($f, $tree, $fName, $filter, $level);

			// recursive loop
			self::getSubfolder($f, $tree, $filter, $level+1);
		}
		return;
	}

	private static function createFolderObj($f, &$tree, $level) {
		$fName = CKFile::makeSafe(str_replace(JPATH_SITE, '', $f));
		$tree->$fName = new stdClass();
		$name = basename($f);
		$tree->$fName->name = ($level == 1 && self::$isRestrictedUser == true) ? 'images' : $name;
		$tree->$fName->path = $f;
		$tree->$fName->level = $level;
		$tree->$fName->files = false;

		return $fName;
	}

	/* 
	 * List the subfolders and files according to the filter
	 */
	public static function getImagesInFolder($f, $filter = '.') {

			// list all authorized files from the folder
			$files = Slideshowck\CKFolder::files($f, $filter, $recurse = false, $fullpath = false);
			if (is_array($files)) natcasesort($files);

			return $files;
		}

	/* 
	 * Set level diff and check for depth
	 */
	private static function prepareList($items) {
		if (! $items) return $items;

		$lastitem = 0;
		foreach ($items as $i => $item)
		{
			self::prepareItem($item);

			if ($item->level != 0) {
				if (isset($items->$lastitem))
				{
					$items->$lastitem->deeper     = ($item->level > $items->$lastitem->level);
					$items->$lastitem->shallower  = ($item->level < $items->$lastitem->level);
					$items->$lastitem->level_diff = ($items->$lastitem->level - $item->level);
				}
			}
			$lastitem = $i;

			
		}

		// for the last item
		if (isset($items->$lastitem))
		{
			$items->$lastitem->deeper     = (1 > $items->$lastitem->level);
			$items->$lastitem->shallower  = (1 < $items->$lastitem->level);
			$items->$lastitem->level_diff = ($item->level - 1);
		}

		return $items;
	}

	/* 
	 * Set the default values
	 */
	private static function prepareItem(&$item) {
		$item->deeper     = false;
		$item->shallower  = false;
		$item->level_diff = 0;
		$item->basepath = str_replace(JPATH_SITE, '', $item->path);
		$item->basepath = str_replace('\\', '/', $item->basepath);
		$item->basepath = trim($item->basepath, '/');
	}

	/**
	 * Get the file and store it on the server
	 * 
	 * @return mixed, the method return
	 */
	public static function ajaxAddPicture() {
		// check the token for security
		if (! \Joomla\CMS\Session\Session::checkToken('get')) {
			$msg = CKText::_('JINVALID_TOKEN');
			echo '{"error" : "' . $msg . '"}';
			exit;
		}

		$app = CKFof::getApplication();
		$input = $app->input;
		$file = $input->files->get('file', '', 'array');
		// $imgpath = '/' . trim($input->get('path', '', 'string'), '/') . '/';
		$imgpath = $input->get('path', '', 'string') ? '/' . trim($input->get('path', '', 'string'), '/') . '/' : '/' . trim(\Joomla\CMS\Component\ComponentHelper::getParams('com_slideshowck')->get('imagespath', 'images/slideshowck'), '/') . '/';

		// makes replacement if specific user management is set
		$user = \Joomla\CMS\Factory::getUser();
		$imgpath = str_replace('$userid', 'user_' . $user->id, $imgpath);

		if (!is_array($file)) {
			$msg = CKText::_('CK_NO_FILE_RECEIVED');
			echo '{"error" : "' . $msg . '"}';
			exit;
		}

		// If there are no files to upload - then bail
		if (empty($file))
		{
			$msg = CKText::_('CK_NO_FILE_RECEIVED');
			echo '{"error" : "' . $msg . '"}';
			exit;
		}

		// Instantiate the media helper
		// To be used if we want to check the files according to the media manager settings
		$mediaHelper = new \Joomla\CMS\Helper\MediaHelper;
		$allowedByJoomlaMediaHelper = $mediaHelper->canUpload($file);

		$filename = CKFile::makeSafe($file['name']);
		$authorizedFileTypes = self::getFileTypes('all');
		$type = CKFile::getExt($filename);
		// Security check
		if (! in_array('.' . $type, $authorizedFileTypes) || ! $allowedByJoomlaMediaHelper) {
			$msg = CKText::_('CK_FILE_NOT_AUTHORIZED');
			echo '{"error" : "' . $msg . '"}';
			exit;
		}

		//Set up the source and destination of the file
		$src = $file['tmp_name'];

		// check if the file exists
		if (!$src || !is_file($src)) {
			$msg = CKText::_('CK_FILE_NOT_EXISTS');
			echo '{"error" : "' . $msg . '"}';
			exit;
		}

		// check if folder exists, if not then create it
		if (!is_dir(JPATH_SITE . $imgpath)) {
			if (!mkdir(JPATH_SITE . $imgpath)) {
				$msg = CKText::_('CK_UNABLE_TO_CREATE_FOLDER') . ' : ' . $imgpath;
				echo '{"error" : "' . $msg . '"}';
				exit;
			}
		}

		$file['filepath'] = JPATH_SITE . $imgpath;

		// Trigger the onContentBeforeSave event.
        $object            = new \stdClass();
        // $object->adapter   = $adapter;
        $object->name      = $filename;
        $object->path      = $imgpath;
        $object->data      = file_get_contents($src);
        $object->extension = strtolower(CKFile::getExt($filename));

		CKFof::importPlugin('media-action');
		$result = CKFof::triggerEvent('onContentBeforeSave', array('com_media.file', &$object, true));

		if (in_array(false, $result, true))
		{
			// There are some errors in the plugins
			throw new Exception(CKText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object->getErrors()), implode('<br />', $errors)), 100);

			return false;
		}

		// write the file
		if (! CKFile::copy($src, JPATH_SITE . $imgpath . $filename)) {
			$msg = CKText::_('CK_UNABLE_WRITE_FILE');
			echo '{"error" : "' . $msg . '"}';
			exit;
		}

		// Trigger the onContentAfterSave event.
		CKFof::triggerEvent('onContentAfterSave', array('com_media.file', &$object, true));
//		$this->setMessage(CKText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));

		echo '{"img" : "' . $imgpath . $filename . '", "filename" : "' . $filename . '"}';
		exit;
	}

	public static function createFolder($path, $folder) {
		$path = CKPath::clean(JPATH_SITE . '/' . $path . '/' . $folder);

		if (!is_dir($path) && !is_file($path))
			{
				if (CKFolder::create($path))
				{
					$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
					CKFile::write($path . '/index.html', $data);
				} else {
					return false;
				}
		}
		return true;
	}
}
