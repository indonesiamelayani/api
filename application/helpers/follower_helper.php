<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function goFollow($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('follower_model');
    $datapost = json_decode($request);
    try {
        $followerUsername = $datapost->username;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->username)) {
            throw new Exception("Parameter username tidak valid");
        }

        if (!isset($datapost->usernameToFollow)) {
            throw new Exception("Parameter username to follow tidak valid");
        }

        $usernametoFollow = $datapost->usernameToFollow;

        $getnama = $CI->follower_model->getNama($followerUsername)->result();
        $nama = $getnama[0]->nama;

        $getNamaFollow = $CI->follower_model->getNama($usernametoFollow)->result();
        $namatofollow = $getNamaFollow[0]->nama;

        $cekFollow = $CI->follower_model->validasiFollow($usernametoFollow);
        if ($cekFollow->num_rows() != 0) {
            throw new Exception("Anda sudah follow akun @" . $usernametoFollow . "");
        }
        $resdata = $CI->follower_model->goFollow($nama, $followerUsername, $usernametoFollow, $namatofollow);
        if (!$resdata) {
            throw new Exception("Gagal Follow.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Follow Sukses.';
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function cekFollowers($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('follower_model');
    $datapost = json_decode($request);
    try {
        $username = $datapost->username;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->username)) {
            throw new Exception("Parameter user tidak valid");
        }

        $resdata = $CI->follower_model->cekFollowers($username);
        if (!$resdata || $resdata->num_rows() == 0) {
            throw new Exception("Data tidak ditemukan.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Cek Followers Sukses.';
        $result->responseData = $resdata->result();
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function cekFollowed($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('follower_model');
    $datapost = json_decode($request);
    try {
        $username = $datapost->username;
        if ($CI->libs_bearer->cekToken() == false) {
            throw new Exception("Access Forbidden");
        }

        if (!isset($datapost->username)) {
            throw new Exception("Parameter user tidak valid");
        }

        $resdata = $CI->follower_model->cekFollowed($username);
        if (!$resdata || $resdata->num_rows() == 0) {
            throw new Exception("Data tidak ditemukan.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Cek Followed Sukses.';
        $result->responseData = $resdata->result();
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}
