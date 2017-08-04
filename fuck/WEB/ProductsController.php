<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Controller\Component\PaginatorComponent;
use Cake\View\Helper\PaginatorHelper;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ProductsController extends AppController {

    public $paginate = [
        'limit' => 20,
        'order' => [
            'Products.title' => 'asc'
        ]
    ];

    public function initialize() {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(\Cake\Event\Event $event) {
        // allow all action
        parent::beforeFilter($event);
        $this->Auth->allow();
    }

    public function view($slug) {
        $product = $this->Products->find('all', [
                    'conditions' => [
                        'slug' => $slug
                    ]
                ])->autoFields(true)->first();
			
        $package_type_query = $this->Products->packageType->find('list', [
            'keyField' => 'id',
            'valueField' => 'type'
                ]
        );
        $package_type = $package_type_query->toArray();
        $this->set('package_type', $package_type);
        $this->set(compact('product'));
		
		$category_list = $this->getCategoryTree();
                $this->set('category_tree', $category_list);
    }

    public function category($slug) {
        $cat_levels = $this->getCategoryTree2();
        /* print '<pre>';
          print_r(in_array(14, $cat_levels['second']));
          die(); */
        $return = [];
        $conditions = [
            'status' => 1
        ]; // enabled broducts


        if ($slug != null) {
            // check is a perent
            $cat = $this->Products->Categories->find('all', [
                        'conditions' => [
                            'slug' => $slug
                        ]
                    ])->first();
            /* print '<pre>';
              //echo $cat['id'];
              print_r($cat_levels['cat_list']['lev3']);
              die(); */
            if (sizeof($cat) > 0) {
                $return['category'] = $cat;
                $sub_categories = [];
				
				if($cat['slug'] == 'daily-deals'){
					$conditions['is_deal'] = 1;
				}else{
                if ($cat ['parent_id'] == 0) {
                    $sub_categories = $cat_levels['cat_list']['lev1'][$cat ['id']];
                    /* print_r($sub_categories);
                      die(); */
                } elseif (in_array($cat ['id'], $cat_levels['second'])) {
                    $sub_categories = $cat_levels['cat_list']['lev2'][$cat ['id']];
                    /* print_r($sub_categories);
                      die(); */
                } elseif (in_array($cat ['id'], $cat_levels['third'])) {
                    $sub_categories = $cat_levels['cat_list']['lev3'][$cat ['id']];
                    /* print_r($sub_categories);
                      die(); */
                }
					 $conditions['category_id IN '] = $sub_categories;
				}

               


                $product_list = $this->Products->find('all', [
                            'conditions' => $conditions,
                            'fields' => [
                                'id',
                                'category_id',
                                'name',
                                'name_si',
                                'name_ta',
                                'sku',
                                'price',
                                'package',
                                'availability',
                                'image', 'is_new', 'is_sale',
                                'is_deal','old_price',
                                'slug'
                            ]
                        ])->contain([
                    'packageType' => function ($q) {
                        return $q->select([
                                    'id',
                                    'type'
                        ]);
                    }
                        ]);
                        /* print '<pre>';
                          print_r($product_list);
                          die(); */

                        $return ['status'] = 0;
                        if (sizeof($product_list) > 0) {
                            $return ['message'] = 'Success';
                        } else {
                            $return ['message'] = 'products not found';
                        }
                        $return ['result'] = $product_list;
                    } else {
                        $return ['status'] = 404;
                        $return ['message'] = 'category not found';
                        $return ['result'] = [];
                    }
                } else {
                    $return ['status'] = 404;
                    $return ['message'] = 'Please supply category id';
                    $return ['result'] = [];
                }
                $this->set('category', $return ['category']);
                $this->set ('cat_slug', $slug);
                $this->set('products', $this->paginate($product_list));
				
				

				//echo $this->request->param('slug');
				//die();
                //Url paginator e.g.: /user/153/?page=2
                $paginateUrl = ['slug' => $slug];
				//print_r($paginateUrl);
				//die();
                $this->set(compact('paginateUrl'));

                //$articles = $this->paginate($product_list);
                $category_list = $this->getCategoryTree();
                $this->set('category_tree', $category_list);
            }

            public function getCategoryTree() {

                // Get first level categories
                $categories = TableRegistry::get('Categories');

                $first_level_categories = $categories->find()->select([
                            'id',
                            'title',
                            'slug'
                        ])->where([
                            'level' => '0',
                            'status' => '1'
                        ])->toArray();

                $second_level_categories = $categories->find()->select([
                            'id',
                            'title',
                            'parent_id',
                            'slug'
                        ])->where([
                            'level' => '1',
                            'status' => '1'
                        ])->toArray();

                $second_category_array = array();

                foreach ($second_level_categories as $second_category) {
                    // $second_category_array[$second_category['parent_id']] = array();
                    if ($second_category && !is_array($second_category_array [$second_category ['parent_id']]))
                        $second_category_array [$second_category ['parent_id']] = array();
                    $second_category_array [$second_category ['parent_id']] [] = $second_category;
                }

                $third_level_categories = $categories->find()->select([
                            'id',
                            'title',
                            'parent_id',
                            'slug'
                        ])->where([
                            'level' => '2',
                            'status' => '1'
                        ])->toArray();

                $third_category_array = array();

                foreach ($third_level_categories as $third_category) {
                    // $second_category_array[$second_category['parent_id']] = array();
                    if ($third_category && !is_array($third_category_array [$third_category ['parent_id']]))
                        $third_category_array [$third_category ['parent_id']] = array();
                    $third_category_array [$third_category ['parent_id']] [] = $third_category;
                }

                return [
                    $first_level_categories,
                    $second_category_array,
                    $third_category_array
                ];
            }

            public function getCategoryTree2() {

                //Get first level categories
                $categories = TableRegistry::get('Categories');

                $first_level_categories = $categories->find('list', ['keyField' => 'id', 'valueField' => 'parent_id'])
                        ->where(['parent_id' => '0'])
                        ->toArray();
                $first_level_categories_id = array_keys($first_level_categories);

                $second_level_categories = $categories->find('list', ['keyField' => 'id', 'valueField' => 'parent_id'])
                        ->where(['parent_id IN ' => $first_level_categories_id])
                        ->toArray();
                $second_level_categories_id = array_keys($second_level_categories);

                $third_level_categories = $categories->find('list', ['keyField' => 'id', 'valueField' => 'parent_id'])
                        ->where(['parent_id IN ' => $second_level_categories_id])
                        ->toArray();
                $third_level_categories_id = array_keys($third_level_categories);
                $catArray = [];
                $catArray2 = [];
                $catArray3 = [];
                foreach ($first_level_categories as $cat_id => $cat_parent) {
                    $catArray[$cat_id][] = $cat_id;
                    foreach ($second_level_categories as $cat2_id => $cat2_parent) {
                        if ($cat2_parent == $cat_id) {
                            $catArray[$cat_id][] = $cat2_id;
                            $catArray2[$cat2_id][] = $cat2_id;
                            foreach ($third_level_categories as $cat3_id => $cat3_parent) {
                                if ($cat3_parent == $cat2_id) {
                                    $catArray[$cat_id][] = $cat3_id;
                                    $catArray2[$cat2_id][] = $cat3_id;
                                    $catArray3[$cat3_id] = $cat3_id;
                                }
                            }
                        }
                    }
                }



                return [
                    'first' => $first_level_categories_id,
                    'second' => $second_level_categories_id,
                    'third' => $third_level_categories_id,
                    'cat_list' => [
                        'lev1' => $catArray,
                        'lev2' => $catArray2,
                        'lev3' => $catArray3
                ]];
            }
            
            public function autocomplete() {
            	header('Content-type: application/json');
            	$word=$this->request->data('query');
            	$like="%".$word."%";
            	$data=$this->Products->find()
            	->select(['id','slug','name'])
            	->where(['name LIKE'=>$like])
            	->toArray();
            	echo json_encode($data);
            	die();
            }

        }
        