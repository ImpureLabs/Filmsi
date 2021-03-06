<?php

/**
 * CinemaTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CinemaTable extends Doctrine_Table
{
	/**
	 *
	 * @return CinemaTable
	 */
	public static function getInstance()
	{
		return Doctrine_Core::getTable('Cinema');
	}

	public function allow($libraryId)
	{
		$album = Doctrine_Core::getTable('Cinema')->findOneByLibraryId($libraryId);
		
		/* Update album state*/
		$album->setState(Library::STATE_ACTIVE);
		$album->save();
	}

	public function cloneObject($libraryId)
	{
		$cinema = Doctrine_Core::getTable('Cinema')->findOneByLibraryId($libraryId);
		
		/* Cloning the object */
		$newCinema = new Cinema();
		$newCinema->setName($cinema->getName() . ' - clone');
		$newCinema->setLocationId($cinema->getLocationId());
		$newCinema->setAddress($cinema->getAddress());
		$newCinema->setPhone($cinema->getPhone());
		$newCinema->setWebsite($cinema->getWebsite());
		$newCinema->setLat($cinema->getLas());
		$newCinema->setLng($cinema->getLng());
		$newCinema->setSeats($cinema->getSeats());
		$newCinema->setSound($cinema->getSound());
		$newCinema->setTicketPrice($cinema->getTicketPrice());
		$newCinema->setIsTypeFilm($cinema->getIsTypeFilm());
		$newCinema->setIsTypeDigital($cinema->getIsTypeDigital());
		$newCinema->setIsType_3d($cinema->getIsType_3d());
		$newCinema->setDescriptionTeaser($cinema->getDescriptionTeaser());
		$newCinema->setDescriptionContent($cinema->getDescriptionContent());
		$newCinema->setMetaDescription($cinema->getMetaDescription());
		$newCinema->setMetaKeywords($cinema->getMetaKeywords());
		$newCinema->setPublishDate($cinema->getPublishDate());
		$newCinema->setState($cinema->getState());
		$newCinema->setUserId(sfContext::getInstance()->getUser()->getGuardUser()->getId());
		$newCinema->setPhotoAlbumId($cinema->getPhotoAlbumId());
		$newCinema->setVideoAlbumId($cinema->getVideoAlbumId());
		
		
		$pieces = explode('.', $cinema->getFilename());
		$extension = $pieces[1];
		$newFilename = md5(time() . rand(0, 999999)) . '.' . $extension;
			
		/* Copying the photo files */
		copy(sfConfig::get('app_cinema_path') . '/' . $cinema->getFilename(), sfConfig::get('app_cinema_path') . '/' . $newFilename);
		copy(sfConfig::get('app_cinema_path') . '/t-' . $cinema->getFilename(), sfConfig::get('app_cinema_path') . '/t-' . $newFilename);
		copy(sfConfig::get('app_cinema_path') . '/ts-' . $cinema->getFilename(), sfConfig::get('app_cinema_path') . '/ts-' . $newFilename);
		
		$newCinema->setFilename($newFilename);
		$newCinema->save();
		
		/* Duplicating the relations to the services */
		$cinemaServices = Doctrine_Core::getTable('CinemaService')->findByCinemaId($cinema->getId());
		foreach ($cinemaServices as $cinemaService){
			$newCinemaService = new CinemaService();
			$newCinemaService->setCinemaId($newCinema->getId());
			$newCinemaService->setServiceId($cinemaService->getServiceId());
			$newCinemaService->save();
		}
	}

	public function getForApi($term)
	{
		$term = '(^| |-)' . $term ;
		
		$bruteCinemas = Doctrine_Query::create()
			->from('Cinema c')
			->andWhere('c.state = 1')
			->andWhere('c.name REGEXP ?', $term)
			->orderBy('c.name ASC')
			->limit(50)
			->execute();

    $cinemas = array();					              
		foreach ($bruteCinemas as $key => $bruteCinema){
			$cinemas[$key]['value'] = $bruteCinema->getId();
			$cinemas[$key]['label'] = $bruteCinema->getName();
		}
		
		return $cinemas;
	}

	public function countByRegion()
	{
		$cinemas = Doctrine_Query::create()
			->select('count(c.id), c.id, l.region')
			->from('Location l')
			->leftJoin('l.Cinema c')
			->groupBy('l.region')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$resultCinemas = array();
		foreach ($cinemas as $cinema){
			$resultCinemas[strtolower(str_replace(array('-', ' '), '', $cinema['region']))] = $cinema['Cinema']['count'];
		}

		return $resultCinemas;
	}

	public function getByRegionKey($region)
	{
		return Doctrine_Query::create()
			->from('Cinema c')
			->innerJoin('c.Location l')
			->where('LOWER(REPLACE(REPLACE(l.region, " ", ""), "-", "")) = ?', $region)
			->execute();
	}

	public function getLocations()
	{
		$q = Doctrine_Query::create()
			->select('c.id, l.city')
			->from('Cinema c')
			->innerJoin('c.Location l')
			->orderBy('l.city ASC')
			->groupBy('c.location_id')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$locations = array();
		foreach($q as $location){
			$locations[$location['Location']['id']] = $location['Location']['city'];
		}

		return $locations;
	}

	public function getGroupedByLocations()
	{
		$q = Doctrine_Query::create()
			->select('c.id, c.name, c.url_key, l.id, l.city')
			->from('Cinema c')
			->innerJoin('c.Location l')
			->orderBy('l.city, c.name')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$results = array();
		foreach ($q as $item){
			if (!isset($results[$item['Location']['id']])){
				$results[$item['Location']['id']]['location_name'] = $item['Location']['city'];
			} 

			$results[$item['Location']['id']]['cinemas'][] = array(
				'id' => $item['id'],
				'name' => $item['name'],
				'url_key' => $item['url_key']
			);
		}

		return $results;
	}

	public function getMultipleByIds($ids)
	{
		return Doctrine_Query::create()
			->select('c.id, c.name, c.url_key')
			->from('Cinema c')
			->whereIn('c.id', $ids)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	public function cleanPhotoAlbums($photoAlbumId)
	{
		return Doctrine_Query::create()
			->update('Cinema a')
			->set('a.photo_album_id', 'null')
			->where('a.photo_album_id = ?', $photoAlbumId)
			->execute();
	}
	
//	public function getForSearch($term, $limit)
//	{
//		$term = preg_replace('/[^a-zA-Z]/i', ' ', $term);
//		$term = preg_replace('/\s+/i', ' ', $term);
//		$terms = explode(' ', $term);
//		unset($term);
//		
//		$qArray = array();
//		$qString = '';
//		foreach ($terms as $term){
//			$qString = 'c.name REGEXP ? or c.meta_keywords REGEXP ? ';
//			$qArray[] = '(^| |-)' . $term;
//			$qArray[] = '(^| |-)' . $term;
//			
//		}
//		
//		return Doctrine_Query::create()
//			->from('Cinema c')
//			->where('c.state = 1')
//			->andWhere($qString, $qArray)
//			->orderBy('c.visit_count desc')
//			->limit($limit)
//			->execute();
//	}
	
	public function getForSearch($term, $limit)
	{
		$term = preg_replace('/[^a-zA-Z]/i', ' ', $term);
		$term = preg_replace('/\s+/i', ' ', $term);
		$terms = explode(' ', $term);
		
		foreach ($terms as $key => $term){
			if (strlen($terms[$key]) <= 2 ){
				unset ($terms[$key]);
			}
		}
		
		unset($term);
		
		$qArray = array();
		$qString = '';
		
		
		$q = Doctrine_Query::create()
			->select('c.id, c.name, c.filename, c.url_key')
			->from('Cinema c')
			->where('c.state = 1');
		
		foreach ($terms as $term){
			$q = $q->andWhere('c.name LIKE ? or c.meta_keywords LIKE ? ', array('%' . $term . '%', '%' . $term . '%'));
			
		}
		
		return $q->orderBy('c.visit_count desc')
			->limit($limit)
			->execute();
	}
	
	public function getDetailsAndSchedulesByFilmAndLocation($filmId, $locationId)
	{
		if (date('N', time()) == '1') {
			$firstDay = date('Y-m-d', time());
			$lastDay  = date('Y-m-d', strtotime("next Sunday"));
		} elseif (date('N', time()) == '7') {
			$firstDay  = date('Y-m-d', strtotime("last Monday"));
			$lastDay = date('Y-m-d', time());
		} else {
			$firstDay  = date('Y-m-d', strtotime("last Monday"));
			$lastDay  = date('Y-m-d', strtotime("next Sunday"));
		}
		
		return Doctrine_Query::create()
			->select('c.id, c.name, c.url_key, c.reservation_url, s.*')
			->from('Cinema c')
			->innerJoin('c.Schedule s')
			->where('c.location_id = ?', $locationId)
			->andWhere('s.film_id = ?', $filmId)
			->andWhere('s.day >= ?', $firstDay)
			->andWhere('s.day <= ?', $lastDay)
			->orderBy('s.day ASC')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
}