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
class ProductsController extends AppController {

    public function isAuthorized($user) {
        if (in_array($this->request->action, ['view', 'index', 'edit'])) {
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
        $products = $this->paginate($this->Products);
		$s = $this->request->query('s');	
		if (! empty ( $s )) {
			$products=$this->paginate($this->Products, ['conditions' => [
					'OR' => [
							'Products.name LIKE' => '%' . $s . '%',
							'Products.name_si LIKE' => '%' . $s . '%',							
							'Products.name_ta LIKE' => '%' . $s . '%'
							
					]]
			
			]);
			
			if($products->isEmpty()){
				$this->Flash->error ( __ ( 'No Result Found' ) );
			}
			
		}
		$this->set ( 's', $s );
		
        $package_type_query = $this->Products->packageType->find('list', ['keyField' => 'id', 'valueField' => 'type']);
        $package_type = $package_type_query->toArray();
        $this->set('package_type', $package_type);
        $this->set(compact('products'));
        $this->set('_serialize', ['products']);
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $product = $this->Products->get($id, ['contain' => ['OrderProducts', 'productSuppliers']]);
        $package_type_query = $this->Products->packageType->find('list', ['keyField' => 'id', 'valueField' => 'type']);
        $package_type = $package_type_query->toArray();
        $this->set('package_type', $package_type);

        $suppliers_query = $this->Products->productSuppliers->find('all', ['conditions' => ['product_id' => $id]])->select(['supp.id', 'supp.firstName', 'supp.lastName'])
                ->join([
            'table' => 'suppliers',
            'alias' => 'supp',
            'type' => 'INNER',
            'conditions' => 'supp.id = supplier_id'
                ]);
        $suppliers = $suppliers_query->toArray();
        $this->set('suppliers', $suppliers);
        $this->set(compact('product'));
        $this->set('_serialize', ['product']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $product = $this->Products->newEntity();
        if ($this->request->is('post')) {
            $product_suppliers = [];
//         	print '<pre>';
//         	print_r($this->request->data());
//         	die();

            $product = $this->Products->patchEntity($product, $this->request->data);
            if ($this->Products->save($product)) {
                /* print '<pre>';
                  print_r($product);
                  echo '<br>'.$product;
                  print_r($product->id);
                  print_r($product->supplierId[0]);
                  print_r($product->supplierId[1]);
                  echo sizeof($product->supplierId);
                  die(); */
                for ($i = 0; $i < sizeof($product->supplierId); $i++) {
                    $product_suppliers[$i] = ['product_id' => $product->id, 'supplier_id' => $product->supplierId[$i]];
                }


                //http://stackoverflow.com/questions/32240026/patchentity-appears-to-erase-foreign-keys

                $product_supplier_entities = $this->Products->ProductSuppliers->newEntities($product_suppliers);
                /*  print '<pre>';
                  print_r($product_suppliers);
                  echo $product_supplier_entities;
                  print_r($product_supplier_entities);
                  die(); */
                $product_supplier_result = $this->Products->ProductSuppliers->saveMany($product_supplier_entities);

                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The product could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('product'));
        $this->set('_serialize', ['product']);


        $suppliers = $this->Products->ProductSuppliers->Suppliers->find()
                ->select(['id', 'firstName', 'lastName'])
                ->formatResults(function($results) {

            return $results->combine('id', function($row) {
                        return $row['firstName'] . ' ' . $row['lastName'];
                    }
            );
        });

        $this->set(compact('suppliers'));

        $packages = $this->Products->PackageType->find()
                ->select(['id', 'type'])
                ->formatResults(function($results) {
            /* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
            return $results->combine('id', function($row) {
                        return $row['type'];
                    }
            );
        });

        $this->set(compact('packages'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $product = $this->Products->get($id, [
            'contain' => []
        ]);

        $new_product_suppliers = [];

        $current_suppliers_query = $this->Products->ProductSuppliers->find('list', ['valueField' => 'supplier_id', 'conditions' => ['product_id' => $id]]);
        $current_suppliers = $current_suppliers_query->toArray();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $product = $this->Products->patchEntity($product, $this->request->data);
            if ($this->Products->save($product)) {

                for ($i = 0; $i < sizeof($product->supplierId); $i++) {
                    $new_product_suppliers[$i] = ['product_id' => $product->id, 'supplier_id' => $product->supplierId[$i]];
                }
                //delete old suppliers and add nes set
                //$supliere_delet_entity=$this->Products->ProductSuppliers->get($current_suppliers);
                $supliere_delet_resul = $this->Products->ProductSuppliers->deleteAll(['product_id' => $id]);

                $product_supplier_entities = $this->Products->ProductSuppliers->newEntities($new_product_suppliers);
                $product_supplier_result = $this->Products->ProductSuppliers->saveMany($product_supplier_entities);

                /*
                  //comparisan for suppliers @ edditing time
                  $compare_new_old=array_diff($new_product_suppliers, $current_suppliers);//compare old and new supplier list
                  if (sizeof($compare_new_old)==0){
                  return;
                  }else{
                  $compare_new_with_cno=array_diff($new_product_suppliers, $compare_new_old);//compare above result with new supplier list
                  $compare_old_cno=array_diff($current_suppliers, $compare_new_old);//compare above result with old supplier list

                  $old_size=sizeof($current_suppliers);//1
                  $new_size=sizeof($new_product_suppliers);//2
                  $comp_old_new_size=sizeof($compare_new_old);//3
                  $compare_old_with_old_new_size=sizeof($compare_old_cno);//4
                  $compare_new_with_old_new_size=sizeof($compare_new_with_cno);//5
                  if ($new_size==0){
                  //no suppliers
                  }
                  elseif($compare_old_with_old_new_size==$old_size+$comp_old_new_size){
                  //new set contain with old values
                  }else if($compare_new_with_old_new_size==$new_size+$comp_old_new_size){
                  //deleted some values
                  }elseif($compare_old_with_old_new_size==$new_size && $compare_new_with_old_new_size==$old_size){
                  //compleately different values
                  }


                  } */




                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The product could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('product'));
        $this->set('_serialize', ['product']);
        $suppliers = $this->Products->ProductSuppliers->Suppliers->find()
                ->select(['id', 'firstName', 'lastName'])
                ->formatResults(function($results) {
            /* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
            return $results->combine('id', function($row) {
                        return $row['firstName'] . ' ' . $row['lastName'];
                    }
            );
        });
        $this->set(compact('suppliers'));
        $packages = $this->Products->PackageType->find()
                ->select(['id', 'type'])
                ->formatResults(function($results) {
            /* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
            return $results->combine('id', function($row) {
                        return $row['type'];
                    }
            );
        });

        $this->set(compact('packages'));


        $this->set('current_suppliers', $current_suppliers);
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The product has been deleted.'));
        } else {
            $this->Flash->error(__('The product could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * import data from a excel sheet
     */
    public function import() {
    	$errors=[];//store error data
        if ($this->request->is('post')) {
            if (!empty($this->request->data('productsSheet'))) {
			
			$filename = $this->request->data['productsSheet']['name'];
			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			if($extension==='csv'){

                $file = $this->request->data('productsSheet.tmp_name');
                //echo $file;
                /* $skufinder=$this->Products->find('all',['conditions'=>['sku'=>'Kolikuttu banana'],'fields'=>['id']]);
                  $skucount=$skufinder->count();
                  echo $skucount;
                  die(); */

                $data = $this->Import->prepareEntityData($file, ['append' => true]);
                $products = [];
                $packages = [];
                $suppliers = [];
                
                $count = 0;
                if (sizeof($data)>0){                	
                
                foreach ($data as $product) {
                	$error=[];
                	
                	if (isset($product['category_id']) && $product['category_id'] != "" && $this->checkCategory($product['category_id']) ) {
                		$products [$count] ['category_id'] = $product ['category_id'];
                	}else{
                		$error[$count]['category_id']="product category id is not valid";
                	}
                	
                	if (isset($product['name']) && $product['name'] != "") {
                		$products[$count]['name'] = $product['name'];
                	}else{
                		$error[$count]['name']="product name is not valid";
                	}
                	
                	if (isset($product['name_si']) && $product['name_si'] != "") {
                		$products[$count]['name_si'] = $product['name_si'];
                	}
                	
                	if (isset($product['name_ta']) && $product['name_ta'] != "") {
                		$products[$count]['name_ta'] = $product['name_ta'];
                	}
                	
                	if (isset($product['sku']) && $product['sku'] != "") {
                		$products[$count]['sku'] = $product['sku'];
                	}else{
                		$error[$count]['sku']="product sku is not valid";
                	}
                	
                	
                    $skufinder = $this->Products->find('all', ['conditions' => ['sku' => $product['sku']]]);
                    $skucount = $skufinder->count();           
                    if ($skucount > 0) {
                        $currentsku = $skufinder->first();
                        $products[$count]['id'] = $currentsku->id;
                        
                    }else{
                    	$products[$count]['created'] = date('Y-m-d H:i:s');                    	
                    }
                    
                    $products[$count]['modified'] = date('Y-m-d H:i:s');
                    
                    

                    $products[$count]['description'] = isset($product['description']) ? $product['description']:null;                    
                    $products[$count]['short_description'] = isset($product['short_description']) ? $product['short_description']:null;
                    
                    if (isset($product['price']) && $product['price'] != "") {
                    	$products[$count]['price'] = $product['price'];
                    }else{
                    	$error[$count]['price']="product price is not valid";
                    }
                    
                    if (isset($product['cost']) && $product['cost'] != "") {
                    	$products[$count]['cost'] = $product['cost'];
                    }else{
                    	$error[$count]['cost']="product cost is not valid";
                    }
                    
                    $products[$count]['availability'] = isset($product['availability']) && $product['availability'] == 1 ? 1: 0;
                    
                    if (isset($product['image']) && $product['image'] != "") {
                    	$products[$count]['image'] = $product['image'];
                    }else{
                    	//$products[$count]['image'] = '/product-img.png';
                    	$error[$count]['image']="product images is not valid";
                    }
                    
                    $products[$count]['status'] = isset($product['status']) && $product['status'] == 1 ? 1: 0;
                    $products[$count]['is_featured'] = isset($product['is_featured']) && $product['is_featured']==1  ? 1 : 0;
                    $products[$count]['is_new'] = isset($product['is_new']) && $product['is_new']==1 ? 1 : 0;
                    $products[$count]['is_sale'] = isset($product['is_sale']) && $product['is_sale']==1 ? 1 : 0;
                    
                    if (isset($product['slug']) && $product['slug'] != "") {
                    	$products [$count] ['slug'] = $product ['slug'];
                    }else{
                    	$error[$count]['slug']="product slug not valid";
                    }
                    
                    $products[$count]['is_deal'] = isset($product['is_deal']) && $product['is_deal'] == 1 ? 1: 0;
                    $products[$count]['old_price'] = isset($product['old_price']) && $product['old_price'] != "" ? $product['old_price']: 0;   
                                       
                    

                    //----------------product package--------------                    
                    if(isset($product['package']) && $product['package']!=""){                     			 
                   		 $package_query = $this->Products->packageType->find('all', ['fields' => ['id']])->where(['type' => $product['package']])->first();
	                     if (sizeof($package_query) > 0) {
	                    	    $packages[$count]['id'] = $package_query->id;
	                   	 }
                   		 $packages[$count]['type'] = $product['package'];
                    }else{
                    	$error[$count]['package']='You havent set correct type of package';
                    }
                    //----------------product supplers--------------
					$chcke_suppliers=$this->checkSuppliers($product['suppliers']);
					if($chcke_suppliers['status']){
						$suppliers[$count] = $product['suppliers'];
					}else{
						$error[$count]['suppliers']=$chcke_suppliers['message'];
					}
                    //echo sizeof($errors);
                    //die();
					if (sizeof($error)>0){
						$errors[]=$error;
						
						if (isset($packages[$count])){
							unset($packages[$count]);
						}
						if (isset($products[$count])){
							unset($products[$count]);
						}
						if (isset($suppliers[$count])){
							unset($suppliers[$count]);
						}
						$session = $this->request->session();
						$session->write('products_import_errors', $errors);
					}
                    $count++;
                }
                
                $packages = array_values($packages);
                $products = array_values($products);
                $suppliers = array_values($suppliers);
                
                 //echo sizeof($errors);
                //die();
                 /*  print '<pre>';
                  print_r($products);
                  echo '-----------------<br>';
                  print_r($packages);
                  echo '-----------------<br>';
                  print_r($suppliers);
                  echo '-----------------<br>';
                  print_r($errors);
                  die(); */  

                $package_entities = $this->Products->packageType->newEntities($packages);
                $package_save = $this->Products->packageType->saveMany($package_entities);	
                /*  print '<pre>';
                 print_r($package_save);
                 echo 'xxxx';
                 die(); */
                  
                if ($package_save) {
                    $this->Flash->success(__('packages save successfully'));
                    for ($i = 0; $i < sizeof($package_save); $i++) {

                        $products[$i]['package'] = $package_save[$i]->id;
                    }
                    
                    
                   /*  print '<pre>';
                    print_r($products);
                    echo 'xxxx';
                    die(); */
                    
                    
                    $product_entities = $this->Products->newEntities($products);
                    $product_save = $this->Products->saveMany($product_entities);
                    $product_ids=[];
                     /* print '<pre>';
                      //print_r($product_save);
                      die(); */

                    if ($product_save) {
                    	
                        $this->Flash->success(__('products save successfully.'));
                        $orderProducts = []; //add orderId,productId
                        for ($j = 0; $j < sizeof($product_save); $j++) {
                        	
                            $product_suppliers = explode(',', $suppliers[$j]);
                            
                            for ($k = 0; $k < sizeof($product_suppliers); $k++) {                            	
                                $orderProducts[]['product_id'] = $product_save[$j]->id;
                                $product_ids[]=$product_save[$j]->id;
                                $arr_size = sizeof($orderProducts);
                                $orderProducts[$arr_size - 1]['supplier_id'] = $product_suppliers[$k];
                            }
                        }
                        
                        $delete_query = $this->Products->productSuppliers->query();
                        $delete_query->delete()->where(['product_id IN' => $product_ids])->execute();//delete old product suppliers details for all products in this excel sheet
                        
                        $orderProducts_entities = $this->Products->productSuppliers->newEntities($orderProducts);
                        $orderProducts_save = $this->Products->productSuppliers->saveMany($orderProducts_entities);
                        /* print '<pre>';
                          print_r($product_ids);
                          die(); */
                        if ($orderProducts_save) {
                            $this->Flash->success(__('productSuppliers save successfully.'));
                        } else {
                            $this->Flash->error(__('productSuppliers not save.'));
                        }
                    } else {
                        $this->Flash->error(__('products not save'));
                    }
                } else {
                    //$this->Flash->error(__('packages not save'));
                	$this->Flash->error(__('products data not saved'));
                }
                
                }else{
                	$this->Flash->error(__('No data found in the file'));
                	return $this->redirect(['action' => 'import']);
                }
				
				}else{
					 $this->Flash->error(__('Please upload a .CSV file'));
					 return $this->redirect(['action' => 'import']);
				}
                
            } else {
                $this->Flash->error(__('Please select an EXCEl file'));
                return $this->redirect(['action' => 'import']);
            }
        }
        //http://stackoverflow.com/questions/22590957/how-do-i-best-avoid-inserting-duplicate-records-in-cakephp
        //https://github.com/cewi/excel
        // * http://stackoverflow.com/questions/4557564/how-to-save-other-languages-in-mysql-table
        //* ALTER TABLE posts MODIFY title VARCHAR(255) CHARACTER SET UTF8;
    }
    
    /**
     * check suppliers are correctly assigned, 
     * $suppliers=1,2,3
     */
    public function checkSuppliers($suppliers){
    	$product_suppliers = explode(',', $suppliers);
    	$return=['status'=>true,'message'=>'product suppliers are assigned'];
    	if (sizeof($product_suppliers)>0){
    		for ($i=0;$i<sizeof($product_suppliers);$i++){
    			if (!is_int((int)$product_suppliers[$i])){
    				$return=['status'=>false,'message'=>'somethin wrong with assigned suppliers'];
    			}
    			else{
    				$product_supplier_model = $this->loadModel ( 'suppliers' );
    				$product_supplier=$product_supplier_model->find('all', ['fields' => ['id']])->where(['id' => $product_suppliers[$i]]);
    				
    				if($product_supplier->count()<=0){
    					$return=['status'=>false,'message'=>'some of suppliers not found in supplier list'];
    				}
    			}
    		}
    		
    	}else{
    		$return=['status'=>false,'message'=>'product suppliers are not assigned'];
    	}
    	return $return;
    }
    
    public function checkCategory($category_id){
    	$category_model = $this->loadModel ( 'categories' );    	
    	if(is_int((int)$category_id)){    	
	    	$category=$category_model->find('all', ['fields' => ['id']])->where(['id' => $category_id]);    	
	    	if($category->count()>0){
	    		$return=true;
	    	}else{
	    		$return=false;
	    	}    	
    	}else{
    		$return=false;
    	}
    	return $return;    	
    }

}
