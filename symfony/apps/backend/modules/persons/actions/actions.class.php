<?php

/**
 * persons actions.
 *
 * @package    filmsi
 * @subpackage persons
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class personsActions extends sfActions
{
  public function executeNewObject(sfWebRequest $request)
  {
  	$this->form = new PersonNewForm();
  	
  	if ($request->isMethod('post')){
  		$this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
  		
  		if ($this->form->isValid()){
  			$this->form->save();
  			$this->redirect($this->generateUrl('default', array('module' => 'persons', 'action' => 'view')) . '?lid=' . $this->form->getObject()->getLibraryId());
  		}
  	}
  }

  public function executeView(sfWebRequest $request)
  {
  	$this->person = Doctrine_Core::getTable('Person')->findOneByLibraryId($request->getParameter('lid'));

	$this->imdbPhotoKeys = ImdbPersonPhotoKeyTable::getInstance()->countByPerson($this->person->getImdb());
  }
  
	public function executeEdit(sfWebRequest $request)
  {
  	$person = Doctrine_Core::getTable('Person')->findOneByLibraryId($request->getParameter('lid'));
  	
  	$this->form = new PersonEditForm($person);
  	
  	if ($request->isMethod('post')){
  		$this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
  		
  		if ($this->form->isValid()){
  			$this->form->save();
  			$this->redirect($this->generateUrl('default', array('module' => 'persons', 'action' => 'view')) . '?lid=' . $this->form->getObject()->getLibraryId());
  		}
  	}
  }

	public function executeApi(sfWebRequest $request)
	{
		$this->setLayout(false);
		$this->getResponse()->setContentType('application/json');

		return $this->renderText(json_encode(Doctrine_Core::getTable('Person')->getForApi($request->getParameter('term'))));
	}

	public function executeApiImdb(sfWebRequest $request)
	{
		$this->setLayout(false);
		$this->getResponse()->setContentType('application/json');

		return $this->renderText(json_encode(Doctrine_Core::getTable('Person')->getForApiImdb($request->getParameter('term'))));
	}

	public function executeImport(sfWebRequest $request)
  {
  	set_time_limit(10000);
  	
  	
  	$parameters = $request->getParameter('person');
  	$imdbCode = $parameters['imdb'];
  	
  	if (false !== Doctrine_Core::getTable('Person')->findOneByImdb($imdbCode)){
  		echo 'Persoana deja exista in baza de date! Click <a href="' . $this->generateUrl('default', array('module' => 'persons', 'action' => 'newObject')) . '">AICI</a> pentru a continua.';
  		exit;
  	}

  	try {
  		$imdbComPerson = new ImdbComPerson($imdbCode);
		$imdbComPerson->parseNamePage();
		$imdbComPerson->parseBioPage();
  		
	  	$person = new Person();
	  	$person->setImdb($imdbComPerson->getImdb());
	  	$person->setFirstName($imdbComPerson->getFirstName());
	  	$person->setLastName($imdbComPerson->getLastName());
	  	$person->setDateOfBirth($imdbComPerson->getDateOfBirth());
	  	$person->setDateOfDeath($imdbComPerson->getDateOfDeath());
	  	$person->setPlaceOfBirth($imdbComPerson->getPlaceOfBirth());
	  	$person->setBiographyContent($imdbComPerson->getBio());
	  	$person->setIsActor($imdbComPerson->getIsActor());
	  	$person->setIsDirector($imdbComPerson->getIsDirector());
	  	$person->setIsScriptwriter($imdbComPerson->getIsScriptwriter());
	  	$person->setIsProducer($imdbComPerson->getIsProducer());
	  	$person->setUserId($this->getUser()->getGuardUser()->getId());
	  	$person->setPublishDate(date('Y-m-d'), time());
	  	
	  	$filenameSource = ( $imdbComPerson->getFilenameSource() == false || strpos($imdbComPerson->getFilenameSource(),'nopicture') !== false ) ? ( sfConfig::get('app_aws_s3_path') . sfConfig::get('app_aws_bucket') . '/' . sfConfig::get('app_asset_aws_s3_folder') . '/default-nophoto.jpg' ) : $imdbComPerson->getFilenameSource();
		
	  	$pieces = explode('.', $filenameSource);
	  	$extension = array_pop($pieces);
		
		$person->setFilename(md5($filenameSource . microtime() . rand(0, 999999)) . '.' . $extension);

		$imageData = getimagesize($filenameSource);
	    $person->createFile(
			$filenameSource, 
			$imageData['mime']
		);

	    $person->save();
  	
	    
  	  /* Add the Acted films */
	    foreach ($imdbComPerson->getActedFilms() as $filmImdbCode)
	    {
	    	/* If the film does not exist in the database, move to the next one */
	    	if (false == $film = Doctrine_Core::getTable('Film')->findOneByImdb($filmImdbCode)){
	    			continue;
	    	}
	    	
	    	/* Check if a connection between the person and the film is already made, if not, create it */
    		if (false == $filmPerson = Doctrine_Core::getTable('FilmPerson')->getOneByFilmAndPerson($film->getId(), $person->getId())){
    			$filmPerson = new FilmPerson();
    			$filmPerson->setFilmId($film->getId());
    			$filmPerson->setPersonId($person->getId());
    		}
    		
    			$filmPerson->setIsActor('1');
    			$filmPerson->save();
	    }
	    
  	  /* Add the Directed films */
	    foreach ($imdbComPerson->getDirectedFilms() as $filmImdbCode)
	    {
	    	/* If the film does not exist in the database, move to the next one */
	    	if (false == $film = Doctrine_Core::getTable('Film')->findOneByImdb($filmImdbCode)){
	    			continue;
	    	}
	    	
	    	/* Check if a connection between the person and the film is already made, if not, create it */
    		if (false == $filmPerson = Doctrine_Core::getTable('FilmPerson')->getOneByFilmAndPerson($film->getId(), $person->getId())){
    			$filmPerson = new FilmPerson();
    			$filmPerson->setFilmId($film->getId());
    			$filmPerson->setPersonId($person->getId());
    		}
    		
    			$filmPerson->setIsDirector('1');
    			$filmPerson->save();
	    }
	    
  	  /* Add the Produced films */
	    foreach ($imdbComPerson->getProducedFilms() as $filmImdbCode)
	    {
	    	/* If the film does not exist in the database, move to the next one */
	    	if (false == $film = Doctrine_Core::getTable('Film')->findOneByImdb($filmImdbCode)){
	    			continue;
	    	}
	    	
	    	/* Check if a connection between the person and the film is already made, if not, create it */
    		if (false == $filmPerson = Doctrine_Core::getTable('FilmPerson')->getOneByFilmAndPerson($film->getId(), $person->getId())){
    			$filmPerson = new FilmPerson();
    			$filmPerson->setFilmId($film->getId());
    			$filmPerson->setPersonId($person->getId());
    		}
    		
    			$filmPerson->setIsProducer('1');
    			$filmPerson->save();
	    }
	    
  	  /* Add the Written films */
	    foreach ($imdbComPerson->getWrittenFilms() as $filmImdbCode)
	    {
	    	/* If the film does not exist in the database, move to the next one */
	    	if (false == $film = Doctrine_Core::getTable('Film')->findOneByImdb($filmImdbCode)){
	    			continue;
	    	}
	    	
	    	/* Check if a connection between the person and the film is already made, if not, create it */
    		if (false == $filmPerson = Doctrine_Core::getTable('FilmPerson')->getOneByFilmAndPerson($film->getId(), $person->getId())){
    			$filmPerson = new FilmPerson();
    			$filmPerson->setFilmId($film->getId());
    			$filmPerson->setPersonId($person->getId());
    		}
    		
    			$filmPerson->setIsScriptwriter('1');
    			$filmPerson->save();
	    }
	    
	    echo '<br />Am terminat de importat persoana <strong>' . $person->getName() . '</strong>';
			@ob_end_flush(); @flush(); @ob_start();
	    
  		
  	} catch (ImdbComPersonException $e){
  		echo 'A aparut o eroare! Click <a href="' . $this->generateUrl('default', array('module' => 'persons', 'action' => 'newObject')) . '">AICI</a> pentru a continua.';
  	}

  	echo 'Importul s-a terminat!  Click <a href="' . $this->generateUrl('default', array('module' => 'persons', 'action' => 'view')) . '?lid=' . $person->getLibraryId() . '">AICI</a> pentru a continua.';
  	
  	exit;
  }

  public function executeImportImdbPhotos(sfWebRequest $request)
  {
  	set_time_limit(10000);

	$person  = Doctrine_Core::getTable('Person')->findOneById($request->getParameter('id'));
  	if (false === $person){
  		echo 'Persoana nu exista in baza de date! Click <a href="' . $this->generateUrl('default', array('module' => 'persons', 'action' => 'newObject')) . '">AICI</a> pentru a continua.';
  		exit;
  	}

	/* If the person doesn't have any album associated with it, just create one */
		if ('' == $person->getPhotoAlbumId()){
			$photoAlbum = new PhotoAlbum();
			$photoAlbum->setName('Person: ' . $person->getName());
			$photoAlbum->setUserId($this->getUser()->getGuardUser()->getId());
			$photoAlbum->setPublishDate(date('Y-m-d'), time());
			$photoAlbum->save();

			/* Assign the photo album to the person */
			$person->setPhotoAlbumId($photoAlbum->getId());
			$person->save();
		}

	$imdbPhotoKeys = ImdbPersonPhotoKeyTable::getInstance()->findByImdb($person->getImdb());

  	try {
		$imdbComPerson = new ImdbComPerson($person->getImdb());
		/* Create the photos and add them to the album */
		$counter = 1;
		foreach ($imdbPhotoKeys as $imdbPhotoKey){

			/* Get the photo url from the photo page */
			$photoUrl = $imdbComPerson->parsePhotoPage($imdbPhotoKey->getPhotoKey());
			if (!$photoUrl){
				echo '<br />Nu s-a putut importa poza cu key-ul:' . $imdbPhoto;
				ob_end_flush(); flush(); ob_start();
				continue;
			}

			
			$photo = new Photo();
			$photo->setAlbumId($person->getPhotoAlbumId());

			$pieces = explode('.', $photoUrl);
			$extension = array_pop($pieces);

			$photo->setFilename(md5($photoUrl . microtime() . rand(0, 999999)). '.' . $extension);
			
			$imageData = getimagesize($photoUrl);
			$photo->createFile(
				$photoUrl, 
				$imageData['mime']
			);

			$photo->save();

			echo '<br />Am terminat de importat poza nr ' . $counter . ' cu key-ul:' . $imdbPhotoKey->getPhotoKey();
			ob_end_flush(); flush(); ob_start();
			$counter += 1;

			/* Delete from database */
			$imdbPhotoKey->delete();
		}

		echo '<br />Am terminat de importat pozele';
			ob_end_flush(); flush(); ob_start();

  	} catch (ImdbComPersonException $e){
  		echo 'A aparut o eroare! Click <a href="' . $this->generateUrl('default', array('module' => 'persons', 'action' => 'newObject')) . '">AICI</a> pentru a continua.';
  	}

  	echo '<br /><br />Importul s-a terminat!  Click <a href="' . $this->generateUrl('default', array('module' => 'persons', 'action' => 'view')) . '?lid=' . $person->getLibraryId() . '">AICI</a> pentru a continua.';

  	exit;
  }

  public function executeImportImdbPhotoKeys(sfWebRequest $request)
  {
  	set_time_limit(10000);

	$person  = Doctrine_Core::getTable('Person')->findOneById($request->getParameter('id'));

	/* Delete any existing keys for this person */
	ImdbPersonPhotoKeyTable::getInstance()->deleteByPerson($person->getImdb());

	/* Import the keys */
	$imdbComPerson = new ImdbComPerson($person->getImdb());

	foreach ($imdbComPerson->parsePhotosPage() as $photoKey){
		$imdbPersonPhotoKey = new ImdbPersonPhotoKey();
		$imdbPersonPhotoKey->setImdb($person->getImdb());
		$imdbPersonPhotoKey->setPhotoKey($photoKey);
		$imdbPersonPhotoKey->save();
	}

  	$this->redirect($this->generateUrl('default', array('module' => 'persons', 'action' => 'view')) . '?lid=' . $person->getLibraryId());
  }
}
