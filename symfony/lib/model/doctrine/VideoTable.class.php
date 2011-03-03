<?php

/**
 * VideoTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class VideoTable extends Doctrine_Table
{
	public static function getInstance()
	{
		return Doctrine_Core::getTable('Video');
	}

	public function getListForView($albumId)
	{
		return Doctrine_Query::create()
			->from('Video p')
			->where('p.album_id = ?', $albumId)
			->orderBy('p.position ASC')
			->execute();
	}

	public function getFirst($albumId)
	{
		return Doctrine_Query::create()
			->from('Video p')
			->where('p.album_id = ?', $albumId)
			->andWhere('p.position = 1')
			->fetchOne();
	}

	public function getNeighbours($albumId, $position)
	{
		$videos = Doctrine_Query::create()
			->from('Video p')
			->select('p.id, p.position')
			->where('p.album_id = ?', $albumId)
			->whereIn('p.position', array($position - 1, $position + 1))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
		$neighbours = array();
		 
		foreach($videos as $video){
			if ($video['position'] == $position - 1){
				$neighbours['previous'] = $video;
			}

			if ($video['position'] == $position + 1){
				$neighbours['next'] = $video;
			}
		}
		 
		return $neighbours;
	}
	
	public function getCountByAlbum($albumId)
	{
		$q = Doctrine_Query::create()
			->select('COUNT(DISTINCT p.id) counter')
			->from('Video p')		
			->where('p.album_id = ?', $albumId)
			->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
			
		return $q['counter'];
	}

	public function getLatestTrailers($limit = null)
	{
		$q = Doctrine_Query::create()
			->select('v.*, f.id, a.id, f.url_key, f.name_ro, f.name_en')
			->from('Video v')
			->innerJoin('v.Album a')
			->innerJoin('a.Film f')
			->orderBy('a.publish_date DESC')
			->where('v.state = 1 AND a.publish_date IS NOT NULL AND a.publish_date <= NOW()');

		if (isset($limit)){
			$q = $q->limit($limit);
		}

		return $q->execute();

	}
}