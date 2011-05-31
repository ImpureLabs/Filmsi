<?php

/**
 * CinemaScheduleTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CinemaScheduleTable extends Doctrine_Table
{
	/**
	 *
	 * @return CinemaScheduleTable
	 */
	public static function getInstance()
	{
		return Doctrine_Core::getTable('CinemaSchedule');
	}

	public function getDetailedByCinema($cinemaId)
	{
		$scheduleBrutes = Doctrine_Query::create()
			->from('CinemaSchedule cs')
			->leftJoin('cs.Film f')
			->innerJoin('cs.Cinema c')
			->where('cs.cinema_id = ?', $cinemaId)
			->orderBy('cs.day ASC')
			->execute();
			
		$schedules = array();
		foreach ($scheduleBrutes as $scheduleBrute){
			$schedules[$scheduleBrute->getDay()][] = array(
				'id' => $scheduleBrute->getId(),
				'film' => $scheduleBrute->getFilmNotInDb() == '1' ? $scheduleBrute->getFilmName() : $scheduleBrute->getFilm()->getName(),
				'film_not_in_bd' => $scheduleBrute->getFilmNotInDb(),
				'schedule' => $scheduleBrute->getSchedule(),
				'format' => $scheduleBrute->getFormat()
			);
		}
		
		return $schedules;
	}

	public function getScheduledFilmsByDays($cinemaId, $days, $limit = null)
	{
		$schedules = Doctrine_Query::create()
			->from('CinemaSchedule s')
			->innerJoin('s.Cinema c')
			->innerJoin('s.Film f')
			->where('f.state = 1 AND c.state = 1')
			->andWhere('c.id = ?', $cinemaId)
			->andWhereIn('s.day', $days)
			->orderBy('s.day ASC')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		$films = array();
		foreach($schedules as $schedule){
			$films[$schedule['Film']['id']]['film'] = $schedule['Film'];
			$films[$schedule['Film']['id']]['schedules'][] = array(
				'day' => $schedule['day'],
				'schedule' => $schedule['schedule'],
				'format' => $schedule['format']
			);
		}

		if (isset($limit)){
			return array_splice($films, 0, $limit);
		} else {
			return $films;
		}
	}

	public function getAllScheduledFilmsFromWeekStart($cinemaId, $weekFirstDay)
	{
		$schedules = Doctrine_Query::create()
			->from('CinemaSchedule s')
			->innerJoin('s.Cinema c')
			->innerJoin('s.Film f')
			->where('f.state = 1 AND c.state = 1')
			->andWhere('c.id = ?', $cinemaId)
			->andWhere('s.day >= ?', $weekFirstDay)
			->orderBy('s.day ASC')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$films = array();
		foreach($schedules as $schedule){
			$films[$schedule['Film']['id']]['film'] = $schedule['Film'];
			$films[$schedule['Film']['id']]['schedules'][] = array(
				'day' => $schedule['day'],
				'schedule' => $schedule['schedule'],
				'format' => $schedule['format']
			);
		}

		if (isset($limit)){
			return array_splice($films, 0, $limit);
		} else {
			return $films;
		}
	}

	/* Get the list of films that are playing in a cinema after a certain date that aren't playing yet */
	public function getFutureFilms($cinemaId, $date, $currentFilmIds, $limit)
	{
		$schedules = Doctrine_Query::create()
			->from('CinemaSchedule s')
			->innerJoin('s.Cinema c')
			->innerJoin('s.Film f')
			->where('f.state = 1 AND c.state = 1')
			->andWhere('c.id = ?', $cinemaId)
			->andWhere('s.day > ?', $date)
			->andWhereNotIn('f.id', $currentFilmIds)
			->orderBy('s.day ASC')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$films = array();
		foreach($schedules as $schedule){
			$films[$schedule['Film']['id']]['film'] = $schedule['Film'];
			$films[$schedule['Film']['id']]['schedules'][] = array(
				'day' => $schedule['day'],
				'schedule' => $schedule['schedule'],
				'format' => $schedule['format']
			);
		}

		if (isset($limit)){
			return array_splice($films, 0, $limit);
		} else {
			return $films;
		}
	}

	public function getAllFilmListByDaysAndLocation($days, $location = null)
	{
		if (!isset($location)){
			return array();
		}
		
		$q = Doctrine_Query::create()
			->from('CinemaSchedule s')
			->innerJoin('s.Film f')
			->innerJoin('s.Cinema c')
			->groupBy('s.film_id')
			->where('f.state = 1')
			->andWhere('c.location_id = ?', $location)
			->andWhereIn('s.day', $days)
			->orderBy('f.name_ro ASC')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$films = array();
		foreach($q as $film){
			$films[] = $film['Film'];
		}
		return $films;
	}
	
	public function getFilmsAndCinemasByDayAndLocationAndFilm($days, $locationId = null, $filmId = null, $limit = null)
	{
		$schedules = Doctrine_Query::create()
			//->select('s.id, s.film_id, c.id, c.name, f.id, f.name_ro')
			->from('CinemaSchedule s')
			->innerJoin('s.Cinema c')
			->innerJoin('s.Film f')
			->whereIn('s.day', $days);
		if (isset($locationId)){
			$schedules = $schedules->andWhere('c.location_id = ?', $locationId);
		}
		if (isset($filmId)){
			$schedules->andWhere('s.film_id = ?', $filmId);
		}
		$schedules = $schedules->execute();

		$films = array();
		foreach($schedules as $schedule){
			$films[$schedule->getFilm()->getId()]['film'] = $schedule->getFilm();
			$films[$schedule->getFilm()->getId()]['cinemas'][] = $schedule->getCinema();
		}

		if (isset($limit)){
			return array_splice($films, 0, $limit);
		} else {
			return $films;
		}

	}
	
	public function deleteByDayAndCinema($date, $cinemaId)
	{
		return Doctrine_Query::create()
			->delete('CinemaSchedule c')
			->where('c.day = ? AND c.cinema_id = ?', array($date, $cinemaId))
			->execute();
	}

	public function deleteOlderThan($days)
	{
		Doctrine_Query::create()
			->delete('CinemaSchedule s')
			->where('s.day < date_sub(NOW(), interval ? day)', $days)
			->execute();
	}
	
	public function getLocationsByFilm($filmId)
	{
		$q = Doctrine_Query::create()
			->select('s.id, c.id, l.id, l.city')
			->from('CinemaSchedule s')
			->innerJoin('s.Cinema c')
			->innerJoin('c.Location l')
			->groupBy('c.location_id')
			->where('s.film_id = ?', $filmId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		echo '<pre>'; var_dump($q); exit;
	}
}