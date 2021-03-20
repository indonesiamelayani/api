<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    

    function addPost($request) {
    $result               = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI   = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('post_model');
    $CI->load->model('follower_model');
    $datapost = json_decode($request);
    try {
        $requestData = $datapost->requestData;
        $user = $datapost->user;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->user)) {
            throw new Exception("Parameter user tidak valid");
        }

        $description = $requestData->description;

        if (!isset($requestData->description)) {
            throw new Exception("Parameter description tidak valid");
        }

        $tag = $requestData->tag;

        if (!isset($requestData->tag)) {
            throw new Exception("Parameter tag tidak valid");
        }

        $photo = $requestData->photo;

        if (!isset($requestData->photo)) {
            throw new Exception("Parameter photo tidak valid");
        }

        $location = $requestData->location;

        $image = base64_decode($photo);
        $image_name = md5(uniqid(rand(), true));
        $filename = $image_name . '.' . 'png';
        $path = "file/".$filename;
        file_put_contents($path . $filename, $image);

        $data_insert = $filename;

        $resdata = $CI->post_model->insert_img($description, $user, $tag, $location, $data_insert);

        if (!$resdata) {
            throw new Exception("Gagal Post.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Post Berhasil';

    } catch (Exception $e) {
    	$result->responseCode = '99';
    	$result->responseDesc = $e->getMessage()." Ln.".$e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}
    function getPost($request) {
    $result               = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI   = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('post_model');
    $CI->load->model('follower_model');
    $datapost = json_decode($request);
    try {
        $user = $datapost->user;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->user)) {
            throw new Exception("Parameter user tidak valid");
        }
        $follower = $CI->follower_model->cekFollowers($user)->result_array();
        foreach( $follower as $key){
        $data[] = $key['followerUsername'];
}
        // print_r($data);die;

        $resdata = $CI->post_model->getPost($data);
        // print_r($resdata->result());die;
        if (!$resdata) {
            throw new Exception("Data tidak ditemukan.");
        }

        $result->responseCode = '00';
        $result->responseDesc = 'Get Post Sukses.';
        $result->responseData = $resdata->result();

    } catch (Exception $e) {
    	$result->responseCode = '99';
    	$result->responseDesc = $e->getMessage()." Ln.".$e->getLine();
    }
}

    function getUser($request) {
    $result               = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI   = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('user_model');
    $datapost = json_decode($request);
    try {
        $user = $datapost->user;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->user)) {
            throw new Exception("Parameter user tidak valid");
        }
        if (!isset($datapost->username)) {
            throw new Exception("Parameter id_user tidak valid");
        }
        $username = $datapost->username;
        
        $resdata = $CI->user_model->getUser($username);
        if (!$resdata || $resdata->num_rows() == 0) {
            throw new Exception("Data tidak ditemukan.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Inquiry Sukses.';
        $result->responseData = $resdata->result();

    } catch (Exception $e) {
    	$result->responseCode = '99';
    	$result->responseDesc = $e->getMessage()." Ln.".$e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}
    function postUser($request) {
    $result               = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI   = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('user_model');
    $datapost = json_decode($request);
    try {
        $user = $datapost->user;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->user)) {
            throw new Exception("Parameter user tidak valid");
        }
        if (!isset($datapost->id_user)) {
            throw new Exception("Parameter id_user tidak valid");
        }
        $id_user = $datapost->id_user;
        
        $resdata = $CI->user_model->getUser($id_user);
        if (!$resdata || $resdata->num_rows() == 0) {
            throw new Exception("Data tidak ditemukan.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Inquiry Sukses.';
        $result->responseData = $resdata->result();

    } catch (Exception $e) {
    	$result->responseCode = '99';
    	$result->responseDesc = $e->getMessage()." Ln.".$e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function login($request) {
    $result               = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI   = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('user_model');
    $datapost = json_decode($request);
    try {
        $requestData = $datapost->requestData;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        $username = $requestData->username;
        if (!isset($requestData->username)) {
            throw new Exception("Parameter username tidak valid");
        }

        $password = $requestData->password;
        if (!isset($requestData->password)) {
            throw new Exception("Parameter password tidak valid");
        }

        $resdata = $CI->user_model->get_user($username,$password);
        if (!$resdata || $resdata->num_rows() == 0) {
            throw new Exception("Akun tidak ditemukan.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Login Sukses.';
        $result->responseData = $resdata->result();
    } catch (Exception $e) {
    	$result->responseCode = '99';
    	$result->responseDesc = $e->getMessage()." Ln.".$e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function register($request) {
    $result               = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI   = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('user_model');
    $datapost = json_decode($request);
    try {
        $requestData = $datapost->requestData;
        $username = $requestData->username;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }
        if (!isset($requestData->username)) {
            throw new Exception("Parameter username tidak valid");
        }
        $nama = $requestData->nama;
        if (!isset($requestData->nama)) {
            throw new Exception("Parameter nama tidak valid");
        }
        $password = $requestData->password;
        if (!isset($requestData->password)) {
            throw new Exception("Parameter password tidak valid");
        }
        $email = $requestData->email;
        if (!isset($requestData->email)) {
            throw new Exception("Parameter email tidak valid");
        }
        $phone = $requestData->phone;
        if (!isset($requestData->phone)) {
            throw new Exception("Parameter phone tidak valid");
        }
        $cekuser = $CI->user_model->cek_user($username);
        $ceknohp = $CI->user_model->cek_nohp($phone);
        if($cekuser->num_rows() != 0){
            throw new Exception("Username sudah digunakan");
        }
        if($ceknohp->num_rows() != 0){
            throw new Exception("Nomor HP sudah digunakan");
        }
        $resdata = $CI->user_model->create_user($username,$nama,$password,$email,$phone);
        if (!$resdata) {
            throw new Exception("Data tidak berhasil disimpan.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Registrasi Sukses.';
    } catch (Exception $e) {
    	$result->responseCode = '99';
    	$result->responseDesc = $e->getMessage()." Ln.".$e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}