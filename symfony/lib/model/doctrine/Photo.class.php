<?php

/**
 * Photo
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    filmsi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Photo extends BasePhoto
{
	public function preInsert($event)
	{
		if (sfContext::getInstance()->getUser()->hasCredential(array('Fara_moderare', 'Moderator'), false)){
			$event->getInvoker()->setState(Library::STATE_ACTIVE);	
		} else {
			$event->getInvoker()->setState(Library::STATE_PENDING);
		}
	}
	
	public function preDelete($event)
	{
		// Delete the big file and the thumbnail
		$event->getInvoker()->deleteFiles();

		return parent::preDelete($event);
	}
	
	public function deleteFiles()
	{
		$s3 = new AmazonS3(sfConfig::get('app_aws_key'), sfConfig::get('app_aws_secret_key'));
		
		$response = $s3->delete_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_photos_aws_s3_folder') . '/' . $this->getFilename());
		$response = $s3->delete_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_photos_aws_s3_folder') . '/t-' . $this->getFilename());
		$response = $s3->delete_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_photos_aws_s3_folder') . '/ts-' . $this->getFilename());
		
		$this->_set('filename', '');
	}
	
	public function createFile($source, $type = null)
	{	
		if (!isset($type)){
			$imageData = getimagesize($source);
			$type = $imageData['mime'];
		}
		
		$sourceData = file_get_contents($source);
		
		/* Initiate the Amazon S3 object */
		$s3 = new AmazonS3(sfConfig::get('app_aws_key'), sfConfig::get('app_aws_secret_key'));
		
		/* Create and upload the the big file */
		$photo = new sfThumbnail(sfConfig::get('app_photos_sourceimage_width'), sfConfig::get('app_photos_sourceimage_height'), true, false, 100);
		$photo->loadData($sourceData, $type);
		
		$response = $s3->create_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_photos_aws_s3_folder') . '/' . $this->getFilename(), array(
			'body' => $photo->toString($type),
			'contentType' => $type,
			'meta' => array(
				'Expires'		=> 'Thu, 16 Apr 2020 05:00:00 GMT',
				'Cache-Control' => 'max-age=315360000'
			),
			'acl' => AmazonS3::ACL_PUBLIC
		));
		
		if (!$response->isOk()){
			echo '<pre>'; var_dump($response);
		}

		/* Create and upload the thumbnail */
		$thumb = new sfThumbnail(sfConfig::get('app_photos_thumbnail_width'), sfConfig::get('app_photos_thumbnail_height'), true, false, 100);
		$thumb->loadData($sourceData, $type);
		
		$response = $s3->create_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_photos_aws_s3_folder') . '/t-' . $this->getFilename(), array(
			'body' => $thumb->toString($type),
			'contentType' => $type,
			'meta' => array(
				'Expires'		=> 'Thu, 16 Apr 2020 05:00:00 GMT',
				'Cache-Control' => 'max-age=315360000'
			),
			'acl' => AmazonS3::ACL_PUBLIC
		));
				
		if (!$response->isOk()){
			echo '<pre>'; var_dump($response);
		}

		/* Create and upload the small thumbnail */
		$thumb = new sfThumbnail(sfConfig::get('app_photos_thumbnail_small_width'), sfConfig::get('app_photos_thumbnail_small_height'), true, false, 100);
		$thumb->loadData($sourceData, $type);
		
		$response = $s3->create_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_photos_aws_s3_folder') . '/ts-' . $this->getFilename(), array(
			'body' => $thumb->toString($type),
			'contentType' => $type,
			'meta' => array(
				'Expires'		=> 'Thu, 16 Apr 2020 05:00:00 GMT',
				'Cache-Control' => 'max-age=315360000'
			),
			'acl' => AmazonS3::ACL_PUBLIC
		));
		
		if (!$response->isOk()){
			echo '<pre>'; var_dump($response);
		}
	}

	public function getThumb()
	{
		return sfConfig::get('app_aws_s3_path') . sfConfig::get('app_aws_bucket') . '/' . sfConfig::get('app_photos_aws_s3_folder') .  '/t-' . $this->_get('filename');
	}

	public function getPhoto()
	{
		return sfConfig::get('app_aws_s3_path') . sfConfig::get('app_aws_bucket') . '/' . sfConfig::get('app_photos_aws_s3_folder') .  '/' . $this->_get('filename');
	}
	
	public function getUrlInParentGallery()
	{
		/* Check if the parent album belongs to any film */
		if ( false !== $film = FilmTable::getInstance()->findOneByPhotoAlbumId($this->getAlbumId())) {
			return sfContext::getInstance()->getRouting()->generate('film_photos', array('id' => $film->getId(), 'key' => $film->getUrlKey())) . '?pid=' . $this->getPosition() . '#scrolled';
		}
		
		/* Check if the parent album belongs to any film */
		if ( false !== $cinema = CinemaTable::getInstance()->findOneByPhotoAlbumId($this->getAlbumId())) {
			return sfContext::getInstance()->getRouting()->generate('cinema_photos', array('id' => $cinema->getId(), 'key' => $cinema->getUrlKey())) . '?pid=' . $this->getPosition() . '#scrolled';
		}
		
		/* Check if the parent album belongs to any film */
		if ( false !== $person = PersonTable::getInstance()->findOneByPhotoAlbumId($this->getAlbumId())) {
			return sfContext::getInstance()->getRouting()->generate('person_photos', array('id' => $person->getId(), 'key' => $person->getUrlKey())) . '?pid=' . $this->getPosition() . '#scrolled';
		}
		
		return false;
	}
}