<?php

/**
 * Cinema
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    cinemasi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Cinema extends BaseCinema
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
		unlink(sfConfig::get('app_cinema_path') . '/' . $event->getInvoker()->getFilename());
		unlink(sfConfig::get('app_cinema_path') . '/t-' . $event->getInvoker()->getFilename());
		unlink(sfConfig::get('app_cinema_path') . '/ts-' . $event->getInvoker()->getFilename());

		return parent::preDelete($event);
	}
	
	public function createFile()
	{
		$sourceFile = sfConfig::get('app_cinema_path') . '/' . $this->getFilename();
		
		if (!file_exists($sourceFile)){
			throw new sfException('Source file not available: ' . $sourceFile);
		}

		/* Create the big file */
		$photo = new sfThumbnail(sfConfig::get('app_cinema_sourceimage_width'), sfConfig::get('app_cinema_sourceimage_height'), true, false, 100);
		$photo->loadFile($sourceFile);
		$photo->save(sfConfig::get('app_cinema_path') . '/' . $this->getFilename());

		/* Create the thumbnail */
		$thumb = new sfThumbnail(sfConfig::get('app_cinema_thumbnail_width'), sfConfig::get('app_cinema_thumbnail_height'), true, false, 100);
		$thumb->loadFile($sourceFile);
		$thumb->save(sfConfig::get('app_cinema_path') . '/t-' . $this->getFilename());

		/* Create the small thumbnail */
		$thumb = new sfThumbnail(sfConfig::get('app_cinema_thumbnail_small_width'), sfConfig::get('app_cinema_thumbnail_small_height'), true, false, 100);
		$thumb->loadFile($sourceFile);
		$thumb->save(sfConfig::get('app_cinema_path') . '/ts-' . $this->getFilename());
	}

	public function getRelatedStires($limit = null, $page = null, $returnArray = true)
    {
		return StireTable::getInstance()->getRelatedByCinema($this->getId(), $limit, $page, $returnArray);
    }

	public function getRelatedStiresCount()
    {
		return StireTable::getInstance()->getRelatedByCinemaCount($this->getId());
    }

	public function getCheckIfVoted()
	{

	}

	public function getVoteScore()
	{
		$q = Doctrine_Query::create()
			->select('AVG(v.grade) score')
			->from('CinemaVote v')
			->where('v.cinema_id = ?', $this->getId())
			->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $q['score'];

	}

	public function getVoteCount()
	{
		$q = Doctrine_Query::create()
			->select('COUNT(v.id) count')
			->from('CinemaVote v')
			->where('v.cinema_id = ?', $this->getId())
			->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $q['count'];
	}

	public function checkIfIpVotedToday($ip)
	{
		$q = Doctrine_Query::create()
			->select('COUNT(v.id) count')
			->from('CinemaVote v')
			->where('v.cinema_id = ?', $this->getId())
			->andWhere('v.ip = ?', $ip)
			->andWhere('DATE(v.created_at) = DATE(NOW())')
			->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

		return (int)$q['count'] === 0 ? false : true;
	}

}