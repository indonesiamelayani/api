<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function getProfilByID($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('profil_model');
    $datapost = json_decode($request);
    try {
        $user = $datapost->user;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->user)) {
            throw new Exception("Parameter user tidak valid");
        }

        $resdata = $CI->profil_model->getProfilByID($user)->result();
        if (!$resdata) {
            throw new Exception("Data tidak ditemukan.");
        }

        $result->responseCode = '00';
        $result->responseDesc = 'Get Profil Sukses.';
        $result->responseData = $resdata;
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function getPostByUser($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('post_model');
    $datapost = json_decode($request);
    try {
        $user = $datapost->user;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->user)) {
            throw new Exception("Parameter user tidak valid");
        }

        $page = $datapost->page;
        if (!isset($datapost->page)) {
            throw new Exception("Parameter page tidak valid");
        }
        $limit = 2;
        $pagging = $page * $limit;
        $resdata = $CI->post_model->getPostByUser($user, $pagging, $limit)->result();
        if (!$resdata) {
            throw new Exception("Data tidak ditemukan.");
        }

        $result->responseCode = '00';
        $result->responseDesc = 'Get Post By User Sukses.';
        $result->responseData = $resdata;
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function editProfil($request)
{
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = &get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('profil_model');
    $CI->load->model('user_model');
    $datapost = json_decode($request);
    try {
        $requestData = $datapost->requestData;
        $username = $requestData->username;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }
        if (!isset($datapost->user)) {
            throw new Exception("Parameter user tidak valid");
        }
        $user = $datapost->user;
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
        
        if($password == NULL || $password == ''){
            $password = $CI->user_model->cek_pw($username)->password;
        }
        $email = $requestData->email;
        if (!isset($requestData->email)) {
            throw new Exception("Parameter email tidak valid");
        }
        $foto = $requestData->foto;
        if (!isset($requestData->foto)) {
            throw new Exception("Parameter email tidak valid");
        }
        $phone = $requestData->phone;
        if (!isset($requestData->phone)) {
            throw new Exception("Parameter phone tidak valid");
        }
        $bio = $requestData->bio;
        if (!isset($requestData->bio)) {
            throw new Exception("Parameter bio tidak valid");
        }
        $foto = $requestData->foto;

        if (!isset($requestData->foto)) {
            throw new Exception("Parameter foto tidak valid");
        }

        $image = base64_decode($foto);
        $image_name = md5(uniqid(rand(), true));
        $filename = $image_name . '.' . 'png';
        if (!file_exists('file/' . $user)) {
            mkdir('file/' . $user, 0777, true);
        }
        $path = 'file/' . $user . '/';
        file_put_contents($path . $filename, $image);

        $data_insert = $user . '/' . $filename;

        $cekuser = $CI->user_model->cek_user($username);
        $ceknohp = $CI->user_model->cek_nohp($phone);
        if ($cekuser->num_rows() == 0) {
            throw new Exception("Username tidak terdaftar");
        }
        if ($ceknohp->num_rows() == 0) {
            throw new Exception("Nomor HP tidak terdaftar");
        }

        $resdata = $CI->profil_model->upd_user($username, $nama, $password, $email, $phone, $bio, $data_insert);
        if (!$resdata) {
            throw new Exception("Data tidak berhasil disimpan.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Update Profil Sukses.';
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

