<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Utility\Security;
use Cake\Mailer\Email;
use Cake\View\Helper\SessionHelper;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController {

    public function isAuthorized($user) {

        // The owner of an article can edit and delete it
        if (in_array($this->request->action, [
                    'userpage'
                ])) {

            if ($this->Auth->user()) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

    /*
     * public function beforeFilter(Event $event)
     * {
     * parent::beforeFilter($event);
     * // Allow users to register and logout.
     * // You should not add the "login" action to allow list. Doing so would
     * // cause problems with normal functioning of AuthComponent.
     * $this->Auth->allow(['add', 'logout']);
     * }
     */

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', [
            'users'
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id
     *        	User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $user = $this->Users->get($id, [
            'contain' => [
                'Callcenter',
                'Delivery',
                'Suppliers'
            ]
                ]);

        $this->set('user', $user);
        $this->set('_serialize', [
            'user'
        ]);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect([
                            'action' => 'index'
                        ]);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', [
            'user'
        ]);
    }

    /**
     * Edit method
     *
     * @param string|null $id
     *        	User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $user = $this->Users->get($id, [
            'contain' => []
                ]);
        if ($this->request->is([
                    'patch',
                    'post',
                    'put'
                ])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect([
                            'action' => 'index'
                        ]);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', [
            'user'
        ]);
    }

    /**
     * Delete method
     *
     * @param string|null $id
     *        	User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod([
            'post',
            'delete'
        ]);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect([
                    'action' => 'index'
                ]);
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow([
            'logout',
            'login',
            'register',
            'forgotpassword',
            'resetpasswordtoken',
            'verifytoken'
        ]);
    }

    public function login() {
        header('Content-type: application/json');
        if ($this->request->is('post')) {
            $return = [];
            $login_type = $this->request->data('login_type');
            $data = $this->request->data();
            if ($login_type != null && $login_type == 1) {
                $user_data = [
                    'user_type' => 5,
                    'status' => 1,
                    'login_type' => $data ['login_type'],
                    'firstName' => $data ['firstname'],
                    'lastName' => $data ['lastname'],
                    'fbid' => $data ['fbid'],
                    'username' => $data ['email']
                ];
                if (isset($data['push_token'])) {
                    $user_data['push_token'] = $data['push_token'];
                }
                $query = $this->Users->find('all', [
                    'conditions' => [
                        'fbid' => $user_data ['fbid']
                    ]
                        ]);
                if ($query->count() > 0) {
                    // loged in
                    $userDetails = $query->first();
                    $user_id = $userDetails->id;
                    $mobtoken = $this->__getMobToken();
                    $query = $this->Users->query();
                    $update_data = [
                        'mobtoken' => $mobtoken,
                        'mobtoken_created_at' => date('Y-m-d H:i:s')
                    ];
                    if (isset($data['push_token'])) {
                        $update_data['push_token'] = $data['push_token'];
                    }


                    $query->update()->set($update_data)->where([
                        'id' => $user_id
                    ])->execute();

                    $return ['status'] = 0;
                    $return ['token'] = $mobtoken;
                    $return ['grand_total'] = $this->__getGrandTotal($user_id);
                    $return ['message'] = 'login successful';
                    echo json_encode($return);
                    die();
                } else {
                    // register
                    $user = $this->Users->newEntity();
                    $user = $this->Users->patchEntity($user, $user_data);
                    /*
                     * $this->Users->save ( $user );
                     * print '<pre>';
                     * print_r($user);
                     * die();
                     */
                    if ($this->Users->save($user)) {
                        $user_data ['user_id'] = $user->id;
                        $customer_model = $this->loadModel('customers');
                        $customer = $customer_model->newEntity();
                        $customer = $customer_model->patchEntity($customer, $user_data);
                        /*
                         * print '<pre>';
                         * print_r($customer);
                         * die();
                         */

                        if ($customer_model->save($customer)) {
                            // $this->Flash->success(__('The user has been saved.'));
                            // Retrieve user from DB
                            $authUser = $this->Users->get($user->id)->toArray();

                            // Log user in using Auth
                            $this->Auth->setUser($authUser);

                            $mobtoken = $this->__getMobToken();
                            $query = $this->Users->query();
                            $query->update()->set([
                                'mobtoken' => $mobtoken,
                                'mobtoken_created_at' => date('Y-m-d H:i:s')
                            ])->where([
                                'id' => $user ['id']
                            ])->execute();

                            // create cart
                            $cart_model = $this->loadModel('Cart');
                            $cart_entity = $cart_model->newEntity([
                                'user_id' => $user->id
                                    ]);
                            // $cart_entity->patchEntity($cart_entity,['user_id'=>$user->id]);
                            $cart_model->save($cart_entity);
                            // Redirect user
                            // return $this->redirect(['controller' => 'Users', 'action' => 'userpage']);
                            $return ['status'] = 0;
                            $return ['token'] = $mobtoken;
                            $return ['grand_total'] = $this->__getGrandTotal($user->id);
                            $return ['message'] = 'register and login successful';
                            echo json_encode($return);
                            die();
                        } else {
                            // $this->Flash->error(__('Ops, Something went wrong'));
                            $return ['status'] = 901;
                            $return ['message'] = "Unable to save customer. Try again.";
                            echo json_encode($return);
                            die();
                        }
                    } else {
                        // $this->Flash->error(__('The user could not be saved. Please, try again.'));
                        $return ['status'] = 902;
                        $return ['message'] = "Unable to save user. Try again.";
                        echo json_encode($return);
                        die();
                    }
                }
            } else {
                /* if (! $this->Auth->user ()) { */
                /* if ($this->request->is ( 'post' )) { */

                // $user = $this->Auth->identify ();
                $user_name = $this->request->data('username');
                $password = $this->request->data('password');
                $query = $this->Users->find('all', [
                    'conditions' => [
                        'username' => $user_name
                    ]
                        ]);

                $count = $query->count();
                $user = $query->first();
                if ($count > 0 && (new DefaultPasswordHasher ())->check($password, $user ['password'])) {

                    if ($user ['status'] == 1) {
                        $this->Auth->setUser($user);
                        $mobtoken = $this->__getMobToken();
                        $query = $this->Users->query();
                        $update_data = [
                            'mobtoken' => $mobtoken,
                            'mobtoken_created_at' => date('Y-m-d H:i:s')
                        ];
                        if (isset($data['push_token'])) {
                            $update_data['push_token'] = $data['push_token'];
                        }

                        $query->update()->set($update_data)->where([
                            'id' => $user ['id']
                        ])->execute();

                        // return $this->redirect($this->Auth->redirectUrl());
                        $return ['status'] = 0;
                        $return ['token'] = $mobtoken;
                        $return ['grand_total'] = $this->__getGrandTotal($user ['id']);
                        $return ['message'] = 'login successful';
                        echo json_encode($return);
                        die();
                    } else {
                        // $this->Flash->error(__('Your account has been disabled'));
                        $return ['status'] = 800;
                        $return ['message'] = 'Account is disabled';
                        echo json_encode($return);
                        die();
                    }
                } else {
                    // $this->Flash->error ( __ ( 'Invalid username or password, try again' ) );
                    $return ['status'] = 400;
                    $return ['message'] = 'Invalid username or password';
                    echo json_encode($return);
                    die();
                }
                /*
                 * } else {
                 * $return ['status'] = 500;
                 * $return ['message'] = "Unauthorized login";
                 * echo json_encode ( $return );
                 * die ();
                 * }
                 */
                /*
                 * } else {
                 * // return $this->redirect(['controller' => 'Users', 'action' => 'userpage']);
                 * $return ['status'] = 0;
                 * $return ['message'] = 'already logedin';
                 * echo json_encode ( $return );
                 * die ();
                 * }
                 */
            }
        } else {
            $return ['status'] = 500;
            $return ['message'] = "Unauthorized login";
            echo json_encode($return);
            die();
        }
    }

    public function logout() {
        header('Content-type: application/json');
        /*
         * $this->Auth->logout ();
         * $return ['status'] = 0;
         * $return ['message'] = 'logout sucess';
         * echo json_encode ( $return );
         * die ();
         */

        if ($this->request->is('post')) {
            // $user_id = $this->request->data('token');
            $token = $this->request->data('token');
            $query = $this->Users->find('all', [
                        'conditions' => [
                            'mobtoken' => $token
                        ]
                    ])->toArray();
            if (sizeof($query) > 0) {
                $user = $this->Users->get($query[0]->id);
                $user->mobtoken = '';
                $user->mobtoken_created_at = date('Y-m-d H:i:s');
                if ($this->Users->save($user)) {
                    $return ['status'] = 0;
                    $return ['message'] = 'logout sucess';
                } else {
                    $return ['status'] = 999;
                    $return ['message'] = 'something wrong';
                }
            } else {
                $return ['status'] = 100;
                $return ['message'] = "invalid token or already loged out";
            }
        } else {
            $return ['status'] = 500;
            $return ['message'] = "Unauthorized access";
        }
        // return $this->redirect($this->Auth->logout());
        echo json_encode($return);
        die();
    }

    public function userpage() {
        if ($this->Auth->user()) {
            $userlevel = $this->Auth->user('user_type');
            if ($userlevel == 1) {
                return $this->redirect([
                            'controller' => 'Customers',
                            'action' => 'search'
                        ]);
            }
            if ($userlevel == 2) {
                return $this->redirect([
                            'controller' => 'Customers',
                            'action' => 'search'
                        ]);
            }
            if ($userlevel == 3) {
                $user_id = $this->Auth->user('id');
                return $this->redirect([
                            'controller' => 'SupplierNotifications',
                            'action' => 'schedule'
                        ]);
            }
            if ($userlevel == 4) {
                return $this->redirect([
                            'controller' => 'DeliveryNotifications',
                            'action' => 'listSuppliervice'
                        ]);
            }
            if ($userlevel == 5) {

                return $this->redirect([
                            'controller' => 'Front',
                            'action' => 'index'
                        ]);
            }
        } else {
            return $this->redirect([
                        'controller' => 'Users',
                        'action' => 'login'
                    ]);
        }
    }

    /* register user */

    public function register() {
        header('Content-type: application/json');
        $return = [];
        $user = $this->Users->newEntity();

        if ($this->request->is('post')) {
            $data = $this->request->data;
            $user_data = [
                'username' => $data ['email'],
                'user_type' => 5,
                'password' => $data ['password'],
                'confirm_password' => $data ['confirm_password'],
                'status' => 1,
                'formType' => $data ['formType']
            ];
            if (isset($data['push_token'])) {
                $user_data['push_token'] = $data['push_token'];
            }
            
//            $dir = new Folder('/var/www/vhosts/direct2door_new/httpdocs/src/Controller');
//            $files = $dir->find('.*\.txt');
//            
//            foreach ($files as $file) {
//                $file = new File($dir->pwd() . DS . $file);
//                 $file->write(json_encode($data));
//                // $file->append('I am adding to the bottom of this file.');
//                // $file->delete(); // I am deleting this file
//                $file->close(); // Be sure to close the file when you're done
//            }
//
//            $return ['status'] = 0;
//            $return['message'] = $data;
//            echo json_encode($return);
//            die();
            //$user_data['push_token'] = $data['first_name'].' '.$data['last_name'];
            // $customer_data[];
            //print_r($data);
            //die();
            $user = $this->Users->patchEntity($user, $user_data);
            //print_r($user);
            //die($user->id);
            if ($this->Users->save($user)) {
                $data['status'] = 1;
                $data ['user_id'] = $user->id;
//                                $data ['firstName'] = $data['first_name'];
//                                $data ['lastName'] = $data['last_name'];
//                                $data ['email'] = $data['email'];
                $customer_model = $this->loadModel('customers');
                $customer_ent = $customer_model->newEntity();
                $customer = $customer_model->patchEntity($customer_ent, $data);
               //print_r($data);
                //die();
                if ($customer_model->save($customer)) {
                    // $this->Flash->success(__('The user has been saved.'));
                    // Retrieve user from DB
                    $authUser = $this->Users->get($user->id)->toArray();

                    // Log user in using Auth
                    $this->Auth->setUser($authUser);

                    $mobtoken = $this->__getMobToken();
                    $query = $this->Users->query();
                    $query->update()->set([
                        'mobtoken' => $mobtoken,
                        'mobtoken_created_at' => date('Y-m-d H:i:s')
                    ])->where([
                        'id' => $user ['id']
                    ])->execute();

                    // create cart
                    $cart_model = $this->loadModel('Cart');
                    $cart_entity = $cart_model->newEntity([
                        'user_id' => $user->id
                            ]);
                    // $cart_entity->patchEntity($cart_entity,['user_id'=>$user->id]);
                    $cart_model->save($cart_entity);
                    // Redirect user
                    // return $this->redirect(['controller' => 'Users', 'action' => 'userpage']);
                    $return ['status'] = 0;
                    $return ['token'] = $mobtoken;
                    $return ['grand_total'] = $this->__getGrandTotal($user->id);
                    $return ['message'] = 'register and login successful';
                    echo json_encode($return);
                    die();
                } else {
                    // $this->Flash->error(__('Ops, Something went wrong'));
                    $return->status = 901;
                    $return->message = "Unable to save customer. Try again.";
                    echo json_encode($return);
                    die();
                }

                // return $this->redirect(['action' => 'register']);
            } else {
                // $this->Flash->error(__('The user could not be saved. Please, try again.'));
                $return ['status'] = 902;
                $return ['message'] = "Unable to save user. Try again.";
                echo json_encode($return);
                die();
            }
        } else {
            $return ['status'] = 500;
            $return ['message'] = "Unauthorized Request";
            echo json_encode($return);
            die();
        }
        /*
         * $this->set(compact('user'));
         * $this->set('_serialize', ['user']);
         * $cityModel=$this->loadModel('city');
         * $cities = $cityModel->find ()->select ( [
         * 'cid',
         * 'cname'
         * ] )->formatResults ( function ($results) {
         * //@var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface
         * return $results->combine ( 'cid', function ($row) {
         * return $row ['cname'];
         * } );
         * } );
         * $this->set ( compact ( 'cities' ) );
         */
    }

    /**
     * Allow a user to request a password reset.
     *
     * @return
     *
     */
    function forgotpassword() {
        header('Content-type: application/json');
        $return = [];
        if (!empty($this->request->data)) {
            $user_name = $this->request->data('username');
            $user = $this->Users->findByUsername($user_name)->first();
            if (empty($user)) {
                // $this->Flash->error(__('Sorry, the username entered was not found.'));
                // $this->redirect('/user/forgotpassword');
                $return ['status'] = 600;
                $return ['message'] = 'The email address not found';
                echo json_encode($return);
                die();
            } else {
                $user = $this->__generatePasswordToken($user);

                if ($this->Users->save($user) && $this->__sendForgotPasswordEmail($user->id)) {
                    // $this->Flash->success(__('Password reset instructions have been sent to your email address. You have 24 hours to complete the request.'));
                    // $this->redirect('/user/login');
                    $return ['status'] = 0;
                    $return ['message'] = 'Password reset instructions have been sent to your email address. You have 24 hours to complete the request.';
                    echo json_encode($return);
                    die();
                } else {
                    // $this->Flash->error(__('Sorry, Something went wrong please try again'));
                    $return ['status'] = 902;
                    $return ['message'] = 'Sorry, Something went wrong please try again';
                    echo json_encode($return);
                    die();
                }
            }
        } else {
            $return ['status'] = 500;
            $return ['message'] = "Unauthorized Request";
            echo json_encode($return);
            die();
        }
    }

    /**
     * Generate a unique hash / token.
     *
     * @param
     *        	Object User
     * @return Object User
     */
    function __generatePasswordToken($user) {
        if (empty($user)) {
            return null;
        }
        // Generate a random string 100 chars in length.
        $token = "";
        for ($i = 0; $i < 100; $i ++) {
            $d = rand(1, 100000) % 2;
            $d ? $token .= chr(rand(33, 79)) : $token .= chr(rand(80, 126));
        }
        (rand(1, 100000) % 2) ? $token = strrev($token) : $token = $token;
        // Generate hash of random string
        $hash = Security::hash($token, 'sha256', true);
        ;
        for ($i = 0; $i < 20; $i ++) {
            $hash = Security::hash($hash, 'sha256', true);
        }

        $user->reset_password_token = $hash;
        $user->token_created_at = date('Y-m-d H:i:s');
        return $user;
    }

    /**
     * Sends password reset email to user's email address.
     *
     * @param
     *        	$id
     * @return
     *
     */
    function __sendForgotPasswordEmail($id = null) {
        if (!empty($id)) {
            $User = $this->Users->get($id);
            $email = new Email('default');
            $email->to($User->username)->replyTo('info@direct2door.lk')->subject('Password Reset Request - DO NOT REPLY')->from('info@direct2door.lk')->template('reset_password_request')->emailFormat('html')->set('User', $User);
            try {
                if ($email->send()) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                $this->Flash->error(__('Sorry, Error in sending email please try again'));
            }
        }
        return false;
    }

    /**
     * Allow user to reset password if $token is valid.
     *
     * @return
     *
     */
    function resetpasswordtoken($reset_password_token = null) {
        // header('Content-type: application/json');
        $return = [];
        if (empty($this->request->data)) {
            $data = $this->Users->findByResetPasswordToken($reset_password_token)->first();

            if (!empty($data->reset_password_token) && !empty($data->token_created_at) && $this->__validToken($data->token_created_at)) {

                $data->id = null;
                $this->request->session()->write('pwreset.reset_password_token', $reset_password_token);
                /*
                 * $return['status']=0;
                 * $return['message']='valid reset request';
                 * $return['pw_reset_token']=$reset_password_token;
                 * echo json_encode($return);
                 * die();
                 */
            } else {
                $this->Flash->error(__('The password reset request has either expired or is invalid.'));
                $this->redirect('/user/login');
                /*
                 * $return['status']=400;
                 * $return['message']='The password reset request has either expired or is invalid.';
                 * echo json_encode($return);
                 * die();
                 */
            }
        } else {

            if ($this->request->data('reset_password_token') != $this->request->session()->read('pwreset.reset_password_token')) {
                $this->Flash->error(__('The password reset request has either expired or is invalid.'));
                $this->redirect('/user/login');
                /*
                 * $return['status']=400;
                 * $return['message']='The password reset request has either expired or is invalid.';
                 * echo json_encode($return);
                 * die();
                 */
            }
            $user = $this->Users->findByResetPasswordToken($this->request->data('reset_password_token'))->first();
            $user = $this->Users->patchEntity($user, $this->request->data);
            // $this->User->id = $user['User']['id'];

            if ($this->Users->save($user)) {

                $user->reset_password_token = null;
                $user->token_created_at = null;

                if ($this->Users->save($user) && $this->__sendPasswordChangedEmail($user->id)) {
                    $this->request->session()->delete('pwreset.reset_password_token');
                    $this->Flash->success(__('Your password was changed successfully. Please login to continue.'));
                    $this->redirect('/user/login');
                    /*
                     * $return['status']=0;
                     * $return['message']='Your password was changed successfully. Please login to continue.';
                     * echo json_encode($return);
                     * die();
                     */
                }
            } else {
                $this->Flash->error(__('Something went wrong please try again'));
                $token = $this->request->session()->read('pwreset.reset_password_token');
                $this->redirect('/user/resetpasswordtoken/' . $token);
                /*
                 * $return['status']=400;
                 * $return['message']='New password is not saved, try again';
                 * $return['pw_reset_token']=$token;
                 * echo json_encode($return);
                 * die();
                 */
            }
        }

        $this->set('reset_password_token', $reset_password_token);
    }

    /**
     * Validate token created at time.
     *
     * @param String $token_created_at        	
     * @return Boolean
     */
    function __validToken($token_created_at) {
        $expired = strtotime($token_created_at) + 86400;
        $time = strtotime("now");
        if ($time < $expired) {
            return true;
        }
        return false;
    }

    /**
     * Notifies user their password has changed.
     *
     * @param
     *        	$id
     * @return
     *
     */
    function __sendPasswordChangedEmail($id = null) {
        if (!empty($id)) {
            $User = $this->Users->get($id);
            $email = new Email('default');
            $email->to($User->username)->replyTo('info@direct2door.lk')->subject('Password Changed - DO NOT REPLY')->from('info@direct2door.lk')->template('password_reset_success')->emailFormat('html')->set('User', $User);

            try {
                if ($email->send()) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                $this->Flash->error(__('Sorry, Error in sending email please try again'));
            }
        }
        return false;
    }

    function __getMobToken() {
        /*
         * $hasher = new DefaultPasswordHasher();
         * $token=$hasher->hash(sha1(Text::uuid()));
         */
        $token = "";
        for ($i = 0; $i < 100; $i ++) {
            $d = rand(1, 100000) % 2;
            $d ? $token .= chr(rand(33, 79)) : $token .= chr(rand(80, 126));
        }
        (rand(1, 100000) % 2) ? $token = strrev($token) : $token = $token;
        // Generate hash of random string
        $hash = Security::hash($token, 'sha256', true);
        ;
        for ($i = 0; $i < 20; $i ++) {
            $hash = Security::hash($hash, 'sha256', true);
        }

        return $hash;
    }

    /**
     * return status 0 if token has in db
     */
    public function verifytoken() {
        if ($this->request->is('post')) {
            $token = $this->request->data('token');

            $user = $this->Users->find('all', [
                'conditions' => [
                    'mobtoken' => $token
                ]
                    ]);
            if ($user->count() > 0) {
                $return ['status'] = 0;
                $return ['message'] = "success";
            } else {
                $return ['status'] = 100;
                $return ['message'] = "token not found";
            }
        } else {
            $return ['status'] = 500;
            $return ['message'] = "Unauthorized access";
        }

        echo json_encode($return);
        die();
    }

    function __getGrandTotal($user_id) {
        $cart_details = $this->Users->Cart->find('all', ['fields' => ['id'], 'conditions' => ['user_id' => $user_id]])->toArray();
        $cart = new CartController();

        if (is_array($cart_details) && !empty($cart_details)) {
            $total = $cart->__getTotal($cart_details[0]->id);
        } else {
            $total['grand_total'] = '0';
        }


        return $total['grand_total'];
    }

}

//https://github.com/hunzinker/CakePHP-Auth-Forgot-Password/blob/master/controllers/users_controller.php