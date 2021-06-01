<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function kirimPesan($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('message_model');
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
        
        if(!isset($requestData->received_by)) {
            throw new Exception("Parameter received_by tidak valid");
        }
        $received_by = $requestData->received_by;

        if(!isset($requestData->pesan)) {
            throw new Exception("Parameter pesan tidak valid");
        }
        $pesan = $requestData->pesan;

        $resdata = $CI->message_model->addPesan($user, $received_by, $pesan);

        if (!$resdata) {
            throw new Exception("Gagal Kirim Pesan.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Kirim Pesan Berhasil';
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function getPesan($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('message_model');
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

        $page = $datapost->page;
        if (!isset($datapost->page)) {
            throw new Exception("Parameter page tidak valid");
        }
        $follower = $CI->follower_model->cekFollowers($user)->result_array();
        foreach ($follower as $key) {
            $data[] = $key['followerUsername'];
        }
        $limit = 2;
        $pagging = $page * $limit;
        $resdata = $CI->message_model->getPesan($data, $pagging, $limit, $user)->result();
        if (!$resdata) {
            throw new Exception("Data tidak ditemukan.");
        }

        $result->responseCode = '00';
        $result->responseDesc = 'Get Pesan Sukses.';
        $result->responseData = $resdata;
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}
