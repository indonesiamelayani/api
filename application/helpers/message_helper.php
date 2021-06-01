<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function addMessage($request)
{
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = &get_instance();
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
        if (!file_exists('file/' . $user)) {
            mkdir('file/' . $user, 0777, true);
        }
        $path = 'file/' . $user . '/';
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
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function getPost($request)
{
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = &get_instance();
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
        $resdata = $CI->post_model->getPost($data, $pagging, $limit)->result();
        if (!$resdata) {
            throw new Exception("Data tidak ditemukan.");
        }

        $result->responseCode = '00';
        $result->responseDesc = 'Get Post Sukses.';
        $result->responseData = $resdata;
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}
