<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
/**
 * SupplierNotifications Controller
 *
 * @property \App\Model\Table\SupplierNotificationsTable $SupplierNotifications
 */
class SupplierNotificationsController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Notification');
	
	}
	public function isAuthorized($user) {
	
		// The owner of an article can edit and delete it
		if (in_array ( $this->request->action, [
				
				'edit',				
				'view',
				'listnotifications',
				'schedule'
				
				
		] )) {
			if (isset ( $user ['user_type'] ) && $user ['user_type'] == 3) {
				return true;
			}
			/* $supplier_query=$this->SupplierNotifications->suppliers->find('all',['conditions'=>['user_id'=>$user['id']]])->contain(['Users'])->first();
			$supplier=$supplier_query->toArray();
			if ($this->SupplierNotifications->isAssigned($supplier['id'])) {
				return true;
			} */
		}
	
		return parent::isAuthorized ( $user );
	}
//to add associated table fields use this array	
	public $paginate = [
			'sortWhitelist' => [
					'id', 'Orders.deliveryDate','Orders.deliveryTime'
			]
	];
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {   	
    	
    	
        $supplierNotifications = $this->paginate($this->SupplierNotifications);

        $this->set(compact('supplierNotifications'));
        $this->set('_serialize', ['supplierNotifications']);
    }

    /**
     * View method
     *
     * @param string|null $id Supplier Notification id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $supplierNotification_orderID = $this->SupplierNotifications->get($id, [
            'contain' => ['Suppliers.OrderProducts']
        ])->orderId;
        //print '<pre>';
        //print_r($supplierNotificationx);
        //die();
        /* $supplierNotification = $this->SupplierNotifications->get($id, [
        		'contain' => ['Suppliers.OrderProducts'=>['conditions'=>['order_id'=>$supplierNotification_orderID],'Suppliers.OrderProducts.Products','Suppliers.OrderProducts.Products.packageType']]
        ]); */
        $supplierNotification = $this->SupplierNotifications->get($id, [
        		'contain' =>['Suppliers.OrderProducts'=>['conditions'=>['order_id'=>$supplierNotification_orderID]],'Suppliers.OrderProducts.Products','Suppliers.OrderProducts.Products.packageType','Orders'],
        		 
        ]);
        ///print_r($supplierNotification);
        //die();
        

        $this->set('supplierNotification', $supplierNotification);
        $this->set('_serialize', ['supplierNotification']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $supplierNotification = $this->SupplierNotifications->newEntity();
        if ($this->request->is('post')) {
            $supplierNotification = $this->SupplierNotifications->patchEntity($supplierNotification, $this->request->data);
            if ($this->SupplierNotifications->save($supplierNotification)) {
                $this->Flash->success(__('The supplier notification has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The supplier notification could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('supplierNotification'));
        $this->set('_serialize', ['supplierNotification']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Supplier Notification id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
    	$supplierNotification_orderID = $this->SupplierNotifications->get($id, [
    			'contain' => ['Suppliers.OrderProducts']
    	])->orderId;
    	
       /*  $supplierNotification = $this->SupplierNotifications->get($id, [
            'contain' => ['Suppliers.OrderProducts'=>['conditions'=>['order_id'=>$supplierNotification_orderID],'Suppliers.OrderProducts.Products','Suppliers.OrderProducts.Products.packageType']]
        ]); */
    	
    	$supplierNotification = $this->SupplierNotifications->get($id, [
    			'contain' =>['Suppliers.OrderProducts'=>['conditions'=>['order_id'=>$supplierNotification_orderID]],'Suppliers.OrderProducts.Products','Suppliers.OrderProducts.Products.packageType','Orders'],
    			
    	]);
        
        
        
        
       /*  print '<pre>';
        print_r($supplierNotification);
        die(); */
        
        if ($this->request->is(['patch', 'post', 'put'])) {
        	
        	$data=$this->request->data;
        	 /*  print '<pre>';
        	 print_r($data);
        	 die(); */ 
        	
        	$updatable_data=[];
        	$orderProductsModel=$this->loadModel('OrderProducts');
        	
        	foreach ($data['mystatus'] as $product_id=>$product_status){
        		$current_status=$orderProductsModel->get([$data['orderId'],$product_id],['fields'=>['status_s']])->toArray();//$current_status['status_s']
        		if($current_status['status_s']==$product_status){
        			continue ;//if current tatus equals to new status return
        		}
        		/*Notification function xxx yy z*/
        		//$this->Notification->setNotification();die();
        		$updatable_data[]=['order_id'=>$data['orderId'],'product_id'=>$product_id,'status_s'=>$product_status];
        		//$user_id=$this->Auth->user('id');
        		//$this->Notification->setNotification('',$product_status,'',$data['orderId'] ,$product_id,$user_id,'');//send notification
        	} 

        	/*  print '<pre>';
        	print_r($updatable_data);
        	die(); */
        	if(sizeof($updatable_data)==0){
        		$this->Flash->error(__('The supplier notification could not be saved. Please, change values.'));
        	}else{
        	$entities = $orderProductsModel->newEntities($updatable_data);//update multiple rows same time using saveMeny
            if ($orderProductsModel->saveMany($entities)) {
            	
            	/*
            	 * save status of notification [checke status for change color]
            	 * */
            	$notification=$this->SupplierNotifications->get($id);
            	$notification->status=1;
            	$this->SupplierNotifications->save($notification);
            	
            	//update order table if all suppliers checked the notifications;
            	$all_order_query=$this->SupplierNotifications->find('all',['conditions'=>['orderId'=>$data['orderId']]]);
            	$checked_notification_query=$this->SupplierNotifications->find('all',['conditions'=>['orderId'=>$data['orderId'],'status'=>1]]);
            	$all_order_count=$all_order_query->count();
            	$checked_notification_count=$checked_notification_query->count();
            	if ($all_order_count==$checked_notification_count){
            		$order=$this->SupplierNotifications->Orders->get($data['orderId']);
            		$order->status=3;
            		$this->SupplierNotifications->Orders->save($order);
            	}
            	
            	/*new notification for callcenter without delivery,
            	 * order id xxx ready form supplier yyy
            	*/
            	$user_id=$this->Auth->user('id');
            	$this->Notification->setNotification('','','',$data['orderId'] ,'',$user_id,'','');//send notification
            	
                $this->Flash->success(__('The supplier notification has been saved.'));

                return $this->redirect(['action' => 'listnotifications']);
            } else {
                $this->Flash->error(__('The supplier notification could not be saved. Please, try again.'));
            }
        }
        }
        $this->set(compact('supplierNotification'));
        $this->set('_serialize', ['supplierNotification']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Supplier Notification id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $supplierNotification = $this->SupplierNotifications->get($id);
        if ($this->SupplierNotifications->delete($supplierNotification)) {
            $this->Flash->success(__('The supplier notification has been deleted.'));
        } else {
            $this->Flash->error(__('The supplier notification could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function listnotifications($type=""){
    	/* $user_id=$this->Auth->user('id');
    	$this->Notification->setNotification('',4,'',174,6,$user_id,'');die(); */
    	
    	$status="";
    	if ($type=="pending" || $type=="new-pending"){
    		$status=0;
    	}elseif ($type=='available'){
    		$status=1;
    	}elseif ($type=='not-available'){
    		$status=2;
    	}elseif ($type=='ready'){
    		$status=3;
    	}elseif ($type=='delivery-hand-over'){
    		$status=4;
    	}elseif ($type=='canceled' || $type=="new-canceled"){
    		$status=9;
    	}
/*     	$myquery="SELECT DISTINCT op.product_id,prod.name,op.product_quantity,pt.type,sn.status_s,op.order_id,ps.supplier_id ".
    	  "FROM supplier_notifications sn ". 
    	  "JOIN product_suppliers ps ON sn.supplierId=ps.supplier_id ". 
    	  "JOIN order_products op ON ps.product_id=op.product_id ".     	  
    	  "JOIN products prod ON prod.id=op.product_id ".
    	  "JOIN package_type pt ON pt.id=prod.package ".
    	  "WHERE op.order_id=sn.orderId"; */
    	$user_id=$this->Auth->user('id');
    	$supplier_query=$this->SupplierNotifications->suppliers->find('all',['conditions'=>['user_id'=>$user_id]])->contain(['Users'])->first();
    	$supplier=$supplier_query->toArray();//get loged in supplier data
        /*
        $subQuery=$this->SupplierNotifications->find('list',['fields'=>['distinct op.product_id','pr.name','op.product_quantity','pt.type','status_s','op.order_id','ps.supplier_id']]) ->distinct(['op.product_id']) 
        ->join(['table'=>'product_suppliers','alias'=>'ps','type'=>'INNER','conditions'=>'supplierId=ps.supplier_id'])
        ->join(['table'=>'order_products','alias'=>'op','type'=>'INNER','conditions'=>'ps.product_id=op.product_id'])
        ->join(['table'=>'products','alias'=>'pr','type'=>'INNER','conditions'=>'pr.id=op.product_id'])
        ->join(['table'=>'package_type','alias'=>'pt','type'=>'INNER','conditions'=>'pt.id=pr.package']);
        
        $query=$this->SupplierNotifications->find('all')->join(['table'=>$subQuery,'alias'=>'sub','type'=>'INNER','conditions'=>'ps__supplier_id=supplierID']);
        echo $query;
    	*/
    	$conditions=['SupplierId'=>$supplier['id']];
    	if ($type!=""){
    		$conditions['status_s']=$status;
    	}
    	if ($type!="" &&($type=="new-pending" || $type=="new-canceled")){    		
    		$conditions['modified >']=new \DateTime('-24 hours');
    	}
    	
    	
        $supplierNotifications = $this->paginate($this->SupplierNotifications,['conditions'=>['SupplierNotifications.SupplierId'=>$supplier['id'],'SupplierNotifications.deleted ='=>0],'contain'=>['Orders'],'order' => ['Orders.deliveryDate' => 'ASC','Orders.deliveryTime'=>'ASC']]);
    	$this->set(compact('supplierNotifications'));
    	$this->set('_serialize', ['supplierNotifications']);
    }
    
    
    public function schedule($type=""){
    	/* $user_id=$this->Auth->user('id');
    	 $this->Notification->setNotification('',4,'',174,6,$user_id,'');die(); */
    	 
    	$status="";
    	if ($type=="pending" || $type=="new-pending"){
    		$status=0;
    	}elseif ($type=='available'){
    		$status=1;
    	}elseif ($type=='not-available'){
    		$status=2;
    	}elseif ($type=='ready'){
    		$status=3;
    	}elseif ($type=='delivery-hand-over'){
    		$status=4;
    	}elseif ($type=='canceled' || $type=="new-canceled"){
    		$status=9;
    	}
    	/*     	$myquery="SELECT DISTINCT op.product_id,prod.name,op.product_quantity,pt.type,sn.status_s,op.order_id,ps.supplier_id ".
    	 "FROM supplier_notifications sn ".
    	 "JOIN product_suppliers ps ON sn.supplierId=ps.supplier_id ".
    	 "JOIN order_products op ON ps.product_id=op.product_id ".
    	 "JOIN products prod ON prod.id=op.product_id ".
    	 "JOIN package_type pt ON pt.id=prod.package ".
    	 "WHERE op.order_id=sn.orderId"; */
    	$user_id=$this->Auth->user('id');
    	$supplier_query=$this->SupplierNotifications->suppliers->find('all',['conditions'=>['user_id'=>$user_id]])->contain(['Users'])->first();
    	$supplier=$supplier_query->toArray();//get loged in supplier data
    	/*
    	 $subQuery=$this->SupplierNotifications->find('list',['fields'=>['distinct op.product_id','pr.name','op.product_quantity','pt.type','status_s','op.order_id','ps.supplier_id']]) ->distinct(['op.product_id'])
    	 ->join(['table'=>'product_suppliers','alias'=>'ps','type'=>'INNER','conditions'=>'supplierId=ps.supplier_id'])
    	 ->join(['table'=>'order_products','alias'=>'op','type'=>'INNER','conditions'=>'ps.product_id=op.product_id'])
    	 ->join(['table'=>'products','alias'=>'pr','type'=>'INNER','conditions'=>'pr.id=op.product_id'])
    	 ->join(['table'=>'package_type','alias'=>'pt','type'=>'INNER','conditions'=>'pt.id=pr.package']);
    
    	 $query=$this->SupplierNotifications->find('all')->join(['table'=>$subQuery,'alias'=>'sub','type'=>'INNER','conditions'=>'ps__supplier_id=supplierID']);
    	 echo $query;
    	*/
    	$conditions=['SupplierId'=>$supplier['id']];
    	if ($type!=""){
    		$conditions['status_s']=$status;
    	}
    	if ($type!="" &&($type=="new-pending" || $type=="new-canceled")){
    		$conditions['modified >']=new \DateTime('-24 hours');
    	}
    	 
    	 
    	$supplierNotifications = $this->paginate($this->SupplierNotifications,['conditions'=>['SupplierNotifications.SupplierId'=>$supplier['id'],'Orders.status < '=>4,'SupplierNotifications.deleted ='=>0],'contain'=>['Orders'],'order' => ['Orders.deliveryDate' => 'ASC','Orders.deliveryTime' => 'ASC']]);
    	$this->set(compact('supplierNotifications'));
    	$this->set('_serialize', ['supplierNotifications']);
    }
}
