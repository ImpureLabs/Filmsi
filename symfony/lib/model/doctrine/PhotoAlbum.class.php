<?php

/**
 * PhotoAlbum
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    filmsi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class PhotoAlbum extends BasePhotoAlbum
{
	
	public function preDelete($event)
	{
		$photos = Doctrine_Core::getTable('Photo')->findByAlbumId($event->getInvoker()->getId());
		
		if ($photos->count() > 0) {
			$photos->delete();	
		}


		/* Delete from all the elemets that are tied to this album */
		ArticleTable::getInstance()->cleanPhotoAlbums($event->getInvoker()->getId());
		FilmTable::getInstance()->cleanPhotoAlbums($event->getInvoker()->getId());
		CinemaTable::getInstance()->cleanPhotoAlbums($event->getInvoker()->getId());
		FestivalEditionTable::getInstance()->cleanPhotoAlbums($event->getInvoker()->getId());
		PersonTable::getInstance()->cleanPhotoAlbums($event->getInvoker()->getId());
		StireTable::getInstance()->cleanPhotoAlbums($event->getInvoker()->getId());
	}

	public function getCover()
	{
	}

	/**
	 * Returns the number of photos in this album
	 */
	public function getPhotoCount()
	{
		return Doctrine_Core::getTable('Photo')->getCountByAlbum($this->getId());
	}

	public function getNonRedcarpetPhotos()
	{
		return PhotoTable::getInstance()->getNonRedcarpetByAlbum($this->getId());
	}

	public function getRedcarpetPhotos()
	{
		return PhotoTable::getInstance()->getRedcarpetByAlbum($this->getId());
	}
}