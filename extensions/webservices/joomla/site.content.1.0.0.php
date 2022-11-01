<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Api Helper class for overriding default methods
 *
 * @package     Redcore
 * @subpackage  Api Helper
 * @since       1.8
 */
class RApiHalHelperSiteContent
{
	/**
	 * Service for creating content.
	 *
	 * @param string $data content
	 *
	 * @return  boolean         True on success. False otherwise.
	 * @throws Exception
	 */
	public function save($data): string
	{
		if (version_compare(JVERSION, '3.0', 'lt')) {
			JTable::addIncludePath(JPATH_PLATFORM . 'joomla/database/table');
		}
		
		$data = (array) $data;
		
		if (isset($data['job_type']) && $data['job_type'] == 'create')
		{
			return $this->createContent($data);
		}
		
		if (isset($data['job_type']) && $data['job_type'] == 'edit')
		{
			return $this->editContent($data);
		}
	}
	
	/**
	 * @throws Exception
	 */
	private function createContent($data): string
	{
		$article = JTable::getInstance('content');
		$article->title            = $data['title'];
		$article->alias            = JFilterOutput::stringURLSafe($data['title']);
		$article->introtext        = '<p>'.$data['description'].'</p>';
		$article->created          = JFactory::getDate()->toSQL();;
		$article->created_by_alias = $data['user'];
		$article->state            = 1;
		$article->access           = 1;
		$article->metadata         = '{"page_title":"'.$data['title'].'","author":"'.$data['user'].'","robots":""}';
		$article->language         = '*';
		
		if (!$article->check()) {
			throw new Exception($article->getError());
			return false;
		}
		
		if ($article->store(TRUE)) {
			
			if (isset($data['image']) && $data['image'])
			{
				$articleId  = $article->get('id');
				$imgSrc     = $this->checkImages($data['image'], $articleId);
				$article->set('introtext', '<p>'.$data['description'].'</p>' . $imgSrc);
				$article->store();
			}
			
			return json_encode($article->getProperties());
			
		}else {
			throw new Exception($article->getError());
			return false;
		}
	}
	
	/**
	 * @throws Exception
	 */
	private function editContent($data): string
	{
		if (empty($data['article_id']))
		{
			return false;
		}
		
		$article = JTable::getInstance('content');
		$id      = $data['article_id'];
		$imgSrc  = '';
		$article->load($id);
		$article->set('title', $data['title']);
		
		if (isset($data['image']) && $data['image'])
		{
			$imgSrc = $this->checkImages($data['image'], $id);
		}

		$article->set('introtext', '<p>'.$data['description'].'</p>' . $imgSrc);
		
		if (!$article->store()) {
			throw new Exception($article->getError());
			return false;
		}
		
		return json_encode($article->getProperties());
	}
	
	
	private function checkImages($images, $articleId)
	{
		$imgSrc    = '';
		$extTypes  = ['gif','jpg','jpe','jpeg','png'];
		
		foreach ($images as $image)
		{
			$typeImg = strtolower(explode('/', $image['type'])[1]);
	
			if (!in_array($typeImg, $extTypes) || $image['error'] != 0)
			{
				continue;
			}

			$imgSrc .= $this->processImages($image, $articleId);
		}
		
		return $imgSrc;
	}
	
	private function processImages($image, $articleId)
	{
		$path      = JPATH_BASE.'/images/aesir/'.$articleId.'/';
		$pathFile  = '/images/aesir/'.$articleId.'/'.$image['name'];
	
		if (!is_dir($path)) {
			mkdir($path,0777,true);
		}

		if (move_uploaded_file($image['tmp_name'], $path.$image['name']))
		{
			return '<p><img src="'.$pathFile.'" /></p>';
		}
	}
}

