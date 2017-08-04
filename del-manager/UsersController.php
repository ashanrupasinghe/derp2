<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
	
	public function isAuthorized($user)
	{
	
	
		// The owner of an article can edit and delete it
		if (in_array($this->request->action, ['userpage'])) {
				
			if ($this->Auth->user()) {
				return true;
			}
		}
	
		return parent::isAuthorized($user);
	}
	
	
	/*
	public function beforeFilter(Event $event)
	{
		parent::beforeFilter($event);
		// Allow users to register and logout.
		// You should not add the "login" action to allow list. Doing so would
		// cause problems with normal functioning of AuthComponent.
		$this->Auth->allow(['add', 'logout']);
	}*/

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Callcenter', 'Delivery', 'Suppliers']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['logout']);
    }

    public function login()
    {
    	if (!$this->Auth->user()){
    		if ($this->request->is('post')) {
    			$user = $this->Auth->identify();
    			if ($user) {
    				if ($user['status']==1){
    				$this->Auth->setUser($user);
    				return $this->redirect($this->Auth->redirectUrl());
    				}else{
    					$this->Flash->error(__('Your account has been disabled'));
    				}
    			}else{
    			$this->Flash->error(__('Invalid username or password, try again'));
    			}
    		}
    	}
    	else{
    		
    		return $this->redirect(['controller' => 'Users', 'action' => 'userpage']);
    		
    	}
        
        
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
    public function userpage(){
    	if ($this->Auth->user()){
    		$userlevel = $this->Auth->user ( 'user_type' );
    		if($userlevel==1){
    			return $this->redirect(['controller' => 'Summary', 'action' => 'summary']);
    		}
    		if($userlevel==2){
    			return $this->redirect(['controller' => 'Customers', 'action' => 'search']);
    		}
    		if($userlevel==3){
    			$user_id=$this->Auth->user('id');
    			return $this->redirect(['controller' => 'SupplierNotifications', 'action' => 'schedule']);
    		}
    		if ($userlevel==4){
    			return $this->redirect(['controller' => 'DeliveryNotifications', 'action' => 'listSuppliervice']);
    		}
			if ($userlevel==6){
    			return $this->redirect(['controller' => 'Orders', 'action' => 'schedule']);
    		}
    	}else {
    		return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    	}
    	 
    	 
    }
}
