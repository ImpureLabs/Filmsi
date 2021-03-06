<?php

/**
 * Stire
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    filmsi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Stire extends BaseStire
{
	public function  getUrlKey() {
		if ($this->_get('url_key') == '') {
			return Doctrine_Inflector::urlize($this->getName());
		} else {
			return $this->_get('url_key');
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
		
		$response = $s3->delete_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_stire_aws_s3_folder') . '/' . $this->getFilename());
		$response = $s3->delete_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_stire_aws_s3_folder') . '/t-' . $this->getFilename());
		$response = $s3->delete_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_stire_aws_s3_folder') . '/ts-' . $this->getFilename());
		
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
		$photo = new sfThumbnail(sfConfig::get('app_stire_sourceimage_width'), sfConfig::get('app_stire_sourceimage_height'), true, false, 100);
		$photo->loadData($sourceData, $type);
		
		$response = $s3->create_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_stire_aws_s3_folder') . '/' . $this->getFilename(), array(
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
		$thumb = new sfThumbnail(sfConfig::get('app_stire_thumbnail_width'), sfConfig::get('app_stire_thumbnail_height'), true, false, 100);
		$thumb->loadData($sourceData, $type);
		
		$response = $s3->create_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_stire_aws_s3_folder') . '/t-' . $this->getFilename(), array(
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
		$thumb = new sfThumbnail(sfConfig::get('app_stire_thumbnail_small_width'), sfConfig::get('app_stire_thumbnail_small_height'), true, false, 100);
		$thumb->loadData($sourceData, $type);
		
		$response = $s3->create_object(sfConfig::get('app_aws_bucket'), sfConfig::get('app_stire_aws_s3_folder') . '/ts-' . $this->getFilename(), array(
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

        public function getRelated($count)
	{
            /* Get the id of the stires related to any of the films */
            $filmStires = Doctrine_Core::getTable('FilmStire')->getRelatedStires($this->getId());
            /* Get the id of the Stires related to any of the persons */
            $personStires = Doctrine_Core::getTable('PersonStire')->getRelatedStires($this->getId());
            /* Get the id of the stires related to any of the cinemas */
            $cinemaStires = Doctrine_Core::getTable('CinemaStire')->getRelatedStires($this->getId());
            /* Get the id of the stires related to any of the festival editions */
            $festivalEditionStires = Doctrine_Core::getTable('FestivalEditionStire')->getRelatedStires($this->getId());

            $stireIds = array_merge($filmStires, $personStires, $cinemaStires, $festivalEditionStires);

            return Doctrine_Core::getTable('Stire')->findLatestByIds($count, $stireIds);
	}

	public function getCountComments()
	{
		return Doctrine_Core::getTable('Comment')->getCountByEntity('Stire', $this->getLibraryId());
	}

	public function getFilenameIsTall()
	{
            $size = getimagesize(sfConfig::get('app_stire_path') . '/' . $this->getFilename());

            if ($size[1] > $size[0]){
                    return true;
            } else {
                    return false;
            }
	}
}