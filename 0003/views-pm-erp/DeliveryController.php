<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Delivery Controller
 *
 * @property \App\Model\Table\DeliveryTable $Delivery
 */
class DeliveryController extends AppController
{
	
	public function isAuthorized($user)
	{
	
	
		// The owner of an article can edit and delete it
		if (in_array($this->request->action, ['view'])) {
	
			if (isset($user['user_type']) && $user['user_type'] == 2) {
				return true;
			}
		}
		
		if (in_array($this->request->action, ['view','index','edit','add'])) {
	
			if (isset($user['user_type']) && $user['user_type'] == 6) {
				return true;
			}
		}
	
		return parent::isAuthorized($user);
	}

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users','city']
        ];
        $delivery = $this->paginate($this->Delivery);

        $this->set(compact('delivery'));
        $this->set('_serialize', ['delivery']);
    }

    /**
     * View method
     *
     * @param string|null $id Delivery id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $delivery = $this->Delivery->get($id, [
            'contain' => ['Users','city']
        ]);

        $this->set('delivery', $delivery);
        $this->set('_serialize', ['delivery']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $delivery = $this->Delivery->newEntity();
        if ($this->request->is('post')) {
            $delivery = $this->Delivery->patchEntity($delivery, $this->request->data);
            if ($this->Delivery->save($delivery)) {
                $this->Flash->success(__('The delivery has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The delivery could not be saved. Please, try again.'));
            }
        }
        //$users = $this->Delivery->Users->find('list', ['limit' => 200]);
        // I think this query should be on MODAL, but-->get all user that relation with supplier table,
        $subquery = $this->Delivery->find ()->select ( 'user_id' )->hydrate ( false )->join ( [
        		'table' => 'users',
        		'alias' => 'u',
        		'type' => 'INNER',
        		'conditions' => 'u.id = user_id'
        ] );
        
        $users = $this->Delivery->Users->find ( 'all', [
        		'limit' => 200,
        		'conditions' => [
        				'user_type' => 4,
        				'id NOT IN' => $subquery
        		]
        ] )->select ( [
        		'id',
        		'username'
        ] )->formatResults ( function ($results) {
        	/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
        	return $results->combine ( 'id', function ($row) {
        		return $row ['username'];
        	} );
        } );
        
        $this->set(compact('delivery', 'users'));
        $this->set('_serialize', ['delivery']);
        
        $cities = $this->Delivery->City->find ()->select ( [
        		'cid',
        		'cname'
        ] )->formatResults ( function ($results) {
        	/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
        	return $results->combine ( 'cid', function ($row) {
        		return $row ['cname'];
        	} );
        } );
        $this->set ( compact ( 'cities' ) );
    }

    /**
     * Edit method
     *
     * @param string|null $id Delivery id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $delivery = $this->Delivery->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $delivery = $this->Delivery->patchEntity($delivery, $this->request->data);
            if ($this->Delivery->save($delivery)) {
                $this->Flash->success(__('The delivery has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The delivery could not be saved. Please, try again.'));
            }
        }
        //$users = $this->Delivery->Users->find('list', ['limit' => 200]);
        // I think this query should be on MODAL, but-->get all user that relation with supplier table,
        
        $subquery = $this->Delivery->find ()->select ( 'user_id' )->hydrate ( false )->join ( [
        		'table' => 'users',
        		'alias' => 'u',
        		'type' => 'INNER',
        		'conditions' => 'u.id = user_id'
        ] );
        
        $users = $this->Delivery->Users->find ( 'all', [
        		'limit' => 200,
        		'conditions' => [
        				'user_type' => 4/* ,
        				'id NOT IN' => $subquery */
        		]
        ] )->select ( [
        		'id',
        		'username'
        ] )->formatResults ( function ($results) {
        	/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
        	return $results->combine ( 'id', function ($row) {
        		return $row ['username'];
        	} );
        } );
        $this->set(compact('delivery', 'users'));
        $this->set('_serialize', ['delivery']);
        
        $cities = $this->Delivery->City->find ()->select ( [
        		'cid',
        		'cname'
        ] )->formatResults ( function ($results) {
        	/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
        	return $results->combine ( 'cid', function ($row) {
        		return $row ['cname'];
        	} );
        } );
        $this->set ( compact ( 'cities' ) );
    }

    /**
     * Delete method
     *
     * @param string|null $id Delivery id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $delivery = $this->Delivery->get($id);
        if ($this->Delivery->delete($delivery)) {
            $this->Flash->success(__('The delivery has been deleted.'));
        } else {
            $this->Flash->error(__('The delivery could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
