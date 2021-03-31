<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function addStories($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('stories_model');
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
        
        $photo = $requestData->photo;

        if (!isset($requestData->photo)) {
            throw new Exception("Parameter photo tidak valid");
        }

        $image = base64_decode($photo);
        $image_name = md5(uniqid(rand(), true));
        $filename = $image_name . '.' . 'png';
        if (!file_exists('file/' . $user)) {
            mkdir('file/' . $user, 0777, true);
        }
        $path = 'file/' . $user . '/';
        file_put_contents($path . $filename, $image);

        $data_insert = $filename;

        $resdata = $CI->stories_model->insert_img($description, $user, $data_insert);
        
        $resid = $CI->stories_model->getIDStories($user, $data_insert);
        if (!$resdata) {
            throw new Exception("Gagal Add Story.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Add Story Berhasil';
        $result->responseData = $resid->result();
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function getStories($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('stories_model');
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
        foreach ($follower as $key) {
            $data[] = $key['followerUsername'];
        }
        $resdata = $CI->stories_model->getStories($data)->result();
        if (!$resdata) {
            throw new Exception("Data tidak ditemukan.");
        }

        $result->responseCode = '00';
        $result->responseDesc = 'Get Stories Sukses.';
        $result->responseData = $resdata;
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function deleteStories($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('stories_model');
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

        if (!isset($requestData->id)) {
            throw new Exception("Parameter id tidak valid");
        }

        $id = $requestData->id;
        
        $cekID = $CI->stories_model->cekID($id);
        $res = $cekID->result();
        
        if ($cekID->num_rows() == 0) {
            throw new Exception("Stories tidak ada atau sudah dihapus.");
        }
        $image = $res['0']->image;
        $path = 'file/' . $user . '/' .$image;
        
        $resdata = $CI->stories_model->deleteStories($id);
        
        if (!$resdata) {
            throw new Exception("Delete Stories Gagal.");
        }
        unlink($path);
        $result->responseCode = '00';
        $result->responseDesc = 'Delete Berhasil';
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}
