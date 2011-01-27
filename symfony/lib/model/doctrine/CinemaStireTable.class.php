<?php

/**
 * CinemaStireTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CinemaStireTable extends Doctrine_Table
{
	public function updateObjects($stireId, $cinemas)
	{
		Doctrine_Query::create()
			->delete('CinemaStire ca')
			->where('ca.stire_id = ?', $stireId)
			->execute();
			
		foreach ($cinemas as $cinema){
			$cinemaStire = new CinemaStire();
			$cinemaStire->setStireId($stireId);
			$cinemaStire->setCinemaId($cinema);
			$cinemaStire->save();
		}
	}

        public function getRelatedStires($stireId)
	{
		$cinemas = Doctrine_Query::create()
			->from('CinemaStire fs')
			->where('fs.stire_id = ?', $stireId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$cinemaIds = array();
		foreach($cinemas as $cinema){
			$cinemaIds[] = $cinema['cinema_id'];
		}

		$stireIds = array();
                if (count($cinemaIds) > 0){
                    $stires = Doctrine_Query::create()
                            ->from('CinemaStire fs')
                            ->whereIn('fs.cinema_id', $cinemaIds)
                            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                    foreach($stires as $stire){
                            $stireIds[] = $stire['stire_id'];
                    }
                }

		return $stireIds;
	}
}