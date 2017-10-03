<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Sessions;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 */
class BoolianConfigurationsController extends AppController {

 public function beforeFilter(\Cake\Event\Event $event) {
		parent::beforeFilter($event);
        // allow all action
        $this->Auth->allow([
            'getboolianconfiguration'
            
        ]);
    }
	
    public function isAuthorized($user) {
        if (in_array($this->request->action, ['index', 'changeStatus','deletedate','adddate'])) {
            if (isset($user['user_type']) && ($user['user_type'] == 2 || $user['user_type'] == 1)) {
                return true;
            }
        }
        return parent::isAuthorized($user);
    }

     public function initialize() {
        parent::initialize();
        $this->loadComponent('Notification');
        $this->loadComponent('RequestHandler');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $configurations = $this->paginate($this->BoolianConfigurations);
        
		
		$unavailable_model=$this->loadModel('Unavailabledate');
		$unavailable_dates=$unavailable_model->find('all');		
		$dates = $this->paginate($unavailable_model);
        $this->set(compact('unavailable_dates'));
        $this->set(compact('configurations'));
        $this->set('_serialize', ['configurations']);
    }
	
	public function changeStatus($id = null){
		$this->request->allowMethod(['post']);
        $config = $this->BoolianConfigurations->get($id);
		$config->config_value = !$config->config_value;
		
        if ($this->BoolianConfigurations->save($config)) {
            $this->Flash->success(__('Status changed successfully'));
        } else {
            $this->Flash->error(__('Could not change the status. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
	}
	
	public function getboolianconfiguration($id ){
		header('Content-type: application/json');
		$config = $this->BoolianConfigurations->get($id);
		echo json_encode(['status'=>0,'value'=>$config->config_value]);
	}
	/*delete unavailable dates*/
	    public function deletedate($id = null) {
        $this->request->allowMethod([
            'post',
            'delete'
        ]);
		$unavailable_model=$this->loadModel('Unavailabledate');
        $date = $unavailable_model->get($id);
		
        if ($unavailable_model->delete($date)) {
            $this->Flash->success(__('The unavailable date has been deleted.'));
        } else {
            $this->Flash->error(__('The unavailable date could not be deleted. Please, try again.'));
        }

        return $this->redirect([
                    'action' => 'index'
                ]);
    }
	
	public function adddate(){
		$unavailable_model=$this->loadModel('Unavailabledate');
		$date = $unavailable_model->newEntity();
        if ($this->request->is('post')) {
				$date = $unavailable_model->patchEntity($date, $this->request->data);
				if ($unavailable_model->save($date)) {
					$this->Flash->success(__('The date has been saved.'));
					return $this->redirect(['action' => 'index']);
				}
		} else {
            $this->Flash->error(__('The date could not be saved. Please, try again.'));
        }

	}
	
	 

  

}
