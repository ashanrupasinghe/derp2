<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\SupplierNotification;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 */
class OrdersController extends AppController {
	
	public function isAuthorized($user) {
		
		// The owner of an article can edit and delete it
		if (in_array ( $this->request->action, [ 
				'add',
				/* 'edit', */
				'delete',
				'view',
				'index',
				'productsuppliersbyid',
				'singlecal',
				'countSubTotal',
				'processdata',
				'cancel',
				'sendOrderemail',
				'send'
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
		$orders = $this->paginate ( $this->Orders,['contain'=>'customers'] );
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
						'OrderProducts.Products.packageType' 
				] 
		] );
		/*  print '<pre>';
		print_r($order);
		die();  */
		$this->set ( 'order', $order );
		$this->set ( '_serialize', [ 
				'order' 
		] );
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
		
		//print '<pre>';
		//$this->sendOrderEmail('new',$suppliers_id,$delivery_id);
		//die();
		//print_r($order_data);
		//die();
		if(!empty($client_id)){
		if ($this->request->is ( 'post' )) {
			$data=$this->processdata($order_data);//rearrange data sets with count total
			$delivery_id=$order_data['deliveryId'];//send for email
			$suppliers_id=$order_data['product_supplier'];//send for email
			$order = $this->Orders->patchEntity ( $order, $data );	
			$saving=$this->Orders->save ( $order );				
			/* 	 print '<pre>';
				print_r($order_data);
				die();  */
			if ($saving) {				
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
					$order_products[$i]=['order_id'=>$order->id,'product_id'=>$order_data['product_name'][$i],'product_quantity'=>$order_data['product_quantity'][$i]];
										
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
				
				
				
				$supplier_notification_entites=$this->Orders->SupplierNotifications->newEntities($supplier_notification);
				$supplier_notification_result=$this->Orders->SupplierNotifications->saveMany($supplier_notification_entites);
				
				$dlilevery_notification_entity=$this->Orders->DeliveryNotifications->newEntity($dilivery_notification);
				$dilivery_notification_result=$this->Orders->DeliveryNotifications->save($dlilevery_notification_entity);
				
				//$this->sendToAll($order->id,'new', $supplerids, $delivery_id);//send emails
				$this->sendToAll2($order->id,'new', $supplerids, $delivery_id,$order_data);//send emails,product_name,product_quantity,product_supplier
				
				
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
		$this->set ( 'clientid', $client_id );
		$client_data_query=$this->Orders->Customers->find('all',['conditions'=>['id'=>$client_id]])->select(['address','city'])->first();
		$client_data=$client_data_query->toArray();
		$this->set('client_data',$client_data);
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
		
		//
		
		
		$deliveries = $this->Orders->Delivery->find ()->contain(['city'])->select ( [ 
				'id',
				'firstName',
				'lastName',
				'city.cname' 
		] ) ->formatResults ( function ($results) {

			return $results->combine ( 'id', function ($row) {
				return $row ['firstName'] . ' ' . $row ['lastName'].' - '.$row['cid']['cname'];
			} );
		} ); 

		$this->set ( compact ( 'deliveries' ) );
		
		
		$callcenterId = $this->Auth->user ( 'id' ); // get from session values
		$usermodel = $this->loadModel ( 'Callcenter' );
		$callcenterId = $usermodel->getcallcenterid ( $callcenterId );
		$this->set ( compact ( 'callcenterId' ) );
		
		$productmodel=$this->loadModel('Products');
		$products=$productmodel->find('list',['fields'=>['id','name']])->distinct(['name']);
		$this->set ( 'products',$products );
		
		
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
		if ($this->Orders->delete ( $order )) {
			$this->Flash->success ( __ ( 'The order has been deleted.' ) );
		} else {
			$this->Flash->error ( __ ( 'The order could not be deleted. Please, try again.' ) );
		}
		
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
			$con_sup=$this->Orders->SupplierNotifications->connection();
			$stmt = $con_sup->execute('UPDATE supplier_notifications SET status_s = ? WHERE orderId = ?',[9, $id]);
			
			$con_del=$this->Orders->DeliveryNotifications->connection();
			$stmt = $con_del->execute('UPDATE delivery_notifications SET status = ? WHERE orderId = ?',[9, $id]);
			
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
	$price_obj=$productSupModel->get($productId,['fields'=>['price']]);
	$price=$price_obj->price;
	$total=$price*$productQuantity;
	
	echo '{"productQuantity":'.$productQuantity.',"productPrice":'.$price.',"total":'.$total.'}';
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
/* public function countFinaltotal($subtotal,$tax_p=0,$discount_p=0,$coupon_value=0){
	$tax=$subtotal*$tax_p/100;
	$discount=$subtotal*$discount_p/100;
	$total=$subtotal+$tax-$discount-$coupon_value;
	return $total;
} */

public function processdata($data){
	$tax_p=10;//tax persontage
	$discount_p=5;//discount persentage
	$counpon_value=0;//call to a function to find coupon values
	$subtotal=$this->countSubTotal($data['product_name'],$data['product_quantity']);//count sub total
	$tax=$subtotal*$tax_p/100;
	$discount=$subtotal*$discount_p/100;
	$total=$subtotal+$tax-$discount-$counpon_value;
	
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
			'discount'=>$discount,	
					
			'couponCode'=>$data['couponCode'],
			
			'total'=>$total,
			'paymentStatus'=>$data['paymentStatus'],
			'status'=>$data['status']
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
$this->sendemail($orderId,$type,$supplier_email,'sup','');
$this->sendemail($orderId,$type,$delivery_email,'del','');

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
public function sendToAll2($orderId,$trype,$suppliers,$delivery,$orderdata){
	
	
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
	/* print_r($emails[4]);
	print_r($emails[3]);
	echo $delivery_mail_string; 
	die();
	*/
	/*  print '<pre>';
	 
	print_r(['del'=>$delivery_mail,'sup'=>$supliers_email]);
	die();  */
	
	//return ['del'=>$delivery_mail,'sup'=>$supliers_email];
	$this->sendemail('new', $supliers_email, 'sup');//suppliers email
	$this->sendemail('new', $delivery_mail, 'del');//delivery email
	
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
	
	foreach ($recipients as $email=>$message_body){
		$message.=$message_body.$message_end;
		
		echo $email.'<br>'.$message;
		/* $email = new Email('default');
		$email->from(['spanrupasinghe11@gmail.com' => 'Direct2door.com'])
		->to($email)
		->subject($subject)
		->send($message); */
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