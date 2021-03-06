<?php

/**
 * FestivalSectionParticipant
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    filmsi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class FestivalSectionParticipant extends BaseFestivalSectionParticipant
{
	public function getFilm()
	{
		return Doctrine_Core::getTable('Film')->findOneByImdb($this->getFilmImdb());
	}

	public function getPerson()
	{
		if ('' == $this->getPersonImdb()){
			return false;
		}
		
		return Doctrine_Core::getTable('Person')->findOneByImdb($this->getPersonImdb());
	}



}