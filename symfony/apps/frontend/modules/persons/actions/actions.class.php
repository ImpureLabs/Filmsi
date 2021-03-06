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
	/**
	* Executes index action
	*
	* @param sfRequest $request A request object
	*/
	public function executeIndex(sfWebRequest $request)
	{
            $this->getResponse()->setTitle('Filmsi.ro - Actori & Regizori');

            $this->persons = Doctrine_Core::getTable('Person')->getBestAlphabetically(array('actor', 'director'));
	}

	public function executeIndexByLetter(sfWebRequest $request)
	{
            $this->getResponse()->setTitle('Filmsi.ro - Actori & Regizori');

            $this->currentPage = (int)$request->getParameter('p', 1);
            $this->persons = PersonTable::getInstance()->getAllByLetter(sfConfig::get('app_persons_page_limit'), $this->currentPage, $request->getParameter('letter'));
            $this->personCount = PersonTable::getInstance()->getCount($request->getParameter('letter'));
            $this->pageCount = ceil($this->personCount / sfConfig::get('app_persons_page_limit'));
            $this->firstPersonCount = sfConfig::get('app_persons_page_limit') * ($this->currentPage - 1) + 1;
            $this->lastPersonCount = $this->firstPersonCount + $this->persons->count() - 1;
            if ($this->pageCount <= 5) {
                    $this->navStart = 1;
                    $this->navEnd = $this->pageCount;
            } else {
                    $this->navStart = $this->currentPage - 2;
                    $this->navEnd = $this->currentPage - 2;

                    if ($this->navStart <= 0){
                            $this->navStart = 1;
                            $this->navEnd = 5;
                    }

                    if ($this->navEnd >= $this->pageCount){
                            $this->navStart = $this->pageCount - 4;
                            $this->navEnd = $this->pageCount;
                    }
            }
	}

	public function executeActors(sfWebRequest $request)
	{
            $this->getResponse()->setTitle('Filmsi.ro - Actori');

            $this->persons = Doctrine_Core::getTable('Person')->getBestAlphabetically('actor');
	}

	public function executeActorsByLetter(sfWebRequest $request)
	{
            $this->getResponse()->setTitle('Filmsi.ro - Actori');


            $this->currentPage = (int)$request->getParameter('p', 1);
            $this->persons = Doctrine_Core::getTable('Person')->getAllByLetter(sfConfig::get('app_persons_page_limit'), $this->currentPage, $request->getParameter('letter'), 'actor');
            $this->personCount = Doctrine_Core::getTable('Person')->getCount($request->getParameter('letter'));
            $this->pageCount = ceil($this->personCount / sfConfig::get('app_persons_page_limit'));
            $this->firstPersonCount = sfConfig::get('app_persons_page_limit') * ($this->currentPage - 1) + 1;
            $this->lastPersonCount = $this->firstPersonCount + $this->persons->count() - 1;
            if ($this->pageCount <= 5) {
                    $this->navStart = 1;
                    $this->navEnd = $this->pageCount;
            } else {
                    $this->navStart = $this->currentPage - 2;
                    $this->navEnd = $this->currentPage - 2;

                    if ($this->navStart <= 0){
                            $this->navStart = 1;
                            $this->navEnd = 5;
                    }

                    if ($this->navEnd >= $this->pageCount){
                            $this->navStart = $this->pageCount - 4;
                            $this->navEnd = $this->pageCount;
                    }
            }
	}

	public function executeDirectors(sfWebRequest $request)
	{
            $this->getResponse()->setTitle('Filmsi.ro - Regizori');

            $this->persons = Doctrine_Core::getTable('Person')->getBestAlphabetically('director');
	}

	public function executeDirectorsByLetter(sfWebRequest $request)
	{
            $this->getResponse()->setTitle('Filmsi.ro - Regizori');

            $this->currentPage = (int)$request->getParameter('p', 1);
            $this->persons = Doctrine_Core::getTable('Person')->getAllByLetter(sfConfig::get('app_persons_page_limit'), $this->currentPage, $request->getParameter('letter'), 'director');
            $this->personCount = Doctrine_Core::getTable('Person')->getCount($request->getParameter('letter'));
            $this->pageCount = ceil($this->personCount / sfConfig::get('app_persons_page_limit'));
            $this->firstPersonCount = sfConfig::get('app_persons_page_limit') * ($this->currentPage - 1) + 1;
            $this->lastPersonCount = $this->firstPersonCount + $this->persons->count() - 1;
            if ($this->pageCount <= 5) {
                    $this->navStart = 1;
                    $this->navEnd = $this->pageCount;
            } else {
                    $this->navStart = $this->currentPage - 2;
                    $this->navEnd = $this->currentPage - 2;

                    if ($this->navStart <= 0){
                            $this->navStart = 1;
                            $this->navEnd = 5;
                    }

                    if ($this->navEnd >= $this->pageCount){
                            $this->navStart = $this->pageCount - 4;
                            $this->navEnd = $this->pageCount;
                    }
            }
	}

	public function executeView(sfWebRequest $request)
	{
		$this->person = Doctrine_Core::getTable('Person')->findOneById($request->getParameter('id'));

		$routeParameters = $this->getRoute()->getParameters();
		$this->personRole = $routeParameters['person_role'];

		$this->films = $this->person->getMostViewedFilmsByRole(8, $this->personRole);

		//$this->awards = Doctrine_Core::getTable('FestivalSectionParticipant')->getDetailedByPerson($this->person->getImdb());
		$this->awards = $this->person->getRecentAwardsDetailed(5);

		/* Add the visit */
		if ($routeParameters['person_role'] == 'actor'){
			$visit = new Visit();
			$visit->setLibraryId($this->person->getLibraryId());
			$visit->setUrl($this->generateUrl('person', array('id' => $this->person->getId(), 'key' => $this->person->getUrlKey())));
			$visit->setName($this->person->getName());
			$visit->setIp($_SERVER['REMOTE_ADDR']);
			$visit->save();
		}


		/* META Stuff */
		$personRoles = '';
		if ($this->person->getIsActor() == '1'){
			$personRoles .= 'actor ';
		}
		if ($this->person->getIsDirector() == '1'){
			$personRoles .= 'regizor ';
		}
		if ($this->person->getIsScriptwriter() == '1'){
			$personRoles .= 'scenarist ';
		}
		if ($this->person->getIsProducer() == '1'){
			$personRoles .= 'producator ';
		}
		$this->getResponse()->setTitle($this->person->getName() . ' - ' . $personRoles . '- Filmsi.ro');

		if ($this->person->getMetaKeywords() == '' || $this->person->getMetaDescription() == ''){
			$metaStuff = $this->person->getName() . ' ' . $personRoles;

			foreach($this->person->getMostViewedFilmsByRole(3) as $key => $film){
				$metaStuff .= $film['name_ro'] . ', ';
			}
		}

		if ($this->person->getMetaKeywords() == ''){
			$this->getResponse()->addMeta('keywords', $metaStuff);
		} else {
			$this->getResponse()->addMeta('keywords', $this->person->getMetaKeywords());
		}
		if ($this->person->getMetaDescription() == ''){
			$this->getResponse()->addMeta('description', $metaStuff);
		} else {
			$this->getResponse()->addMeta('description', $this->person->getMetaDescription());
		}
	}

	public function executeBiography(sfWebRequest $request)
	{
		$this->person = Doctrine_Core::getTable('Person')->findOneById($request->getParameter('id'));


		/* META Stuff */
		$personRoles = '';
		if ($this->person->getIsActor() == '1'){
			$personRoles .= 'actor ';
		}
		if ($this->person->getIsDirector() == '1'){
			$personRoles .= 'regizor ';
		}
		if ($this->person->getIsScriptwriter() == '1'){
			$personRoles .= 'scenarist ';
		}
		if ($this->person->getIsProducer() == '1'){
			$personRoles .= 'producator ';
		}
		$this->getResponse()->setTitle('Biografie ' . $this->person->getName() . ' - ' . $personRoles . '- Filmsi.ro');

		if ($this->person->getMetaKeywords() == '' || $this->person->getMetaDescription() == ''){
			$metaStuff = 'Biografie ' . $this->person->getName() . ' ' . $personRoles;

			foreach($this->person->getMostViewedFilmsByRole(3) as $key => $film){
				$metaStuff .= $film['name_ro'] . ', ';
			}
		}

		if ($this->person->getMetaKeywords() == ''){
			$this->getResponse()->addMeta('keywords', $metaStuff);
		} else {
			$this->getResponse()->addMeta('keywords', $this->person->getMetaKeywords());
		}
		if ($this->person->getMetaDescription() == ''){
			$this->getResponse()->addMeta('description', $metaStuff);
		} else {
			$this->getResponse()->addMeta('description', $this->person->getMetaDescription());
		}
	}

	public function executeAwards(sfWebRequest $request)
	{
		$this->person = Doctrine_Core::getTable('Person')->findOneById($request->getParameter('id'));
		$this->awards = $this->person->getRecentAwardsDetailed(0);

		/* META Stuff */
		$personRoles = '';
		if ($this->person->getIsActor() == '1'){
			$personRoles .= 'actor ';
		}
		if ($this->person->getIsDirector() == '1'){
			$personRoles .= 'regizor ';
		}
		if ($this->person->getIsScriptwriter() == '1'){
			$personRoles .= 'scenarist ';
		}
		if ($this->person->getIsProducer() == '1'){
			$personRoles .= 'producator ';
		}
		$this->getResponse()->setTitle('Premii ' . $this->person->getName() . ' - ' . $personRoles . '- Filmsi.ro');

		if ($this->person->getMetaKeywords() == '' || $this->person->getMetaDescription() == ''){
			$metaStuff = 'Premii ' . $this->person->getName() . ' ' . $personRoles;

			foreach($this->person->getMostViewedFilmsByRole(3) as $key => $film){
				$metaStuff .= $film['name_ro'] . ', ';
			}
		}

		if ($this->person->getMetaKeywords() == ''){
			$this->getResponse()->addMeta('keywords', $metaStuff);
		} else {
			$this->getResponse()->addMeta('keywords', $this->person->getMetaKeywords());
		}
		if ($this->person->getMetaDescription() == ''){
			$this->getResponse()->addMeta('description', $metaStuff);
		} else {
			$this->getResponse()->addMeta('description', $this->person->getMetaDescription());
		}
	}

	public function executeFilms(sfWebRequest $request)
	{
		$this->person = Doctrine_Core::getTable('Person')->findOneById($request->getParameter('id'));
		$this->films = $this->person->getMostViewedFilmsByRole(0, null, Doctrine_Core::HYDRATE_RECORD);

		/* META Stuff */
		$personRoles = '';
		if ($this->person->getIsActor() == '1'){
			$personRoles .= 'actor ';
		}
		if ($this->person->getIsDirector() == '1'){
			$personRoles .= 'regizor ';
		}
		if ($this->person->getIsScriptwriter() == '1'){
			$personRoles .= 'scenarist ';
		}
		if ($this->person->getIsProducer() == '1'){
			$personRoles .= 'producator ';
		}
		$this->getResponse()->setTitle('Filmografie ' . $this->person->getName() . ' - ' . $personRoles . '- Filmsi.ro');

		if ($this->person->getMetaKeywords() == '' || $this->person->getMetaDescription() == ''){
			$metaStuff = 'Filmografie ' . $this->person->getName() . ' ' . $personRoles;

			foreach($this->person->getMostViewedFilmsByRole(3) as $key => $film){
				$metaStuff .= $film['name_ro'] . ', ';
			}
		}

		if ($this->person->getMetaKeywords() == ''){
			$this->getResponse()->addMeta('keywords', $metaStuff);
		} else {
			$this->getResponse()->addMeta('keywords', $this->person->getMetaKeywords());
		}
		if ($this->person->getMetaDescription() == ''){
			$this->getResponse()->addMeta('description', $metaStuff);
		} else {
			$this->getResponse()->addMeta('description', $this->person->getMetaDescription());
		}
	}

	public function executePhotos(sfWebRequest $request)
	{
		$this->person = Doctrine_Core::getTable('Person')->findOneById($request->getParameter('id'));
		$this->photos = $this->person->getPhotoAlbum()->getPhotos();
		$this->photoCount = $this->photos->count();

		$this->currentPhoto = $request->getParameter('pid', 1);

		/* META Stuff */
		$personRoles = '';
		if ($this->person->getIsActor() == '1'){
			$personRoles .= 'actor ';
		}
		if ($this->person->getIsDirector() == '1'){
			$personRoles .= 'regizor ';
		}
		if ($this->person->getIsScriptwriter() == '1'){
			$personRoles .= 'scenarist ';
		}
		if ($this->person->getIsProducer() == '1'){
			$personRoles .= 'producator ';
		}
		$this->getResponse()->setTitle('Fotografii ' . $this->person->getName() . ' - ' . $personRoles . '- Filmsi.ro');

		if ($this->person->getMetaKeywords() == '' || $this->person->getMetaDescription() == ''){
			$metaStuff = 'Fotografii ' . $this->person->getName() . ' ' . $personRoles;

			foreach($this->person->getMostViewedFilmsByRole(3) as $key => $film){
				$metaStuff .= $film['name_ro'] . ', ';
			}
		}

		if ($this->person->getMetaKeywords() == ''){
			$this->getResponse()->addMeta('keywords', $metaStuff);
		} else {
			$this->getResponse()->addMeta('keywords', $this->person->getMetaKeywords());
		}
		if ($this->person->getMetaDescription() == ''){
			$this->getResponse()->addMeta('description', $metaStuff);
		} else {
			$this->getResponse()->addMeta('description', $this->person->getMetaDescription());
		}
	}

	public function executeStiri(sfWebRequest $request)
	{
		$this->person = Doctrine_Core::getTable('Person')->findOneById($request->getParameter('id'));

		$this->currentPage = (int)$request->getParameter('p', 1);
		$this->stires = $this->person->getRelatedStires(sfConfig::get('app_stire_page_limit'), $this->currentPage, false);

		$this->stireCount = $this->person->getRelatedStiresCount();
		$this->pageCount = ceil($this->stireCount / sfConfig::get('app_stire_page_limit'));
		$this->firstStireCount = sfConfig::get('app_stire_page_limit') * ($this->currentPage - 1) + 1;
		$this->lastStireCount = $this->firstStireCount + $this->stires->count() - 1;
		if ($this->pageCount <= 5) {
				$this->navStart = 1;
				$this->navEnd = $this->pageCount;
		} else {
				$this->navStart = $this->currentPage - 2;
				$this->navEnd = $this->currentPage - 2;

				if ($this->navStart <= 0){
						$this->navStart = 1;
						$this->navEnd = 5;
				}

				if ($this->navEnd >= $this->pageCount){
						$this->navStart = $this->pageCount - 4;
						$this->navEnd = $this->pageCount;
				}
		}


		/* META Stuff */
		$personRoles = '';
		if ($this->person->getIsActor() == '1'){
			$personRoles .= 'actor ';
		}
		if ($this->person->getIsDirector() == '1'){
			$personRoles .= 'regizor ';
		}
		if ($this->person->getIsScriptwriter() == '1'){
			$personRoles .= 'scenarist ';
		}
		if ($this->person->getIsProducer() == '1'){
			$personRoles .= 'producator ';
		}
		$this->getResponse()->setTitle('Stiri ' . $this->person->getName() . ' - ' . $personRoles . '- Filmsi.ro');

		if ($this->person->getMetaKeywords() == '' || $this->person->getMetaDescription() == ''){
			$metaStuff = 'Stiri ' . $this->person->getName() . ' ' . $personRoles;

			foreach($this->person->getMostViewedFilmsByRole(3) as $key => $film){
				$metaStuff .= $film['name_ro'] . ', ';
			}
		}

		if ($this->person->getMetaKeywords() == ''){
			$this->getResponse()->addMeta('keywords', $metaStuff);
		} else {
			$this->getResponse()->addMeta('keywords', $this->person->getMetaKeywords());
		}
		if ($this->person->getMetaDescription() == ''){
			$this->getResponse()->addMeta('description', $metaStuff);
		} else {
			$this->getResponse()->addMeta('description', $this->person->getMetaDescription());
		}
	}

	public function executeAlertAdd(sfWebRequest $request)
	{
		if (!$this->getUser()->isAuthenticated()){
			$this->setTemplate('alertAddNotAuthenticated');
			return sfView::SUCCESS;
		}

		$this->person = PersonTable::getInstance()->findOneById($request->getParameter('id'));

		$this->form = new PersonAlertForm();
		$this->form->setDefault('person_id', $this->person->getId());
		$this->form->setDefault('user_id', $this->getUser()->getGuardUser()->getId());

		if ($request->isMethod('post')){
			$this->form->bind($request->getParameter($this->form->getName()));

			if ($this->form->isValid()){
				$this->form->save();

				return $this->renderText(json_encode(array('status' => true)));
			}
		}
	}
}
