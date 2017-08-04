<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Callcenter Controller
 *
 * @property \App\Model\Table\CallcenterTable $Callcenter
 */
class CallcenterController extends AppController
{
	
	public function isAuthorized($user)
	{
	
	
		// The owner of an article can edit and delete it
		if (in_array($this->request->action, ['view'])) {
				
			if (isset($user['user_type']) && $user['user_type'] == 2) {
				return true;
			}
		}
		
		if (in_array($this->request->action, ['view'])) {
				
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
        $callcenter = $this->paginate($this->Callcenter);

        $this->set(compact('callcenter'));
        $this->set('_serialize', ['callcenter']);
    }

    /**
     * View method
     *
     * @param string|null $id Callcenter id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $callcenter = $this->Callcenter->get($id, [
            'contain' => ['Users','city']
        ]);

        $this->set('callcenter', $callcenter);
        $this->set('_serialize', ['callcenter']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $callcenter = $this->Callcenter->newEntity();
        if ($this->request->is('post')) {
            $callcenter = $this->Callcenter->patchEntity($callcenter, $this->request->data);
            if ($this->Callcenter->save($callcenter)) {
                $this->Flash->success(__('The callcenter has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The callcenter could not be saved. Please, try again.'));
            }
        }
        //$users = $this->Callcenter->Users->find('list', ['limit' => 200]);
        // I think this query should be on MODAL, but-->get all user that relation with supplier table,
        $subquery = $this->Callcenter->find ()->select ( 'user_id' )->hydrate ( false )->join ( [
        		'table' => 'users',
        		'alias' => 'u',
        		'type' => 'INNER',
        		'conditions' => 'u.id = user_id'
        ] );
        
        $users = $this->Callcenter->Users->find ( 'all', [
        		'limit' => 200,
        		'conditions' => [
        				'user_type' => 2,
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
        
        
        $this->set(compact('callcenter', 'users'));
        $this->set('_serialize', ['callcenter']);
        
        $cities = $this->Callcenter->City->find ()->select ( [
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
     * @param string|null $id Callcenter id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $callcenter = $this->Callcenter->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $callcenter = $this->Callcenter->patchEntity($callcenter, $this->request->data);
            if ($this->Callcenter->save($callcenter)) {
                $this->Flash->success(__('The callcenter has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The callcenter could not be saved. Please, try again.'));
            }
        }
        //$users = $this->Callcenter->Users->find('list', ['limit' => 200]);\
        // I think this query should be on MODAL, but-->get all user that relation with supplier table,
        $subquery = $this->Callcenter->find ()->select ( 'user_id' )->hydrate ( false )->join ( [
        		'table' => 'users',
        		'alias' => 'u',
        		'type' => 'INNER',
        		'conditions' => 'u.id = user_id'
        ] );
        
        $users = $this->Callcenter->Users->find ( 'all', [
        		'limit' => 200,
        		'conditions' => [
        				'user_type' => 2
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
        
        $this->set(compact('callcenter', 'users'));
        $this->set('_serialize', ['callcenter']);
        
        $cities = $this->Callcenter->City->find ()->select ( [
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
     * @param string|null $id Callcenter id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $callcenter = $this->Callcenter->get($id);
        if ($this->Callcenter->delete($callcenter)) {
            $this->Flash->success(__('The callcenter has been deleted.'));
        } else {
            $this->Flash->error(__('The callcenter could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
