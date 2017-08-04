<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

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

        if ($this->request->is('post')) {
            if (!empty($this->request->data('productsSheet'))) {

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
                foreach ($data as $product) {
                    $skufinder = $this->Products->find('all', ['conditions' => ['sku' => $product['sku']]]);
                    $skucount = $skufinder->count();
                    /* print '<pre>';
                      print_r($skufinder->toArray()); */
                    //echo $skucount."<br>";
                    //echo $product['name']."<br>";

                    if ($skucount > 0) {
                        $currentsku = $skufinder->first();
                        $products[$count]['id'] = $currentsku->id;
                    }
                    $products[$count]['name'] = $product['name'];
                    if (isset($product['name_si']) && $product['name_si'] != "") {
                        $products[$count]['name_si'] = $product['name_si'];
                    }
                    if (isset($product['name_ta']) && $product['name_ta'] != "") {
                        $products[$count]['name_ta'] = $product['name_ta'];
                    }

                    //Added the category id
                    $products [$count] ['category_id'] = $product ['category_id'];
                    $products [$count] ['slug'] = $product ['url_key'];

                    $products[$count]['sku'] = $product['sku'];
                    $products[$count]['description'] = $product['description'];
                    $products[$count]['price'] = $product['price'];
                    
                    if (isset($product['availability']) && $product['availability'] != "") {
                        $products[$count]['availability'] = $product['availability'];
                    } else {
                        $products[$count]['availability'] = 1;
                    }
                    
                    if (isset($product['image']) && $product['image'] != "") {
                        $products[$count]['image'] = $product['image'];
                    }else{
                        $products[$count]['image'] = '/product-img.png';
                    }
                    
                    if (isset($product['status']) && $product['status'] != "") {
                        $products[$count]['status'] = $product['status'];
                    } else {
                        $products[$count]['status'] = 1;
                    }
                    
                    if (isset($product['is_deal']) && $product['is_deal'] == "1") {
                        $products[$count]['is_deal'] = 1;
                    } else {
                        $products[$count]['is_deal'] = 0;
                    }
                    
                    if (isset($product['old_price']) && $product['old_price'] != "") {
                        $products[$count]['old_price'] = $product['old_price'];
                    } else {
                        $products[$count]['old_price'] = 0;
                    }

                    //$products[$count]['suppliers']=['_ids'=>[$product['suppliers']]];    			 
                    $package_query = $this->Products->packageType->find('all', ['fields' => ['id']])->where(['type' => $product['package']])->first();
                    if (sizeof($package_query) > 0) {
                        $packages[$count]['id'] = $package_query->id;
                    }
                    $packages[$count]['type'] = $product['package'];

                    $suppliers[$count] = $product['suppliers'];

                    $count++;
                }
                /*  print '<pre>';
                  print_r($products);
                  print_r($packages);
                  print_r($suppliers);
                  die(); */

                $package_entities = $this->Products->packageType->newEntities($packages);
                $package_save = $this->Products->packageType->saveMany($package_entities);
                /* print '<pre>';
                  print_r($package_save);
                  die(); */
                if ($package_save) {
                    $this->Flash->success(__('packages save successfully'));
                    for ($i = 0; $i < sizeof($package_save); $i++) {

                        $products[$i]['package'] = $package_save[$i]->id;
                    }
                    $product_entities = $this->Products->newEntities($products);
                    $product_save = $this->Products->saveMany($product_entities);
                    /* print '<pre>';
                      print_r($product_save);
                      die(); */

                    if ($product_save) {
                        $this->Flash->success(__('products save successfully.'));
                        $orderProducts = []; //add orderId,productId
                        for ($j = 0; $j < sizeof($product_save); $j++) {
                            $product_suppliers = explode(',', $suppliers[$j]);
                            for ($k = 0; $k < sizeof($product_suppliers); $k++) {
                                $orderProducts[]['product_id'] = $product_save[$j]->id;
                                $arr_size = sizeof($orderProducts);
                                $orderProducts[$arr_size - 1]['supplier_id'] = $product_suppliers[$k];
                            }
                        }
                        /* print '<pre>';
                          print_r($orderProducts);
                          die(); */
                        $orderProducts_entities = $this->Products->productSuppliers->newEntities($orderProducts);
                        $orderProducts_save = $this->Products->productSuppliers->saveMany($orderProducts_entities);
                        /* print '<pre>';
                          print_r($orderProducts_save);
                          die(); */
                        if ($orderProducts_save) {
                            $this->Flash->success(__('productSuppliers save successfully.'));
                        } else {
                            $this->Flash->error(__('productSuppliers not save.'));
                        }
                    } else {
                        $this->Flash->reeor(__('products not save'));
                    }
                } else {
                    $this->Flash->error(__('packages not save'));
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

}
