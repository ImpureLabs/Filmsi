<?php

/**
 * FilmAlertTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class FilmAlertTable extends Doctrine_Table
{
    public static function getInstance()
    {
        return Doctrine_Core::getTable('FilmAlert');
    }

	public function deleteByUserAndFilm($userId, $filmId)
	{
		return Doctrine_Query::create()
			->delete('FilmAlert fa')
			->where('fa.user_id = ? AND fa.film_id = ?', array($userId, $filmId))
			->execute();
	}
}