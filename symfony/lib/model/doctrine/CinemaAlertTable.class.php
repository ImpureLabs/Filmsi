<?php

/**
 * CinemaAlertTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CinemaAlertTable extends Doctrine_Table
{
    public static function getInstance()
    {
        return Doctrine_Core::getTable('CinemaAlert');
    }

	public function getByUser($userId)
	{
		return Doctrine_Query::create()
			->select('a.id, c.name cinema_name, c.id cinema_id, c.url_key cinema_url_key, c.location_id location_id')
			->from('CinemaAlert a')
			->innerJoin('a.Cinema c')
			->where('a.user_id =?', $userId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	public function updateCinemasForUser($userId, $cinemaIds = array())
	{
		Doctrine_Query::create()
			->delete('CinemaAlert a')
			->where('a.user_id = ?', $userId)
			->execute();

		if (count($cinemaIds) > 0){
			foreach ($cinemaIds as $cinemaId){
				$cinemaAlert = new CinemaAlert();
				$cinemaAlert->setUserId($userId);
				$cinemaAlert->setCinemaId($cinemaId);
				$cinemaAlert->save();
			}
		}
	}
    
}