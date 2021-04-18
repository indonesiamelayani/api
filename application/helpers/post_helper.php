<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function addPost($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
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

        $judul = $requestData->judul;

        if (!isset($requestData->judul)) {
            throw new Exception("Parameter judul tidak valid");
        }
        $description = $requestData->description;

        if (!isset($requestData->description)) {
            throw new Exception("Parameter description tidak valid");
        }

        $tag = $requestData->tag;

        if (!isset($requestData->tag)) {
            throw new Exception("Parameter tag tidak valid");
        }
        
        $kategori = $requestData->kategori;

        if (!isset($requestData->kategori)) {
            throw new Exception("Parameter kategori tidak valid");
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

        $data_insert = $user.'/'.$filename;

        $resdata = $CI->post_model->insert_img($judul, $description, $user, $tag, $location, $kategori, $data_insert);
        
        $resid = $CI->post_model->getIDPost($user, $data_insert);
        if (!$resdata) {
            throw new Exception("Gagal Post.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Add Post Berhasil';
        $result->responseData = $resid->result();
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function addLike($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('post_model');
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

        $id_post = $requestData->id_post;

        if (!isset($requestData->id_post)) {
            throw new Exception("Parameter id_post tidak valid");
        }

        $resdata = $CI->post_model->insertLike($id_post, $user);
        $countLike = $CI->post_model->countLike($id_post)->result();
        if (!$resdata && !$countLike){
            throw new Exception("Gagal Like.");
        }
        $count = $countLike['0']->count;
        $updateCount = $CI->post_model->updateLike($id_post, $count);
        if (!$updateCount){
            throw new Exception("Gagal Like.");
        }
        $result->responseCode = '00';
        $result->responseDesc = 'Add Like Berhasil';
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function unLike($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
    $CI->load->model('activity_model');
    $CI->load->model('post_model');
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

        $id_post = $requestData->id_post;

        if (!isset($requestData->id_post)) {
            throw new Exception("Parameter id_post tidak valid");
        }

        $resdata = $CI->post_model->unLike($id_post, $user);
        $countLike = $CI->post_model->countLike($id_post)->result();
        if (!$resdata && !$countLike){
            throw new Exception("Gagal Like.");
        }
        $count = $countLike['0']->count;
        $updateCount = $CI->post_model->updateLike($id_post, $count);
        $result->responseCode = '00';
        $result->responseDesc = 'Unlike Berhasil';
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}

function deletePost($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
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

        if (!isset($requestData->id)) {
            throw new Exception("Parameter id tidak valid");
        }

        $id = $requestData->id;
        
        $cekID = $CI->post_model->cekID($id);
        $res = $cekID->result();
        
        if ($cekID->num_rows() == 0) {
            throw new Exception("Post tidak ada atau sudah dihapus.");
        }
        $image = $res['0']->image;
        $path = 'file/' . $user . '/' .$image;
        
        $resdata = $CI->post_model->deletePost($id);
        if (!$resdata) {
            throw new Exception("Gagal Delete Post.");
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

function getPost($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
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
        $resdata = $CI->post_model->getPost($data, $pagging, $limit, $user)->result();
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
function getAllPost($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
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
        $limit = 2;
        $pagging = $page * $limit;
        $resdata = $CI->post_model->getAllPost($limit, $pagging)->result();
        if (!$resdata) {
            throw new Exception("Data tidak ditemukan.");
        }

        $result->responseCode = '00';
        $result->responseDesc = 'Get All Post Sukses.';
        $result->responseData = $resdata;
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}
function getPostByID($request) {
    $result = new stdClass;
    $result->responseCode = "";
    $result->responseDesc = "";

    $user = '';
    $CI = & get_instance();
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

        $id = $datapost->id;
        if (!isset($datapost->id)) {
            throw new Exception("Parameter id tidak valid");
        }
        
        $resdata = $CI->post_model->getPostByID($id)->result();
        if (!$resdata) {
            throw new Exception("Data tidak ditemukan.");
        }

        $result->responseCode = '00';
        $result->responseDesc = 'Get Post By ID Sukses.';
        $result->responseData = $resdata;
    } catch (Exception $e) {
        $result->responseCode = '99';
        $result->responseDesc = $e->getMessage() . " Ln." . $e->getLine();
    }

    $CI->activity_model->insert_activity((isset($datapost->requestMethod) ? $CI->security->xss_clean(trim($datapost->requestMethod)) : '') . ' RESPONSE ', json_encode(array("responseCode" => $result->responseCode, "responseDesc" => $result->responseDesc)));
    return $result;
}
