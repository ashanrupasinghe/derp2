<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;

class NotificationComponent extends Component
{
	public function doComplexOperation($amount1, $amount2)
	{
		return $amount1 + $amount2;
	}
	//$type=1,$orderId=174
	/*
	 * $order_status: when change throug OrderController=>add,cancel,compleat
	 * 1-pending/add order, 4-delivery_tookover,5-delivered,6-completed, 9-cancelled
	 * 
	 * $sup_status: when change throug SupplierNotificationsController
	 *  1-available, 2-not available, 3-ready, 4-delivery handed over
	 *  
	 * $del_status: when change throug DeliveryNotificationsController
	 * 1-took over
	 * 
	 * $orderId: orderId
	 * $prod_id: when change throug SupplierNotificationsController
	 * $supplier_user_id: when change throug SupplierNotificationsController, user ID of the supplier
	 * $del_id: when change throug DeliveryNotificationsController
	 * $custom: 111=>notify to deliver by callcenter, 222=> cron job delivery notification, 333=>cronjob supplier notification, 444=> cron job delivery notification call center, 555=>cronjob supplier notification call center
	 * */
	public function setNotification($order_status="",$sup_status="",$del_status="",$orderId=174,$prod_id="",$supplier_user_id="",$del_id="",$custom=""){
		//load models
		$orderModel  = TableRegistry::get('Orders');
		$orderProdctsModel  = TableRegistry::get('OrderProducts');
		$userModel=TableRegistry::get('Users');
		$userNotificationModel=TableRegistry::get('UserNotifications');
		$productsModel=TableRegistry::get('Products');
		$suppliersModel=TableRegistry::get('Suppliers');
		$deliveryModel=TableRegistry::get('Delivery');
		$type="";//notification type
		//identify type
		if ($order_status!=""){
			switch ($order_status){
				case 1:
					$type=1;
					break;
				case 4:
					$type=7;
					break;					
				case 5:
					$type=8;
					break;					
				case 6:
					$type=10;
					break;					
				case 9:
					$type=9;
					break;					
			}
		}elseif ($sup_status!=""){
			switch ($sup_status){
				case 1:
					$type=2;
					break;
				case 2:
					$type=3;
					break;
				case 3:
					$type=4;
					break;
				case 4:
					$type=5;
					break;
				
			}
			
		}elseif ($del_status!=""){
			switch ($del_status){
				case 1:
					$type=6;
					break;
			
			}
		}elseif ($supplier_user_id!="" && $sup_status=="" ){
			//send only one notification: product available
			$type=11;//supplier send notification @ submit time
			
		}
		elseif ($custom!=""){
			switch ($custom){
				case 111:
					$type=12;
					break;
				case 222:
					$type=13;
					break;
				case 333:
					$type=14;
			}
		}
		
		
		$adminQuery=$userModel->find('all',['fields'=>['id'],'conditions'=>['user_type'=>1]])->toArray();
		$admin_users=[];//contain admin ids
		foreach ($adminQuery as $admin){
			$admin_users[]=$admin['id'];
		}
		/*  print '<pre>';
		print_r($admin_users); */
		
		
		$callcenterQuery=$userModel->find('all',['fields'=>['id'],'conditions'=>['user_type'=>2]])->toArray();
		$callcenter_users=[];//contain callcenter staff ids
		foreach ($callcenterQuery as $callcenter){
			$callcenter_users[]=$callcenter['id'];
		}
		/*  print '<pre>';*/
		 //print_r($callcenter_users);  
		//http://book.cakephp.org/3.0/en/orm.html#quick-example
		$order_id=$orderId;
		
		$product_id=$prod_id;
		$product_name="";
		if ($prod_id!=""){		
		$product_name_q=$productsModel->get($product_id,['fields'=>['name']])->toArray();
		
		$product_name=$product_name_q['name'];
		}	
		$sup_id=$suppliersModel->find('all',['conditions'=>['user_id'=>$supplier_user_id],'fields'=>['id']])->first();//find supplier id
		$supplier_id=$sup_id['id'];
		$supplier_name="";
		if ($supplier_id!=""){
		$supplier_name_q=$suppliersModel->get($supplier_id,['fields'=>['firstName','lastName']])->toArray();
		$supplier_name=$supplier_name_q['firstName'].' '.$supplier_name_q['lastName'];
		}
		
		$delivery_id=$del_id;
		$delivery_name="";
		/* if ($delivery_id!=""){
		$delivery_name_q=$deliveryModel->get($delivery_id,['fields'=>['firstName','lastName']])->toArray();
		$delivery_name=$delivery_name_q['firstName'].' '.$delivery_name_q['lastName'];
		} */
		
		$client_name_q=$orderModel->get($order_id,['contain'=>['customers']]);		
		$client_name=$client_name_q->customer->firstName.' '.$client_name_q->customer->lastName;
		
		$user_list=[];
		
		$del=$orderModel->get($order_id,['contain'=>['delivery.Users']]);		
		$deliveryStaff=[$del->delivery->user->id];//contain delivery staff id
		$delivery_name=$del->delivery->firstName.' '.$del->delivery->lastName;//contain delivery staff id
		
		
		//$msg1_add_order="New order request, Order ID: ".$order_id;
		//$msg2_product_available=$product_name." is available for order ".$order_id." from ".$supplier_name;
		//$msg3_product_not_available=$product_name." is not available for order ".$order_id." from ".$supplier_name;
		//$msg4_product_ready=$product_name." is ready for order ".$order_id." from ".$supplier_name;
		//$msg5_product_handover=$product_name." is handed over by ".$supplier_name." for order ".$order_id." to ".$delivery_name;
		//$msg6_product_took_over=$product_name." is took over by ".$delivery_name." for order ".$order_id." from ".$supplier_name;
		//$msg7_order_took_over="products for order ".$order_id." are took over by ".$delivery_name;
		//$msg8_order_delevered="order ".$order_id." is delevered by ".$delivery_name." to ".$client_name;
		//$msg9_order_canceled="order ".$order_id."is canceled";
		//$msg10_order_compleated="order ".$order_id." is compleated";
		
		//$msg11_products_availability="Products avilability for order ID: ".$order_id." from ".$supplier_name;
		$message="";//final message
		$url="";//url for the order or notification
		
		if($type==1 || $type==9){
			//echo 'ashan';
			$suppliers=[];//supplier array
			
			
			/* $sups=$orderProdctsModel->find('all',['fields'=>['Users.id','supplier_id','Suppliers.firstName','Suppliers.lastName'],'conditions'=>['order_id'=>$order_id]])->distinct('supplier_id')->contain('Suppliers.Users')->toArray();
			for ($i=0;$i<sizeof($sups);$i++){
				$suppliers[$i]['user_id']=$sups[$i]['supplier_id'];
				$suppliers[$i]['supplier_id']=$sups[$i]['supplier']->user->id;
				$suppliers[$i]['name']=$sups[$i]['supplier']->firstName.' '.$sups[$i]['supplier']->lastName;
			} */
			//$del=$orderModel->get($order_id,['contain'=>['OrderProducts','customers','delivery','OrderProducts.Suppliers']]);
			/* $del=$orderModel->get($order_id,['contain'=>['delivery','delivery.Users']]);
			$deliveryStaff[0]['user_id']=$del['deliveryId'];
			$deliveryStaff[0]['delivery_id']=$del['delivery']->user->id;
			$deliveryStaff[0]['name']=$del['delivery']->firstName.' '.$del['delivery']->lastName; */
			
			
			
			$sups=$orderProdctsModel->find('all',['fields'=>['Users.id'],'conditions'=>['order_id'=>$order_id]])->distinct('supplier_id')->contain('Suppliers.Users')->toArray();
			for ($i=0;$i<sizeof($sups);$i++){				
				$suppliers[$i]=$sups[$i]['Users']->id;				
			}
			$user_list=[];//admin.calls.dels.sups
			if ($type==1){
				$user_list=array_merge($admin_users,$callcenter_users,$suppliers);//admin.calls.dels.sups
				$message="New order request, Order ID: ".$order_id;
			}else{
				$user_list=array_merge($admin_users,$callcenter_users,$suppliers,$deliveryStaff);//admin.calls.dels.sups
				$message="order ID: ".$order_id." is canceled";
			}
			//$url="/orders/view/".$order_id;
			
			
		}
		elseif ($type<=5){
			//$status_s="";//1-available, 2-not available, 3-ready, 4-delivery handed over
			$user_list=array_merge($admin_users,$callcenter_users,$deliveryStaff);
			
			if ($type==2){
				$message=$product_name." is available for order ID: ".$order_id." from ".$supplier_name;
			}
			elseif ($type==3){
				$message=$product_name." is not available for order ID: ".$order_id." from ".$supplier_name;
			}
			elseif ($type==4){
				$message=$product_name." is ready for order ID: ".$order_id." from ".$supplier_name;
			}
			else {
				$message=$product_name." is handed over by ".$supplier_name." for order ID: ".$order_id." to ".$delivery_name;;
			}
		}
		elseif ($type<=8){
			//$status_d="";
			
			$user_list=array_merge($admin_users,$callcenter_users);
			if ($type==6){
				$message=$product_name." is took over by ".$delivery_name." for order ID: ".$order_id." from ".$supplier_name;
			}
			elseif ($type==7){
				$message="products for order ID: ".$order_id." are took over by ".$delivery_name;
			}else {
				$message="order ID: ".$order_id." is delevered by ".$delivery_name." to ".$client_name;
			}
		}
		elseif($type==10){
			$user_list=$admin_users;
			$message="order ID: ".$order_id." is compleated";
		}
		elseif($type==11){
			$user_list=array_merge($admin_users,$callcenter_users);;
			$message="Products avilability for order ID: ".$order_id." from ".$supplier_name;
		}
		elseif ($type==12){
			$user_list=$deliveryStaff;
			$message="Products avilabile for order ID: ".$order_id.", Collect them and diliver please";
			
			$s_list_q=$orderProdctsModel->find('all',['fields'=>['Users.id','Suppliers.firstName','Suppliers.lastName'],'conditions'=>['order_id'=>$order_id,'status_s'=>1]])->distinct('supplier_id')->contain('Suppliers.Users')->toArray();
			
			$s_list=[];
			for ($i=0;$i<sizeof($s_list_q);$i++){
				$s_list[$i]=['user_id'=>$s_list_q[$i]->Suppliers->user->id,'first_name'=>$s_list_q[$i]->Suppliers->firstName,'last_name'=>$s_list_q[$i]->Suppliers->lastName];
			}
			
			/*
			 $m="Order 95 is ready. Collect from supplier.name";
			 ---$s_list-contain suppliers details----
			  [ ['user_id' => (int) 12,	'first_name' => 'Kasun','last_name' => 'Kalhara'],
				['user_id' => (int) 14,	'first_name' => 'Pemasiri',	'last_name' => 'Kemadasa'],
				['user_id' => (int) 18,	'first_name' => 'Gayan','last_name' => 'Kavinda']]
			*/
		}
		/* $data=[$order_id,];
		$userNotificationEntity = $userNotificationModel->newEntity ();
		$userNotification = $userNotificationModel->patchEntity ( $userNotificationEntity, $data );
		$saving=$userNotificationModel->save ( $userNotification ); */
		/* print '<pre>';
		print_r($user_list);
		echo $message; */
		$rows=[];//data to save
		if ($type==12){
			//set of notifications to driver
			
			for ($i=0;$i<sizeof($s_list);$i++){
				//one notification for separate users
				$message="Order ".$order_id." is ready. Collect from ".$s_list[$i]['first_name']." ".$s_list[$i]['last_name'];
				$rows[$i]=['orderId'=>$order_id,'userId'=>$user_list[0],'notification'=>$message,'type'=>$type,'seen'=>0];
					
			}
		}
		else{
			for ($i=0;$i<sizeof($user_list);$i++){			
					//one notification for separate users
				$rows[$i]=['orderId'=>$order_id,'userId'=>$user_list[$i],'notification'=>$message,'type'=>$type,'seen'=>0];
			
			}
		}
		
		
		$notification_entities=$userNotificationModel->newEntities($rows);		
		$notifications_save_result=$userNotificationModel->saveMany($notification_entities);
		return $notifications_save_result;//newly add remove if there error
		
	}
	
	public function getNotificationCount($user_id){		
		$userNotificationModel=TableRegistry::get('UserNotifications');
		// In a controller or table method.
		$query = $userNotificationModel->find('all', ['conditions' => ['userId'=>$user_id,'seen'=>0,'deleted ='=>0]]);
		$number = $query->count();
		return $number;
	
	}
	
	public function getLatestNotifications($user_id){
		$userNotificationModel=TableRegistry::get('UserNotifications');
		$query = $userNotificationModel->find('all', ['fields'=>['id','orderId','notification','type','created'],'conditions' => ['userId'=>$user_id,'seen'=>0,'deleted ='=>0]])->order(['created' => 'DESC'])->limit(5)->toArray();
		return $query;
		/* print '<pre>';
		print_r($query);
		die(); */
	}
	
	
	/*
	 * $order_status: when change throug OrderController=>add,cancel,compleat
	 * 1-pending/add order, 4-delivery_tookover,5-delivered,6-completed, 9-cancelled
	 *
	 * $sup_status: when change throug SupplierNotificationsController
	 *  1-available, 2-not available, 3-ready, 4-delivery handed over
	 *
	 * $del_status: when change throug DeliveryNotificationsController
	 * 1-took over
	 *
	 * $orderId: orderId
	 * $prod_id: when change throug SupplierNotificationsController
	 * $supplier_user_id: when change throug SupplierNotificationsController, user ID of the supplier
	 * $del_id: when change throug DeliveryNotificationsController
	 * */
	public function setNotification_first_compleate_function($order_status="",$sup_status="",$del_status="",$orderId=174,$prod_id="",$supplier_user_id="",$del_id=""){
		//load models
		$orderModel  = TableRegistry::get('Orders');
		$orderProdctsModel  = TableRegistry::get('OrderProducts');
		$userModel=TableRegistry::get('Users');
		$userNotificationModel=TableRegistry::get('UserNotifications');
		$productsModel=TableRegistry::get('Products');
		$suppliersModel=TableRegistry::get('Suppliers');
		$deliveryModel=TableRegistry::get('Delivery');
		$type="";//notification type
		//identify type
		if ($order_status!=""){
			switch ($order_status){
				case 1:
					$type=1;
					break;
				case 4:
					$type=7;
					break;
				case 5:
					$type=8;
					break;
				case 6:
					$type=10;
					break;
				case 9:
					$type=9;
					break;
			}
		}elseif ($sup_status!=""){
			switch ($sup_status){
				case 1:
					$type=2;
					break;
				case 2:
					$type=3;
					break;
				case 3:
					$type=4;
					break;
				case 4:
					$type=5;
					break;
	
			}
				
		}elseif ($del_status!=""){
			switch ($del_status){
				case 1:
					$type=6;
					break;
						
			}
		}
	
	
		$adminQuery=$userModel->find('all',['fields'=>['id'],'conditions'=>['user_type'=>1]])->toArray();
		$admin_users=[];//contain admin ids
		foreach ($adminQuery as $admin){
			$admin_users[]=$admin['id'];
		}
		/*  print '<pre>';
			print_r($admin_users); */
	
	
		$callcenterQuery=$userModel->find('all',['fields'=>['id'],'conditions'=>['user_type'=>2]])->toArray();
		$callcenter_users=[];//contain callcenter staff ids
		foreach ($callcenterQuery as $callcenter){
			$callcenter_users[]=$callcenter['id'];
		}
		/*  print '<pre>';*/
		//print_r($callcenter_users);
		//http://book.cakephp.org/3.0/en/orm.html#quick-example
		$order_id=$orderId;
	
		$product_id=$prod_id;
		$product_name="";
		if ($prod_id!=""){
			$product_name_q=$productsModel->get($product_id,['fields'=>['name']])->toArray();
	
			$product_name=$product_name_q['name'];
		}
		$sup_id=$suppliersModel->find('all',['conditions'=>['user_id'=>$supplier_user_id],'fields'=>['id']])->first();//find supplier id
		$supplier_id=$sup_id['id'];
		$supplier_name="";
		if ($supplier_id!=""){
			$supplier_name_q=$suppliersModel->get($supplier_id,['fields'=>['firstName','lastName']])->toArray();
			$supplier_name=$supplier_name_q['firstName'].' '.$supplier_name_q['lastName'];
		}
	
		$delivery_id=$del_id;
		$delivery_name="";
		/* if ($delivery_id!=""){
			$delivery_name_q=$deliveryModel->get($delivery_id,['fields'=>['firstName','lastName']])->toArray();
			$delivery_name=$delivery_name_q['firstName'].' '.$delivery_name_q['lastName'];
			} */
	
		$client_name_q=$orderModel->get($order_id,['contain'=>['customers']]);
		$client_name=$client_name_q->customer->firstName.' '.$client_name_q->customer->lastName;
	
		$user_list=[];
	
		$del=$orderModel->get($order_id,['contain'=>['delivery.Users']]);
		$deliveryStaff=[$del->delivery->user->id];//contain delivery staff id
		$delivery_name=$del->delivery->firstName.' '.$del->delivery->lastName;//contain delivery staff id
	
	
		//$msg1_add_order="New order request, Order ID: ".$order_id;
		//$msg2_product_available=$product_name." is available for order ".$order_id." from ".$supplier_name;
		//$msg3_product_not_available=$product_name." is not available for order ".$order_id." from ".$supplier_name;
		//$msg4_product_ready=$product_name." is ready for order ".$order_id." from ".$supplier_name;
		//$msg5_product_handover=$product_name." is handed over by ".$supplier_name." for order ".$order_id." to ".$delivery_name;
		//$msg6_product_took_over=$product_name." is took over by ".$delivery_name." for order ".$order_id." from ".$supplier_name;
		//$msg7_order_took_over="products for order ".$order_id." are took over by ".$delivery_name;
		//$msg8_order_delevered="order ".$order_id." is delevered by ".$delivery_name." to ".$client_name;
		//$msg9_order_canceled="order ".$order_id."is canceled";
		//$msg10_order_compleated="order ".$order_id." is compleated";
		$message="";//final message
		$url="";//url for the order or notification
	
		if($type==1 || $type==9){
			//echo 'ashan';
			$suppliers=[];//supplier array
				
				 	
			/* $sups=$orderProdctsModel->find('all',['fields'=>['Users.id','supplier_id','Suppliers.firstName','Suppliers.lastName'],'conditions'=>['order_id'=>$order_id]])->distinct('supplier_id')->contain('Suppliers.Users')->toArray();
				for ($i=0;$i<sizeof($sups);$i++){
				$suppliers[$i]['user_id']=$sups[$i]['supplier_id'];
				$suppliers[$i]['supplier_id']=$sups[$i]['supplier']->user->id;
				$suppliers[$i]['name']=$sups[$i]['supplier']->firstName.' '.$sups[$i]['supplier']->lastName;
			} */
			//$del=$orderModel->get($order_id,['contain'=>['OrderProducts','customers','delivery','OrderProducts.Suppliers']]);
			/* $del=$orderModel->get($order_id,['contain'=>['delivery','delivery.Users']]);
				$deliveryStaff[0]['user_id']=$del['deliveryId'];
				$deliveryStaff[0]['delivery_id']=$del['delivery']->user->id;
			$deliveryStaff[0]['name']=$del['delivery']->firstName.' '.$del['delivery']->lastName; */
				
				
				
			$sups=$orderProdctsModel->find('all',['fields'=>['Users.id'],'conditions'=>['order_id'=>$order_id]])->distinct('supplier_id')->contain('Suppliers.Users')->toArray();
			for ($i=0;$i<sizeof($sups);$i++){
				$suppliers[$i]=$sups[$i]['Users']->id;
			}
			$user_list=array_merge($admin_users,$callcenter_users,$suppliers,$deliveryStaff);//admin.calls.dels.sups
			if ($type==1){
				$message="New order request, Order ID: ".$order_id;
			}else{
				$message="order ID: ".$order_id." is canceled";
			}
			//$url="/orders/view/".$order_id;
				
				
		}
		elseif ($type<=5){
			//$status_s="";//1-available, 2-not available, 3-ready, 4-delivery handed over
			$user_list=array_merge($admin_users,$callcenter_users,$deliveryStaff);
				
			if ($type==2){
				$message=$product_name." is available for order ID: ".$order_id." from ".$supplier_name;
			}
			elseif ($type==3){
				$message=$product_name." is not available for order ID: ".$order_id." from ".$supplier_name;
			}
			elseif ($type==4){
				$message=$product_name." is ready for order ID: ".$order_id." from ".$supplier_name;
			}
			else {
				$message=$product_name." is handed over by ".$supplier_name." for order ID: ".$order_id." to ".$delivery_name;;
			}
		}
		elseif ($type<=8){
			//$status_d="";
				
			$user_list=array_merge($admin_users,$callcenter_users);
			if ($type==6){
				$message=$product_name." is took over by ".$delivery_name." for order ID: ".$order_id." from ".$supplier_name;
			}
			elseif ($type==7){
				$message="products for order ID: ".$order_id." are took over by ".$delivery_name;
			}else {
				$message="order ID: ".$order_id." is delevered by ".$delivery_name." to ".$client_name;
			}
		}
		elseif($type==10){
			$user_list=$admin_users;
			$message="order ID: ".$order_id." is compleated";
		}
		/* $data=[$order_id,];
			$userNotificationEntity = $userNotificationModel->newEntity ();
			$userNotification = $userNotificationModel->patchEntity ( $userNotificationEntity, $data );
			$saving=$userNotificationModel->save ( $userNotification ); */
		/* print '<pre>';
			print_r($user_list);
			echo $message; */
		$rows=[];//data to save
		for ($i=0;$i<sizeof($user_list);$i++){
			
			$rows[$i]=['orderId'=>$order_id,'userId'=>$user_list[$i],'notification'=>$message,'type'=>$type,'seen'=>0];
		}
		
		$notification_entities=$userNotificationModel->newEntities($rows);
		$notifications_save_result=$userNotificationModel->saveMany($notification_entities);
	
	
	}
	
	public function isSentToDriver($orderId){
		$userNotificationModel=TableRegistry::get('UserNotifications');
		$query=$userNotificationModel->find('all',['conditions'=>['orderId'=>$orderId,'type'=>12]])->toArray();
		if (sizeof($query)>0){
			return 1;
		}
		else{
			return 0;
		}
	}
	public function isSentToSuppliers($orderId){
		$ordersModel=TableRegistry::get('Orders');
		$query=$ordersModel->find('all',['conditions'=>['status IN'=>[2,3,4,5,6,7,9],'id'=>$orderId]])->toArray();
		if (sizeof($query)>0){
			return 1;
		}
		else{
			return 0;
		}
	}
	public function isSentEmail($orderId){
		$ordersModel=TableRegistry::get('Orders');
		$query=$ordersModel->find('all',['conditions'=>['email'=>1,'id'=>$orderId]])->toArray();
		if (sizeof($query)>0){
			return 1;
		}
		else{
			return 0;
		}
	}
	
	public function chronjob(){		
		
		$current__date_time=Time::now();//now
		$current__date=$current__date_time->format('Y-m-d');
		$current__time=$current__date_time->format('H:i:s');		
		/* $notify_time_supp=$current__date_time->modify('+90 mins')->format('H:i:s');
		$notify_time_del=$current__date_time->modify('+60 mins')->format('H:i:s'); */
		$orderModel  = TableRegistry::get('Orders');
		$connection = ConnectionManager::get('default');
		$notifications=[];		
		//check order- delivery status, if call center not sent notification to delivery staff, he do not know about the order, we chack whether notification is sent
		//$query_del="SELECT orders.id as orderId, delivery.user_id, orders.deliveryDate, orders.deliveryTime FROM orders JOIN user_notifications ON user_notifications.orderId=orders.id JOIN delivery ON delivery.id=orders.deliveryId WHERE orders.deliveryDate='".$current__date."' AND '".$current__time."' >=  SUBTIME(orders.deliveryTime, '01:00:00') AND type=12";
		$query_del=  "SELECT orders.id as orderId, delivery.user_id, orders.deliveryDate, orders.deliveryTime". 
					 " FROM orders". 
					 " JOIN user_notifications ON user_notifications.orderId=orders.id". 
					 " JOIN delivery ON delivery.id=orders.deliveryId". 
					 " JOIN order_products ON order_products.order_id=orders.id".
					 " WHERE orders.deliveryDate='".$current__date."' AND '".$current__time."' >=  SUBTIME(orders.deliveryTime, '01:00:00') AND user_notifications.type=12".
					 " AND order_products.status_d=0".
					 " GROUP BY delivery.user_id";
		$orderes_noti_del = $connection->execute($query_del)->fetchAll('assoc');
		
		//check order- supplier status
		//$query_sup="SELECT orders.id as orderId,suppliers.user_id , orders.deliveryDate, orders.deliveryTime FROM orders JOIN supplier_notifications ON supplier_notifications.orderId=orders.id JOIN suppliers ON suppliers.id= supplier_notifications.supplierId WHERE deliveryDate='".$current__date."' AND '".$current__time."' >=  SUBTIME(deliveryTime, '01:30:00')";
		$query_sup= "SELECT orders.id as orderId,suppliers.user_id , orders.deliveryDate, orders.deliveryTime".
					" FROM orders ".
					" JOIN supplier_notifications ON supplier_notifications.orderId=orders.id". 
					" JOIN suppliers ON suppliers.id= supplier_notifications.supplierId". 
					" JOIN order_products ON order_products.order_id=orders.id".
					" WHERE deliveryDate='".$current__date."' AND '".$current__time."' >=  SUBTIME(orders.deliveryTime, '01:30:00')".
					" AND order_products.status_s=0".
					" GROUP BY order_products.supplier_id";
		$orderes_noti_sup = $connection->execute($query_sup)->fetchAll('assoc');
		
		
		
		
		
		//send to suppliers
		if(sizeof($orderes_noti_sup)>0){
			for($i=0;$i<sizeof($orderes_noti_sup);$i++){
				$message_supp="Order ID: ".$orderes_noti_sup[$i]['orderId']." will have been delivered at ".$orderes_noti_sup[$i]['deliveryTime'].", ".$orderes_noti_sup[$i]['deliveryDate'].". Please confirm your products availability";
				$notifications[$i]=['orderId'=>$orderes_noti_sup[$i]['orderId'],'userId'=>$orderes_noti_sup[$i]['user_id'],'notification'=>$message_supp,'type'=>222,'seen'=>0];
			}
		}
		
		//send to delivery starff
		if (sizeof($orderes_noti_del)>0){
			$supplier_size=sizeof($orderes_noti_sup);
			for($i=0;$i<sizeof($orderes_noti_del);$i++){
				$message_del="Order ID: ".$orderes_noti_del[$i]['orderId']." will have been delivered at ".$orderes_noti_del[$i]['deliveryTime'].", ".$orderes_noti_del[$i]['deliveryDate'].". Please Picke the products and deliver to the customer";
				$notifications[$i+$supplier_size]=['orderId'=>$orderes_noti_del[$i]['orderId'],'userId'=>$orderes_noti_del[$i]['user_id'],'notification'=>$message_del,'type'=>333,'seen'=>0];
				
			}
		}
		if (sizeof($notifications)>0){
			/* print '<pre>';
			print_r($notifications);
			die();  */ 
			
			$userNotificationModel=TableRegistry::get('UserNotifications');
		$notification_entities=$userNotificationModel->newEntities($notifications);
		$notifications_save_result=$userNotificationModel->saveMany($notification_entities);
		
		if ($notifications_save_result){
			$this->Flash->success(__('The notifications have been sent.'));
			//$this->out('The notifications have been sent.');
		}else{
			$this->Flash->error(__('The notifications not have been sent.'));
			//$this->out('The notifications not have been sent.');
		}
		}else{
			$this->Flash->error(__('Nothing to notify.'));
			//$this->out('Nothing to notify.');
		}
		
		
		
	}
	
	
	/*
	 * new with call center edited 2017-01-9
	 * */
	
	public function sendNotifications(){
	
		$current__date_time=Time::now();//now
		$current__date=$current__date_time->format('Y-m-d');
		$current__time=$current__date_time->format('H:i:s');
		/* $notify_time_supp=$current__date_time->modify('+90 mins')->format('H:i:s');
		 $notify_time_del=$current__date_time->modify('+60 mins')->format('H:i:s'); */
		$orderModel  = TableRegistry::get('Orders');
		$connection = ConnectionManager::get('default');
		$userModel=TableRegistry::get('Users');
		$notifications=[];
		$notifications_callcenter=[];
		//get callcenter user list
		$callcenterQuery=$userModel->find('all',['fields'=>['id'],'conditions'=>['user_type'=>2]])->toArray();
		$callcenter_users=[];//contain callcenter staff ids
		foreach ($callcenterQuery as $callcenter){
			$callcenter_users[]=$callcenter['id'];
		}
		$callcenter_users_length=sizeof($callcenter_users);
	
		//check order- delivery status, if call center not sent notification to delivery staff, he do not know about the order, we chack whether notification is sent
		//$query_del="SELECT orders.id as orderId, delivery.user_id, orders.deliveryDate, orders.deliveryTime FROM orders JOIN user_notifications ON user_notifications.orderId=orders.id JOIN delivery ON delivery.id=orders.deliveryId WHERE orders.deliveryDate='".$current__date."' AND '".$current__time."' >=  SUBTIME(orders.deliveryTime, '01:00:00') AND type=12";
		$query_del=  "SELECT orders.id as orderId, delivery.user_id, orders.deliveryDate, orders.deliveryTime".
				" FROM orders".
				" JOIN user_notifications ON user_notifications.orderId=orders.id".
				" JOIN delivery ON delivery.id=orders.deliveryId".
				//" JOIN order_products ON order_products.order_id=orders.id".
				" WHERE orders.deliveryDate='".$current__date."' AND '".$current__time."' >=  SUBTIME(orders.deliveryTime, '01:00:00') AND user_notifications.type=12".
				//" AND order_products.status_d=0".
				//" GROUP BY delivery.user_id";
				" AND orders.status IN (1,2,3,7)";
		$orderes_noti_del = $connection->execute($query_del)->fetchAll('assoc');
		/* print '<pre>';
		print_r($orderes_noti_del);
		die(); */
		//check order- supplier status
		//$query_sup="SELECT orders.id as orderId,suppliers.user_id , orders.deliveryDate, orders.deliveryTime FROM orders JOIN supplier_notifications ON supplier_notifications.orderId=orders.id JOIN suppliers ON suppliers.id= supplier_notifications.supplierId WHERE deliveryDate='".$current__date."' AND '".$current__time."' >=  SUBTIME(deliveryTime, '01:30:00')";
		$query_sup= "SELECT orders.id as orderId,suppliers.user_id , orders.deliveryDate, orders.deliveryTime".
				" FROM orders ".
				" JOIN supplier_notifications ON supplier_notifications.orderId=orders.id".
				" JOIN suppliers ON suppliers.id= supplier_notifications.supplierId".
				//" JOIN order_products ON order_products.order_id=orders.id".
				" WHERE orders.deliveryDate='".$current__date."' AND '".$current__time."' >=  SUBTIME(orders.deliveryTime, '01:30:00')".
				//" AND order_products.status_s=0".
				//" GROUP BY order_products.supplier_id";
				"AND supplier_notifications.status=0";
		$orderes_noti_sup = $connection->execute($query_sup)->fetchAll('assoc');
	
		//send to suppliers
		if(sizeof($orderes_noti_sup)>0){
			for($i=0;$i<sizeof($orderes_noti_sup);$i++){
				$message_supp="Order ID: ".$orderes_noti_sup[$i]['orderId']." will have been delivered at ".$orderes_noti_sup[$i]['deliveryTime'].", ".$orderes_noti_sup[$i]['deliveryDate'].". Please confirm your products availability";
				$notifications[]=['orderId'=>$orderes_noti_sup[$i]['orderId'],'userId'=>$orderes_noti_sup[$i]['user_id'],'notification'=>$message_supp,'type'=>333,'seen'=>0];
	
	
	
				if ($callcenter_users_length>0){
					$message_supp_callcenter="Order ID: ".$orderes_noti_sup[$i]['orderId']." will have been delivered at ".$orderes_noti_sup[$i]['deliveryTime'].", ".$orderes_noti_sup[$i]['deliveryDate'].". Supplier ID: ".$orderes_noti_sup[$i]['user_id']." not confirm products availability yet";
					for ($x=0;$x<$callcenter_users_length;$x++){						
						$notifications_callcenter[]=['orderId'=>$orderes_noti_sup[$i]['orderId'],'userId'=>$callcenter_users[$x],'notification'=>$message_supp_callcenter,'type'=>555,'seen'=>0];;//555
					}
				}
			}
		}
		
		
	
		//send to delivery starff
		
		if (sizeof($orderes_noti_del)>0){
			$supplier_size=sizeof($orderes_noti_sup);
			for($i=0;$i<sizeof($orderes_noti_del);$i++){
				$message_del="Order ID: ".$orderes_noti_del[$i]['orderId']." will have been delivered at ".$orderes_noti_del[$i]['deliveryTime'].", ".$orderes_noti_del[$i]['deliveryDate'].". Please Picke the products and deliver to the customer";
				$notifications[]=['orderId'=>$orderes_noti_del[$i]['orderId'],'userId'=>$orderes_noti_del[$i]['user_id'],'notification'=>$message_del,'type'=>222,'seen'=>0];
	
				if ($callcenter_users_length>0){
					$message_del_callcenter="Order ID: ".$orderes_noti_del[$i]['orderId']." will have been delivered at ".$orderes_noti_del[$i]['deliveryTime'].", ".$orderes_noti_del[$i]['deliveryDate'].". Delivery staff ID: ".$orderes_noti_del[$i]['user_id']." not Picke the products yet";
					for ($x=0;$x<$callcenter_users_length;$x++){
						$notifications_callcenter[]=['orderId'=>$orderes_noti_del[$i]['orderId'],'userId'=>$callcenter_users[$x],'notification'=>$message_del_callcenter,'type'=>444,'seen'=>0];;//555;//444
					}
	
				}
	
			}
		}
		/* print '<pre>';
		print_r($notifications);
		die(); */
		
		
		$notifications=array_merge($notifications,$notifications_callcenter);
		if (sizeof($notifications)>0){
			 /* print '<pre>';
			 print_r($notifications);
			 die();  */
	
			$userNotificationModel=TableRegistry::get('UserNotifications');
			$notification_entities=$userNotificationModel->newEntities($notifications);
			$notifications_save_result=$userNotificationModel->saveMany($notification_entities);
				
			if ($notifications_save_result){
				$this->Flash->success('The notifications have been sent.');
			}else{
				$this->Flash->error('The notifications not have been sent.');
			}
		}else{
			$this->Flash->error('Nothing to notify.');
		}
	
	
	
	}
}