<?php

/**
 * FestivalSectionPersonTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class FestivalSectionPersonTable extends Doctrine_Table
{
	public function getDetailedByFestivalSection($festivalSectionId)
	{
		return Doctrine_Query::create()
			->from('FestivalSectionPerson fp')
			->innerJoin('fp.Person p')
			->where('fp.festival_section_id = ?', $festivalSectionId)
			->orderBy('p.last_name')
			->execute();
	}
	
	
	public function update($personDetails, $festivalSectionId)
	{
		Doctrine_Query::create()
			->delete('FestivalSectionPerson fp')
			->where('fp.festival_section_id = ?', $festivalSectionId)
			->execute();
				
		foreach ($personDetails as $personDetail){
			$festivalSectionPerson = new FestivalSectionPerson();
			$festivalSectionPerson->setFestivalSectionId($festivalSectionId);
			$festivalSectionPerson->setPersonId($personDetail['id']);
			$festivalSectionPerson->setIsJury($personDetail['is_jury']);
			$festivalSectionPerson->setIsNominee($personDetail['is_nominee']);
			$festivalSectionPerson->setIsWinner($personDetail['is_winner']);
			$festivalSectionPerson->save();
		}
	}
}