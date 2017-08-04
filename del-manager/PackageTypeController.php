<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PackageType Controller
 *
 * @property \App\Model\Table\PackageTypeTable $PackageType
 */
class PackageTypeController extends AppController
{


	public function isAuthorized($user) {
		
		// The owner of an article can edit and delete it
		if (in_array ( $this->request->action, [ 
				'add',
				'edit',
				'delete',
				'view',
				'index',
				
		] )) {
			
			if (isset ( $user ['user_type'] ) && $user ['user_type'] == 2) {
				return true;
			}
			
		}
		
		return parent::isAuthorized ( $user );
	}

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $packageType = $this->paginate($this->PackageType);

        $this->set(compact('packageType'));
        $this->set('_serialize', ['packageType']);
    }

    /**
     * View method
     *
     * @param string|null $id Package Type id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $packageType = $this->PackageType->get($id, [
            'contain' => []
        ]);

        $this->set('packageType', $packageType);
        $this->set('_serialize', ['packageType']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $packageType = $this->PackageType->newEntity();
        if ($this->request->is('post')) {
            $packageType = $this->PackageType->patchEntity($packageType, $this->request->data);
            if ($this->PackageType->save($packageType)) {
                $this->Flash->success(__('The package type has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The package type could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('packageType'));
        $this->set('_serialize', ['packageType']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Package Type id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $packageType = $this->PackageType->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $packageType = $this->PackageType->patchEntity($packageType, $this->request->data);
            if ($this->PackageType->save($packageType)) {
                $this->Flash->success(__('The package type has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The package type could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('packageType'));
        $this->set('_serialize', ['packageType']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Package Type id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $packageType = $this->PackageType->get($id);
        if ($this->PackageType->delete($packageType)) {
            $this->Flash->success(__('The package type has been deleted.'));
        } else {
            $this->Flash->error(__('The package type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
