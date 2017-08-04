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
use App\Model\Table\CartProductsTable;
use App\Model\Table\CartTable;
use Cake\I18n\Time;
use Cake\Mailer\Email;

class CartController extends AppController {

    public function isAuthorized($user) {
        if (in_array($this->request->action, [
                    /* 'getcart', */
                    'getAddress',
                    'updateAddress',
                    'addAddress',
                    'updateDeliveryTime',
                    'completeCheckout',
                    'getCheckout',
                    'checkout',
                    'dashboard',
                    'placeOrder',
                    'getwishlist',
                    'deleteWishListItem',
                    'addWishListItem',
                    'verifyDeliverySlots'
                        ]
                )) {
            if (isset($user ['user_type']) && $user ['user_type'] == 5) {
                return true;
            }
        }
        return parent::isAuthorized($user);
    }

    public function beforeFilter(\Cake\Event\Event $event) {
        // allow all action
        parent::beforeFilter($event);
        $this->Auth->allow([
            'addproduct',
            'deleteproduct',
            'getcart',
            'quickedit',
            'sendNotification',
            'boka',
            'verifyDeliverySlots'
        ]);
    }

    public function initialize() {
        parent::initialize();
        $this->loadComponent('Notification');
        $this->loadComponent('RequestHandler');
    }

    public function addproduct() {

        // header('Content-type: application/json');
        // if ($this->request->is('post')) {
        // $data=$this->request->data();//cart_id,product_id,qty,type[default-1]
        // $product_id = $this->request->query('product_id');
        // $product_qty = $this->request->query('qty');
        $product_id = $this->request->data('product_id');
        $product_qty = $this->request->data('qty');

        $session = $this->request->session();
        $phpsessid = $session->read('d2d_session_id');
        $phpsessid = '123456';
        $phpsessid = session_id();
        $cart_product_model = $this->loadModel('CartProducts');
        if ($session->read('cart_id') == "") {
            $cart_id = $this->add('', $phpsessid);
            $session->write('cart_id', $cart_id);
        } else {
            $cart_id = $session->read('cart_id');
        }

        if ($product_id != null && $product_qty != null) {
            $cart_product_model = $this->loadModel('CartProducts');
            if ($cart_id && !($this->__isInCart($cart_id, $product_id, 1))) {
                $is_in_wishlist = $this->__isInCart($cart_id, $product_id, 0);

                $data = [
                    'cart_id' => $cart_id,
                    'product_id' => $product_id,
                    'qty' => $product_qty,
                    'type' => 1
                ];

                $product_entity = $cart_product_model->newEntity($data);
                $saving = $cart_product_model->save($product_entity);

                if ($saving) {
                    // if in wishlist remove that
                    if ($is_in_wishlist) {
                        $cart_product_model->deleteAll([
                            'cart_id' => $cart_id,
                            'product_id' => $product_id,
                            'type' => 0
                        ]);
                    }
                    $cart_products = CartProductsTable::getCart($cart_id, 1);
                    $wishlist_products = CartProductsTable::getCart($cart_id, 0);

                    $return ['status'] = 0;
                    $return ['message'] = 'Product has been added to cart';
                    $return ['result'] ['product_list'] = $cart_products;
                    $return ['result'] ['cart_size'] = sizeof($cart_products);
                    $return ['result'] ['total'] = $this->__getTotal($cart_id);

                    $return ['result'] ['wishlist_product_list'] = $wishlist_products;
                    $return ['result'] ['wishlist_size'] = sizeof($wishlist_products);
                    $return ['result'] ['wishlist_total'] = $this->__getTotal($cart_id, 0);
                } else {
                    $return ['status'] = 500;
                    $return ['message'] = 'Product not added to cart';
                }
            } else {
                $current_product_qty = $this->__isInCart($cart_id, $product_id, 1);
                
                
                /* if ($current_product_qty != $product_qty) { */

                    $cart_product = $cart_product_model->find('all', [
                                'conditions' => [
                                    'cart_id' => $cart_id,
                                    'product_id' => $product_id
                                ]
                            ])->first();

//                    $cart_product->qty = $current_product_qty+$product_qty;
                                $cart_product->qty = $product_qty;
                    if ($cart_product_model->save($cart_product)) {
                        $cart_products = CartProductsTable::getCart($cart_id, 1);
                        $wishlist_products = CartProductsTable::getCart($cart_id, 0);

                        $return ['status'] = 0;
                        $return ['message'] = 'Pruduct qty updated successfully';
                        $return ['result'] ['product_list'] = $cart_products;
                        $return ['result'] ['cart_size'] = sizeof($cart_products);
                        $return ['result'] ['total'] = $this->__getTotal($cart_id);

                        $return ['result'] ['wishlist_product_list'] = $wishlist_products;
                        $return ['result'] ['wishlist_size'] = sizeof($wishlist_products);
                        $return ['result'] ['wishlist_total'] = $this->__getTotal($cart_id, 0);
                    } else {
                        $return ['status'] = 500;
                        $return ['message'] = 'Culd not update the qty';
                    }
               /* }  else {
                    $return ['status'] = 888;
                    $return ['message'] = "Product with same same quantity already in your cart";
                } */
                
                
            }
        } else {
            $return ['status'] = 500;
            $return ['message'] = 'PLease select a product ant qty';
            /*
             * $product = $this->__isInCart($cart_id, $product_id, 1);
             * $new_qty = $product_qty; //+ $product['qty'];
             * $product->qty = $new_qty;
             * if ($cart_product_model->save($product)) {
             * $return ['status'] = 0;
             * $return ['message'] = 'Pruduct qty updated successfully';
             * } else {
             * $return ['status'] = 500;
             * $return ['message'] = 'Culd not update the qty';
             * }
             */
        }
        /*
         * } else {
         * $return ['status'] = 500;
         * $return ['message'] = "Unauthorized acess";
         * }
         */
        echo json_encode($return);
        // echo json_encode($return);
        die();
    }

    private function __isCartExists($session_id) {
        $categories = TableRegistry::get('Cart');

        $cart = $categories->find()->select([
                    'id'
                ])->where([
                    'session_id' => $session_id
                ])->toArray();

        if (!empty($cart)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($user_id = NULL, $phpsessid = NULL) {
        $cart = $this->Cart->newEntity();
        $data = array(
            'session_id' => $phpsessid,
            'user_id' => $user_id
        );

        $cart = $this->Cart->patchEntity($cart, $data);
        if ($result = $this->Cart->save($cart)) {
            return $result->id;
        } else {
            return - 1;
        }
    }

    public function __isInCart($cart_id, $product_id, $type = 1) {
        $cart_product_model = $this->loadModel('CartProducts');

        $result = $cart_product_model->find('all', [
                    'conditions' => [
                        'cart_id' => $cart_id,
                        'product_id' => $product_id,
                        'type' => $type
                    ]
                ])->toArray();

        if (sizeof($result) > 0) {
            return $result [0] ['qty'];
        }
        return false;
    }

    public function __getTotal($cart_id, $type = 1) {
        $tax_p = 0; // tax persontage 10
        $discount_p = 0; // discount persentage 5
        $counpon_value = 0; // call to a function to find coupon values
        $sub_total = CartTable::getTotal($cart_id, $type);

        $tax = $sub_total * $tax_p / 100;
        $discount = $sub_total * $discount_p / 100;
        $grand_total = $sub_total + $tax - $discount - $counpon_value;

        $total ['sub_total'] = $sub_total;
        $total ['tax'] = $tax;
        $total ['discount'] = $discount;
        $total ['counpon_value'] = $counpon_value;
        $total ['grand_total'] = $grand_total;
        return $total;
    }

    public function getcart() {
        /*
         * $token=$this->__getToken();
         * $chck = $this->__checkToken ($token);
         * if ($chck ['boolean']) {
         * $cart_id = $this->__getCurrentCartId ( $chck ['user_id'] );
         */
        $session = $this->request->session();
        $cart_id = $session->read('cart_id');
        if ($cart_id) {

            $total = $this->__getTotal($cart_id);
            $cart_products = CartProductsTable::getCart($cart_id, 1);
            // $cart_products = $this->__getProductList ( $cart_id, 1 );

            if (sizeof($cart_products) > 0) {
                $return ['status'] = 0;
                $return ['message'] = 'success';
                $return ['result'] ['product_list'] = $cart_products;
                $return ['result'] ['total'] = $total;
                // $this->set(['return'=>$return]);
            } else {
                $return ['status'] = 0;
                $return ['message'] = 'your cart is empty';
                $return ['result'] ['product_list'] = $cart_products;
                $return ['result'] ['total'] = $total;
                // $this->set(['return'=>$return]);
            }
        } else {
            $return ['status'] = 0;
            $return ['message'] = 'your cart is empty';
            $return ['result'] ['product_list'] = [];
            $return ['result'] ['total'] = [
                'sub_total' => 0,
                'tax' => 0,
                'discount' => 0,
                'counpon_value' => 0,
                'grand_total' => 0
                    ]
            ;
            /*
             * $return ['status'] = 444;
             * $return ['message'] = "you haven't create a cart";
             */
            // $this->Flash->error(__('you have not create a cart'));
        }
        $this->set([
            'return' => $return
        ]);
        /*
         * } else {
         * $return ['status'] = 100;
         * $return ['message'] = $chck ['message'];
         * $this->Flash->error(__('Token miss match'));
         * }
         */
    }

    /**
     *
     * @param unknown $token        	
     * @return multitype:boolean string
     */
    function __checkToken($token) {
        $user_model = $this->loadModel('users');
        $user = $user_model->find('all', [
                    'conditions' => [
                        'mobtoken' => $token
                    ]
                ])->first();

        if (sizeof($user) <= 0) {
            return [
                'boolean' => false,
                'message' => 'token not found'
            ];
        } else {
            /*
             * $mobtoken_created_at = $user->mobtoken_created_at;
             * $mobtoken_created_at = new Time ( $mobtoken_created_at );
             */

            /*
             * echo $mobtoken_created_at;
             * die ();
             */

            /*
             * if ($mobtoken_created_at->wasWithinLast ( 1 )) {
             * $user->mobtoken_created_at = date ( 'Y-m-d H:i:s' );
             * $user_model->save ( $user );
             *
             * return [
             * 'boolean' => true,
             * 'message' => 'token matched',
             * 'user_id' => $user->id
             * ];
             * } else {
             * return [
             * 'boolean' => false,
             * 'message' => 'token expired'
             * ];
             * }
             */
            $user->mobtoken_created_at = date('Y-m-d H:i:s');
            $user_model->save($user);

            return [
                'boolean' => true,
                'message' => 'token matched',
                'user_id' => $user->id
            ];
        }
    }

    public function __getCurrentCartId($user_id) {
        // $session_id = $this->__getSessionId ();
        // $user_id = $this->__getUserId ();
        $cart_id = $this->Cart->find('all', [
                    'fields' => [
                        'id'
                    ]
                ])->where([
                    'user_id' => $user_id
                ])->toArray();

        if (sizeof($cart_id) > 0) {
            $cart_id = $cart_id [0]->id;
        } else {
            $cart_id = null;
        }
        return $cart_id;
    }

    private function __getToken() {
        $user_id = $this->Auth->user('id');
        $um = $this->loadModel('Users');
        $u = $um->get($user_id); // current logedin user
        return $u->mobtoken;
    }

    public function getAddress() {
        $token = $this->__getToken();
        $chck = $this->__checkToken($token);
        if ($chck ['boolean']) {

            $cart_id = $this->__getCurrentCartId($chck ['user_id']);

            if ($cart_id) {
                $shippingModel = $this->loadModel('Shipping');
                $shipping = $shippingModel->find('all', [
                            'fields' => [
                                'id',
                                'street_number',
                                'street_address',
                                'city'
                            ],
                            'conditions' => [
                                'cart_id' => $cart_id
                            ],
                            'order' => [
                                'Shipping.created_at' => 'DESC'
                            ]
                        ])->distinct([
                            'street_number',
                            'street_address',
                            'city'
                        ])->formatResults(function ($results) {
                            return $results->combine('{n}', function ($row) {
                                        return [
                                            'id' => $row ['id'],
                                            'address' => $row ['street_number'] . ', ' . $row ['street_address'] . ', ' . $row ['city']
                                        ];
                                    });
                        })->toArray();
                $return ['status'] = 0;
                $return ['message'] = "Success";
                $return ['result'] = $shipping;
            } else {
                $return ['status'] = 444;
                $return ['message'] = "you haven't create a cart";
            }
        } else {
            $return ['status'] = 100;
            $return ['message'] = $chck ['message'];
        }

        return $return;
    }

    public function updateAddress() {
        $this->request->allowMethod([
            'post'
        ]);
        header('Content-type: application/json');

        $token = $this->__getToken();
        $address_id = $this->request->data('address_id');
        // print_r($address_id);

        $chck = $this->__checkToken($token);
        if ($chck ['boolean']) {
            $cart_id = $this->__getCurrentCartId($chck ['user_id']);

            if ($cart_id) {
                $shippingModel = $this->loadModel('Shipping');
                $address = $shippingModel->get($address_id); // selected address
                /*
                 * print '<pre>';
                 * print_r($cart_id);
                 * die();
                 */
                $currrent_shipping_details = $shippingModel->find('all', [
                            'conditions' => [
                                'cart_id' => $cart_id,
                                'order_id' => 0
                            ]
                        ])->toArray();
                if (sizeof($currrent_shipping_details) > 0) {
                    $currrent_shipping = $shippingModel->get($currrent_shipping_details [0]->id);
                    $currrent_shipping->street_number = $address->street_number;
                    $currrent_shipping->street_address = $address->street_address;
                    $currrent_shipping->city = $address->city;
                    $currrent_shipping->country = $address->country;
                } else {
                    $data = [
                        'cart_id' => $cart_id,
                        'street_number' => $address->street_number,
                        'street_address' => $address->street_address,
                        'city' => $address->city,
                        'country' => $address->country,
                        'phone_number' => $address->phone_number
                    ];
                    $currrent_shipping = $shippingModel->newEntity($data);
                }
                if ($shippingModel->save($currrent_shipping)) {

                    $shipping = $shippingModel->find('all', [
                                'fields' => [
                                    'id',
                                    'street_number',
                                    'street_address',
                                    'city'
                                ],
                                'conditions' => [
                                    'cart_id' => $cart_id
                                ],
                                'order' => [
                                    'Shipping.created_at' => 'DESC'
                                ]
                            ])->distinct([
                                'street_number',
                                'street_address',
                                'city'
                            ])->formatResults(function ($results) {
                                return $results->combine('{n}', function ($row) {
                                            return [
                                                'id' => $row ['id'],
                                                'address' => $row ['street_number'] . ', ' . $row ['street_address'] . ', ' . $row ['city']
                                            ];
                                        });
                            })->toArray();

                    $return ['status'] = 0;
                    $return ['message'] = "success";
                    $return ['result'] = $shipping;
                } else {
                    $return ['status'] = 912;
                    $return ['message'] = "Culd not update address";
                }
            } else {
                $return ['status'] = 444;
                $return ['message'] = "you haven't create a cart";
            }
        } else {
            $return ['status'] = 100;
            $return ['message'] = $chck ['message'];
        }

        echo json_encode($return);
        die();
    }

    public function addAddress() {
        $this->request->allowMethod([
            'post'
        ]);
        header('Content-type: application/json');

        $token = $this->__getToken();
        $phone_number = $this->request->data('phone_number');
        $street_number = $this->request->data('street_number');
        $street_address = $this->request->data('street_address');
        $city = $this->request->data('city');
        $country = $this->request->data('country');

        $chck = $this->__checkToken($token);
        if ($chck ['boolean']) {

            $cart_id = $this->__getCurrentCartId($chck ['user_id']);
            $data = [
                'cart_id' => $cart_id,
                'street_number' => $street_number,
                'street_address' => $street_address,
                'city' => $city,
                'country' => $country,
                'phone_number' => $phone_number
            ];
            if ($cart_id) {
                $shippingModel = $this->loadModel('Shipping');
                $currrent_shipping_details = $shippingModel->find('all', [
                            'conditions' => [
                                'cart_id' => $cart_id,
                                'order_id' => 0
                            ]
                        ])->toArray();
                if (sizeof($currrent_shipping_details) > 0) {
                    $currrent_shipping = $shippingModel->get($currrent_shipping_details [0]->id);
                    $currrent_shipping->street_number = $data ['street_number'];
                    $currrent_shipping->street_address = $data ['street_address'];
                    $currrent_shipping->city = $data ['city'];
                    $currrent_shipping->country = $data ['country'];
                    $currrent_shipping->phone_number = $data ['phone_number'];
                    if ($shippingModel->save($currrent_shipping)) {

                        $return ['status'] = 0;
                        $return ['message'] = "Success";
                        $return ['result'] = $this->__getAddress($cart_id);
                    } else {
                        $return ['status'] = 912;
                        $return ['message'] = "Address not saved";
                    }
                } else {
                    $shippingEntity = $shippingModel->newEntity($data);
                    if ($shippingModel->save($shippingEntity)) {
                        $return ['status'] = 0;
                        $return ['message'] = "Success";
                        $return ['result'] = $this->__getAddress($cart_id);
                    } else {
                        $return ['status'] = 912;
                        $return ['message'] = "Address not saved";
                    }
                }
            } else {
                $return ['status'] = 444;
                $return ['message'] = "you haven't create a cart";
            }
        } else {
            $return ['status'] = 100;
            $return ['message'] = $chck ['message'];
        }

        echo json_encode($return);
        die();
    }

    public function updateDeliveryTime() {
        $this->request->allowMethod([
            'post'
        ]);
        header('Content-type: application/json');

        $token = $this->__getToken();
        $delivery_time = $this->request->data('delivery_time');
        $delivery_date = $this->request->data('delivery_date');

        //

        $delivery_time_string = strtotime("$delivery_date $delivery_time"); // new Time($delivery_time);//$delivery_time->i18nFormat('yyyy-MM-dd HH:mm:ss');

        $delivery_time_formated = date('Y-m-d h:m:s', $delivery_time_string);
        // echo $delivery_time_string.'--|||--'.$delivery_time_formated;
        // die();
        $chck = $this->__checkToken($token);
        if ($chck ['boolean']) {
            $cart_id = $this->__getCurrentCartId($chck ['user_id']);

            if ($cart_id) {
                $shippingModel = $this->loadModel('Shipping');
                $currrent_shipping_details = $shippingModel->find('all', [
                            'conditions' => [
                                'cart_id' => $cart_id,
                                'order_id' => 0
                            ]
                        ])->toArray();

                if (sizeof($currrent_shipping_details) > 0) {
                    $currrent_shipping = $shippingModel->get($currrent_shipping_details [0]->id);
                    $currrent_shipping->delivery_date_time = $delivery_time_formated;

                    if ($shippingModel->save($currrent_shipping)) {
                        $return ['status'] = 0;
                        $return ['message'] = "success";
                    } else {
                        $return ['status'] = 916;
                        $return ['message'] = "Culd not save delivery time";
                    }
                } else {
                    $last_shipping = $shippingModel->find('all', [
                                'fields' => [
                                    'id',
                                    'street_number',
                                    'street_address',
                                    'city',
                                    'country',
                                    'phone_number'
                                ],
                                'conditions' => [
                                    'cart_id' => $cart_id
                                ],
                                'order' => [
                                    'Shipping.created_at' => 'DESC'
                                ],
                                'limit' => 1
                            ])->toArray();
                    /*
                     * print '<pre>';
                     * print_r($last_shipping);
                     * die();
                     */
                    if (sizeof($last_shipping) > 0) {
                        $data = [
                            'cart_id' => $cart_id,
                            'street_number' => $last_shipping [0]->street_number,
                            'street_address' => $last_shipping [0]->street_address,
                            'city' => $last_shipping [0]->city,
                            'country' => $last_shipping [0]->country,
                            'phone_number' => $last_shipping [0]->phone_number,
                            'delivery_date_time' => $delivery_time_formated
                        ];

                        $shippingEntity = $shippingModel->newEntity($data);
                        if ($shippingModel->save($shippingEntity)) {
                            $return ['status'] = 0;
                            $return ['message'] = "Success";
                        } else {
                            $return ['status'] = 911;
                            $return ['message'] = "Address and time not saved";
                        }
                    } else {
                        $return ['status'] = 910;
                        $return ['message'] = "please fille delivery address first";
                    }
                }
            } else {
                $return ['status'] = 444;
                $return ['message'] = "you haven't create a cart";
            }
        } else {
            $return ['status'] = 100;
            $return ['message'] = $chck ['message'];
        }

        echo json_encode($return);
        die();
    }

    public function completeCheckout() {
        $this->request->allowMethod([
            'post'
        ]);
        header('Content-type: application/json');

        $token = $this->__getToken();
        $chck = $this->__checkToken($token);
        if ($chck ['boolean']) {

            $cart_id = $this->__getCurrentCartId($chck ['user_id']);

            if ($cart_id) {
                if ($this->__iscartEmpty($cart_id)) {
                    $return ['status'] = 522;
                    $return ['message'] = "you cart is empty";
                } else {
                    // add shipping details
                    // echo 'ssss';
                    $order_id = $this->__addOrder($cart_id, $chck ['user_id']);
                    if ($order_id) {
                        $addOrderProducts = $this->__addOrderProducts($cart_id, $order_id);
                        if ($addOrderProducts) {
                            // update shipping order id
                            $this->__updateShippingOrderId($cart_id, $order_id);
                            if ($this->__clearCart($cart_id)) {
                                $this->__sendNotification($order_id, $cart_id, $chck ['user_id']);
                                $return ['status'] = 0;
                                $return ['message'] = "success";
                            } else {
                                $return ['status'] = 906;
                                $return ['message'] = "something went wrong, cart not clear";
                            }
                        } else {
                            $return ['status'] = 907;
                            $return ['message'] = "something went wrong, order products not saved";
                        }
                    } else {
                        $return ['status'] = 908;
                        $return ['message'] = "something went wrong, order data not saved";
                    }

                    $return ['status'] = 0;
                    $return ['message'] = "success";
                }
            } else {
                $return ['status'] = 444;
                $return ['message'] = "you haven't create a cart";
            }
        } else {
            $return ['status'] = 100;
            $return ['message'] = $chck ['message'];
        }

        echo json_encode($return);
        die();
    }

    function __iscartEmpty($cartID) {
        $cart_products = $this->loadModel('CartProducts');
        $cart_products->find('all', [
            'conditions' => [
                'cart_id' => $cartID
            ]
        ])->toArray();
        if (sizeof($cartID > 0)) {
            return false;
        }
        return true;
    }

    function __clearCart($cart_id) {
        if ($cart_id) {
            $cart_product_model = $this->loadModel('CartProducts');
            if ($cart_product_model->deleteAll([
                        'cart_id' => $cart_id,
                        'type' => 1
                    ])) {
                return true;
            } else {
                return false;
            }
        }
    }

    function __updateShippingOrderId($cart_id, $order_id) {
        $shippingModel = $this->loadModel('Shipping');
        $currrent_shipping_details = $shippingModel->find('all', [
                    'conditions' => [
                        'cart_id' => $cart_id,
                        'order_id' => 0
                    ]
                ])->toArray();
        $currrent_shipping = $shippingModel->get($currrent_shipping_details [0]->id);
        $currrent_shipping->order_id = $order_id;
        $update_shipping_order_id = $shippingModel->save($currrent_shipping);
    }

    function __addOrder($cart_id, $user_id) {
        $shippingModel = $this->loadModel('Shipping');
        $currrent_shipping_details = $shippingModel->find('all', [
                    'conditions' => [
                        'cart_id' => $cart_id,
                        'order_id' => 0
                    ]
                ])->toArray();
        $currrent_shipping = $shippingModel->get($currrent_shipping_details [0]->id);
        $total = $this->__getTotal($cart_id);
        $delivery_date_time = $currrent_shipping->delivery_date_time;
        $delivery_date_time = strtotime($delivery_date_time);

        $delivery_date = date('Y-m-d', $delivery_date_time);
        $delivery_time = date('H:i:s', $delivery_date_time);
        $order = [
            'customerId' => $this->getCustomerID($user_id),
            'address' => $currrent_shipping->street_number . ' ' . $currrent_shipping->street_address . ' ' . $currrent_shipping->city . ' ' . $currrent_shipping->country,
            'city' => $this->__getCityID($currrent_shipping->city), // city id
            'callcenterId' => 11, // have to null--in original 11
            'deliveryId' => 7, // default --in original 11
            'subTotal' => $total ['sub_total'],
            'tax' => $total ['tax'],
            'discount' => $total ['discount'],
            'couponCode' => $total ['counpon_value'],
            'total' => $total ['grand_total'],
            'deliveryDate' => $delivery_date,
            'deliveryTime' => $delivery_time,
            'note' => $currrent_shipping->note,
            'supplier_note' => '',
            'paymentStatus' => 1,
            'status' => 1,
            'deleted' => 0,
			'email'=>1,
			'platform'=>2
        ];

        $orderModel = $this->loadModel('Orders');
        $orderEntity = $orderModel->newEntity($order);

        $orderSaved = $orderModel->save($orderEntity);
        if ($orderSaved) {
            return $orderSaved->id;
        }
        return false;
    }

    function __addOrderProducts($cart_id, $order_id = 1) {
        // cart products
        $productModel = $this->loadModel('Products');
        $cartproductsModel = $this->loadModel('CartProducts');
        $cartProducts = $cartproductsModel->find('all', [
                    'conditions' => [
                        'cart_id' => $cart_id,
                        'type' => 1
                    ]
                ])->toArray();
        /*
         * $cartProductsArray = array_reduce($cartProducts, function ($result, $item) {
         * $item = (array) $item;
         * $result[] = $item;
         * return $result;
         * }, array());
         */

        // order products
        $ordeProducts = [];
        $i = 0;
        foreach ($cartProducts as $prduct) {
            $product = $productModel->get($prduct->product_id, [
                        'contain' => [
                            'productSuppliers',
                            'productSuppliers.Suppliers' => [
                                'conditions' => [
                                    'status' => 1
                                ]
                            ]
                        ]
                    ])->toArray();

            $ordeProducts [$i] ['order_id'] = $order_id;
            $ordeProducts [$i] ['product_id'] = $prduct->product_id;
            $ordeProducts [$i] ['product_quantity'] = $prduct->qty;
            $ordeProducts [$i] ['product_price'] = $product ['price'];
            $ordeProducts [$i] ['supplier_id'] = $product ['product_suppliers'] [0] ['supplier_id'];
            $ordeProducts [$i] ['status_s'] = 0;
            $ordeProducts [$i] ['status_d'] = 0;
            $ordeProducts [$i] ['deleted'] = 0;
            $i ++;
        }
        $orderProductsModel = $this->loadModel('OrderProducts');
        $entitiies = $orderProductsModel->newEntities($ordeProducts);
        $savedOrderProducts = $orderProductsModel->saveMany($entitiies);

        if ($savedOrderProducts) {
            return true;
        }
        return false;
    }

    function __getCityID($cityName) {
        $cityModel = $this->loadModel('City');
        $city = $cityModel->find('all', [
                    'conditions' => [
                        'cname' => $cityName
                    ]
                ])->toArray();
        if (sizeof($city) > 0) {
            return $city [0]->cid;
        } else {
            $cityEntity = $cityModel->newEntity([
                'did' => 0,
                'cname' => $cityName
            ]);
            if ($cityModel->save($cityEntity)) {
                return $cityEntity->cid;
            } else {
                return 0;
            }
        }
    }

    public function getCheckout() {
        $token = $this->__getToken();
        $chck = $this->__checkToken($token);
        if ($chck ['boolean']) {

            $cart_id = $this->__getCurrentCartId($chck ['user_id']);

            if ($cart_id) {
                // $now=Time::now();
                $return ['status'] = 0;
                $return ['message'] = "success";
                $return ['delivery_time'] = $this->__getDeliveryTime($cart_id);
                $return ['delay_time'] = 240;
                $return ['delivery_address'] = $this->__getLastAddress($cart_id);
                $return ['unavailable_date'] = $this->__getUnavailableDates();
                $return ['delivery_start_time'] = new Time('06:00:00');
                $return ['delivery_end_time'] = new Time('18:00:00');

                $unavailable_dates_array = array();
                foreach ($return ['unavailable_date'] AS $date) {
                    $unavailable_dates_array[] = date('d-m-Y', strtotime($date->date));
                }

                $next_five_days = $this->__getNextFiveDays();

                foreach ($next_five_days AS $key => $next_day) {
                    if (in_array($next_day, $unavailable_dates_array)) {
                        unset($next_five_days[$key]);
                    }
                }
                $return["next_days_for_delivery"] = $next_five_days;
            } else {
                $return ['status'] = 444;
                $return ['message'] = "you haven't create a cart";
            }
        } else {
            $return ['status'] = 100;
            $return ['message'] = $chck ['message'];
        }

        return $return;
    }

    function __getNextFiveDays() {
        $dates = array();
        $i = -1;

        while (true) {
            $i++;

            $time = strtotime("+$i days");
            $dayOfWeek = date('w', $time);

            /* 'w' - day of week, numeric, i.e. "0" (Sunday) to "6" (Saturday) */
            if (($dayOfWeek == 0) or ( $dayOfWeek == 6)) {
                continue;
            }

            $dates[] = date('d-m-Y', $time);

            if (count($dates) >= 5) {
                break;
            }
        }
        return $dates;
    }

    function __getDeliveryTime($cart_id) {
        $shippingModel = $this->loadModel('Shipping');

        $current_shipping = $shippingModel->find('all', [
                    'fields' => [
                        'delivery_date_time'
                    ],
                    'conditions' => [
                        'cart_id' => $cart_id,
                        'order_id' => 0
                    ],
                    'order' => [
                        'Shipping.created_at' => 'DESC'
                    ]
                ])->toArray();
        if (sizeof($current_shipping) > 0) {
            if ($current_shipping [0]->delivery_date_time != null) {
                $delevery_date_time = $current_shipping [0]->delivery_date_time;
                $delevery_date_time_format_changed = new Time($delevery_date_time);

                if ($delevery_date_time_format_changed->wasWithinLast('5 years') || $delevery_date_time_format_changed->isWithinNext('210 minutes')) {
                    return '';
                } else {
                    return $delevery_date_time;
                }

                /*
                 * $now=new Time();
                 * $now_plus_3h=$now->modify('+3 hours');
                 */
                // echo 'past'.$delevery_date_time_format_changed->wasWithinLast(1000).'<br>';
                // echo 'next'.$delevery_date_time_format_changed->isWithinNext('210 minutes');
                // $now_with_3_hrs=$now->addMonth(3);
                /*
                 * print_r($now);
                 * echo '<br>';
                 * print_r($now_with_3_hrs);
                 */

                // $currrent_date_time=$now->i18nFormat();
                // $currrent_date_time_with_3_hrs=$now_with_3_hrs->i18nFormat();
            }
            return '';
        } else {
            return '';
        }
    }

    function __getLastAddress($cart_id) {
        $shippingModel = $this->loadModel('Shipping');
        /*
         * $last_shipping = $shippingModel->find ( 'all', [
         * 'conditions' => [
         * 'cart_id' => $cart_id
         * ],
         * 'order' => [
         * 'Shipping.created_at' => 'DESC'
         * ]
         * ] )->toArray ();
         */
        $last_shipping = $shippingModel->find('all', [
                    'fields' => [
                        'id',
                        'street_number',
                        'street_address',
                        'city'
                    ],
                    'conditions' => [
                        'cart_id' => $cart_id
                    ],
                    'order' => [
                        'Shipping.created_at' => 'DESC'
                    ]
                ])->formatResults(function ($results) {
                    return $results->combine('{n}', function ($row) {
                                return [
                                    'id' => $row ['id'],
                                    'address' => $row ['street_number'] . ', ' . $row ['street_address'] . ', ' . $row ['city']
                                ];
                            });
                })->toArray();

        $current_shipping = $shippingModel->find('all', [
                    'fields' => [
                        'id',
                        'street_number',
                        'street_address',
                        'city'
                    ],
                    'conditions' => [
                        'cart_id' => $cart_id,
                        'order_id' => 0
                    ],
                    'order' => [
                        'Shipping.created_at' => 'DESC'
                    ]
                ])->formatResults(function ($results) {
                    return $results->combine('{n}', function ($row) {
                                return [
                                    'id' => $row ['id'],
                                    'address' => $row ['street_number'] . ', ' . $row ['street_address'] . ', ' . $row ['city']
                                ];
                            });
                })->toArray();
        /*
         * print '<pre>';
         * print_r($current_shipping[0]);
         * die();
         */
        if (sizeof($current_shipping) <= 0) {
            if (sizeof($last_shipping) > 0) {
                return $last_shipping [0];
            } else {
                return null;
            }
        } else {
            return $current_shipping [0];
        }
    }

    function __getUnavailableDates() {
        $unavailableModel = $this->loadModel('Unavailabledate');
        /*
         * $result = $unavailableModel->find ( 'list', [
         * 'keyField' => 'id',
         * 'valueField' => 'date'
         * ] )->toArray ();
         * return array_values ( $result );
         */
        $result = $unavailableModel->find('all', [
                    'fields' => [
                        'date'
                    ]
                ])->toArray();
        return array_map(function ($result) {
            return $result;
        }, $result);
    }

    public function checkout() {
        $address_book = $this->getAddress();
        $session = $this->request->session();
        $cart_id = $session->read('cart_id');
        $total = $this->__getTotal($cart_id);
        $address_book = $address_book ['result'];

        // $address_book='';
        $address_book [] = [
            'id' => '',
            'address' => 'New Address'
        ];
        $get_checkout = $this->getCheckout();

        $this->set([
            'address_book' => $address_book,
            'get_checkout' => $get_checkout,
            'cart_total' => $total
        ]);
    }

    public function __getAddress($cart_id) {
        $shippingModel = $this->loadModel('Shipping');

        $shipping = $shippingModel->find('all', [
                    'fields' => [
                        'id',
                        'street_number',
                        'street_address',
                        'city'
                    ],
                    'conditions' => [
                        'cart_id' => $cart_id
                    ],
                    'order' => [
                        'Shipping.created_at' => 'DESC'
                    ]
                ])->distinct([
                    'street_number',
                    'street_address',
                    'city'
                ])->formatResults(function ($results) {
                    return $results->combine('{n}', function ($row) {
                                return [
                                    'id' => $row ['id'],
                                    'address' => $row ['street_number'] . ', ' . $row ['street_address'] . ', ' . $row ['city']
                                ];
                            });
                })->toArray();

        return $shipping;
    }

    public function dashboard() {
        $user_id = $this->Auth->user('id');

        // $customers_model=$this->loadModel('');
        $user = $this->Cart->Users->find('all', [
                    'conditions' => [
                        'Users.id' => $user_id
                    ],
                    'contain' => [
                        'Customers' => [
                            'fields' => [
                                'id',
                                'user_id',
                                'firstName',
                                'lastName',
                                'newsLetter',
                                'address',
                                'city',
                                'email',
                                'mobileNo'
                            ]
                        ]
                    ]
                ])->toArray();

        $orders = (new OrdersController ())->getOrderList();
        $this->set([
            'user' => $user [0]->customer,
            'orders' => $orders ['result']
        ]);
        $category_list = $this->getCategoryTree();
        $this->set('category_tree', $category_list);
    }

    public function deleteproduct() {
        $this->request->allowMethod([
            'post',
            'delete'
        ]);

        header('Content-type: application/json');
        if ($this->request->is('post')) {
            $product_id = $this->request->data('product_id');
            // $token = $this->__getToken();
            // $chck = $this->__checkToken ( $token );
            // if ($chck ['boolean']) {
            if ($product_id != null) {
                $session = $this->request->session();
                $cart_id = $session->read('cart_id');
                if ($cart_id) {
                    $cart_product_model = $this->loadModel('CartProducts');

                    $product = $cart_product_model->find('all', [
                                'fields' => [
                                    'id'
                                ],
                                'conditions' => [
                                    'cart_id' => $cart_id,
                                    'product_id' => $product_id,
                                    'type' => 1
                                ]
                            ])->toArray();
                    if (sizeof($product) > 0) {
                        if ($cart_product_model->delete($cart_product_model->get($product [sizeof($product) - 1]->id))) {
                            $return ['status'] = 0;
                            $return ['message'] = 'Pruduct deleted successfully';
                            $return ['result'] = $this->__getcartIn($cart_id);
                        } else {
                            $return ['status'] = 914;
                            $return ['message'] = 'Culd not delete the product';
                        }
                    } else {
                        $return ['status'] = 411;
                        $return ['message'] = 'The product not found in the cart';
                    }
                } else {
                    $return ['status'] = 444;
                    $return ['message'] = 'you havent create a cart';
                }
            } else {
                $return ['status'] = 410;
                $return ['message'] = 'please select product id';
            }
            /*
             * } else {
             * $return ['status'] = 100;
             * $return ['message'] = $chck ['message'];
             * }
             */
        } else {
            $return ['status'] = 500;
            $return ['message'] = "Unauthorized acess";
        }
        echo json_encode($return);
        die();
    }

    public function __getcartIn($cart_id, $type = 1) {
        $this->request->allowMethod([
            'post',
            'get'
        ]);
        header('Content-type: application/json');
        // $cart_id = $this->__getCurrentCartId ( $user_id );

        if ($cart_id) {

            $total = $this->__getTotal($cart_id, 1);
            $cart_products = CartProductsTable::getCart($cart_id, $type);
            // $cart_products = $this->__getProductList ( $cart_id, 1 );

            if (sizeof($cart_products) > 0) {
                $return ['status'] = 0;
                $return ['message'] = 'success';
                $return ['result'] ['product_list'] = $cart_products;
                $return ['result'] ['cart_size'] = sizeof($cart_products);
                $return ['result'] ['total'] = $total;
            } else {
                $return ['status'] = 0;
                $return ['message'] = 'your cart is empty';
                $return ['result'] ['product_list'] = [];
                $return ['result'] ['cart_size'] = 0;
                $return ['result'] ['total'] = $total;
            }
        } else {
            $return ['status'] = 444;
            $return ['message'] = "you haven't create a cart";
        }
        return $return;
        die();
    }

    /**
     * pass data to quic edit
     * 
     * @param unknown $cart_id        	
     * @param unknown $procuct_id        	
     */
    public function quickedit() {
        $this->request->allowMethod([
            'post'
        ]);
        header('Content-type: application/json');
        $session = $this->request->session();
        $cart_id = $session->read('cart_id');
        $product_id = $this->request->data('product_id');

        $prduct_model = $this->loadModel('Products');
        $result = $prduct_model->find('all', [
                    'conditions' => [
                        'Products.id' => $product_id
                    ],
                    'contain' => [
                        'packageType',
                        'CartProducts' => [
                            'conditions' => [
                                'cart_id' => $cart_id,
                                'product_id' => $product_id
                            ]
                        ]
                    ]
                ])->toArray();
        echo json_encode($result [0]);
        die();
        // $cart_product=$this->loadModel('CartProducts');
    }

    public function placeOrder() {
        $this->request->allowMethod([
            'post'
        ]);
        header('Content-type: application/json');

        $token = $this->__getToken();
        $order_id = $this->request->data('order_id');

        $chck = $this->__checkToken($token);

        if ($chck ['boolean']) {
            if ($order_id) {
                $cart_id = $this->__getCurrentCartId($chck ['user_id']);
                $orderModel = $this->loadModel('Orders');
                $order = $orderModel->find('all', [
                            'conditions' => [
                                'Orders.id' => $order_id
                            ],
                            'contain' => [
                                'OrderProducts'
                            ]
                        ])->toArray();
                if (sizeof($order) > 0) {
                    $i = 0;
                    foreach ($order [0]->order_products as $product) {
                        $cart_products [$i] ['cart_id'] = $cart_id;
                        $cart_products [$i] ['product_id'] = $product ['product_id'];
                        $cart_products [$i] ['qty'] = $product ['product_quantity'];
                        $cart_products [$i] ['type'] = 1;
                        $i ++;
                    }
                    /*
                     * print_r( $cart_products);
                     * die();
                     */
                    if (sizeof($cart_products) > 0) {
                        $this->__clearCart($cart_id);
                        $cartProductModel = $this->loadModel('CartProducts');
                        $cartPrdoductsEntities = $cartProductModel->newEntities($cart_products);
                        if ($cartProductModel->saveMany($cartPrdoductsEntities)) {

                            $cart_products = CartProductsTable::getCart($cart_id, 1);

                            $return ['status'] = 0;
                            $return ['message'] = 'go to checkout process to compleae reorder';
                            $return ['result'] ['product_list'] = $cart_products;
                            $return ['result'] ['cart_size'] = sizeof($cart_products);
                            $return ['result'] ['total'] = $this->__getTotal($cart_id);
                        } else {
                            $return ['status'] = 904;
                            $return ['message'] = 'products not saved';
                        }
                    } else {
                        $return ['status'] = 611;
                        $return ['message'] = 'no products found in order';
                    }
                } else {
                    $return ['status'] = 433;
                    $return ['message'] = 'no order found';
                }
            } else {
                $return ['status'] = 520;
                $return ['message'] = 'order id can not be empty';
            }
        } else {
            $return ['status'] = 100;
            $return ['message'] = $chck ['message'];
        }
        echo json_encode($return);
        die();
    }

    /*
     * return customer id of a user
     */

    function getCustomerID($userID) {
        $customerModel = $this->loadModel('Customers');
        $query = $customerModel->find()->select('id')->where([
                    'user_id' => $userID
                ])->first();
        return $query->id;
    }

    public function getwishlist() {
        $session = $this->request->session();
        $cart_id = $session->read('cart_id');
        if ($cart_id) {

            $cart_products = CartProductsTable::getCart($cart_id, 0);

            if (sizeof($cart_products) > 0) {
                $return ['status'] = 0;
                $return ['message'] = 'success';
                $return ['result'] ['product_list'] = $cart_products;
            } else {
                $return ['status'] = 0;
                $return ['message'] = 'your cart is empty';
                $return ['result'] ['product_list'] = $cart_products;
            }
        } else {
            $return ['status'] = 0;
            $return ['message'] = 'your cart is empty';
            $return ['result'] ['product_list'] = [];
        }
        $this->set([
            'return' => $return
        ]);
    }

    public function deleteWishListItem() {
        $this->request->allowMethod([
            'post',
            'delete'
        ]);

        header('Content-type: application/json');
        if ($this->request->is('post')) {
            $product_id = $this->request->data('product_id');
            // $token = $this->__getToken();
            // $chck = $this->__checkToken ( $token );
            // if ($chck ['boolean']) {
            if ($product_id != null) {
                $session = $this->request->session();
                $cart_id = $session->read('cart_id');
                if ($cart_id) {
                    $cart_product_model = $this->loadModel('CartProducts');

                    $product = $cart_product_model->find('all', [
                                'fields' => [
                                    'id'
                                ],
                                'conditions' => [
                                    'cart_id' => $cart_id,
                                    'product_id' => $product_id,
                                    'type' => 0
                                ]
                            ])->toArray();
                    if (sizeof($product) > 0) {
                        if ($cart_product_model->delete($cart_product_model->get($product [sizeof($product) - 1]->id))) {
                            $return ['status'] = 0;
                            $return ['message'] = 'Pruduct deleted successfully';
                            $return ['result'] = $this->__getcartIn($cart_id, 0);
                        } else {
                            $return ['status'] = 914;
                            $return ['message'] = 'Culd not delete the product';
                        }
                    } else {
                        $return ['status'] = 411;
                        $return ['message'] = 'The product not found in the cart';
                    }
                } else {
                    $return ['status'] = 444;
                    $return ['message'] = 'you havent create a wishlist';
                }
            } else {
                $return ['status'] = 410;
                $return ['message'] = 'please select product id';
            }
            /*
             * } else {
             * $return ['status'] = 100;
             * $return ['message'] = $chck ['message'];
             * }
             */
        } else {
            $return ['status'] = 500;
            $return ['message'] = "Unauthorized acess";
        }
        echo json_encode($return);
        die();
    }

    public function addWishListItem() {
        header('Content-type: application/json');

        if (!$this->Auth->user()) {
            $return ['status'] = 500;
            $return ['message'] = 'Please login before add to wishlist';
        } else {

            $product_id = $this->request->data('product_id');
            $product_qty = $this->request->data('qty');

            $session = $this->request->session();
            $cart_id = $session->read('cart_id');

            if ($product_id != null && $product_qty != null) {
                $cart_product_model = $this->loadModel('CartProducts');

                if ($cart_id && !($this->__isInCart($cart_id, $product_id, 0))) {

                    $data = [
                        'cart_id' => $cart_id,
                        'product_id' => $product_id,
                        'qty' => $product_qty,
                        'type' => 0
                    ];

                    $product_entity = $cart_product_model->newEntity($data);
                    $saving = $cart_product_model->save($product_entity);

                    if ($saving) {
                        $cart_products = CartProductsTable::getCart($cart_id, 0);

                        $return ['status'] = 0;
                        $return ['message'] = 'Product has been added to Wishlist';
                        $return ['result'] ['product_list'] = $cart_products;
                        $return ['result'] ['cart_size'] = sizeof($cart_products);
                        $return ['result'] ['total'] = $this->__getTotal($cart_id, 0);
                    } else {
                        $return ['status'] = 500;
                        $return ['message'] = 'Product not added to Wishlist';
                    }
                } else {
                    $current_product_qty = $this->__isInCart($cart_id, $product_id, 0);

                    if ($current_product_qty != $product_qty) {

                        $cart_product = $cart_product_model->find('all', [
                                    'conditions' => [
                                        'cart_id' => $cart_id,
                                        'product_id' => $product_id
                                    ]
                                ])->first();

                        $cart_product->qty = $product_qty;
                        if ($cart_product_model->save($cart_product)) {
                            $cart_products = CartProductsTable::getCart($cart_id, 0);
                            $return ['status'] = 0;
                            $return ['message'] = 'Pruduct qty updated successfully';
                            $return ['result'] ['product_list'] = $cart_products;
                            $return ['result'] ['cart_size'] = sizeof($cart_products);
                            $return ['result'] ['total'] = $this->__getTotal($cart_id, 0);
                        } else {
                            $return ['status'] = 500;
                            $return ['message'] = 'Culd not update the qty';
                        }
                    } else {
                        $return ['status'] = 888;
                        $return ['message'] = "Product with same same quantity already in your Wishlist";
                    }
                }
            } else {
                $return ['status'] = 500;
                $return ['message'] = 'Please select a product and qty';
            }
        }
        echo json_encode($return);
        die();
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
            if (!is_array($second_category_array [$second_category ['parent_id']]))
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
            if (!is_array($third_category_array [$third_category ['parent_id']]))
                $third_category_array [$third_category ['parent_id']] = array();
            $third_category_array [$third_category ['parent_id']] [] = $third_category;
        }

        return [
            $first_level_categories,
            $second_category_array,
            $third_category_array
        ];
    }

    private function __sendNotification($orderId, $cart_id, $user_id) {
        $order_model = $this->loadModel('Orders');
        $orders_products_model = $this->loadModel('OrderProducts');
        $supplier_notify_model = $this->loadModel('SupplierNotifications');
        $delivery_notify_model = $this->loadModel('DeliveryNotifications');

        $order = $order_model->find('all', [
                    'fields' => [
                        'deliveryId'
                    ],
                    'conditions' => [
                        'id' => $orderId
                    ]
                ])->first();

        $supplers = $orders_products_model->find()->select(['supplier_id'])->distinct(['supplier_id'])->where(['order_id' => $orderId])->toArray();
        $supplerids = array_map(create_function('$o', 'return $o->supplier_id;'), $supplers);

        $dilivery_notification = [
            'deliveryId' => $order->deliveryId,
            'notificationText' => 'del nofify',
            'sentFrom' => 2,
            'orderId' => $orderId
        ];

        for ($j = 0; $j < sizeof($supplerids); $j ++) {
            $supplier_notification [$j] = [
                'supplierId' => $supplerids [$j],
                'notificationText' => 'notify',
                'sentFrom' => 2,
                'orderId' => $orderId
            ];
        }

        //$supplier_notification_entites = $supplier_notify_model->newEntities($supplier_notification);
        //$supplier_notification_result = $supplier_notify_model->saveMany($supplier_notification_entites);

        $dlilevery_notification_entity = $delivery_notify_model->newEntity($dilivery_notification);
        $dilivery_notification_result = $delivery_notify_model->save($dlilevery_notification_entity);

        //$this->Notification->setNotification(1, '', '', $orderId, '', '', '', '');
        $this->sendToAll2($orderId, 'new', $supplerids, $order->deliveryId, "", $cart_id, $user_id); // send emails,product_name,product_quantity,product_supplier
    }

    // new order information email
    /**
     *
     * @param unknown $orderId
     * @param unknown $trype
     * @param unknown $suppliers
     * @param unknown $delivery
     * @param unknown $orderdata
     * $cart_id: for web and mobile
     *        	the email sent @ order saving time, so product price get from products table,
     *        	if you sent this email after long time, you need to get price from OrderProducts table,
     *        	because product price may change time to time
     */
    public function sendToAll2($orderId, $trype, $suppliers, $delivery, $orderdata = null, $cart_id = null, $user_id = null) {
        $id_of_the_order = $orderId;
        $products_model = $this->loadModel('Products');
        $suppliers_model = $this->loadModel('Suppliers');
        $delivery_model = $this->loadModel('Delivery');
        $order_products_model = $this->loadModel('OrderProducts');
        $order_model = $this->loadModel('Orders');
        $order_products = $order_products_model->find('all', ['conditions' => ['order_id' => $orderId]])->toArray(); //for web, mobile
        $orderdata['product_name'] = array_map(create_function('$o', 'return $o->product_id;'), $order_products); //not name, ids
        $orderdata['product_supplier'] = array_map(create_function('$o', 'return $o->supplier_id;'), $order_products);
        $orderdata['product_quantity'] = array_map(create_function('$o', 'return $o->product_quantity;'), $order_products);

        /* print '<pre>';
          print_r($orderdata);
          echo '<br>';
          print_r($orderdata);
          echo '<br>';

          echo '<br>';
          die(); */


        //$countedval = $this->processdata ( $orderdata );//for erp
        $countedval = $this->processdata($orderdata);  //web and mobile

        $sub_total = $countedval ['subTotal'];
        $total = $countedval ['total'];
        $tax = $countedval ['tax'];
        $discount = $countedval ['discount'];


        $total_string = "<br><table border='1'><tr>" . "<th>Sub Total</th>" . "<td>" . $sub_total . "</td></tr>" . "<tr><th>Tax</th>" . "<td>" . $tax . "</td></tr>" . "<tr><th>Discount</th>" . "<td>" . $discount . "</td></tr>" . "<tr><th>Total</th>" . "<td>" . $total . "</td></tr>" . "</tr></table><br><hr>";

        $orderId = "<h4>Order ID: " . $orderId . "</h4>";
        $sup_string = "<hr><br><table border='1'>" . "<tr>" .
                /* "<th>#</th>". */
                "<th>Supplier Id</th>" . "<th>Supplier name</th>" . "<th>Address</th>" . "<th>City</th>" .
                /* "<th>Email</th>". */
                "<th>Contact No.</th>" . "<th>Mobile No.</th>" . "<th>Product Id</th>" . "<th>Product name</th>" . "<th>Product price</th>" . "<th>Package</th>" . "<th>Quantity</th>" . "<th>Ammount</th>" . "</tr>";
        $sup_string_end = "</table>";
        $row = "";
        $supliers_email = [];
        $delivery_mail = [];
        $delivery_mail_string = $orderId . $sup_string;

        foreach ($suppliers as $suplier) {
            $count = 1;
            $sup_email = "";
            for ($i = 0; $i < sizeof($orderdata ['product_name']); $i ++) {
                if ($suplier == $orderdata ['product_supplier'] [$i]) {
                    $product_details = $products_model->get($orderdata ['product_name'] [$i], [
                        'contain' => [
                            'packageType'
                        ]
                    ]);
                    $quntity = $orderdata ['product_quantity'] [$i];
                    $supplier_details = $suppliers_model->get($orderdata ['product_supplier'] [$i], [
                        'contain' => 'city'
                    ]);

                    $row .= "<tr style='min-height:35px'>";
                    $colspan = 1;
                    if ($count == 1) {

                        /* $row.="<td rowspan='2'>".($i+1)."</td>"; */

                        $row .= "<td rowspan='" . $colspan . "'>" . $supplier_details->id . "</td>"; // price for the orderd quantity
                        $row .= "<td rowspan='" . $colspan . "'>" . $supplier_details->firstName . " " . $supplier_details->lastName . "</td>"; // name
                        $row .= "<td rowspan='" . $colspan . "'>" . $supplier_details->address . "</td>"; // address
                        $row .= "<td rowspan='" . $colspan . "'>" . $supplier_details->cid->cname . "</td>"; // city
                        /* $row.="<td>".$supplier_details->email."</td>";//email */
                        $row .= "<td rowspan='" . $colspan . "'>" . $supplier_details->contactNo . "</td>"; // contact
                        $row .= "<td rowspan='" . $colspan . "'>" . $supplier_details->mobileNo . "</td>"; // mobile
                        $sup_email = $supplier_details->email;
                    } else {
                        $row .= "<td></td><td></td><td></td><td></td><td></td><td></td>";
                    }

                    $row .= "<td>" . $product_details->id . "</td>"; // product id
                    $row .= "<td>" . $product_details->name . "</td>"; // name
                    $row .= "<td>" . $product_details->price . "</td>"; // price of a unit
                    $row .= "<td>" . $product_details->package_type->type . "</td>"; // unit
                    $row .= "<td>" . $quntity . "</td>"; // number of unit ordered
                    $row .= "<td>" . $product_details->price * $quntity . "</td>"; // price for the orderd quantity

                    $row .= "</tr>";

                    $count ++;
                }
            }
            $colspan = $count;
            $delivery_mail_string .= $row;
            $supliers_email [$sup_email] = $orderId . $sup_string . $row . $sup_string_end;
            $row = "";
        }
        $delivery_mail_string .= $sup_string_end . $total_string;
        $delivery_mail_addrrss = $delivery_model->get($delivery, [
            'fields' => [
                'email'
            ]
        ]);
        // echo $delivery_mail_addrrss['email'];
        // 
        $admin_mail_address = Configure::read('admin_email');
        //$customer=$order_model->find('all',['conditions'=>['Orders.id'=>$orderId],'contain'=>['Customers']])->first();		
        //$customer_mail_address=$customer->customers['email'];//"ashanrupasinghe11@gmail.com"
        //$customer_mail_address="ashanrupasinghe11@gmail.com";
        $order_data = (new OrdersController ())->__getOrderMailData($id_of_the_order);

        $user = $this->Cart->Users->get($user_id);
        $customer_mail [$user->username] = $order_data;
        //$customer_mail [$this->Auth->user('username')] = $order_data;
        $admin_mail [$admin_mail_address] = $order_data;
        $delivery_mail [$delivery_mail_addrrss ['email']] = $delivery_mail_string;


        /*
         * print_r($emails[4]);
         * print_r($emails[3]);
         * echo $delivery_mail_string;
         * die();
         */
        /*
         * print '<pre>';
         *
         * print_r(['del'=>$delivery_mail,'sup'=>$supliers_email]);
         * die();
         */

        // return ['del'=>$delivery_mail,'sup'=>$supliers_email];
        /*
         * print_r($supliers_email);
         * print_r($delivery_mail);
         */
        /* 	print '<pre>';
          print_r($supliers_email);
          echo "<br>";
          print_r($delivery_mail);
          echo "<br>";
          print_r($admin_mail);echo "<br>";
          print_r($customer_mail);
          die(); */

        //$this->sendemail('new', $supliers_email, 'sup'); // suppliers email
        $this->sendemail('new', $delivery_mail, 'del'); // delivery email
        $this->sendemail2('new', $admin_mail, 'admin'); // delivery email
        $this->sendemail2('new', $customer_mail, 'cus'); // delivery email
        // die();
    }

    /*
     * $orderid:order ID
     * $type:new/cancel
     * $recipients:array with email address
     * $recipient_type:sup/del
     * $products: product array, currently can send @ proceed new order, cancelation cand
     */

    public function sendemail($type = 'new', $recipients, $recipient_type) {
        $subject = "";
        $message = "";
        $message_full = "";
        $hello = "Hello ";
        if ($recipient_type == 'del') {
            $hello .= "Delevery person,\n";
        } elseif ($recipient_type == 'sup') {
            $hello .= "Supplier,\n";
        } elseif ($recipient_type == 'admin') {
            $hello .= "Admin,\n";
        } elseif ($recipient_type == 'cus') {
            $hello .= "Customer,\n";
        }

        if ($type == 'new') {
            $subject = "New Order Notification";
            $message = $hello . "New order has been made,\n";
        } elseif ($type == 'cancel') {
            $subject = "Order Cancellation";
            $message = $hello . "Cancelled a order,\n";
        }
        $message_end = "\nPlease check the system for more details";

        foreach ($recipients as $email_add => $message_body) {
            $message_full = $message . $message_body . $message_end;

            // echo 'xxx'.$email.'<br>'.$message_full;
            $from_mail_address = Configure::read('from_email');
            $email = new Email('default');
            $email->from([
                $from_mail_address => 'Direct2door.lk'
            ])->to($email_add)->subject($subject)->emailFormat('html')->send($message_full);
            $message_full = "";
        }
    }

    public function sendemail2($type = 'new', $recipients, $recipient_type) {
        $from_mail_address = Configure::read('from_email');
        $subject = Configure::read('add_order_email_subject');
        foreach ($recipients as $email_add => $data) {
            $email = new Email ();
            $email->template('customerorder')
                    ->viewVars($data)
                    ->from([$from_mail_address => 'Direct2door.lk'])
                    ->to($email_add)
                    ->subject($subject)
                    ->emailFormat('html')
                    ->send();
        }
    }

    /*
     * public function countFinaltotal($subtotal,$tax_p=0,$discount_p=0,$coupon_value=0){
     * $tax=$subtotal*$tax_p/100;
     * $discount=$subtotal*$discount_p/100;
     * $total=$subtotal+$tax-$discount-$coupon_value;
     * return $total;
     * }
     */

    public function processdata($data) {
        $tax_p = 0; // tax persontage 10
        $discount_p = 0; // discount persentage 5
        $counpon_value = 0; // call to a function to find coupon values
        $subtotal = $this->countSubTotal($data ['product_name'], $data ['product_quantity']); // count sub total
        $tax = $subtotal * $tax_p / 100;
        $discount = $subtotal * $discount_p / 100;
        $total = $subtotal + $tax - $discount - $counpon_value;
        // change adding discount directly,

        $direct_discount = ""; //$data ['direct_discount'];
        if ($direct_discount == "") {
            $direct_discount = 0;
        }
        $direct_total = $subtotal - $direct_discount;

        $newdata = [

            'subTotal' => $subtotal,
            'tax' => $tax,
            'discount' => $direct_discount,
            'couponCode' => "", //$data ['couponCode'],
            'total' => $direct_total,
        ]; // supplier informed

        return $newdata;
    }

    // for php count
    public function countSubTotal($arrIds, $arrQuantity) {
        $productSupModel = $this->loadModel('Products');
        $subTotal = 0;
        for ($i = 0; $i < sizeof($arrIds); $i ++) {
            $price_obj = $productSupModel->get($arrIds [$i], [
                'fields' => [
                    'price'
                ]
            ]);
            $price = $price_obj->price;
            $total = $price * $arrQuantity [$i];
            $subTotal += $total;
        }
        return $subTotal;
    }

    public function boka() {
        $order_data = (new OrdersController ())->__getOrderMailData(38);
        print '<pre>';
        $x = $order_data['order']->shipping[0]->delivery_date_time;

        echo $x->format('Y-m-d') . '<br>' . $x->format('g:i A');
        die();

        /*
         * shipping taabele 
          order table


          src/Controller/CartController.php
          src/Controller/OrdersController.php

          src/Model/Table/OrdersTable.php
          src/Model/Table/ShippingTable.php

          src/Template/Email/html/customerorder-1.ctp
          src/Template/Email/html/customerorder.ctp
          src/Template/Layout/Email/html/default-invoice.ctp


          Configure::read('admin_email'); */
    }

    public function verifyDeliverySlots() {
        $delivery_date = $this->request->data('selected_date');

        $delivery_date_timstamp = strtotime($delivery_date);

        $delivery_date_object = new \DateTime($delivery_date);
        $today_date = new \DateTime(date('Y-m-d'));
        
        $interval = $today_date->diff($delivery_date_object);
        $diffDays = $interval->format('%a');

        $unavailable_dates_array = array();

        $unavailable_dates = $this->__getUnavailableDates();

        foreach ($unavailable_dates AS $date) {
            $unavailable_dates_array[] = date('d-m-Y', strtotime($date->date));
        }
        
        $delivery_array = [];
        $message = [];
        $status = '404';
        
        if (in_array(date('d-m-Y', $delivery_date_timstamp), $unavailable_dates_array)) {
            $delivery_array = [];
            $message = "Sorry. Please select a different date. Delivery is not available on this date";
        } else if ($diffDays < 0) {
            $delivery_array = [];
            $message = "Sorry. Please select a future date";
        } else if ($diffDays == 0) {
            $current_hour = date('H');
            $delivery_span = $current_hour + 4;

            if ($delivery_span < 12) {
                $status = '200';
                $delivery_array = ['10:00:00' => '10:00 AM to 12:00 NOON', '12:00:00' => '12:00 NOON to 2:00 PM', '2:00:00' => '2:00 PM to 4:00 PM',
                    '4:00:00' => '4:00 PM to 6:00 PM', '6:00:00' => '6:00 PM to 8:00 PM'];
            } else if ($delivery_span < 14) {
                $status = '200';
                $delivery_array = ['12:00:00' => '12:00 NOON to 2:00 PM', '2:00:00' => '2:00 PM to 4:00 PM',
                    '4:00:00' => '4:00 PM to 6:00 PM', '6:00:00' => '6:00 PM to 8:00 PM'];
            } else if ($delivery_span < 16) {
                $status = '200';
                $delivery_array = ['2:00:00' => '2:00 PM to 4:00 PM',
                    '4:00:00' => '4:00 PM to 6:00 PM', '6:00:00' => '6:00 PM to 8:00 PM'];
            } else if ($delivery_span < 18) {
                $status = '200';
                $delivery_array = ['4:00:00' => '4:00 PM to 6:00 PM', '6:00:00' => '6:00 PM to 8:00 PM'];
            } else if ($delivery_span < 20) {
                $status = '200';
                $delivery_array = ['6:00:00' => '6:00 PM to 8:00 PM'];
            }else{
                $status = '404';
              $delivery_array = [];
              $message = "No slots available. Please select a different date.";
            }
            
        } else {
            $status = '200';
            $delivery_array = ['10:00:00' => '10:00 AM to 12:00 NOON', '12:00:00' => '12:00 NOON to 2:00 PM', '2:00:00' => '2:00 PM to 4:00 PM',
                '4:00:00' => '4:00 PM to 6:00 PM', '6:00:00' => '6:00 PM to 8:00 PM'];
        }
        $return = array();
        $return['status'] = $status;
        $return['time_slots'] = $delivery_array;
        $return['message'] = $message;
        echo json_encode($return);
        die();
    }

}
