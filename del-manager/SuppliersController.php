<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Suppliers Controller
 *
 * @property \App\Model\Table\SuppliersTable $Suppliers
 */
class SuppliersController extends AppController {
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
	public function index() {
		$this->paginate = [ 
				'contain' => [ 
						'Users',
						'city' 
				] 
		];
		$suppliers = $this->paginate ( $this->Suppliers );
		
		$this->set ( compact ( 'suppliers' ) );
		$this->set ( '_serialize', [ 
				'suppliers' 
		] );
	}
	
	/**
	 * View method
	 *
	 * @param string|null $id
	 *        	Supplier id.
	 * @return \Cake\Network\Response|null
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function view($id = null) {
		$supplier = $this->Suppliers->get ( $id, [ 
				'contain' => [ 
						'Users','city' 
				] 
		] );
		
		$this->set ( 'supplier', $supplier );
		$this->set ( '_serialize', [ 
				'supplier' 
		] );
	}
	
	/**
	 * Add method
	 *
	 * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
	 */
	public function add() {
		$supplier = $this->Suppliers->newEntity ();
		if ($this->request->is ( 'post' )) {
			
			/*
			 * $this->loadModel('Users');
			 * $user = $this->Users->newEntity();
			 * $user->username = $this->request->data['username'];
			 * $user->password = $this->request->data['password'];
			 * $user->user_type = 3;
			 * $user_id = $this->Users->save($user);
			 */
			// echo $user_id;
			// die();
			/*
			 * if ($user_id) {
			 * unset($this->request->data['username']);
			 * unset($this->request->data['password']);
			 */
			// SELECT * FROM users WHERE id NOT IN(SELECT user_id FROM `users` as u JOIN suppliers as c WHERE u.id=c.user_id)
			
			$supplier = $this->Suppliers->patchEntity ( $supplier, $this->request->data );
			// $supplier->user_id = $user_id;
			if ($this->Suppliers->save ( $supplier )) {
				
				$this->Flash->success ( __ ( 'The supplier has been saved.' ) );
				
				return $this->redirect ( [ 
						'action' => 'index' 
				] );
			} else {
				$this->Flash->error ( __ ( 'The supplier could not be saved. Please, try again.' ) );
			}
			/*
			 * } else {
			 * $this->Flash->error(__('Username exists.'));
			 * }
			 */
		}
		/*
		 * $users = $this->Suppliers->Users->find('list', ['conditions'=>['user_type'=>3],'limit' => 200])
		 * ->select('id','username')
		 * ->formatResults( function ($results) {
		 * return $results->combine ('id',function ($row) {return $row ['username'];
		 * } );
		 * } ) ;
		 */
		// I think this query should be on MODAL, but-->get all user that relation with supplier table,
		$subquery = $this->Suppliers->find ()->select ( 'user_id' )->hydrate ( false )->join ( [ 
				'table' => 'users',
				'alias' => 'u',
				'type' => 'INNER',
				'conditions' => 'u.id = user_id' 
		] );
		
		$users = $this->Suppliers->Users->find ( 'all', [ 
				'limit' => 200,
				'conditions' => [ 
						'user_type' => 3,
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
		
		$this->set ( compact ( 'supplier', 'users' ) );
		$this->set ( '_serialize', [ 
				'supplier' 
		] );
		
		$cities = $this->Suppliers->City->find ()->select ( [ 
				'cid',
				'cname' 
		] )->formatResults ( function ($results) {
			/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			return $results->combine ( 'cid', function ($row) {
				return $row ['cname'];
			} );
		} );
		$this->set ( compact ( 'cities' ) );
		
		// debug($query->first());
	}
	
	/**
	 * Edit method
	 *
	 * @param string|null $id
	 *        	Supplier id.
	 * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function edit($id = null) {
		$supplier = $this->Suppliers->get ( $id, [ 
				'contain' => [ ] 
		] );
		if ($this->request->is ( [ 
				'patch',
				'post',
				'put' 
		] )) {
			$supplier = $this->Suppliers->patchEntity ( $supplier, $this->request->data );
			if ($this->Suppliers->save ( $supplier )) {
				$this->Flash->success ( __ ( 'The supplier has been saved.' ) );
				
				return $this->redirect ( [ 
						'action' => 'index' 
				] );
			} else {
				$this->Flash->error ( __ ( 'The supplier could not be saved. Please, try again.' ) );
			}
		}
		// I think this query should be on MODAL, but-->get all user that relation with supplier table,
		
		$subquery = $this->Suppliers->find ()->select ( 'user_id' )->hydrate ( false )->join ( [
				'table' => 'users',
				'alias' => 'u',
				'type' => 'INNER',
				'conditions' => 'u.id = user_id'
		] );
		
		$users = $this->Suppliers->Users->find ( 'all', [
				'limit' => 200,
				'conditions' => [
						'user_type' => 3/* ,
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
		
		
		$this->set ( compact ( 'supplier', 'users' ) );
		$this->set ( '_serialize', [ 
				'supplier' 
		] );
		
		$cities = $this->Suppliers->City->find ()->select ( [ 
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
	 * @param string|null $id
	 *        	Supplier id.
	 * @return \Cake\Network\Response|null Redirects to index.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function delete($id = null) {
		$this->request->allowMethod ( [ 
				'post',
				'delete' 
		] );
		$supplier = $this->Suppliers->get ( $id );
		if ($this->Suppliers->delete ( $supplier )) {
			$this->Flash->success ( __ ( 'The supplier has been deleted.' ) );
		} else {
			$this->Flash->error ( __ ( 'The supplier could not be deleted. Please, try again.' ) );
		}
		
		return $this->redirect ( [ 
				'action' => 'index' 
		] );
	}
}
