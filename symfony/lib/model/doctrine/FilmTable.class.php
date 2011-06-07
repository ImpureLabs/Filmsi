<?php

/**
 * FilmTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class FilmTable extends Doctrine_Table
{
	/**
	 * @return FilmTable
	 */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Film');
    }
    
    public function allow($libraryId)
    {
        $album = Doctrine_Core::getTable('Film')->findOneByLibraryId($libraryId);

        /* Update album state*/
        $album->setState(Library::STATE_ACTIVE);
        $album->save();
    }

    public function cloneObject($libraryId)
    {
        $film = Doctrine_Core::getTable('Film')->findOneByLibraryId($libraryId);

        /* Cloning the album */
        $newFilm = new Film();
        $newFilm->setImdb($film->getImdb());
        $newFilm->setNameRo($film->getNameRo() . ' - clone');
        $newFilm->setNameEn($film->getNameEn() . ' - clone');
        $newFilm->setYear($film->getYear());
        $newFilm->setRating($film->getRating());
        $newFilm->setMetaDescription($film->getMetaDescription());
        $newFilm->setMetaKeywords($film->getMetaKeywords());
        $newFilm->setDuration($film->getDuration());
        $newFilm->setIsTypeFilm($film->getIsTypeFilm());
        $newFilm->setIsTypeDigital($film->getIsTypeDigital());
        $newFilm->setIsType_3d($film->getIsType_3d());
        $newFilm->setDistribuitor($film->getDistribuitor());
        $newFilm->setDescriptionTeaser($film->getDescriptionTeaser());
        $newFilm->setDescriptionContent($film->getDescriptionContent());
        $newFilm->setPublishDate($film->getPublishDate());
        $newFilm->setState($film->getState());
        $newFilm->setUserId(sfContext::getInstance()->getUser()->getGuardUser()->getId());
        $newFilm->setPhotoAlbumId($film->getPhotoAlbumId());
        $newFilm->setVideoAlbumId($film->getVideoAlbumId());


        $pieces = explode('.', $film->getFilename());
        $extension = $pieces[1];
        $newFilename = md5(time() . rand(0, 999999)) . '.' . $extension;

        /* Copying the photo files */
        copy(sfConfig::get('app_film_path') . '/' . $film->getFilename(), sfConfig::get('app_film_path') . '/' . $newFilename);
        copy(sfConfig::get('app_film_path') . '/t-' . $film->getFilename(), sfConfig::get('app_film_path') . '/t-' . $newFilename);
        copy(sfConfig::get('app_film_path') . '/ts-' . $film->getFilename(), sfConfig::get('app_film_path') . '/ts-' . $newFilename);

        $newFilm->setFilename($newFilename);
        $newFilm->save();

        /* Duplicating the relations to the genres */
        $filmGenres = Doctrine_Core::getTable('FilmGenre')->findByFilmId($film->getId());
        foreach ($filmGenres as $filmGenre){
            $newFilmGenre = new FilmGenre();
            $newFilmGenre->setFilmId($newFilm->getId());
            $newFilmGenre->setGenreId($filmGenre->getGenreId());
            $newFilmGenre->save();
        }

        /* Duplicating the relations to the persons */
        $filmPersons = Doctrine_Core::getTable('FilmPerson')->findByFilmId($film->getId());
        foreach ($filmPersons as $filmPerson){
            $newPerson = new FilmPerson();
            $newPerson->setFilmId($newFilm->getId());
            $newPerson->setPersonId($filmPerson->getPersonId());
            $newPerson->setIsActor($filmPerson->getIsActor());
            $newPerson->setIsDirector($filmPerson->getIsDirector());
            $newPerson->setIsScriptwriter($filmPerson->getIsScriptwriter());
            $newPerson->setIsProducer($filmPerson->getIsProducer());
            $newPerson->save();
        }
    }

    public function getForApi($term)
    {
        $term = '(^| |-)' . $term ;

        $bruteFilms = Doctrine_Query::create()
            ->from('Film f')
            ->andWhere('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
            ->andWhere('f.name_ro REGEXP ? OR f.name_en REGEXP ?', array($term, $term))
            ->orderBy('f.name_ro ASC')
            ->limit(50)
            ->execute();

        $films = array();
        foreach ($bruteFilms as $key => $bruteFilm){
            $films[$key]['value'] = $bruteFilm->getId();
            $films[$key]['label'] = $bruteFilm->getNameRo() . '(' . $bruteFilm->getNameEn() . ')';
        }

        return $films;
    }

	public function getForApiImdb($term)
	{
		$term = '(^| |-)' . $term ;

		$bruteFilms = Doctrine_Query::create()
			->from('Film f')
			->andWhere('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
			->andWhere('f.name_ro REGEXP ? OR f.name_en REGEXP ?', array($term, $term))
			->orderBy('f.name_ro ASC')
			->limit(50)
			->execute();

		$films = array();
		foreach ($bruteFilms as $key => $bruteFilm){
			$films[$key]['value'] = $bruteFilm->getImdb();
			$films[$key]['label'] = $bruteFilm->getNameRo() . '(' . $bruteFilm->getNameEn() . ')';
		}

		return $films;
	}

	public function countByImdb($imdb)
	{
		$q = Doctrine_Query::create()
			->select('COUNT(f.id) counter')
			->from('Film f')
			->where('f.imdb = ?', $imdb)
			->fetchOne(array(), Doctrine_Core::HYDRATE_SCALAR);

		return $q['f_counter'];
	}

	public function getFilteredByStatus($filters, $limit = null)
	{
		$q = Doctrine_Query::create()
			->from('Film f')
			->orderBy('f.id DESC')
			->addWhere('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()');

		if (isset($limit)){
			$q->limit($limit);
		}

		if (isset($filters['in_production'])){
			$q->addWhere('f.status_in_production = 1');
		}

		if (isset($filters['in_cinema'])){
			$q->addWhere('f.status_cinema= 1');
		}

		if (isset($filters['in_dvd'])){
			$q->addWhere('f.status_dvd = 1');
		}

		if (isset($filters['in_bluray'])){
			$q->addWhere('f.status_bluray = 1');
		}

		if (isset($filters['in_online'])){
			$q->addWhere('f.status_online = 1');
		}

		if (isset($filters['in_tv'])){
			$q->addWhere('f.status_tv = 1');
		}

		if (isset($filters['imdb'])){
			$q->addWhere('f.imdb = ?', $filters['imdb']);
		}

		if (isset($filters['offset'])){
			$q->offset($filters['offset']);
		}

		return $q->execute();

	}

	public function countFilteredByStatus($filters)
	{
		$q = Doctrine_Query::create()
			->select('COUNT(f.id) counter')
			->from('Film f')
			->orderBy('f.id DESC')
			->addWhere('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()');

		if (isset($filters['in_production']) && $filters['in_production'] == '1'){
			$q->addWhere('f.status_in_production = 1');
		}

		if (isset($filters['in_cinema']) && $filters['in_cinema'] == '1'){
			$q->addWhere('f.status_cinema= 1');
		}

		if (isset($filters['in_dvd']) && $filters['in_dvd'] == '1'){
			$q->addWhere('f.status_dvd = 1');
		}

		if (isset($filters['in_bluray']) && $filters['in_bluray'] == '1'){
			$q->addWhere('f.status_bluray = 1');
		}

		if (isset($filters['in_online']) && $filters['in_online'] == '1'){
			$q->addWhere('f.status_online = 1');
		}

		if (isset($filters['in_tv']) && $filters['in_tv'] == '1'){
			$q->addWhere('f.status_tv = 1');
		}

		$q = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SCALAR);

		return $q['f_counter'];

    }

	public function getTrailersQuery($limit = null, $page = null)
	{
		$q = Doctrine_Query::create()
			->select('f.id, f.name_ro, f.name_en, f.url_key, v.id video_id, v.code video_code, v.name video_name')
			->from('Film f')
			->innerJoin('f.Videos v')
			->where('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
			->andWhere('v.position = 1')
			->orderBy('f.visit_count DESC');
		
		if (!empty ($limit)){
				$q->limit($limit);
		}

		if (!empty ($page)){
				$q->offset(($page - 1) * $limit );
		}

		return $q;
	}

	public function countTrailersQuery()
	{
		return Doctrine_Query::create()
			->select('COUNT(f.id) count')
			->from('Film f')
			->where('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
			->andWhere('f.video_album_id IS NOT NULL');
	}

	public function getTrailerByVideoId($videoId)
	{
		return Doctrine_Query::create()
			->select('f.id, f.name_ro, f.year, f.name_en, f.url_key, v.id video_id, v.code video_code, v.name video_name')
			->from('Film f')
			->innerJoin('f.Videos v')
			->where('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
			->andWhere('v.id = ?', $videoId)
			->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	public function getInCinemaNow($when = null, $limit = null, $page = null, $genres = array(), $ratings = array(), $locations = array(), $hydrator = Doctrine_Core::HYDRATE_ARRAY)
	{
		$q = Doctrine_Query::create()
			->select('f.id, f.name_ro, f.name_en, f.url_key, f.filename, f.status_cinema_year, f.status_cinema_month, f.status_cinema_day, f.status_dvd_year, f.status_dvd_month, f.status_dvd_day, f.status_bluray_year, f.status_bluray_month, f.status_bluray_day')
			->from('Film f')
			->innerJoin('f.Schedule s')
			->where('f.state = 1')
			->andWhereIn('s.day', $when)
			->orderBy('f.visit_count DESC');

		if (isset($limit)){
			$q->limit($limit);
		}

		if (isset($page)){
			$q->offset(($page - 1) * $limit );
		}

		if (count($locations) > 0){
			$q->innerJoin('s.Cinema c')
				->andWhereIn('c.location_id', $locations);
		}

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		return  $q->execute(array(), $hydrator);
		
	}

	public function getInCinemaNowCount($when = null, $genres = array(), $ratings = array(), $locations = array())
	{
		$q = Doctrine_Query::create()
			->select('count(DISTINCT f.id)')
			->from('Film f')
			->innerJoin('f.Schedule s')
			->where('f.state = 1')
			->whereIn('s.day', $when)
			->orderBy('f.visit_count DESC');

		if (count($locations) > 0){
			$q->innerJoin('s.Cinema c')
				->andWhereIn('c.location_id', $locations);
		}

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		$count =  $q->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		return $count['count'];
	}

	public function getInCinemaSoon($limit = null, $page = null, $genres = array(), $ratings = array(), $hydrator = Doctrine_Core::HYDRATE_ARRAY)
	{
		$q = Doctrine_Query::create()
			->select('f.id, f.name_ro, f.name_en, f.url_key, f.filename')
			->from('Film f')
			->where('f.state = 1 AND f.status_cinema = 1 AND f.status_cinema_day = 0')
			->orderBy('f.visit_count DESC');

		if (isset($limit)){
			$q->limit($limit);
		}

		if (isset($page)){
			$q->offset(($page - 1) * $limit );
		}

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		return  $q->execute(array(), $hydrator);

	}

	public function getInCinemaSoonCount($genres = array(), $ratings = array())
	{
		$q = Doctrine_Query::create()
			->select('count(DISTINCT f.id)')
			->from('Film f')
			->where('f.state = 1 AND f.status_cinema = 1 AND f.status_cinema_day = 0');

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		$count =  $q->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $count['count'];
	}

	public function getOnDvdAndBlurayNow($limit = null, $page = null, $genres = array(), $ratings = array(), $awards = array(), $onDvd = true, $onBluray = true, $hydrator = Doctrine_Core::HYDRATE_ARRAY)
	{
		$qStringForDvd = <<<text
	f.status_dvd = 1 AND 
	f.status_dvd_year IS NOT NULL AND f.status_dvd_year != 0 AND
	f.status_dvd_month IS NOT NULL AND f.status_dvd_month != 0 AND
	f.status_dvd_day IS NOT NULL AND f.status_dvd_day != 0 AND
	STR_TO_DATE(CONCAT(f.status_dvd_year, '-', f.status_dvd_month, '-', f.status_dvd_day),'%Y-%m-%d') <= NOW()
text;
		$qStringForBluray = <<<text
	f.status_bluray = 1 AND 
	f.status_bluray_year IS NOT NULL AND f.status_bluray_year != 0 AND
	f.status_bluray_month IS NOT NULL AND f.status_bluray_month != 0 AND
	f.status_bluray_day IS NOT NULL AND f.status_bluray_day != 0 AND
	STR_TO_DATE(CONCAT(f.status_bluray_year, '-', f.status_bluray_month, '-', f.status_bluray_day),'%Y-%m-%d') <= NOW()
text;
		if ($onDvd and $onBluray){
			$qString = '(' . $qStringForDvd . ') OR (' . $qStringForBluray . ')';
		} elseif ($onDvd) {
			$qString = $qStringForDvd;
		}  elseif ($onBluray) {
			$qString = $qStringForBluray;
		}

		$q = Doctrine_Query::create()
			->select('f.id, f.name_ro, f.name_en, f.url_key, f.filename')
			->from('Film f')
			->where('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
			->andWhere($qString)
			->orderBy('f.visit_count DESC');

		if (isset($limit)){
			$q->limit($limit);
		}

		if (isset($page)){
			$q->offset(($page - 1) * $limit );
		}

		if (count($awards) > 0){
			/* Get all the imdbcodes for all the film partiipants for the selectted festivals */
			$participantImdbCodes = FestivalTable::getInstance()->getWinnerFilmCodesByFestivals($awards);
			if (count($participantImdbCodes) > 0){
				$q->andWhereIn('f.imdb', $participantImdbCodes);
			}
		}

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		return  $q->execute(array(), $hydrator);

	}

	public function getOnDvdAndBlurayNowCount($genres = array(), $ratings = array(), $awards = array(), $onDvd = true, $onBluray = true)
	{
		$qStringForDvd = <<<text
	f.status_dvd = 1 AND 
	f.status_dvd_year IS NOT NULL AND f.status_dvd_year != 0 AND
	f.status_dvd_month IS NOT NULL AND f.status_dvd_month != 0 AND
	f.status_dvd_day IS NOT NULL AND f.status_dvd_day != 0 AND
	STR_TO_DATE(CONCAT(f.status_dvd_year, '-', f.status_dvd_month, '-', f.status_dvd_day),'%Y-%m-%d') <= NOW()
text;
		$qStringForBluray = <<<text
	f.status_bluray = 1 AND 
	f.status_bluray_year IS NOT NULL AND f.status_bluray_year != 0 AND
	f.status_bluray_month IS NOT NULL AND f.status_bluray_month != 0 AND
	f.status_bluray_day IS NOT NULL AND f.status_bluray_day != 0 AND
	STR_TO_DATE(CONCAT(f.status_bluray_year, '-', f.status_bluray_month, '-', f.status_bluray_day),'%Y-%m-%d') <= NOW()
text;
		if ($onDvd and $onBluray){
			$qString = '(' . $qStringForDvd . ') OR (' . $qStringForBluray . ')';
		} elseif ($onDvd) {
			$qString = $qStringForDvd;
		}  elseif ($onBluray) {
			$qString = $qStringForBluray;
		}

		$q = Doctrine_Query::create()
			->select('count(DISTINCT f.id)')
			->from('Film f')
			->where('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
			->andWhere($qString);

		if (count($awards) > 0){
			/* Get all the imdbcodes for all the film partiipants for the selectted festivals */
			$participantImdbCodes = FestivalTable::getInstance()->getWinnerFilmCodesByFestivals($awards);
			if (count($participantImdbCodes) > 0){
				$q->andWhereIn('f.imdb', $participantImdbCodes);
			}
		}

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		$count =  $q->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $count['count'];
	}

	public function getOnDvdAndBluraySoon($limit = null, $page = null, $genres = array(), $ratings = array(), $awards = array(), $onDvd = true, $onBluray = true, $hydrator = Doctrine_Core::HYDRATE_ARRAY)
	{
		$qStringForDvd = <<<text
(
	f.status_dvd = 1 AND
	f.status_dvd_year IS NOT NULL AND f.status_dvd_year != 0 AND
	f.status_dvd_month IS NOT NULL AND f.status_dvd_month != 0 AND
	f.status_dvd_day IS NOT NULL AND f.status_dvd_day != 0 AND
	STR_TO_DATE(CONCAT(f.status_dvd_year, '-', f.status_dvd_month, '-', f.status_dvd_day),'%Y-%m-%d') > NOW()
) OR (
	f.status_dvd = 1 AND 
	f.status_dvd_year IS NOT NULL AND f.status_dvd_year != 0 AND
	f.status_dvd_month IS NOT NULL AND f.status_dvd_month != 0 AND
	(f.status_dvd_day IS NULL OR f.status_dvd_day = 0) AND
	STR_TO_DATE(CONCAT(f.status_dvd_year, '-', f.status_dvd_month, '-01'),'%Y-%m') > NOW()
)
text;
		$qStringForBluray = <<<text
(
	f.status_bluray = 1 AND 
	f.status_bluray_year IS NOT NULL AND f.status_bluray_year != 0 AND
	f.status_bluray_month IS NOT NULL AND f.status_bluray_month != 0 AND
	f.status_bluray_day IS NOT NULL AND f.status_bluray_day != 0 AND
	STR_TO_DATE(CONCAT(f.status_bluray_year, '-', f.status_bluray_month, '-', f.status_bluray_day),'%Y-%m-%d') > NOW()
) OR (
	f.status_bluray = 1 AND 
	f.status_bluray_year IS NOT NULL AND f.status_bluray_year != 0 AND
	f.status_bluray_month IS NOT NULL AND f.status_bluray_month != 0 AND
	(f.status_bluray_day IS NULL OR f.status_bluray_day = 0) AND
	STR_TO_DATE(CONCAT(f.status_bluray_year, '-', f.status_bluray_month, '-01'),'%Y-%m') > NOW()
)
text;
		if ($onDvd and $onBluray){
			$qString = '(' . $qStringForDvd . ') OR (' . $qStringForBluray . ')';
		} elseif ($onDvd) {
			$qString = $qStringForDvd;
		}  elseif ($onBluray) {
			$qString = $qStringForBluray;
		}

		$q = Doctrine_Query::create()
			->select('f.id, f.name_ro, f.name_en, f.url_key, f.filename')
			->from('Film f')
			->where('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
			->andWhere($qString)
			->orderBy('f.visit_count DESC');

		if (isset($limit)){
			$q->limit($limit);
		}

		if (isset($page)){
			$q->offset(($page - 1) * $limit );
		}

		if (count($awards) > 0){
			/* Get all the imdbcodes for all the film partiipants for the selectted festivals */
			$participantImdbCodes = FestivalTable::getInstance()->getWinnerFilmCodesByFestivals($awards);
			if (count($participantImdbCodes) > 0){
				$q->andWhereIn('f.imdb', $participantImdbCodes);
			}
		}

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		return  $q->execute(array(), $hydrator);

	}

	public function getOnDvdAndBluraySoonCount($genres = array(), $ratings = array(), $awards = array(), $onDvd = true, $onBluray = true)
	{
		$qStringForDvd = <<<text
(
	f.status_dvd = 1 AND 
	f.status_dvd_year IS NOT NULL AND f.status_dvd_year != 0 AND
	f.status_dvd_month IS NOT NULL AND f.status_dvd_month != 0 AND
	f.status_dvd_day IS NOT NULL AND f.status_dvd_day != 0 AND
	STR_TO_DATE(CONCAT(f.status_dvd_year, '-', f.status_dvd_month, '-', f.status_dvd_day),'%Y-%m-%d') > NOW()
) OR (
	f.status_dvd = 1 AND f.status_dvd_year IS NOT NULL AND
	f.status_dvd_year IS NOT NULL AND f.status_dvd_year != 0 AND
	f.status_dvd_month IS NOT NULL AND f.status_dvd_month != 0 AND
	(f.status_dvd_day IS NULL OR f.status_dvd_day = 0) AND
	STR_TO_DATE(CONCAT(f.status_dvd_year, '-', f.status_dvd_month, '-01'),'%Y-%m') > NOW()
)
text;
		$qStringForBluray = <<<text
(
	f.status_bluray = 1 AND 
	f.status_bluray_year IS NOT NULL AND f.status_bluray_year != 0 AND
	f.status_bluray_month IS NOT NULL AND f.status_bluray_month != 0 AND
	f.status_bluray_day IS NOT NULL AND f.status_bluray_day != 0 AND
	STR_TO_DATE(CONCAT(f.status_bluray_year, '-', f.status_bluray_month, '-', f.status_bluray_day),'%Y-%m-%d') > NOW()
) OR (
	f.status_bluray = 1 AND 
	f.status_bluray_year IS NOT NULL AND f.status_bluray_year != 0 AND
	f.status_bluray_month IS NOT NULL AND f.status_bluray_month != 0 AND
	(f.status_bluray_day IS NULL OR f.status_bluray_day = 0) AND
	STR_TO_DATE(CONCAT(f.status_bluray_year, '-', f.status_bluray_month, '-01'),'%Y-%m') > NOW()
)
text;
		if ($onDvd and $onBluray){
			$qString = '(' . $qStringForDvd . ') OR (' . $qStringForBluray . ')';
		} elseif ($onDvd) {
			$qString = $qStringForDvd;
		}  elseif ($onBluray) {
			$qString = $qStringForBluray;
		}

		$q = Doctrine_Query::create()
			->select('count(DISTINCT f.id)')
			->from('Film f')
			->where('f.state = 1 AND f.publish_date IS NOT NULL AND f.publish_date <= NOW()')
			->andWhere($qString);

		if (count($awards) > 0){
			/* Get all the imdbcodes for all the film partiipants for the selectted festivals */
			$participantImdbCodes = FestivalTable::getInstance()->getWinnerFilmCodesByFestivals($awards);
			if (count($participantImdbCodes) > 0){
				$q->andWhereIn('f.imdb', $participantImdbCodes);
			}
		}

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		$count =  $q->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

		return $count['count'];
	}

	public function getOnTvSoon($limit = null, $page = null, $genres = array(), $ratings = array(), $hydrator = Doctrine_Core::HYDRATE_ARRAY)
	{
		$q = Doctrine_Query::create()
			->select('f.id, f.name_ro, f.name_en, f.url_key, f.filename')
			->from('Film f')
			->where('f.state = 1 AND f.status_tv = 1 AND f.status_tv_day = 0')
			->orderBy('f.visit_count DESC');

		if (isset($limit)){
			$q->limit($limit);
		}

		if (isset($page)){
			$q->offset(($page - 1) * $limit );
		}

		if (count($ratings) > 0){
			$q->andWhereIn('f.rating', $ratings);
		}

		if (count($genres) > 0){
			$q->innerJoin('f.FilmGenre g')
				->andWhereIn('g.genre_id', $genres);
		}

		return  $q->execute(array(), $hydrator);

	}

	public function cleanPhotoAlbums($photoAlbumId)
	{
		return Doctrine_Query::create()
			->update('Film a')
			->set('a.photo_album_id', 'null')
			->where('a.photo_album_id = ?', $photoAlbumId)
			->execute();
	}

	public function cleanVideoAlbums($videoAlbumId)
	{
		return Doctrine_Query::create()
			->update('Film a')
			->set('a.video_album_id', 'null')
			->where('a.video_album_id = ?', $videoAlbumId)
			->execute();
	}
	
	public function findOneByImdbForShopImport($imdbCode)
	{
		return Doctrine_Query::create()
			->select('f.id')
			->from('Film f')
			->whereIn('f.imdb', $imdbCode)
			->fetchOne();
	}
	
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
			->select('f.id, f.name_ro, f.name_en, f.filename, f.url_key')
			->from('Film f')
			->where('f.state = 1');
		
		foreach ($terms as $term){
			$q = $q->andWhere('f.name_ro LIKE ? or f.name_en LIKE ? ', array('%' . $term . '%', '%' . $term . '%'));
			
		}
		
		return $q->orderBy('f.visit_count desc')
			->limit($limit)
			->execute();
	}
	
	public function getBest($limit)
	{
		return Doctrine_Query::create()
			->from('Film f')
			->orderBy('f.visit_count DESC')
			->limit($limit)
			->execute();
	}
}