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


	
    public function isAuthorized($user) {
        if (in_array($this->request->action, ['index', 'changeStatus'])) {
            if (isset($user['user_type']) && ($user['user_type'] == 2 || $user['user_type'] == 1)) {
                return true;
            }
        }
		
        return parent::isAuthorized($user);
    }
	
	
	 public function initialize() {
        parent::initialize();
    }
	
	 public function beforeFilter(\Cake\Event\Event $event) {
		parent::beforeFilter($event);
        // allow all action
        $this->Auth->allow([
            'getboolianconfiguration',
			'getunavailabledates'
            
        ]);
    }

    

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $configurations = $this->paginate($this->BoolianConfigurations);
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
	
	public function getboolianconfiguration(){
		header('Content-type: application/json');
		$key=$this->request->data('key');
		$config = $this->BoolianConfigurations->find('all',['contitions'=>['config_key'=>$key]])->first();
		echo json_encode(['status'=>0,'value'=>$config->config_value]);
		die();
	}
	
	public function getunavailabledates(){
		header('Content-type: application/json');

		$unavailable_model=$this->loadModel('Unavailabledate');
		$dates=$unavailable_model->find('all')->select('date')->toArray();
		 $formated_dates=array_map(function($date_time){
			return date('Y-m-d',strtotime($date_time->date));
		},$dates);
		 	//$formated_dates=["2017-10-3", "2017-10-13", "2017-10-25", "2017-10-20"];
		echo json_encode(['status'=>'0','dates'=>$formated_dates]);
		die();
	}
	 
	 
	

  

}
