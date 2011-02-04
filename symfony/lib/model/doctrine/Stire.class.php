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
	public function preDelete($event)
	{
		// Delete the big file and the thumbnail
		unlink(sfConfig::get('app_stire_path') . '/' . $event->getInvoker()->getFilename());
		unlink(sfConfig::get('app_stire_path') . '/t-' . $event->getInvoker()->getFilename());
		unlink(sfConfig::get('app_stire_path') . '/ts-' . $event->getInvoker()->getFilename());

		return parent::preDelete($event);
	}
	
	public function createFile()
	{
		$sourceFile = sfConfig::get('app_stire_path') . '/' . $this->getFilename();
		
		if (!file_exists($sourceFile)){
			throw new sfException('Source file not available: ' . $sourceFile);
		}

		/* Create the big file */
		$photo = new sfThumbnail(sfConfig::get('app_stire_sourceimage_width'), sfConfig::get('app_stire_sourceimage_height'), true, false, 100);
		$photo->loadFile($sourceFile);
		$photo->save(sfConfig::get('app_stire_path') . '/' . $this->getFilename());

		/* Create the thumbnail */
		$thumb = new sfThumbnail(sfConfig::get('app_stire_thumbnail_width'), sfConfig::get('app_stire_thumbnail_height'), true, false, 100);
		$thumb->loadFile($sourceFile);
		$thumb->save(sfConfig::get('app_stire_path') . '/t-' . $this->getFilename());

		/* Create the small thumbnail */
		$thumb = new sfThumbnail(sfConfig::get('app_stire_thumbnail_small_width'), sfConfig::get('app_stire_thumbnail_small_height'), true, false, 100);
		$thumb->loadFile($sourceFile);
		$thumb->save(sfConfig::get('app_stire_path') . '/ts-' . $this->getFilename());
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