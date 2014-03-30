<?php
//AlbumController.php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Form\AlbumForm;
use Doctrine\ORM\EntityManager;
use Album\Entity\Album;


class AlbumController extends AbstractActionController {

	protected $em; //doctrine2 entity manager

	public function setEntityManager(EntityManager $em) {
	  $this->em = $em;
	}

	public function getEntityManager() {
		if(null === $this->em) {
			$this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		}
		return $this->em;
	}

	public function indexAction() {
		return new ViewModel(array(
			'albums' => $this->getEntityManager()->getRepository('Album\Entity\Album')->findAll(),
		));
	}

	public function showAction() {
		$id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('album');
      }

		//remember to add a show.phtml!!!
		return new ViewModel(array(
			'album' => $this->getEntityManager()->find('Album\Entity\Album', $id),
		));
	}

	public function addAction() {
		$form = new AlbumForm();
		$form->get('submit')->setValue('Add');

		$request = $this->getRequest();
		if($request->isPost()) {
			$album = new Album();
			$form->setInputFilter($album->getInputFilter());
			$form->setData($request->getPost());

			if($form->isValid()) {
				//use doctrine2 to persist album in db
				$album->populate($form->getData()); 
				$this->getEntityManager()->persist($album);
				$this->getEntityManager()->flush();

				//redirect to list of albums
				return $this->redirect()->toRoute('album');
			}
		}
		return array('form' => $form);
	}

	public function editAction() {
	   $id = (int) $this->params()->fromRoute('id', 0);
	   if (!$id) {
	       return $this->redirect()->toRoute('album', array(
	           'action' => 'add'
	       ));
	   }

	   // Get the Album with the specified id.  An exception is thrown
	   // if it cannot be found, in which case go to the index page.
	   try {
	   	//get with doctrine2
	       $album = $this->getEntityManager()->find('Album\Entity\Album', $id);
	       //$album = $this->getAlbumTable()->getAlbum($id);
	   }
	   catch (\Exception $ex) {
	       return $this->redirect()->toRoute('album', array(
	           'action' => 'index'
	       ));
	   }

	   $form  = new AlbumForm();
	   $form->bind($album);
	   $form->get('submit')->setAttribute('value', 'Edit');

	   $request = $this->getRequest();
	   if ($request->isPost()) {
	       $form->setInputFilter($album->getInputFilter());
	       $form->setData($request->getPost());

	       if ($form->isValid()) {
	       		$form->bindValues(); //hmm...
               $this->getEntityManager()->flush();
	           //$this->getAlbumTable()->saveAlbum($album);

	           // Redirect to list of albums
	           return $this->redirect()->toRoute('album');
	       }
	   }

	   return array(
	       'id' => $id,
	       'form' => $form,
	   );
	}

	public function deleteAction() {
		$id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('album');
      }

      $request = $this->getRequest();
      if ($request->isPost()) {
          $del = $request->getPost('del', 'No');

          if ($del == 'Yes') {
              $id = (int) $request->getPost('id');
              $album = $this->getEntityManager()->find('Album\Entity\Album', $id);
                if ($album) {
                    $this->getEntityManager()->remove($album);
                    $this->getEntityManager()->flush();
                }
              //$this->getAlbumTable()->deleteAlbum($id);
          }

          // Redirect to list of albums
          return $this->redirect()->toRoute('album');
      }

      return array(
          'id'    => $id,
          'album' => $this->getEntityManager()->find('Album\Entity\Album', $id),
      );
	}
}