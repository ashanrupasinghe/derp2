<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\SupplierNotification;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;
use Cake\I18n\Time;
use Cake\Core\Configure;
use Cake\I18n\Date;
use Cake\I18n\Number;
use App\Model\Entity\Order;

use PHPExcel;
use Symfony\Component\VarDumper\Cloner\Data;
/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 */
class OrdersController extends AppController {
	
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Notification');
		$this->loadComponent('RequestHandler');
	
	}
	
	public function isAuthorized($user) {
		
		// The owner of an article can edit and delete it
		if (in_array ( $this->request->action, [ 
				'add',
				/* 'edit', */
				'editorder',
				'delete',
				'view',
				'index',
				'productsuppliersbyid',
				'singlecal',
				'countSubTotal',
				'processdata',
				'cancel',
				'sendOrderemail',
				'send',
				'viewpdf',
				'schedule',
				'update',
				'notify',
				'notify2',
				'autoSendNotification',
				'getInvoice',
				'sendsuppliernotifications',
				'sendemails' 
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
	public function index() {
		$orders = $this->paginate ( $this->Orders,['contain'=>'customers','order' => ['Orders.deliveryDate' => 'ASC','Orders.deliveryTime' => 'ASC'],'conditions'=>['deleted ='=>0]] );
		/* print '<pre>';
		print_r($orders);
		die(); */
		$callcenter_query=$this->Orders->Callcenter->find('list',['keyField'=>'id','valueField'=>'users.username'])->select(['id','users.username'])
							->join ( [
				'table' => 'users',
				'alias' => 'users',
				'type' => 'INNER',	
				'conditions' => 'user_id = users.id'
		] );
							$callcenters=$callcenter_query->toArray();

							$this->set('callcenters',$callcenters);	
													
		$delivery_query=$this->Orders->Delivery->find('list',['keyField'=>'id','valueField'=>'users.username'])->select(['id','users.username'])
							->join ( [
				'table' => 'users',
				'alias' => 'users',
				'type' => 'INNER',	
				'conditions' => 'user_id = users.id'
		] );
							$deliveries=$delivery_query->toArray();

							$this->set('deliveries',$deliveries);	
						
		
		$cities_query=$this->Orders->City->find('list',['keyField'=>'cid','valueField'=>'cname']);
		$city=$cities_query->toArray();
		$this->set('cities',$city);

		$this->set ( compact ( 'orders' ) );
		$this->set ( '_serialize', [ 
				'orders' 
		] );
	}
	
	/**
	 * View method
	 *
	 * @param string|null $id
	 *        	Order id.
	 * @return \Cake\Network\Response|null
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function view($id = null) {
		$order = $this->Orders->get ( $id, [ 
				'contain' => [ 
						'OrderProducts',
						'callcenter',
						'delivery',
						'customers',
						'city',
						'OrderProducts.Products',
						'OrderProducts.Products.packageType', 
						'OrderProducts.Suppliers',
						'OrderProducts.Suppliers.city'
				] 
				
				
		] );
		
		//for pdf genarate;
		$this->viewBuilder()->options([
				'pdfConfig' => [
						'orientation' => 'portrait',
						'filename' => 'Order-' . $id.'.pdf'
				]
		]);
		
		/*  print '<pre>';
		print_r($order);
		die();  */
		$total=$this->countTotal($id);
		$this->set('total_pdf',$total);
		$this->set ( 'order', $order );
		$this->set ( '_serialize', [ 
				'order' 
		] );
	}
	
	
	/**
	 * 
	 */
	public function update($id = null,$delivery_id=null) {
		$order = $this->Orders->get ( $id, [
				'contain' => [
						'OrderProducts',
						'callcenter',
						'delivery',
						'customers',
						'city',
						'OrderProducts.Products',
						'OrderProducts.Products.packageType',
						'OrderProducts.Suppliers',
						'OrderProducts.Suppliers.city'
				]
	
	
		] );
		
		$pending_payment_orders=$this->Orders->find('all',['conditions'=>['Orders.paymentStatus'=>1,'Orders.customerId'=>$order->customerId,'Orders.id !='=>$id,'Orders.status'=>5,],'contain' => ['delivery','DeliveryNotifications']])->toArray();
	
		if ($this->request->is(['patch', 'post', 'put'])) {
			//$order = $this->Users->patchEntity($order, $this->request->data);
			$data=$this->request->data;
			$order->status=$data['status'];
			$order->paymentStatus=$data['paymentStatus'];
			$order_id=$id;
			if ($this->Orders->save($order)) {
				/*Notification function xxx yy z*/
				$this->Notification->setNotification($data['status'],'','',$order_id,'','','','');
				$this->Flash->success(__('The Order has been saved.'));
		
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The Order could not be saved. Please, try again.'));
			}
		}
		$notified=$this->Notification->isSentToDriver($id);//delevery
		$notified_sup=$this->Notification->isSentToSuppliers($id);//suppliers
		$notified_email=$this->Notification->isSentEmail($id);//email
		
		$this->set('notified',$notified);
		$this->set('notified_sup',$notified_sup);
		$this->set('notified_email',$notified_email);
		
		$total=$this->countTotal($id);
		$this->set('total_pdf',$total);
		$this->set ( 'order', $order );
		$this->set('pending_payment_orders',$pending_payment_orders);
		$deliveries = $this->Orders->Delivery->find ()->contain(['city'])->select ( [
				'id',
				'firstName',
				'lastName',
				'city.cname'
		] )->where(['status'=>1])->order(['rate' =>'DESC']) ->formatResults ( function ($results) {
		
			return $results->combine ( 'id', function ($row) {
				return $row ['firstName'] . ' ' . $row ['lastName'].' - '.$row['cid']['cname'];
			} );
		} );
		$this->set ( 'deliveries', $deliveries );
		$this->set ( '_serialize', [
				'order'
		] );
	}
	
	/*
	 * notify to delivery staff	 * 
	 * will not use, use notify 2 instead of this
	 * */
	public function notify($order_id,$delivery_staff_id=null){
		//send the notification
		/*Notification function xxx yy z*/
		$result=$this->Notification->setNotification('','','',$order_id,'','','',111);
		if($result){
			$order=$this->Orders->get($order_id);
			$order->status=7;//driver informed
			if($this->Orders->save($order)){
				$this->Flash->success ( __ ( 'Order status changed to driver notified.' ) );
			}else{
				$this->Flash->success ( __ ( 'Order status not changed to driver notified.' ) );
			}
			
			$this->Flash->success ( __ ( 'The notification has been sent.' ) );
		}
		else{
			$this->Flash->error ( __ ( 'The notification could not be sent. Please, try again.' ) );
		}
		return $this->redirect ( [
				'action' => 'index'
		] );
	}
	/*
	 * notify to delivery staff	 *
	 * 
	 * use insedead of notify
	 * */
	public function notify2(){
//first need update order table and delivery notifications table using selected deliver id
//then send notifications 
		if ($this->request->is(['patch', 'post', 'put'])) {
		//send the notification
		/*Notification function xxx yy z*/
		$order_id=$this->request->data('order_id');
		$delivery_id=$this->request->data('deliveryId');
		//echo $order_id.$delivery_id;
		$orderx=$this->Orders->get($order_id);
		$orderx->deliveryId=$delivery_id;
		$driverSave=$this->Orders->save($orderx);
		$query=$this->Orders->DeliveryNotifications;
		$driverNotifySave=$query->query()
		->update()
		->set(['deliveryId' => $delivery_id])
		->where(['orderId' => $order_id])
		->execute();
		
		
		if ($driverSave){		
		$result=$this->Notification->setNotification('','','',$order_id,'','','',111);
		/* debug($result);
		die(); */
		if($result){
			$order=$this->Orders->get($order_id);
			$order->status=7;//driver informed
			if($this->Orders->save($order)){
				$this->Flash->success ( __ ( 'Order status changed to driver notified.' ) );
			}else{
				$this->Flash->success ( __ ( 'Order status not changed to driver notified.' ) );
			}
				
			$this->Flash->success ( __ ( 'The notification has been sent.' ) );
		}
		else{
			$this->Flash->error ( __ ( 'The notification could not be sent. Please, try again.' ) );
		}		
		}else{
			$this->Flash->success ( __ ( 'driver culd not be changed, check again' ) );
		}
		return $this->redirect ( [
				'action' => 'index'
		] );
		}
	}	
	
	/**
	 * send notifications using chronjob
	 */
	public function autoSendNotification(){
		$this->Notification->sendNotifications();
	}
	
	/**
	 * Add method
	 *
	 * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
	 */
	public function add() {

		if($this->Auth->user ( 'user_type')==2){
		$session = $this->request->session ();
		$client_id = $session->read ( 'Config.clientid' );		
		$order = $this->Orders->newEntity ();
		$order_data=$this->request->data();
		$order_id="";
		
		//print '<pre>';
		//$this->sendOrderEmail('new',$suppliers_id,$delivery_id);
		//die();
		//print_r($order_data);
		//die();
		if(!empty($client_id)){
		if ($this->request->is ( 'post' )) {

			$data=$this->processdata($order_data);//rearrange data sets with count total
			/* debug($data);
			die(); */
			$products_price=$this->getProductPrice($order_data['product_name']);//get product price
			$delivery_id=$order_data['deliveryId'];//send for email
			$suppliers_id=$order_data['product_supplier'];//send for email
			$order = $this->Orders->patchEntity ( $order, $data );	
			$saving=$this->Orders->save ( $order );				
			/* 	 print '<pre>';
				print_r($order_data);
				die();  */
			if ($saving) {			
				$order_id=$order->id;
				//$session->destroy('Config.clientid');
				
				//delevery notification
				//$dilivery_id=$order->deliveryId;
				//set delivery id, one order has one delivery person
				$dilivery_notification=['deliveryId'=>$order->deliveryId,'notificationText'=>'del nofify','sentFrom'=>1,'orderId'=>$order->id];
				//create array for order_pruducts table
				$order_products=[];
				//supplier noification
				$supplier_notification=[];
				$supplerids=array_values(array_unique($order_data['product_supplier']));//get uniqu values of supplier ids
				
			/* 	print '<pre>';
				print_r($dilivery_notification);
				print_r($order_products);
				print_r($supplerids); 
				
				die();*/
				//set order products array, one order has many products
				  for($i=0;$i<sizeof($order_data['product_name']);$i++){
				 	//order_pruducts table
					$order_products[$i]=['order_id'=>$order->id,'product_id'=>$order_data['product_name'][$i],'product_quantity'=>$order_data['product_quantity'][$i],'product_price'=>$products_price[$i],'supplier_id'=>$order_data['product_supplier'][$i]];
										
				}  
				//set supplier notification array, one suplier has one notification per order
				for($j=0; $j<sizeof($supplerids);$j++){
					$supplier_notification[$j]=['supplierId'=>$supplerids[$j],'notificationText'=>'notify','sentFrom'=>1,'orderId'=>$order->id];
				}
				
				
	/* 			print '<pre>';
				print_r($dilivery_notification);
				print_r($order_products);
				print_r($supplier_notification);
				
				die(); */
				
				//print_r($saving);
				
				
				
				$order_product_entities = $this->Orders->OrderProducts->newEntities($order_products);
				$order_product_result = $this->Orders->OrderProducts->saveMany($order_product_entities);
				
				
				
				//$supplier_notification_entites=$this->Orders->SupplierNotifications->newEntities($supplier_notification);----commented-2017-06-21
				//$supplier_notification_result=$this->Orders->SupplierNotifications->saveMany($supplier_notification_entites);----commented-2017-06-21
				
				$dlilevery_notification_entity=$this->Orders->DeliveryNotifications->newEntity($dilivery_notification);
				$dilivery_notification_result=$this->Orders->DeliveryNotifications->save($dlilevery_notification_entity);
				
				/*Notification function xxx yy z*/
				//$this->Notification->setNotification(1,'','',$order_id,'','','','');----commented-2017-06-21
				
				//$this->sendToAll($order->id,'new', $supplerids, $delivery_id);//send emails
				//$this->sendToAll2($order->id,'new', $supplerids, $delivery_id,$order_data,$client_id);//send emails,product_name,product_quantity,product_supplier----commented-2017-06-21
				
				
				
				$this->Flash->success ( __ ( 'The order has been saved.' ) );
				
				return $this->redirect ( [ 
						'action' => 'index' 
				] );
			} else {
				$this->Flash->error ( __ ( 'The order could not be saved. Please, try again.' ) );
			}
		}
	
		
		$this->set ( compact ( 'order' ) );
		$this->set ( '_serialize', [ 
				'order' 
		] );
		//get numbers of orders for the customer
		$numOfOrders=$this->getCustomerNumOfOrder($client_id);
		$this->set ( 'numOfOrders', $numOfOrders );
		
		$this->set ( 'clientid', $client_id );
		$client_data_query=$this->Orders->Customers->find('all',['conditions'=>['id'=>$client_id]])->select(['address','city'])->first();
		$client_data=$client_data_query->toArray();
		$this->set('client_data',$client_data);
		
		$callcenters = $this->Orders->Callcenter->find ()->select ( [ 
				'id',
				'firstName',
				'lastName' 
		] )->where(['status'=>1])->formatResults ( function ($results) {
			/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			return $results->combine ( 'id', function ($row) {
				return $row ['firstName'] . ' ' . $row ['lastName'];
			} );
		} );
		$this->set ( compact ( 'callcenters' ) );//callcenter staff dropdown
		
		//
		
		
		$deliveries = $this->Orders->Delivery->find ()->contain(['city'])->select ( [ 
				'id',
				'firstName',
				'lastName',
				'city.cname' 
		] )->where(['status'=>1])->order(['rate' =>'DESC']) ->formatResults ( function ($results) {

			return $results->combine ( 'id', function ($row) {
				return $row ['firstName'] . ' ' . $row ['lastName'].' - '.$row['cid']['cname'];
			} );
		} ); 

		$this->set ( compact ( 'deliveries' ) );//delivery staff dropdown
		
		
		$callcenterId = $this->Auth->user ( 'id' ); // get from session values
		$usermodel = $this->loadModel ( 'Callcenter' );
		$callcenterId = $usermodel->getcallcenterid ( $callcenterId );
		$this->set ( compact ( 'callcenterId' ) );
		
		$productmodel=$this->loadModel('Products');
		$products=$productmodel->find('list',['fields'=>['id','name'],'conditions'=>['status'=>1]])->distinct(['name']);
		$this->set ( 'products',$products );//product dropdown
		
		
		$cities = $this->Orders->City->find ()->select ( [ 
				'cid',
				'cname' 
		] )->formatResults ( function ($results) {
			/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			return $results->combine ( 'cid', function ($row) {
				return $row ['cname'];
			} );
		} );
		$this->set ( compact ( 'cities' ) );
		$current_date_hidden=date('Y-m-d');	
		$current_date_show=date('d F Y');

		$current__date_time=Time::now();//now
		$delivery__time=$current__date_time->modify('+250mins')->format('H:i');
		
						
		$this->set('current_date_hidden',$current_date_hidden);		
		$this->set('current_date_show',$current_date_show);
		$this->set('delivery_time',$delivery__time);
		
		}else{
			$this->redirect(['controller'=>'customers','action'=>'search']);
		}
		}else{
			$this->Flash->error ( __ ( 'Please login as a callcenter, to add an order' ) );
			$this->redirect(['controller'=>'orders','action'=>'index']);
		}
		
	}
	
	/**
	 * Edit method
	 *
	 * @param string|null $id
	 *        	Order id.
	 * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function edit($id = null) {
		
		$order = $this->Orders->get ( $id, [ 
				'contain' => [ ] 
		] );
		if ($this->request->is ( [ 
				'patch',
				'post',
				'put' 
		] )) {
			$order_data=$this->request->data();//submited data
			$no_of_old_products=$order_data['editorder'];//number of product oder before
			if(sizeof($order_data['product_name'])>$no_of_old_products){
				$data=$this->processdata($order_data);//rearrange data sets with count total
				$order = $this->Orders->patchEntity ( $order, $data );
				//$saving=$this->Orders->save ( $order );
			}
			/* print '<pre>';
			print_r($this->request->data);
			echo $no_of_old_products;
			die();  */
			$order = $this->Orders->patchEntity ( $order, $this->request->data );
			if ($this->Orders->save ( $order )) {
				
				
				if(sizeof($order_data['product_name'])>$no_of_old_products){
				//$dilivery_notification=['deliveryId'=>$order->deliveryId,'notificationText'=>'del nofify','sentFrom'=>1,'orderId'=>$order->id];
				//create array for order_pruducts table
				$order_products=[];
				//supplier noification
				$supplier_notification=[];
				
				for($i=$no_of_old_products;$i<sizeof($order_data['product_name']);$i++){
					//order_pruducts table
					$order_products[$i]=['order_id'=>$order->id,'product_id'=>$order_data['product_name'][$i],'product_quantity'=>$order_data['product_quantity'][$i]];
					$supplier_notification[$i]=['supplierId'=>$order_data['product_supplier'][$i],'notificationText'=>'notify','sentFrom'=>1,'orderId'=>$order->id];
				}
				
				
				//print_r($saving);
				
				
				
				$order_product_entities = $this->Orders->OrderProducts->newEntities($order_products);
				$order_product_result = $this->Orders->OrderProducts->saveMany($order_product_entities);
				
				
				
				$supplier_notification_entites=$this->Orders->SupplierNotifications->newEntities($supplier_notification);
				$supplier_notification_result=$this->Orders->SupplierNotifications->saveMany($supplier_notification_entites);
				
				//$dlilevery_notification_entity=$this->Orders->DeliveryNotifications->newEntity($dilivery_notification);
				//$dilivery_notification_result=$this->Orders->DeliveryNotifications->save($dlilevery_notification_entity);
				
				}
				
				
				$this->Flash->success ( __ ( 'The order has been saved.' ) );
				
				return $this->redirect ( [ 
						'action' => 'index' 
				] );
			} else {
				$this->Flash->error ( __ ( 'The order could not be saved. Please, try again.' ) );
			}
			
			
		}
		$this->set ( compact ( 'order' ) );
		$this->set ( '_serialize', [ 
				'order' 
		] );
		
		$callcenters = $this->Orders->Callcenter->find ()->select ( [
				'id',
				'firstName',
				'lastName'
		] )->formatResults ( function ($results) {
			/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			return $results->combine ( 'id', function ($row) {
				return $row ['firstName'] . ' ' . $row ['lastName'];
			} );
		} );
		$this->set ( compact ( 'callcenters' ) );
		
		$productmodel=$this->loadModel('Products');
		$products=$productmodel->find('list',['fields'=>['id','name']])->distinct(['name']);
		$this->set ( 'products',$products );
		
		$callcenterId = $this->Auth->user ( 'id' ); // get from session values
		$usermodel = $this->loadModel ( 'Callcenter' );
		$callcenterId = $usermodel->getcallcenterid ( $callcenterId );
		$this->set ( compact ( 'callcenterId' ) );
		$deliveries = $this->Orders->Delivery->find ()->select ( [ 
				'id',
				'firstName',
				'lastName' 
		] )->formatResults ( function ($results) {
			/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			return $results->combine ( 'id', function ($row) {
				return $row ['firstName'] . ' ' . $row ['lastName'];
			} );
		} );
		$this->set ( compact ( 'deliveries' ) );
		
		$cities = $this->Orders->City->find ()->select ( [ 
				'cid',
				'cname' 
		] )->formatResults ( function ($results) {
			/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			return $results->combine ( 'cid', function ($row) {
				return $row ['cname'];
			} );
		} );
		//$this->set ( compact ( 'cities' ) );
		
			$cities = $this->Orders->City->find ()->select ( [
					'cid',
					'cname'
			] )->formatResults ( function ($results) {				
				return $results->combine ( 'cid', function ($row) {
					return $row ['cname'];
				} );
			} );
			$this->set ( compact ( 'cities' ) );
//get current supplier list
/*
 $subQuery=SELECT DISTINCT order_products.product_id,order_products.order_id,product_suppliers.supplier_id FROM `supplier_notifications` JOIN product_suppliers ON supplier_notifications.supplierId=product_suppliers.supplier_id JOIN order_products ON product_suppliers.product_id=order_products.product_id WHERE order_products.order_id=supplier_notifications.orderId
 $products=$productmodel->find('list',['fields'=>['id','name']])->distinct(['name']); 
 SELECT OrderProducts.product_id, OrderProducts.product_quantity , p.price , package.type, supdata.supplier_id FROM order_products OrderProducts INNER JOIN products p ON OrderProducts.product_id = p.id INNER JOIN package_type package ON p.package = package.id INNER JOIN ( SELECT distinct op.product_id, op.order_id, ps.supplier_id FROM supplier_notifications SupplierNotifications INNER JOIN product_suppliers ps ON supplierId=ps.supplier_id INNER JOIN order_products op ON ps.product_id=op.product_id ) supdata ON supdata.product_id = OrderProducts.product_id WHERE OrderProducts.order_id = 56
  
  $last=SELECT distinct op.product_id, op.order_id,op.product_quantity, ps.supplier_id, p.price,(op.product_quantity*p.price) as total, pt.type FROM supplier_notifications SupplierNotifications INNER JOIN product_suppliers ps ON supplierId=ps.supplier_id INNER JOIN order_products op ON ps.product_id=op.product_id INNER JOIN products p ON p.id=op.product_id INNER JOIN package_type pt ON pt.id = p.package WHERE op.order_id=56;
 * */
			
			
/*			$subQuery=$this->Orders->SupplierNotifications->find('list',['fields'=>['distinct op.product_id','op.order_id','ps.supplier_id']])
						->join(['table'=>'product_suppliers','alias'=>'ps','type'=>'INNER','conditions'=>'supplierId=ps.supplier_id'])
						->join(['table'=>'order_products','alias'=>'op','type'=>'INNER','conditions'=>'ps.product_id=op.product_id']);
			
			$order_product_details_query=$this->Orders->OrderProducts->find('all',['conditions' =>['order_id'=>$id],'fields'=>['product_id','product_quantity','p.price','package.type','supdata.ps__supplier_id']])
									->join([
											'table'=>'products',
											'alias'=>'p',
											'type'=>'INNER',
											'conditions' => 'product_id = p.id'
											])
									->join([
											'table'=>'package_type',
											'alias'=>'package',
											'type'=>'INNER',
											'conditions' => 'p.package = package.id'
											])
									->join([
											'table'=>$subQuery,
											'alias'=>'supdata',
											'type'=>'INNER',
											// 'conditions' => 'supdata.product_id = product_id' 
											'conditions' => 'op__product_id = product_id'
									]);
									
			$ordered_products=$order_product_details_query->toArray();	
			*/
			$query="SELECT distinct op.product_id, op.order_id,op.product_quantity, ps.supplier_id, p.price,(op.product_quantity*p.price) as total, pt.type FROM supplier_notifications SupplierNotifications INNER JOIN product_suppliers ps ON supplierId=ps.supplier_id INNER JOIN order_products op ON ps.product_id=op.product_id INNER JOIN products p ON p.id=op.product_id INNER JOIN package_type pt ON pt.id = p.package WHERE op.order_id=".$id;
			$connection = ConnectionManager::get('default');
			$ordered_products = $connection->execute($query)->fetchAll('assoc');
			/*print '<pre>';
			print_r($ordered_products);
			die();*/
			//print_r($ordered_products);

			for ($i=0;$i<sizeof($ordered_products);$i++) {
				$ordered_products[$i]['supplier_list']=$this->productsuppliersbyidtoEdit($ordered_products[$i]['product_id'])->toArray();
				}	
		
			$this->set('ordered_products',$ordered_products);	

			
			/*  print '<pre>';
			print_r($ordered_products);
			
			die(); */	 				
	}
	
	
	
	
	
	
	public function editorder($id = null) {
	
		if($this->Auth->user ( 'user_type')==2){
			
		$order = $this->Orders->get ( $id, [
				'contain' => [ ]
		] );
		
		if ($this->request->is ( ['patch','post','put'] )) {
			$order_data=$this->request->data();
			$data=$this->processdata($order_data);//rearrange data sets with count total
			$products_price=$this->getProductPrice($order_data['product_name']);//get product price
			$delivery_id=$order_data['deliveryId'];//send for email
			$suppliers_id=$order_data['product_supplier'];//send for email
			$order = $this->Orders->patchEntity ( $order, $data );
			$saving=$this->Orders->save ( $order );

			if ($saving) {
				//create array for order_pruducts table
				$order_products=[];
				//set order products array, one order has many products
				for($i=0;$i<sizeof($order_data['product_name']);$i++){
					//order_pruducts table
					$order_products[$i]=['order_id'=>$order->id,'product_id'=>$order_data['product_name'][$i],'product_quantity'=>$order_data['product_quantity'][$i],'product_price'=>$products_price[$i],'supplier_id'=>$order_data['product_supplier'][$i]];
				}
				$deleete_query=$this->Orders->OrderProducts->query();
				$deleete_query->delete()
				->where(['order_id' => $id])
				->execute();
				$order_product_entities = $this->Orders->OrderProducts->newEntities($order_products);
				$order_product_result = $this->Orders->OrderProducts->saveMany($order_product_entities);
				$this->Flash->success ( __ ( 'The order has been saved.' ) );
				
				return $this->redirect ( [
						'action' => 'index'
				] );
			}else{
				$this->Flash->error ( __ ( 'The order could not be update. Please, try again.' ) );
			}
			
			print '<pre>';						
			print_r($this->request->data());
			die();
			
			
		}
		
		
		$this->set ( compact ( 'order' ) );
		$this->set ( '_serialize', [
				'order'
		] );
		
		$numOfOrders=$this->getCustomerNumOfOrder($order->customerId);
		$this->set ( 'numOfOrders', $numOfOrders );
		
		$this->set ( 'clientid', $order->customerId );
		$client_data_query=$this->Orders->Customers->find('all',['conditions'=>['id'=>$order->customerId]])->select(['address','city'])->first();
		$client_data=$client_data_query->toArray();
		$this->set('client_data',$client_data);
		
		$callcenters = $this->Orders->Callcenter->find ()->select ( [
				'id',
				'firstName',
				'lastName'
		] )->where(['status'=>1])->formatResults ( function ($results) {
			/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			return $results->combine ( 'id', function ($row) {
				return $row ['firstName'] . ' ' . $row ['lastName'];
			} );
		} );
		$this->set ( compact ( 'callcenters' ) );//callcenter staff dropdown
		
		$deliveries = $this->Orders->Delivery->find ()->contain(['city'])->select ( [
				'id',
				'firstName',
				'lastName',
				'city.cname'
		] )->where(['status'=>1])->order(['rate' =>'DESC']) ->formatResults ( function ($results) {
		
			return $results->combine ( 'id', function ($row) {
				return $row ['firstName'] . ' ' . $row ['lastName'].' - '.$row['cid']['cname'];
			} );
		} );
		
			$this->set ( compact ( 'deliveries' ) );//delivery staff dropdown
			$callcenterId = $this->Auth->user ( 'id' ); // get from session values
			$usermodel = $this->loadModel ( 'Callcenter' );
			$callcenterId = $usermodel->getcallcenterid ( $callcenterId );
			$this->set ( compact ( 'callcenterId' ) );
			
			$productmodel=$this->loadModel('Products');
			$products=$productmodel->find('list',['fields'=>['id','name'],'conditions'=>['status'=>1]])->distinct(['name']);
			$product_cost=$productmodel->find('list',['keyField'=>'id','valueField'=>'cost'])->toArray();
			/* print '<pre>';
			print_r($product_cost);
			die(); */
			$this->set ( 'product_cost',$product_cost );//product dropdown
			$this->set ( 'products',$products );//product dropdown
			
			
			$cities = $this->Orders->City->find ()->select ( [ 
				'cid',
				'cname' 
		] )->formatResults ( function ($results) {
			/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			return $results->combine ( 'cid', function ($row) {
				return $row ['cname'];
			} );
		} );
		$this->set ( compact ( 'cities' ) );
			
			$current_date_hidden=date('Y-m-d',strtotime($order->deliveryDate));
			$current_date_show=date('d F Y',strtotime($order->deliveryDate));
			
			//$current__date_time=Time::now();//now
			//$delivery__time=$current__date_time->modify('+250mins')->format('H:i');
			$delivery__time=date('H:i',strtotime($order->deliveryTime));
			
			
			$this->set('current_date_hidden',$current_date_hidden);
			$this->set('current_date_show',$current_date_show);
			$this->set('delivery_time',$delivery__time);
			
			$order_products=$this->Orders->OrderProducts->find('all',['conditions'=>['order_id'=>$order->id],'contain'=>['Products','Products.packageType','Products.productSuppliers.Suppliers','Products.productSuppliers.Suppliers.city']])->toArray();
			/* print '<pre>';
			print_r($order_products);
			die(); */ 
			$this->set('order_products',$order_products);
			
			$current_product_ids=array_map(function ($product){
				return $product->product_id;
			},$order_products);
			$this->set('current_product_ids',$current_product_ids);
			
		}else{
			
			$this->Flash->error ( __ ( 'Please login as a callcenter, to edit an order' ) );			
			$this->redirect(['controller'=>'orders','action'=>'index']);		
		}

	
			
			
	}
	
	/**
	 * Delete method
	 *
	 * @param string|null $id
	 *        	Order id.
	 * @return \Cake\Network\Response|null Redirects to index.
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
	 */
	public function delete($id = null) {
		$this->request->allowMethod ( [ 
				'post',
				'delete' 
		] );
			$order = $this->Orders->get ( $id );
			$order->deleted=1;
			$this->Orders->save($order);
			
			$orderProductQuery=$this->Orders->OrderProducts->query();			
			$suppliernotificationQuery=$this->Orders->supplierNotifications->query();
			$deliveryNotificationQuery=$this->Orders->deliveryNotifications->query();
			$userNotificationQuery=$this->Orders->userNotifications->query();
			
			$orderProductQuery->update()->set(['deleted' => 1])->where(['order_id' => $id])->execute();
			$suppliernotificationQuery->update()->set(['deleted' => 1])->where(['orderId' => $id])->execute();
			$deliveryNotificationQuery->update()->set(['deleted' => 1])->where(['orderId' => $id])->execute();
			$userNotificationQuery->update()->set(['deleted' => 1])->where(['orderId' => $id])->execute();
			
			$this->Flash->success ( __ ( 'The order has been deleted.' ) );
			
		
		/* 
		 debug($order);
			die();
			 
		 if ($this->Orders->delete ( $order )) {
			$this->Flash->success ( __ ( 'The order has been deleted.' ) );
		} else {
			$this->Flash->error ( __ ( 'The order could not be deleted. Please, try again.' ) );
		} */
		
		return $this->redirect ( [ 
				'action' => 'index' 
		] );
	}
	
	//cancel
//http://stackoverflow.com/questions/28337049/how-do-i-run-transactions-in-cakephp3-while-retrieving-last-insert-id-and-work-f	
	public function cancel($id=null){
		
		$this->request->allowMethod ( [
				'patch',
				'post',
				'put'
				 				
		] );
		/* $con=$this->Orders->connection();
		
		$x=$con->transactional(function (){ */
			$order = $this->Orders->get ( $id );
			$order->status=9;
			if($this->Orders->save($order)){
				$con_order_products=$this->Orders->OrderProducts->connection();
				$stmt = $con_order_products->execute('UPDATE order_products SET status_s = ? WHERE order_id = ?',[9, $id]);
				/*Notification function xxx yy z*/
				$this->Notification->setNotification(9,'','',$id,'','','','');//send notifications
/* 			$con_sup=$this->Orders->SupplierNotifications->connection();
			$stmt = $con_sup->execute('UPDATE supplier_notifications SET status_s = ? WHERE orderId = ?',[9, $id]);
			
			$con_del=$this->Orders->DeliveryNotifications->connection();
			$stmt = $con_del->execute('UPDATE delivery_notifications SET status = ? WHERE orderId = ?',[9, $id]); */
			
			 $suppliers_id=$this->Orders->SupplierNotifications->find('list',['keyField'=>'id','valueField'=>'supplierId'],['conditions'=>['orderId'=>$id]])->toArray();
			$delivery_id=$this->Orders->SupplierNotifications->find('list',['keyField'=>'id','valueField'=>'deliveryId'],['conditions'=>['orderId'=>$id]])->toArray();
			$suppliers_id=array_values($suppliers_id);
			$delivery_id=array_values($delivery_id);
			$this->sendToAll($id,'cancel', $suppliers_id, $delivery_id); 
			/* } */
/* 			
		}); */
		
		/* echo $x.':aaa' */;
		//die();
		/* if ($x) { */
			//cancel delevery and supplier notifications
			$this->Flash->success ( __ ( 'The order has been canceled.' ) );
			
			
			
		} else {
			$this->Flash->error ( __ ( 'The order could not be canceled. Please, try again.' ) );
		}
		return $this->redirect ( [
				'action' => 'index'
		] );
	}
	
	public function productsuppliers(){
		
		$this->request->allowMethod ( ['post'] );
		//$productId = $this->request->data( 'productId' );
		$productName = $this->request->data( 'productId' );
		$productmodel=$this->loadModel('Products');
		$product_supplier_city=$productmodel->find('all',['conditions' =>['name'=>$productName]])
		->select(['s.id','s.firstName','s.lastName','city.cname','pack.type'])
		->join ( [
				'table' => 'suppliers',
				'alias' => 's',
				'type' => 'INNER',
				'conditions' => 'products.supplierId = s.id'
		] )
		->join ( [
				'table' => 'city',
				'alias' => 'city',
				'type' => 'INNER',
				'conditions' => 'city.cid = s.city'
		] )
		->join ( [
				'table' => 'package_type',
				'alias' => 'pack',
				'type' => 'INNER',
				'conditions' => 'products.package = pack.id'
		] )/*  ->formatResults ( function ($results) {			
			return $results->combine ( 'id', function ($row) {
				return $row ['s']['firstName'] . ' ' . $row['s'] ['lastName'].' - '.$row['city']['cname'];
			} );
		} ) */;
		
		//return $product_supplier_city;
		$this->set ( 'suppliers',$product_supplier_city );
		$this->set ( '_serialize', [
				'suppliers'
		] );
		
		
		//echo 'kkaakskakaksa';
		//$productmodel=$this->loadModel('Products');
		/*$this->set ('productId',$productId);*/
		//SELECT s.id,s.firstName,s.lastName,city.cname FROM suppliers s join (SELECT * FROM `products` as p WHERE name="leeks") p ON s.id=p.supplierID join city ON city.cid=s.city
		//kasun kalhara, moratuwa
		//http://stackoverflow.com/questions/30413740/how-to-join-multiple-tables-using-cakephp-3

		
	
		
		
	
}
/*
 * get supplier list according to the product id*/
public function productsuppliersbyid(){
	$this->request->allowMethod ( ['post'] );	
	$productId = $this->request->data( 'productId' );
	$productSupModel=$this->loadModel('ProductSuppliers');
	
	$product_supplier_city=$productSupModel->find('all',['conditions' =>['product_id'=>$productId]])
	->select(['s.id','s.firstName','s.lastName','city.cname','pack.type'])
	->join([
			'table'=>'products',
			'alias'=>'products',
			'type'=>'INNER',
			'conditions'=>'products.id=product_id'
	])
	->join ( [
			'table' => 'suppliers',
			'alias' => 's',
			'type' => 'INNER',
			'conditions' => 'supplier_Id = s.id'
	] )
	->join ( [
			'table' => 'city',
			'alias' => 'city',
			'type' => 'INNER',
			'conditions' => 'city.cid = s.city'
	] )
	->join ( [
			'table' => 'package_type',
			'alias' => 'pack',
			'type' => 'INNER',
			'conditions' => 'products.package = pack.id'
	] )
	->where(['s.status'=>1])
	->order(['s.rate' =>'DESC'])/*  ->formatResults ( function ($results) {
			return $results->combine ( 'id', function ($row) {
					return $row ['s']['firstName'] . ' ' . $row['s'] ['lastName'].' - '.$row['city']['cname'];
					} );
			} ) */;
	
	//return $product_supplier_city;
	$this->set ( 'suppliers',$product_supplier_city );
	$this->set ( '_serialize', [
			'suppliers'
	] );
	
}


/*get product supplier list for edit view*/
public function productsuppliersbyidtoEdit($productid){
	//$this->request->allowMethod ( ['post'] );
	$productId = $productid;
	$productSupModel=$this->loadModel('ProductSuppliers');

	$product_supplier_city=$productSupModel->find('all',['conditions' =>['product_id'=>$productId]])
	->select(['s.id','s.firstName','s.lastName','city.cname'])
	->join([
			'table'=>'products',
			'alias'=>'products',
			'type'=>'INNER',
			'conditions'=>'products.id=product_id'
	])
	->join ( [
			'table' => 'suppliers',
			'alias' => 's',
			'type' => 'INNER',
			'conditions' => 'supplier_Id = s.id'
	] )
	->join ( [
			'table' => 'city',
			'alias' => 'city',
			'type' => 'INNER',
			'conditions' => 'city.cid = s.city'
	] )
	  ->formatResults ( function ($results) {
			return $results->combine ( 's.id', function ($row) {
					return $row ['s']['firstName'] . ' ' . $row['s'] ['lastName'].' - '.$row['city']['cname'];
					} );
			} );
	  	return $product_supplier_city;

/* 	//return $product_supplier_city;
	$this->set ( 'suppliers',$product_supplier_city );
	$this->set ( '_serialize', [
			'suppliers'
	]  );*/

}

//jquery calculae single product total ammount
public function singlecal(){
	//$this->request->allowMethod ( ['post'] );
	$productId = $this->request->data( 'productId' );
	$productQuantity = $this->request->data( 'quantity' );
// 	$productQuantity=5;
// 	$productId=31;
	$productSupModel=$this->loadModel('Products');
	$price_obj=$productSupModel->get($productId,['fields'=>['price','cost']]);
	$price=$price_obj->price;
	$total=$price*$productQuantity;
	$cost=$price_obj->cost;
	$totalCost=$cost*$productQuantity;
	
	echo '{"productQuantity":'.$productQuantity.',"productPrice":'.$price.',"total":'.$total.',"productCost":'.$cost.',"productTotalCost":'.$totalCost.'}';
	//echo '{"total":'.$total.'}';
	//$singleCalPrice=['total'];
	//echo json_encode($singleCalPrice);
}
//for php count
public function countSubTotal($arrIds,$arrQuantity){
	$productSupModel=$this->loadModel('Products');
	$subTotal=0;
	for ($i=0;$i<sizeof($arrIds);$i++){
	$price_obj=$productSupModel->get($arrIds[$i],['fields'=>['price']]);
	$price=$price_obj->price;
	$total=$price*$arrQuantity[$i];
	$subTotal+=$total;
	}
	return $subTotal;
}
/**
 * 
 * @param unknown $arrIds
 * @param unknown $arrQuantity
 * @return multitype:
 * 
 * to store product price on order products table
 */
public function getProductPrice($arrIds){
	$productSupModel=$this->loadModel('Products');
	$product_price=[];
	for ($i=0;$i<sizeof($arrIds);$i++){
		$price_obj=$productSupModel->get($arrIds[$i],['fields'=>['price']]);
		$price=$price_obj->price;
		$product_price[$i]=$price;
	}
	return $product_price;
}

/* public function countFinaltotal($subtotal,$tax_p=0,$discount_p=0,$coupon_value=0){
	$tax=$subtotal*$tax_p/100;
	$discount=$subtotal*$discount_p/100;
	$total=$subtotal+$tax-$discount-$coupon_value;
	return $total;
} */

public function processdata($data){
	$tax_p=0;//tax persontage 10
	$discount_p=0;//discount persentage 5
	$counpon_value=0;//call to a function to find coupon values
	$subtotal=$this->countSubTotal($data['product_name'],$data['product_quantity']);//count sub total
	$tax=$subtotal*$tax_p/100;
	$discount=$subtotal*$discount_p/100;
	$total=$subtotal+$tax-$discount-$counpon_value;
	//change adding discount directly,
	
	$direct_discount=$data['direct_discount'];
	if ($direct_discount==""){
		$direct_discount=0;
	}
	$direct_total=$subtotal-$direct_discount;
	
	$newdata=[
			'customerId'=>$data['customerId'],
			'address'=>$data['address'],
			'city'=>$data['city'],
			'latitude'=>$data['latitude'],
			'longitude'=>$data['longitude'],
			'callcenterId'=>$data['callcenterId'],
			'deliveryId'=>$data['deliveryId'],
			
			'subTotal'=>$subtotal,
			'tax'=>$tax,
			'discount'=>$direct_discount,	
					
			'couponCode'=>$data['couponCode'],
			
			'total'=>$direct_total,
			'deliveryDate'=>$data['del-date'],
			'deliveryTime'=>$data['del-time'],
			'note'=>$data['del-note'],
			'supplier_note'=>$data['supp-note'],
			'paymentStatus'=>$data['paymentStatus'],
			//'status'=>$data['status']//selected status
			//'status'=>2//supplier informed :up to 2017-06-21
			'status'=>1//pending: change from 2017-06-21
	];
	return $newdata;
}
/*
 * type:cancel/new
 * $suppliers: array with id [1,3,4]
 * $delivery: id of the delever
 * $products: product array, currently can send @ proceed new order, cancelation cand
 * */
public function sendToAll($orderId,$type,$suppliers,$delivery,$products=''){
//admin. customer, suppliers, delever
$delivery_email=$this->Orders->DeliveryNotifications->Delivery->find('list',['keyField'=>'id','valueField'=>'email'],['conditions'=>['id'=>$delivery]])->toArray();
$delivery_email=array_values($delivery_email);//get only values from associative array,

//print_r( $delivery_email->toArray());
$supplier_email=$this->Orders->SupplierNotifications->Suppliers->find('list',['keyField'=>'id','valueField'=>'email'],['conditions'=>['id'=>$suppliers]])->toArray();
$supplier_email=array_values($supplier_email);
//print_r( $supplier_email->toArray());
//$all_email=array_merge($delivery_email,$supplier_email);
//print_r($all_email);
$this->sendemail2($orderId,$type,$supplier_email,'sup','');
$this->sendemail2($orderId,$type,$delivery_email,'del','');

$this->redirect(['action'=>'index']);
	 
}


public function deliveryEmail($orderdata){
	
	$orderId="<h4>Order ID: ".$orderId."</h4>";
	$sup_string="<hr><br><table border='1'>".
				"<tr>".
				"<th>index</th>".
				"<th>Product Id</th>".
				"<th>Product name</th>".
				"<th>Product price</th>".
				"<th>Package</th>".
				"<th>Quantity</th>".
				"<th>Ammount</th>".
				"<th>Supplier Id</th>".
				"<th>Supplier name</th>".
				"<th>Address</th>".
				"<th>City</th>".
				/* "<th>Email</th>". */
				"<th>Contact No.</th>".
				"<th>Mobile No.</th></tr>";
	$sup_string_end="</table>";
	$row="";
	//print '<pre>';
	$products_model=$this->loadModel('Products');
	$suppliers_model=$this->loadModel('Suppliers');
	//product_name,product_quantity,product_supplier
	for($i=0;$i<sizeof($orderdata['product_name']);$i++){
		$product_details=$products_model->get($orderdata['product_name'][$i],['contain'=>['packageType']]);
		$quntity=$orderdata['product_quantity'][$i];
		$supplier_details=$suppliers_model->get($orderdata['product_supplier'][$i],['contain'=>'city']);
		
		$row.="<tr style='min-height:35px'>";
		$row.="<td>".($i+1)."</td>";
		$row.="<td>".$product_details->id."</td>";//product id
		$row.="<td>".$product_details->name."</td>";//name
		$row.="<td>".$product_details->price."</td>";//price of a unit
		$row.="<td>".$product_details->package_type->type."</td>";//unit
		$row.="<td>".$quntity."</td>";//number of unit ordered
		$row.="<td>".$product_details->price*$quntity."</td>";//price for the orderd quantity
		$row.="<td>".$supplier_details->id."</td>";//price for the orderd quantity
		$row.="<td>".$supplier_details->firstName." ".$supplier_details->lastName."</td>";//name
		$row.="<td>".$supplier_details->address."</td>";//address
		$row.="<td>".$supplier_details->cid->cname."</td>";//city
		/* $row.="<td>".$supplier_details->email."</td>";//email */
		$row.="<td>".$supplier_details->contactNo."</td>";//contact
		$row.="<td>".$supplier_details->mobileNo."</td>";//mobile
		$row.="</tr>";		
}
	$countedval=$this->processdata($orderdata);
	
		
	$sub_total=$countedval['subTotal'];
	$total=$countedval['total'];
	$tax=$countedval['tax'];
	$discount=$countedval['discount'];
	$tax=$countedval['tax'];
	
	$total_string="<br><table border='1'><tr>".
				"<th>Sub Total</th>".
				"<td>".$sub_total."</td></tr>".
				"<tr><th>Tax</th>".
				"<td>".$tax."</td></tr>".
				"<tr><th>Discount</th>".
				"<td>".$discount."</td></tr>".
				"<tr><th>Total</th>".
				"<td>".$total."</td></tr>".
				  "</tr></table><br><hr>";
	
	echo $orderId.$sup_string.$row.$sup_string_end.$total_string;
	
	die();
	
	
/*	
	
	//admin. customer, suppliers, delever
	$delivery_email=$this->Orders->DeliveryNotifications->Delivery->find('list',['keyField'=>'id','valueField'=>'email'],['conditions'=>['id'=>$delivery]])->toArray();
	$delivery_email=array_values($delivery_email);//get only values from associative array,

	//print_r( $delivery_email->toArray());
	$supplier_email=$this->Orders->SupplierNotifications->Suppliers->find('list',['keyField'=>'id','valueField'=>'email'],['conditions'=>['id'=>$suppliers]])->toArray();
	$supplier_email=array_values($supplier_email);
	//print_r( $supplier_email->toArray());
	//$all_email=array_merge($delivery_email,$supplier_email);
	//print_r($all_email);
	$this->sendemail($orderId,$type,$supplier_email,'sup','');
	$this->sendemail($orderId,$type,$delivery_email,'del','');

	$this->redirect(['action'=>'index']);
*/
}

//new order information email
/**
 * 
 * @param unknown $orderId
 * @param unknown $trype
 * @param unknown $suppliers
 * @param unknown $delivery
 * @param unknown $orderdata
 * 
 * the email sent @ order saving time, so product price get from products table,
 * if you sent this email after long time, you need to get price from OrderProducts table,
 * because product price may change time to time
 */
public function sendToAll2($orderId,$trype,$suppliers,$delivery,$orderdata,$client_id=null){
	
	$id_of_the_order=$orderId;
	$products_model=$this->loadModel('Products');
	$suppliers_model=$this->loadModel('Suppliers');
	$delivery_model=$this->loadModel('Delivery');
	
	$countedval=$this->processdata($orderdata);
	
	
	
	$sub_total=$countedval['subTotal'];
	$total=$countedval['total'];
	$tax=$countedval['tax'];
	$discount=$countedval['discount'];
	$tax=$countedval['tax'];
	
	$total_string="<br><table border='1'><tr>".
			"<th>Sub Total</th>".
			"<td>".$sub_total."</td></tr>".
			"<tr><th>Tax</th>".
			"<td>".$tax."</td></tr>".
			"<tr><th>Discount</th>".
			"<td>".$discount."</td></tr>".
			"<tr><th>Total</th>".
			"<td>".$total."</td></tr>".
			"</tr></table><br><hr>";
	
	$orderId="<h4>Order ID: ".$orderId."</h4>";
	$sup_string="<hr><br><table border='1'>".
			"<tr>".
			/* "<th>#</th>". */
			"<th>Supplier Id</th>".
			"<th>Supplier name</th>".
			"<th>Address</th>".
			"<th>City</th>".
			/* "<th>Email</th>". */
			"<th>Contact No.</th>".
			"<th>Mobile No.</th>".
			"<th>Product Id</th>".
			"<th>Product name</th>".
			"<th>Product price</th>".
			"<th>Package</th>".
			"<th>Quantity</th>".
			"<th>Ammount</th>".
			"</tr>";
	$sup_string_end="</table>";
	$row="";
	$supliers_email=[];
	$delivery_mail=[];
	$delivery_mail_string=$orderId.$sup_string;
	
	foreach ($suppliers as $suplier){
		$count=1;
		$sup_email="";
		for($i=0;$i<sizeof($orderdata['product_name']);$i++){
			if($suplier==$orderdata['product_supplier'][$i]){
				$product_details=$products_model->get($orderdata['product_name'][$i],['contain'=>['packageType']]);
				$quntity=$orderdata['product_quantity'][$i];
				$supplier_details=$suppliers_model->get($orderdata['product_supplier'][$i],['contain'=>'city']);
				
				
		$row.="<tr style='min-height:35px'>";
		$colspan=1;
		if ($count==1){
			
		/* $row.="<td rowspan='2'>".($i+1)."</td>"; */
		
		
			$row.="<td rowspan='".$colspan."'>".$supplier_details->id."</td>";//price for the orderd quantity
			$row.="<td rowspan='".$colspan."'>".$supplier_details->firstName." ".$supplier_details->lastName."</td>";//name
			$row.="<td rowspan='".$colspan."'>".$supplier_details->address."</td>";//address
			$row.="<td rowspan='".$colspan."'>".$supplier_details->cid->cname."</td>";//city
			/* $row.="<td>".$supplier_details->email."</td>";//email */
			$row.="<td rowspan='".$colspan."'>".$supplier_details->contactNo."</td>";//contact
			$row.="<td rowspan='".$colspan."'>".$supplier_details->mobileNo."</td>";//mobile
			$sup_email=$supplier_details->email;
		}else{
			$row.="<td></td><td></td><td></td><td></td><td></td><td></td>";
		}
		
		$row.="<td>".$product_details->id."</td>";//product id
		$row.="<td>".$product_details->name."</td>";//name
		$row.="<td>".$product_details->price."</td>";//price of a unit
		$row.="<td>".$product_details->package_type->type."</td>";//unit
		$row.="<td>".$quntity."</td>";//number of unit ordered
		$row.="<td>".$product_details->price*$quntity."</td>";//price for the orderd quantity
		 
		$row.="</tr>";
					
				
				 $count++;
			}
			
		}
		$colspan=$count;
		$delivery_mail_string.=$row;
		$supliers_email[$sup_email]= $orderId.$sup_string.$row.$sup_string_end;
		$row="";
	}
	$delivery_mail_string.=$sup_string_end.$total_string;
	$delivery_mail_addrrss=$delivery_model->get($delivery,['fields'=>['email']]);
	//echo $delivery_mail_addrrss['email'];
	
	$delivery_mail[$delivery_mail_addrrss['email']]=$delivery_mail_string;
	
	$admin_mail_address=Configure::read('admin_email');
	$order_data=$this->__getOrderMailData($id_of_the_order);
	$admin_mail [$admin_mail_address] = $order_data;
	$customer=$this->Orders->Customers->get($client_id);
	if($customer->email!=null){
		$customer_mail [$customer->email] = $order_data;
	}
	
	
	/* print_r($emails[4]);
	print_r($emails[3]);
	echo $delivery_mail_string; 
	die();
	*/
	/*  print '<pre>';
	 
	print_r(['del'=>$delivery_mail,'sup'=>$supliers_email]);
	die();  */
	
	//return ['del'=>$delivery_mail,'sup'=>$supliers_email];
/* 	print_r($supliers_email);
	print_r($delivery_mail); */
	
	$this->sendemail('new', $supliers_email, 'sup');//suppliers email
	$this->sendemail('new', $delivery_mail, 'del');//delivery email
	$this->sendemail3('new', $admin_mail, 'admin');//admin mail
	if($customer->email!=null){
		$this->sendemail3('new', $customer_mail, 'cus');//admin mail
	}
	
	//die();
	
}

/**
 *
 * @param unknown $orderId
 * @param unknown $trype
 * @param unknown $suppliers
 * @param unknown $delivery
 * @param unknown $orderdata
 *
 * the email sent @ order saving time, so product price get from products table,
 * if you sent this email after long time, you need to get price from OrderProducts table,
 * because product price may change time to time
 * 
 * the email sent manyally in backe end, there is a issue,
 * if mail sent after long time.
 * product price are difrent than ordering time
 */
public function sendToAll3($orderId,$trype,$suppliers,$delivery,$totaldata,$client_id=null){

	$id_of_the_order=$orderId;
	//$products_model=$this->loadModel('Products');
	//$suppliers_model=$this->loadModel('Suppliers');
	//$delivery_model=$this->loadModel('Delivery');

	



	/* $sub_total=$totaldata['subTotal'];
	$total=$totaldata['total'];
	$tax=$totaldata['tax'];
	$discount=$totaldata['discount'];
	$tax=$totaldata['tax']; */

	/* $total_string="<br><table border='1'><tr>".
			"<th>Sub Total</th>".
			"<td>".$sub_total."</td></tr>".
			"<tr><th>Tax</th>".
			"<td>".$tax."</td></tr>".
			"<tr><th>Discount</th>".
			"<td>".$discount."</td></tr>".
			"<tr><th>Total</th>".
			"<td>".$total."</td></tr>".
			"</tr></table><br><hr>";

	$orderId="<h4>Order ID: ".$orderId."</h4>";
	$sup_string="<hr><br><table border='1'>".
			"<tr>".
	
	"<th>Supplier Id</th>".
	"<th>Supplier name</th>".
	"<th>Address</th>".
	"<th>City</th>".
	
	"<th>Contact No.</th>".
	"<th>Mobile No.</th>".
	"<th>Product Id</th>".
	"<th>Product name</th>".
	"<th>Product price</th>".
	"<th>Package</th>".
	"<th>Quantity</th>".
	"<th>Ammount</th>".
	"</tr>";
	$sup_string_end="</table>";
	$row="";
	$supliers_email=[];
	$delivery_mail=[];
	$delivery_mail_string=$orderId.$sup_string;
 */
	/* foreach ($suppliers as $suplier){
		$count=1;
		$sup_email="";
		for($i=0;$i<sizeof($orderdata['product_name']);$i++){
			if($suplier==$orderdata['product_supplier'][$i]){
				$product_details=$products_model->get($orderdata['product_name'][$i],['contain'=>['packageType']]);
				$quntity=$orderdata['product_quantity'][$i];
				$supplier_details=$suppliers_model->get($orderdata['product_supplier'][$i],['contain'=>'city']);


				$row.="<tr style='min-height:35px'>";
				$colspan=1;
				if ($count==1){
						
					


					$row.="<td rowspan='".$colspan."'>".$supplier_details->id."</td>";//price for the orderd quantity
					$row.="<td rowspan='".$colspan."'>".$supplier_details->firstName." ".$supplier_details->lastName."</td>";//name
					$row.="<td rowspan='".$colspan."'>".$supplier_details->address."</td>";//address
					$row.="<td rowspan='".$colspan."'>".$supplier_details->cid->cname."</td>";//city
					
					$row.="<td rowspan='".$colspan."'>".$supplier_details->contactNo."</td>";//contact
					$row.="<td rowspan='".$colspan."'>".$supplier_details->mobileNo."</td>";//mobile
					$sup_email=$supplier_details->email;
				}else{
					$row.="<td></td><td></td><td></td><td></td><td></td><td></td>";
				}

				$row.="<td>".$product_details->id."</td>";//product id
				$row.="<td>".$product_details->name."</td>";//name
				$row.="<td>".$product_details->price."</td>";//price of a unit
				$row.="<td>".$product_details->package_type->type."</td>";//unit
				$row.="<td>".$quntity."</td>";//number of unit ordered
				$row.="<td>".$product_details->price*$quntity."</td>";//price for the orderd quantity
					
				$row.="</tr>";
					

				$count++;
			}
				
		}
		$colspan=$count;
		$delivery_mail_string.=$row;
		$supliers_email[$sup_email]= $orderId.$sup_string.$row.$sup_string_end;
		$row="";
	} */
	//$delivery_mail_string.=$sup_string_end.$total_string;
	//$delivery_mail_addrrss=$delivery_model->get($delivery,['fields'=>['email']]);
	//echo $delivery_mail_addrrss['email'];

	//$delivery_mail[$delivery_mail_addrrss['email']]=$delivery_mail_string;

	$admin_mail_address=Configure::read('admin_email');
	$order_data=$this->__getOrderMailData($id_of_the_order);
	$admin_mail [$admin_mail_address] = $order_data;
	$customer=$this->Orders->Customers->get($client_id);
	if($customer->email!=null){
		$customer_mail [$customer->email] = $order_data;
	}


	//$this->sendemail('new', $supliers_email, 'sup');//suppliers email
	//$this->sendemail('new', $delivery_mail, 'del');//delivery email
	$admin_r=$this->sendemail3('new', $admin_mail, 'admin');//admin mail
	$customer_r=true;
	if($customer->email!=null){
		$customer_r=$this->sendemail3('new', $customer_mail, 'cus');//admin mail
	}	

}

/*
 * $orderid:order ID
 * $type:new/cancel
 * $recipients:array with email address
 * $recipient_type:sup/del
 * $products: product array, currently can send @ proceed new order, cancelation cand
 * */
public function sendemail($type='new',$recipients,$recipient_type){
	$subject="";
	$message="";
	$message_full="";
	$hello="Hello ";
	if ($recipient_type=='del'){
		$hello.="Delevery person,\n";
	}elseif ($recipient_type=='sup'){
		$hello.="Supplier,\n";
	}
	
	if ($type=='new'){
		$subject="New Order Notification";
		$message=$hello."New order has been made,\n";
	}
	elseif ($type=='cancel'){
		$subject="Order Cancellation";
		$message=$hello."Cancelled a order,\n";
	}
	$message_end="\nPlease check the system for more details";
	
	foreach ($recipients as $email_add=>$message_body){
		$message_full=$message.$message_body.$message_end;
		
		//echo 'xxx'.$email.'<br>'.$message_full;
		
		 $email = new Email('default');
		$email->from(['spanrupasinghe11@gmail.com' => 'Direct2door.com'])
		->to($email_add)
		->subject($subject)
		->emailFormat('html')
		->send($message_full); 
		$message_full="";
	}
	
	
	
	
	
	
}


/*
 * $orderid:order ID
 * $type:new/cancel
 * $recipients:array with [email=>message] address
 * $recipient_type:sup/del
 * $products: product array, currently can send @ proceed new order, cancelation cand
 * */
public function sendemail2($orderid,$type='new',$recipients,$recipient_type){
	$subject="";
	$message="";
	$hello="Hello ";
	if ($recipient_type=='del'){
		$hello.="Delevery person,\n";
	}elseif ($recipient_type=='sup'){
		$hello.="Supplier,\n";
	}

	if ($type=='new'){
		$subject="New Order Notification";
		$message=$hello."New order has been made,\nOrder ID: ".$orderid." \nPlease check the system for more details";
	}
	elseif ($type=='cancel'){
		$subject="Order Cancellation";
		$message=$hello."Cancelled a order,\nOrder ID: ".$orderid." \nPlease check the system for more details";
	}

	$email = new Email('default');
	$email->from(['spanrupasinghe11@gmail.com' => 'Direct2door.com'])
	->to($recipients)
	->subject($subject)
	->send($message);

}




public function schedule() {
	$orders = $this->paginate ( $this->Orders,['contain'=>'customers','conditions'=>['Orders.status IN'=>[1,2,3,4,7],'deleted ='=>0],'order' => ['Orders.deliveryDate' => 'ASC','Orders.deliveryTime' => 'ASC']] );
	/* print '<pre>';
		print_r($orders);
	die(); */
	$callcenter_query=$this->Orders->Callcenter->find('list',['keyField'=>'id','valueField'=>'users.username'])->select(['id','users.username'])
	->join ( [
			'table' => 'users',
			'alias' => 'users',
			'type' => 'INNER',
			'conditions' => 'user_id = users.id'
	] );
	$callcenters=$callcenter_query->toArray();

	$this->set('callcenters',$callcenters);
		
	$delivery_query=$this->Orders->Delivery->find('list',['keyField'=>'id','valueField'=>'users.username'])->select(['id','users.username'])
	->join ( [
			'table' => 'users',
			'alias' => 'users',
			'type' => 'INNER',
			'conditions' => 'user_id = users.id'
	] );
	$deliveries=$delivery_query->toArray();

	$this->set('deliveries',$deliveries);


	$cities_query=$this->Orders->City->find('list',['keyField'=>'cid','valueField'=>'cname']);
	$city=$cities_query->toArray();
	$this->set('cities',$city);

	$this->set ( compact ( 'orders' ) );
	$this->set ( '_serialize', [
			'orders'
	] );
}

public function getCustomerNumOfOrder($id){
	$query=$this->Orders->find('all',['conditions'=>['customerId'=>$id]]);
	$number=$query->count()+1;
	$number= Number::ordinal($number);
	return "This is your ".$number." Order";;
}

/**
 * count total focus on pdf
 * @param unknown $orderId
 * @return multitype:string: array contain total of available products and not available products
 * [ [available] => 400 [notavailable] => 200]
 * 
 * need to modifid to cupancode, tax, discounts, etc
 */
public function countTotal($orderId){
    
			$orderProductQuery_available=$this->Orders->OrderProducts->find();
			$orderProductQuery_not_available=$this->Orders->OrderProducts->find();
			//$query = $articles->find();
			$available_sum= $orderProductQuery_available->select(['total' => $orderProductQuery_available->func()->sum('product_quantity*product_price')])
							->where(['order_id' => $orderId,'status_s < '=>2])->first();
			$not_available_sum= $orderProductQuery_not_available->select(['total' => $orderProductQuery_not_available->func()->sum('product_quantity*product_price')])
			->where(['order_id' => $orderId,'status_s'=>2])->first();			
			if (empty($available_sum['total'])){
				$available_sum['total']=0;
			}
			if (empty($not_available_sum['total'])){
				$not_available_sum['total']=0;
			}
			//$total=['available'=>$available_sum['total'],'notavailable'=>$not_available_sum['total']];
			/* print_r($total);
			die(); */
			//get discount
			$order=$this->Orders->get($orderId,['fields'=>['discount']]);
			$order_discount=$order->discount;//contain in order table, call center add at ordered time
			$total_ammount=$available_sum['total']-$order_discount;
			$total=['available'=>$total_ammount,'notavailable'=>$not_available_sum['total'],'direct_discount'=>$order_discount,'subtotal'=>$available_sum['total']];
			/* debug($total);
			die(); */
			return $total;	
			
			
}


	

public function getInvoice($id = null) {
	//http://stackoverflow.com/questions/12611148/how-to-export-data-to-an-excel-file-using-phpexcel
	//http://stackoverflow.com/questions/15887953/cakephp-file-download-link
	$order = $this->Orders->get ( $id, [
			'contain' => [
					'OrderProducts',
					'callcenter',
					'delivery',
					'customers',
					'city',
					'OrderProducts.Products',
					'OrderProducts.Products.packageType',
					'OrderProducts.Suppliers',
					'OrderProducts.Suppliers.city'
			]


	] );

	

	/*  print '<pre>';
		print_r($order);
	die();  */
	$total=$this->countTotal($id);
	
	/* $this->set('total_pdf',$total);
	$this->set ( 'order', $order );
	$this->set ( '_serialize', [
			'order'
	] ); */
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Customer");
	$name=$order->customer->firstName.' '.$order->customer->lastName;
	$address=$order->customer->address;
	$contact=$order->customer->mobileNo;
	$myId=$order->customer->id;
	$myPurchase="";
	$objPHPExcel->getActiveSheet()->SetCellValue('A2', "Name");
	$objPHPExcel->getActiveSheet()->SetCellValue('B2', $name);
	$objPHPExcel->getActiveSheet()->SetCellValue('A3', "Address");
	$objPHPExcel->getActiveSheet()->SetCellValue('B3', $address);
	$objPHPExcel->getActiveSheet()->SetCellValue('A4', "Contact");
	$objPHPExcel->getActiveSheet()->SetCellValue('B4', $contact);
	$objPHPExcel->getActiveSheet()->SetCellValue('A5', "My ID");
	$objPHPExcel->getActiveSheet()->SetCellValue('B5', $myId);
	$objPHPExcel->getActiveSheet()->SetCellValue('A6', "My purchase");
	$objPHPExcel->getActiveSheet()->SetCellValue('B6', $myPurchase);
	
	$objPHPExcel->getActiveSheet()->SetCellValue('G1', "Invoice");
	$number=$order->id;
	$order_creatd=$order->created;	
	$created__date_time=new Time($order_creatd);//now
	$created__date=$created__date_time->format('Y-m-d');
	$created__time=$created__date_time->format('H:i:s');
	$date=$created__date;
	$time=$created__time;
	$subtotal=$total['subtotal'];
	$discount=$order->discount;
	$totla=$total['available'];
	$objPHPExcel->getActiveSheet()->SetCellValue('G2', "Number");
	$objPHPExcel->getActiveSheet()->SetCellValue('H2', $number);
	$objPHPExcel->getActiveSheet()->SetCellValue('G3', "Date");
	$objPHPExcel->getActiveSheet()->SetCellValue('H3', $date);
	$objPHPExcel->getActiveSheet()->SetCellValue('G4', "Time");
	$objPHPExcel->getActiveSheet()->SetCellValue('H4', $time);
	$objPHPExcel->getActiveSheet()->SetCellValue('G5', "Subtotal");
	$objPHPExcel->getActiveSheet()->SetCellValue('H5', $subtotal);
	$objPHPExcel->getActiveSheet()->SetCellValue('G6', "Discount");
	$objPHPExcel->getActiveSheet()->SetCellValue('H6', $discount);
	$objPHPExcel->getActiveSheet()->SetCellValue('G7', "Total");
	$objPHPExcel->getActiveSheet()->SetCellValue('H7', $totla);
	
	
	//8-to a,b,c,d,e
	$objPHPExcel->getActiveSheet()->SetCellValue('A9', "No.");
	$objPHPExcel->getActiveSheet()->SetCellValue('B9', "Description");
	$objPHPExcel->getActiveSheet()->SetCellValue('C9', "Qty");
	$objPHPExcel->getActiveSheet()->SetCellValue('D9', "Unit price");
	$objPHPExcel->getActiveSheet()->SetCellValue('E9', "Amount");
	/* $product_data=[
			['name'=>'Ala','qty'=>10,'unit_price'=>100,'ammount'=>100],
			['name'=>'Bathala','qty'=>5,'unit_price'=>75,'ammount'=>375],
			['name'=>'Mun','qty'=>10,'unit_price'=>25,'ammount'=>250]
	]; */
	$product_data=$order->order_products;
	
	for($i=1;$i<=sizeof($product_data);$i++){
		$rowCount=$i+9;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $rowCount);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $product_data[$i-1]['product']->name);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $product_data[$i-1]->product_quantity);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $product_data[$i-1]->product_price);
		if ($product_data[$i-1]->status_s!=2){
		$item_price= $product_data[$i-1]->product_price;
		$item_qty=$product_data[$i-1]->product_quantity;
		$item_tota_price=$item_price*$item_qty;
		}
		else{
			$item_tota_price=0;
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $item_tota_price);
	}
	
	// Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
	$objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
	
	// Write the Excel file to filename some_excel_file.xlsx in the current directory
	$file_name="/var/www/vhosts/direct2door_erp/webroot/invoice/order_".$id."_invoice.xlsx"; //for server
	//$file_name="/var/www/vhosts/direct2door_erp/webroot/invoice/order_".$id."_invoice.xlsx"; //for demo ?
	//$file_name="invoice/order_".$id."_invoice.xlsx"; //for local
	$objWriter->save($file_name);
	
	$this->response->file($file_name, array(
			'download' => true,
			
	));
	return $this->response;
	
	
}
/*new to admin

*/
	public function sendemail3($type = 'new', $recipients, $recipient_type){
		$from_mail_address=Configure::read('from_email');
		$subject=Configure::read('add_order_email_subject');
		$result=false;
		foreach ( $recipients as $email_add => $data ) {
			$email = new Email ();
			$result=$email 	->template('customerorder')
			->viewVars($data)
			->from ( [$from_mail_address => 'Direct2door.lk'] )
			->to ( $email_add )
			->subject ( $subject )
			->emailFormat ( 'html' )
			->send();
		}
		return $result;
	
	}
	
		public function __getOrderMailData($id = null) {
		$order = $this->Orders->get ( $id, [
				'contain' => [
						'OrderProducts',
						'callcenter',
						'delivery',
						'customers',
						'city',						
						'OrderProducts.Products',
						'OrderProducts.Products.packageType',
						'OrderProducts.Suppliers',
						'OrderProducts.Suppliers.city'
				]
		] );
	
	
		$total = $this->countTotal ( $id );
		return ['order'=>$order,'total_pdf'=>$total];
	
	}
/**
 * save supplier noifications data and, all user notification data
 * #delevery notifications set to default user, so do not need change that,
 */	
public function sendsuppliernotifications(){
	$this->request->allowMethod ( ['post'] );
	$order_id=$this->request->data('order_id');
	
	$order=$this->Orders->get($order_id);
	$order_products_model = $this->loadModel ( 'OrderProducts' );
	$supplers=$order_products_model->find('all',['fields'=>['DISTINCT OrderProducts.supplier_id'],'conditions'=>['order_id'=>$order_id]])->toArray();
	$supplerids = array_map(create_function('$o', 'return $o["DISTINCT OrderProducts"]["supplier_id"];'), $supplers);
	
	
	//save supplier notification
	//update order table status
	//
	$supplier_notification=[];
	
	
	//set supplier notification array, one suplier has one notification per order
	for($j=0; $j<sizeof($supplerids);$j++){
		$supplier_notification[$j]=['supplierId'=>$supplerids[$j],'notificationText'=>'notify','sentFrom'=>1,'orderId'=>$order_id];
	}
	
	$supplier_notification_entites=$this->Orders->SupplierNotifications->newEntities($supplier_notification);
	$supplier_notification_result=$this->Orders->SupplierNotifications->saveMany($supplier_notification_entites);
	/*Notification function xxx yy z*/
	$this->Notification->setNotification(1,'','',$order_id,'','','','');
	$order->status=2;
	if($this->Orders->save($order)){
		$this->Flash->success ( __ ( 'Order notifications have been sent.' ) );
		return $this->redirect ( [
				'action' => 'update/'.$order->id]);
	}else{
		$this->Flash->error(__('Order notifications have not been sent.'));
	}
	
}	
/**
 * all add order email ssent from here
 */
public function sendemails(){
	$this->request->allowMethod ( ['post'] );
	$order_id=$this->request->data('order_id');
	$order=$this->Orders->get($order_id);
	$order_products_model = $this->loadModel ( 'OrderProducts' );
	$supplers=$order_products_model->find('all',['fields'=>['DISTINCT OrderProducts.supplier_id'],'conditions'=>['order_id'=>$order_id]])->toArray();
	$supplerids = array_map(create_function('$o', 'return $o["DISTINCT OrderProducts"]["supplier_id"];'), $supplers);
	
	$total_data['subTotal']=$order->subTotal;
	$total_data['total']=$order->total;
	$total_data['tax']=$order->tax;
	$total_data['discount']=$order->discount;
	//$this->sendToAll($order->id,'new', $supplerids, $delivery_id);//send emails
	$this->sendToAll3($order_id,'new', $supplerids, $order->deliveryId,$total_data,$order->customerId);//send emails,product_name,product_quantity,product_supplier
	
	$order->email=1;
	if($this->Orders->save($order)){
	$this->Flash->success ( __ ( 'Order emails have been sent.' ) );
	return $this->redirect ( [
			'action' => 'update/'.$order->id]);
	}else{
		$this->Flash->error(__('Order emails have not been sent.'));
	}
}	
	


}
//http://www.jqueryscript.net/form/jQuery-Plugin-To-Duplicate-and-Remove-Form-Fieldsets-Multifield.html
//http://stackoverflow.com/questions/17175534/clonned-select2-is-not-responding
//http://stackoverflow.com/questions/28518158/jquery-select2-dropdown-disabled-when-cloning-a-table-row
//http://stackoverflow.com/questions/32415132/how-to-clone-select2-v4-ajax


//http://stackoverflow.com/questions/11054402/jquery-onchange-event-for-cloned-field



///https://www.packtpub.com/books/content/working-simple-associations-using-cakephp

//http://stackoverflow.com/questions/34651392/cakephp-3-x-multiple-records-from-one-form-into-multiple-tables
//http://stackoverflow.com/questions/16443656/cannot-save-associated-data-with-hasmany-through-join-model
//http://stackoverflow.com/questions/4260445/save-multiple-records-for-one-model-in-cakephp

//http://stackoverflow.com/questions/30711705/get-last-inserted-id-after-inserting-to-associated-table

/*
 SELECT 
  name, 
   ( 3959 * acos( cos( radians(42.290763) ) * cos( radians( locations.lat ) ) 
   * cos( radians(locations.lng) - radians(-71.35368)) + sin(radians(42.290763)) 
   * sin( radians(locations.lat)))) AS distance 
FROM locations 
WHERE active = 1 
HAVING distance < 10 
ORDER BY distance;

http://stackoverflow.com/questions/24370975/find-distance-between-two-points-using-latitude-and-longitude-in-mysql
http://stackoverflow.com/questions/8599200/calculate-distance-given-2-points-latitude-and-longitude
http://stackoverflow.com/questions/1006654/fastest-way-to-find-distance-between-two-lat-long-points
  
 * */


/*http://stackoverflow.com/questions/26772946/how-to-access-one-controller-action-inside-another-controller-action
 * 
 * http://stackoverflow.com/questions/4557564/how-to-save-other-languages-in-mysql-table
 * ALTER TABLE posts MODIFY title VARCHAR(255) CHARACTER SET UTF8;
 * */