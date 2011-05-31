<?php

/**
 * FilmPersonTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class FilmPersonTable extends Doctrine_Table
{
	/**
	 *
	 * @return FilmPersonTable
	 */
	public static function getInstance()
	{
		return Doctrine_Core::getTable('FilmPerson');
	}

	public function getDetailedByFilm($filmId)
	{
		return Doctrine_Query::create()
			->from('FilmPerson fp')
			->innerJoin('fp.Person p')
			->where('fp.film_id = ?', $filmId)
			->orderBy('p.last_name')
			->execute();
	}
	
	public function update($personDetails, $filmId)
	{
		Doctrine_Query::create()
			->delete('FilmPerson fp')
			->where('fp.film_id = ?', $filmId)
			->execute();
			
		foreach ($personDetails as $personDetail){
			$filmPerson = new FilmPerson();
			$filmPerson->setFilmId($filmId);
			$filmPerson->setPersonId($personDetail['id']);
			$filmPerson->setIsActor($personDetail['is_actor']);
			$filmPerson->setIsDirector($personDetail['is_director']);
			$filmPerson->setIsScriptwriter($personDetail['is_scriptwriter']);
			$filmPerson->setIsProducer($personDetail['is_producer']);
			$filmPerson->save();
		}
	}

	public function getOneByFilmAndPerson($filmId, $personId)
	{
		return Doctrine_Query::create()
			->from('FilmPerson fp')
			->where('fp.film_id = ? AND fp.person_id = ?', array($filmId, $personId))
			->fetchOne();
	}

	public function getBestActorsByFilm($filmId, $limit = null)
	{
		$q = Doctrine_Query::create()
			->from('FilmPerson fp')
			->innerJoin('fp.Person p')
			->where('fp.film_id = ?', $filmId)
			->andWhere('fp.is_actor = 1')
			->orderBy('p.visit_count DESC');

		if (isset ($limit)){
			$q->limit($limit);
		}

		$filmPersons = $q->execute();

		$actors = array();
		foreach($filmPersons as $filmPerson){
			$actors[] = $filmPerson->getPerson();
		}

		return $actors;
	}

	public function getBestDirectorsByFilm($filmId, $limit = null)
	{
		$q = Doctrine_Query::create()
			->from('FilmPerson fp')
			->innerJoin('fp.Person p')
			->where('fp.film_id = ?', $filmId)
			->andWhere('fp.is_director = 1')
			->orderBy('p.visit_count DESC');

		if (isset ($limit)){
			$q->limit($limit);
		}

		$filmPersons = $q->execute();

		$actors = array();
		foreach($filmPersons as $filmPerson){
			$actors[] = $filmPerson->getPerson();
		}

		return $actors;
	}



	public function getBestScriptwritersByFilm($filmId, $limit = null)
	{
		$q = Doctrine_Query::create()
			->from('FilmPerson fp')
			->innerJoin('fp.Person p')
			->where('fp.film_id = ?', $filmId)
			->andWhere('fp.is_scriptwriter = 1')
			->orderBy('p.visit_count DESC');

		if (isset ($limit)){
			$q->limit($limit);
		}

		$filmPersons = $q->execute();

		$scriptwriters = array();
		foreach($filmPersons as $filmPerson){
			$scriptwriters[] = $filmPerson->getPerson();
		}

		return $scriptwriters;
	}



	public function getBestProducersByFilm($filmId, $limit = null)
	{
		$q = Doctrine_Query::create()
			->from('FilmPerson fp')
			->innerJoin('fp.Person p')
			->where('fp.film_id = ?', $filmId)
			->andWhere('fp.is_producer = 1')
			->orderBy('p.visit_count DESC');

		if (isset ($limit)){
			$q->limit($limit);
		}

		$filmPersons = $q->execute();

		$producers = array();
		foreach($filmPersons as $filmPerson){
			$producers[] = $filmPerson->getPerson();
		}

		return $producers;
	}

}