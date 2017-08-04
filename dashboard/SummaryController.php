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
use Cake\Collection\Collection;

use PHPExcel;
use Symfony\Component\VarDumper\Cloner\Data;
/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 */
class SummaryController extends AppController {
	
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Notification');
		$this->loadComponent('RequestHandler');
	
	}
	
	


public function summary() {	
	$from = $this->request->query('from');	
	$to = $this->request->query('to');	
	
	$validate_from=true;
	$validate_to=true;
	$valid_rang=true;
	
	if($from!=null || $to!=null){
	$validate_from=$this->validateDate($from);
	$validate_to=$this->validateDate($to);
		if($validate_from || $validate_to){
				$valid_rang=strtotime($from) <= strtotime($to) ? true: false;
				
		}
	}
	
	
	
	
	

	$orders_model = $this->loadModel ( 'Orders' );
	$customer_model = $this->loadModel ( 'Customers' );
	
	
		
	if($from!=null && $to!=null && $validate_to && $validate_from && $valid_rang){
	//1
		$order_count_conditions["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) >="]=$from;
		$order_count_conditions["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) <="]=$to;

		$total_sum_conditions["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) >="]=$from;
		$total_sum_conditions["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) <="]=$to;
		
		$customer_count_conditions["DATE(created) >="]=$from;
		$customer_count_conditions["DATE(created) <="]=$to;
	//2	
		$pie_erp_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) >="]=$from;
		$pie_erp_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) <="]=$to;
		
		$pie_web_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) >="]=$from;
		$pie_web_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) <="]=$to;
		
		$pie_mob_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) >="]=$from;
		$pie_mob_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) <="]=$to;
	//3	
		$grop_by_plateforme_last_10_days_conditions["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) >="]=$from;
		$grop_by_plateforme_last_10_days_conditions["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) <="]=$to;	
	}else{
	//1 -
	//2
		$pie_erp_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) ="]=date('Y-m-d');
		$pie_web_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) ="]=date('Y-m-d');
		$pie_mob_count_condition["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) ="]=date('Y-m-d');
	//3	
		$ten_days_ago = date('Y-m-d', strtotime('-10 days', strtotime(date('Y-m-d'))));
		$grop_by_plateforme_last_10_days_conditions["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')) >"]=$ten_days_ago;
	
	}
	
	
	//front 1st section top count
		$order_count_conditions['deleted !=']=1;
		$total_sum_conditions['deleted !=']=1;//'paymentStatus !='=>1
		$customer_count_conditions['status']=1;
	
	$order_count=$orders_model->find('all', [
								'fields' => ['count_orders' => 'COUNT(id)'],
								'conditions'=>$order_count_conditions
								])->first();
								//echo $order_count->Sql();
								//die();
	
	$total_sum=$orders_model->find('all', [
								'fields' => ['total_sum' => 'TRUNCATE(SUM(total),2)'],
								'conditions'=>$total_sum_conditions
								])->first('created');	
	
	$customer_count=$customer_model->find('all', [
								'fields' => ['customer_count' => 'COUNT(id)'],
								'conditions'=>$customer_count_conditions
								])->first();
	
	$this->set('total_sum',$total_sum->total_sum);
	$this->set('order_count',$order_count->count_orders);				
	$this->set('customer_count',$customer_count->customer_count);			
	
//	$order_count_graph_2,$total_sum_graph_1,$customer_count_graph_3 not used in front now
/*							
	$order_count_graph_2 = $orders_model->find('all', [
								'fields' => ['count_orders' => 'COUNT(id)'],
								'group' => ["YEAR(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))","WEEK(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))"],
								'order' =>['created'=>'ASC']
								]) 
								->map(function ($row) { 
								return (int) $row->count_orders;        
								})
								->toArray();
    
	$total_sum_graph_1 = $orders_model->find('all', [
								'fields' => ['total_sum' => 'TRUNCATE(SUM(total),2)'],
								'conditions'=>['deleted !='=>1, 'paymentStatus !='=>1],
								'group' => ["YEAR(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))","WEEK(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))"],
								'order' =>['created'=>'ASC']
								]) 
								->map(function ($row) { 
									return (int) $row->total_sum;        
								})
								->toArray();							
	
	$customer_count_graph_3 = $customer_model->find('all', [
								'fields' => ['customer_count' => 'COUNT(id)'],
								'group' => ["YEAR(created)","WEEK(created)"],
								'order' =>['created'=>'ASC']
								]) 
								->map(function ($row) { 
									return (int) $row->customer_count;        
								})
								->toArray();
								
	$this->set('total_sum_graph_1',$total_sum_graph_1);
	$this->set('order_count_graph_2',$order_count_graph_2);
	$this->set('customer_count_graph_3',$customer_count_graph_3);
								*/
					
			
	
	
	
	//pie chart
	$pie_erp_count_condition['deleted !=']=1;
	$pie_erp_count_condition['platform']=1;		
	$pie_web_count_condition['deleted !=']=1;
	$pie_web_count_condition['platform']=2;	
	$pie_mob_count_condition['deleted !=']=1;
	$pie_mob_count_condition['platform']=3;
	
	
	$pie_erp_count=$orders_model->find('all', [
								'fields' => ['count_orders' => 'COUNT(id)'],
								'conditions'=>$pie_erp_count_condition
								])->first();
	$pie_web_count=$orders_model->find('all', [
								'fields' => ['count_orders' => 'COUNT(id)'],
								'conditions'=>$pie_web_count_condition
								])->first();
	$pie_mob_count=$orders_model->find('all', [
								'fields' => ['count_orders' => 'COUNT(id)'],
								'conditions'=>$pie_mob_count_condition
								])->first();
								
	$pie_platform_count=['erp'=>$pie_erp_count->count_orders,'web'=>$pie_web_count->count_orders,'mob'=>$pie_mob_count->count_orders];	
	$this->set('pie_platform_count',$pie_platform_count);
	 
	
	
	$grop_by_plateforme_last_10_days_conditions['deleted !=']=1;
	
												 
	$grop_by_plateforme_last_10_days = $orders_model->find('all', [
								'fields' => [
												'sum' => 'TRUNCATE(SUM(total),2)',
												'period'=>"DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))",
												'platform'
											],
								'conditions'=>$grop_by_plateforme_last_10_days_conditions,
								'group' => ["DATE(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))","platform"],
								'order' =>['period'=>'ASC']
					]) 
					->map(function ($row) { 
						$platform="";
						switch($row->platform){
							case 1:
							$platform="erp";
							break;
							case 2:
							$platform="web";
							break;
							case 3:
							$platform="mobile";
							break;
							default:
							return;
						}
						return ['period'=>$row->period,'platform'=>$platform,'sum'=>$row->sum];        
					 })
					->toArray();
					
					$dates=[];
					$date_by_platform=[];//['date'=>['erp'=>'','web'=>'','mobile'=>''],'date'=>[]]
					foreach($grop_by_plateforme_last_10_days as $x){
						if(!in_array($x['period'],$dates)){
							$dates[]=$x['period'];
						}
						$date_by_platform[$x['period']][$x['platform']]=$x['sum'];
					}
					$barchart_data=[];
					foreach($date_by_platform as $date=>$platfoems){
						
						$erp=isset($platfoems['erp'])?$platfoems['erp']:0;
						$web=isset($platfoems['web'])?$platfoems['web']:0;
						$mobile=isset($platfoems['mobile'])?$platfoems['mobile']:0;						
						$total=$erp+$web+$mobile;					
						
						$barchart_data[]=['period'=>$date,'erp'=>$erp,'web'=> $web,'mobile'=>$mobile,'total'=>$total];
					}
					$this->set('barchart_data',$barchart_data);
					
					//line chart data
					$line_chart=[];
					$line_chart_erp=[];
					$line_chart_web=[];
					$line_chart_mobile=[];
					$line_chart_total=[];
					$line_chart_dates=[];
					
					foreach($date_by_platform as $date=>$platfoems){	
						$erp=isset($platfoems['erp'])?$platfoems['erp']:0;
						$web=isset($platfoems['web'])?$platfoems['web']:0;
						$mobile=isset($platfoems['mobile'])?$platfoems['mobile']:0;									
						$line_chart_erp[]=$erp;
						$line_chart_web[]=$web;					
						$line_chart_mobile[]=$mobile;
						$line_chart_total[]=$erp+$web+$mobile;					
						$line_chart_dates[]=date('m/d',strtotime($date));						
					}
					$line_chart_data=['erp'=>$line_chart_erp,'web'=>$line_chart_web,'mobile'=>$line_chart_mobile,'all'=>$line_chart_total,'dates'=>$line_chart_dates];
					
					$this->set('line_chart_data',$line_chart_data);
					$message=null;
					if(!$validate_to || !$validate_from || !$valid_rang){
					 $from=null;
					 $to=null;
					 $message="Please select a valid date range";					 
					}
					
					$this->set('from',$from);
					$this->set('to',$to);
					$this->set('message',$message);
	 
	 /*
	  'count_orders' => 'COUNT(id)',
			'created'=>"STR_TO_DATE(created, '%m/%d/%y, %h:%i %p')",
			'created_x'=>"created",
			'created_date'=>"DAY(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))",
			'created_month'=>"MONTH(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))",
			'created_year'=>"YEAR(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))",
			'created_week'=>"WEEK(STR_TO_DATE(created, '%m/%d/%y, %h:%i %p'))",
	 */
	 
}	

function validateDate($date)
{
    $d = \DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
	


}
