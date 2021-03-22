<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Service extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('activity_model');
    }

    public function index_post() {
        ini_set('max_execution_time', 300);
        $datapost = json_decode($this->post('request'));
        if (isset($datapost->requestMethod)) {
            $_SESSION['user'] = (isset($datapost->user) ? $this->security->xss_clean(trim($datapost->user)) : (isset($datapost->requestUser) ? $this->security->xss_clean(trim($datapost->requestUser)) : ''));
            $refno = (isset($datapost->refno) ? $this->security->xss_clean(trim($datapost->refno)) : '');

                $this->activity_model->counter_activity($this->security->xss_clean(trim($datapost->requestMethod)));
                $this->activity_model->insert_activity($this->security->xss_clean(trim($datapost->requestMethod)) . ' ' . $refno . ' REQUEST ', $this->security->xss_clean(trim($this->post('request'))));
                switch (trim($datapost->requestMethod)) {
                    case 'addPost':
                        $this->load->helper('post_helper');
                        $this->response(addPost($this->post('request')));
                        break;
                    case 'getPost':
                        $this->load->helper('post_helper');
                        $this->response(getPost($this->post('request')));
                        break;
                    case 'goFollow':
                        $this->load->helper('follower_helper');
                        $this->response(goFollow($this->post('request')));
                        break;
                    case 'inquiryPekerjaan':
                        $this->load->helper('user_helper');
                        $this->response(inquiryPekerjaan($this->post('request')));
                        break;
                    case 'login':
                        $this->load->helper('user_helper');
                        $this->response(login($this->post('request')));
                        break;
                    case 'register':
                        $this->load->helper('user_helper');
                        $this->response(register($this->post('request')));
                        break;
                    case 'getAllUser':
                        $this->load->helper('user_helper');
                        $this->response(getAllUser($this->post('request')));
                        break;
                    case 'getUser':
                        $this->load->helper('user_helper');
                        $this->response(getUser($this->post('request')));
                        break;
                    case 'postUser':
                        $this->load->helper('user_helper');
                        $this->response(postUser($this->post('request')));
                        break;
                    case 'cekFollowers':
                        $this->load->helper('follower_helper');
                        $this->response(cekFollowers($this->post('request')));
                        break;
                    case 'cekFollowed':
                        $this->load->helper('follower_helper');
                        $this->response(cekFollowed($this->post('request')));
                        break;

                    default:
                        $this->response((object) array('responseCode' => '08', 'responseDesc' => 'Unknown Request Method[' . $datapost->requestMethod . ']', 'responseData' => array()), 404);
                }

            unset($_SESSION['user']);
        } else {
            $this->activity_model->insert_activity_error('CRASH REPORT', json_encode($this->post()));
            $this->response((object) array('responseCode' => '08', 'responseDesc' => 'Unknown Request Method or Not Isset', 'responseData' => array()), 404);
        }
    }

}
