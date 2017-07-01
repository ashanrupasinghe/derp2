<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Customers Controller
 *
 * @property \App\Model\Table\CustomersTable $Customers
 */
class CustomersController extends AppController {

    // In a controller
    // Make the new component available at $this->Math,
    // as well as the standard $this->Csrf
    /* public function initialize()
      {
      parent::initialize();
      $this->loadComponent('Notification');

      } */


    public function isAuthorized($user) {


        // The owner of an article can edit and delete it
        if (in_array($this->request->action, ['add', 'edit', 'delete', 'view', 'index', 'search', 'result', 'check', 'import'])) {

            if (isset($user['user_type']) && $user['user_type'] == 2) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

    public function initialize() {
        parent::initialize();
        $this->loadComponent('Cewi/Excel.Import');
        ini_set('memory_limit', '256M');
        //set_time_limit(0); Infinite
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {

        /* $this->Notification->setNotification();

          die(); */
		$customer = $this->Customers->get(1);
		
        $customers = $this->paginate($this->Customers);
		$s = $this->request->query('s');	
		$phone= SUBSTR($s, 1);
		
	
		if (! empty ( $s )) {
			$customers=$this->paginate($this->Customers, ['conditions' => [
					'OR' => [
							'Customers.firstName LIKE' => '%' . $s . '%',
							'Customers.lastName LIKE' => '%' . $s . '%',
							//'Customers.customer_full_name LIKE'=>'%' . $s . '%',
							'Customers.mobileNo =' => $s,
							//'Customers.mobileNo =' => $phone,
					]]
			
			]);
			
			if($customers->isEmpty()){
				$this->Flash->error ( __ ( 'No Result Found, Please Add New Client' ) );
			}
			
		}
		$this->set ( 's', $s );
        $this->set(compact('customers'));
        $cities_query = $this->Customers->City->find('list', ['keyField' => 'cid', 'valueField' => 'cname']);
        $city = $cities_query->toArray();
        $this->set('cities', $city);
        $this->set('_serialize', [
            'customers'
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id
     *        	Customer id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $customer = $this->Customers->get($id, [
            'contain' => ['city']
        ]);
        /* 	print '<pre>';
          print_r($customer);
          die(); */
        $this->set('customer', $customer);
        $this->set('_serialize', [
            'customer'
        ]);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $customer = $this->Customers->newEntity();
        if ($this->request->is('post')) {
            $customer = $this->Customers->patchEntity($customer, $this->request->data);
            if ($this->Customers->save($customer)) {
                $this->Flash->success(__('The customer has been saved.'));

                return $this->redirect([
                            'action' => 'index'
                ]);
            } else {
                $this->Flash->error(__('The customer could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('customer'));
        $this->set('_serialize', [
            'customer'
        ]);

        $cities = $this->Customers->City->find()->select([
                    'cid',
                    'cname'
                ])->formatResults(function ($results) {
            /* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
            return $results->combine('cid', function ($row) {
                        return $row ['cname'];
                    });
        });
        $this->set(compact('cities'));
    }

    /**
     * Edit method
     *
     * @param string|null $id
     *        	Customer id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $customer = $this->Customers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is([
                    'patch',
                    'post',
                    'put'
                ])) {
            $customer = $this->Customers->patchEntity($customer, $this->request->data);
            if ($this->Customers->save($customer)) {
                $this->Flash->success(__('The customer has been saved.'));

                return $this->redirect([
                            'action' => 'index'
                ]);
            } else {
                $this->Flash->error(__('The customer could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('customer'));
        $this->set('_serialize', [
            'customer'
        ]);

        $cities = $this->Customers->City->find()->select([
                    'cid',
                    'cname'
                ])->formatResults(function ($results) {
            /* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
            return $results->combine('cid', function ($row) {
                        return $row ['cname'];
                    });
        });
        $this->set(compact('cities'));
    }

    public function search() {
        $ordersModel = $this->loadModel('Orders');
        $orders = $this->paginate($ordersModel, ['contain' => 'customers', 'order' => ['Orders.modified' => 'DESC'], 'conditions' => ['Orders.status NOT IN' => [6, 9], 'deleted =' => 0]]);
        /* print '<pre>';
          print_r($orders);
          die(); */
        $callcenter_query = $ordersModel->Callcenter->find('list', ['keyField' => 'id', 'valueField' => 'users.username'])->select(['id', 'users.username'])
                ->join([
            'table' => 'users',
            'alias' => 'users',
            'type' => 'INNER',
            'conditions' => 'user_id = users.id'
        ]);
        $callcenters = $callcenter_query->toArray();

        $this->set('callcenters', $callcenters);

        $delivery_query = $ordersModel->Delivery->find('list', ['keyField' => 'id', 'valueField' => 'users.username'])->select(['id', 'users.username'])
                ->join([
            'table' => 'users',
            'alias' => 'users',
            'type' => 'INNER',
            'conditions' => 'user_id = users.id'
        ]);
        $deliveries = $delivery_query->toArray();

        $this->set('deliveries', $deliveries);


        $cities_query = $ordersModel->City->find('list', ['keyField' => 'cid', 'valueField' => 'cname']);
        $city = $cities_query->toArray();
        $this->set('cities', $city);

        $this->set(compact('orders'));
        $this->set('_serialize', [
            'orders'
        ]);

        /*
         * $customer = $this->Customers->get($phone_name);
         * print_r($customer);
         * die();
         */
        // https://github.com/friendsofcake/search#table-class
        // $this->set(compact('customer'));
        // $this->set('_serialize', ['customer']);
    }

    public function result() {
        $customers = $this->paginate($this->Customers);
        $s = $this->request->data('s');
        if (!empty($s)) {
            $customers = $this->Customers->find('all', [
                'conditions' => [
                    'OR' => [
                        'Customers.firstName LIKE' => '%' . $s . '%',
                        'Customers.lastName LIKE' => '%' . $s . '%',
                        'Customers.mobileNo =' => $s
                    ]
                ]
            ]);
            /* print '<pre>';
              print_r($customers);
              print '</pre>'; */
            if ($customers->isEmpty()) {
                $this->Flash->error(__('No Result Found, Please Add New Client'));
            }
            $this->set('customers', $customers);
            $this->set('s', $s);
            $this->set('_serialize', [
                'customers'
            ]);
        } else {
            $this->Flash->error(__('Add a Name or Phone. Please'));
            return $this->redirect([
                        'action' => 'search'
            ]);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id
     *        	Customer id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod([
            'post',
            'delete'
        ]);
        $customer = $this->Customers->get($id);
        if ($this->Customers->delete($customer)) {
            $this->Flash->success(__('The customer has been deleted.'));
        } else {
            $this->Flash->error(__('The customer could not be deleted. Please, try again.'));
        }

        return $this->redirect([
                    'action' => 'index'
        ]);
    }

    //set customer id to session
    public function check($id) {
        $this->request->allowMethod([
            'post'
        ]);
        $clientid = $this->Customers->get($id);
        $session = $this->request->session();
        //$session->read('Config.language');
        $session->write('Config.clientid', $id);
        return $this->redirect(['controller' => 'orders', 'action' => 'add']);
    }

    /**
     * import data from a excel sheet
     */
    public function importnew() {

        if ($this->request->is('post')) {
            if (!empty($this->request->data('customersSheet'))) {
                $file = $this->request->data('customersSheet.tmp_name');
                $data = $this->Import->prepareEntityData($file, ['append' => true]);

                $customers = [];
                $count = 0;
                foreach ($data as $customer) {
                    if (isset($customer['id']) && $customer['id'] != "") {
                        $customers[$count]['id'] = $customer['id']; //if use id col, u can update data.
                    }
                    if (isset($customer['firstName']) && $customer['firstName'] != "") {
                        $customers[$count]['firstName'] = $customer['firstName']; //firstName
                    }
                    if (isset($customer['lastName']) && $customer['lastName'] != "") {
                        $customers[$count]['lastName'] = $customer['lastName']; //lastName
                    }
                    if (isset($customer['address']) && $customer['address'] != "") {
                        $customers[$count]['address'] = $customer['address']; //address
                    }
                    if (isset($customer['city']) && $customer['city'] != "") {
                        $customers[$count]['city'] = intval($customer['city']); //city[city id]
                    }
                    if (isset($customer['latitude']) && $customer['latitude'] != "") {
                        $customers[$count]['latitude'] = $customer['latitude']; //latitude
                    }
                    if (isset($customer['longitude']) && $customer['longitude'] != "") {
                        $customers[$count]['longitude'] = $customer['longitude']; //longitude
                    }
                    if (isset($customer['email']) && $customer['email'] != "") {
                        $customers[$count]['email'] = $customer['email']; //email
                    }
                    if (isset($customer['mobileNo']) && $customer['mobileNo'] != "") {
                        $customers[$count]['mobileNo'] = $customer['mobileNo']; //mobileNo
                    }
                    if (isset($customer['created']) && $customer['created'] != "") {
                        $customers[$count]['created'] = $customer['created']; //created
                    }/* else{
                      $customers[$count]['created']=date('d/m/y,g:i A');
                      } */
                    if (isset($customer['modified']) && $customer['modified'] != "") {
                        $customers[$count]['modified'] = $customer['modified']; //modified
                    }/*
                      else{
                      $customers[$count]['modified']=date('d/m/y,g:i A');
                      } */
                    if (isset($customer['status']) && $customer['status'] != "") {
                        $customers[$count]['status'] = $customer['status']; //status[1,0]
                    }

                    $count++;
                }
                /* print '<pre>';
                  print_r($customers);
                  die(); */
                if (sizeof($customers) > 0) {
                    $customers_entities = $this->Customers->newEntities($customers);

                    $customers_save = $this->Customers->saveMany($customers_entities);

                    if ($customers_save) {
                        $this->Flash->success(__('customers save successfully.'));
                    } else {
                        $this->Flash->error(__('customers not save.'));
                    }
                } else {
                    $this->Flash->error(__('no customers to import.'));
                }
            } else {
                //$this->Flash->error(__('Please select an EXCEl file'));
                return $this->redirect(['action' => 'import']);
            }
        }
    }

    public function import() {
        if ($this->request->is('post')) {
            
            $this->loadModel('Users');
            $this->loadModel('Cart');

            if (!empty($this->request->data('customersSheet'))) {

                $file = $this->request->data('customersSheet.tmp_name');
                // echo $file;
                /*
                 * $skufinder=$this->Products->find('all',['conditions'=>['sku'=>'Kolikuttu banana'],'fields'=>['id']]);
                 * $skucount=$skufinder->count();
                 * echo $skucount;
                 * die();
                 */



                $data = $this->Import->prepareEntityData($file, [
                    'append' => true
                ]);
                $customers = [];
                $users = [];
                $cart = [];
                $count = 0;

                $user_id = 70;

                foreach ($data as $product) {
                    $sub_users = $sub_cart = $sub_customer = array();
                    
                    $emailfinder = $this->Users->find('all', [
                        'conditions' => [
                            'username' => $product ['email']
                        ]
                    ]);
                    $emailcount = $emailfinder->count();
                    /*
                     * print '<pre>';
                     * print_r($skufinder->toArray());
                     */
                    // echo $skucount."<br>";
                    // echo $product['name']."<br>";

                    if ($emailcount > 0) {
                        
                    } else {
                        $sub_users ['username'] = $product ['email'];
                        $sub_users ['user_type'] = 5;
                        $sub_users ['password'] = md5(rand());
                        //$users [$count] ['created'] = $users [$count] ['modified'] = date('Y-m-d H:i:s', strtotime($product ['created_at']));

                        $sub_customer ['user_id'] = $user_id;
                        $sub_customer ['firstName'] = $product ['firstname'];
                        $sub_customer ['lastName'] = $product ['lastname'];
                        //$customers [$count] ['created'] = $customers [$count] ['modified'] = date('Y-m-d H:i:s', strtotime($product ['created_at']));

                        $sub_cart ['user_id'] = $user_id;
                        //$cart [$count] ['created'] = $cart [$count] ['modified'] = date('Y-m-d H:i:s', strtotime($product ['created_at']));

                        $count ++;
                        $user_id++;
                    }
                    $users[] = $sub_users;
                    $cart[] = $sub_cart;
                    $customers[] = $sub_customer;
                }

//                  print '<pre>';
//                  print_r($users);
//                  print_r($customers);
//                  print_r($cart);
//                    die();


                //$product_entities = $this->Users->newEntities($users);
                //$product_save = $this->Users->saveMany($product_entities);
                
                $customer_entities = $this->Customers->newEntities($customers);
                    $customer_save = $this->Customers->saveMany($customer_entities);
                    
                $product_entities = $this->Users->newEntities($users);
                $product_save = $this->Users->saveMany($product_entities);

                //print '<pre>';
                echo $customer_save;
                die();
//                     

                if ($product_save) {

                    $customer_entities = $this->Customers->newEntities($customers);
                    $customer_save = $this->Customers->saveMany($customer_entities);

                    $cart_entities = $this->Cart->newEntities($cart);
                    $cart_save = $this->Cart->saveMany($cart_entities);

                    if ($cart_save) {
                        $this->Flash->success(__('Cart save successfully.'));
                    } else {
                        $this->Flash->error(__('Cart not save.'));
                    }
                } else {
                    $this->Flash->error(__('Users not save'));
                }
            }
            $this->Flash->error(__('Please select an EXCEl file'));
            return $this->redirect([
                        'action' => 'import'
            ]);
        }
    }

}
