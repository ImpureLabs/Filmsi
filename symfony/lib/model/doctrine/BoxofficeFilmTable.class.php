<?php

/**
 * BoxofficeFilmTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class BoxofficeFilmTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object BoxofficeFilmTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('BoxofficeFilm');
    }

	public function getByType($type)
	{
		$q = Doctrine_Query::create()
			->select('b.type, f1.id, f1.name_ro, f1.name_en, f1.filename, f1.url_key,
				f2.id, f2.name_ro, f2.name_en, f2.filename, f2.url_key,
				f3.id, f3.name_ro, f3.name_en, f3.filename, f3.url_key,
				f4.id, f4.name_ro, f4.name_en, f4.filename, f4.url_key,
				f5.id, f5.name_ro, f5.name_en, f5.filename, f5.url_key, ')
			->from('BoxofficeFilm b')
			->innerJoin('b.Film1 f1')
			->innerJoin('b.Film2 f2')
			->innerJoin('b.Film3 f3')
			->innerJoin('b.Film4 f4')
			->innerJoin('b.Film5 f5');

		if ($type == 'ro'){
			$q = $q->where('b.type = "ro"');
		} else {
			$q = $q->where('b.type = "us"');
		}

		return $q->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
}