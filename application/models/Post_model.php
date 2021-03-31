<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Post_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function insert_img($description, $user, $tag, $location, $kategori, $data_insert) {
        $implus = $this->load->database('implus', TRUE);
        $data = array(
            'username' => $user,
            'description' => $description,
            'tag' => $tag,
            'location' => $location,
            'image' => $data_insert,
            'kategori' => $kategori
        );
        $implus->set('date', 'NOW()', FALSE);
        $implus->insert('post', $data);
        $implus->close();
        return $implus;
    }

    function insertLike($id_post, $user) {
        $implus = $this->load->database('implus', TRUE);
        $data = array(
            'userlike' => $user,
            'id_post' => $id_post
        );
        $implus->insert('like', $data);
        $implus->close();
        return $implus;
    }
    
    function unLike($id_post, $user){
        $implus = $this->load->database('implus', TRUE);
        $implus->where('id_post', $id_post);
        $implus->where('userlike', $user);
        $qryget = $implus->delete('like');
        $implus->close();
        return $qryget;
    }
    
    function countLike($id_post){
        $implus = $this->load->database('implus', TRUE);
        $qryget = $implus->query('SELECT COUNT(*) as count FROM `like` WHERE id_post='.$id_post.'');
        $implus->close();
        return $qryget;
    }
    
    function updateLike($id_post, $count){
        $implus = $this->load->database('implus', TRUE);
        $implus->set('countLike', $count);
        $implus->where('id', $id_post);
        $implus->update('post');
        $implus->close();
        return $implus;
    }
    
    function getPost($username, $offset, $limit) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id,username,description,tag,kategori,location,image,date,countLike');
        $implus->where_in('username', $username);
        $implus->order_by('date', 'DESC');
        $implus->limit($limit, $offset);
        $qryget = $implus->get('post');
        $implus->close();
        return $qryget;
    }
    
    function getIDPost($username, $data_insert){
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id');
        $implus->where('username', $username);
        $implus->where('image', $data_insert);
        $qryget = $implus->get('post');
        $implus->close();
        return $qryget;
    }
    function cekID($id){
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id, image');
        $implus->where('id', $id);
        $qryget = $implus->get('post');
        $implus->close();
        return $qryget;
    }
    
    function deletePost($id){
        $implus = $this->load->database('implus', TRUE);
        $implus->where('id', $id);
        $qryget = $implus->delete('post');
        $implus->close();
        return $qryget;
    }
    
    function getPostByID($username, $offset, $limit) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username,description,tag,kategori,location,image,date');
        $implus->where('username', $username);
        $implus->order_by('date', 'DESC');
        $implus->limit($limit, $offset);
        $qryget = $implus->get('post');
        $implus->close();
        return $qryget;
    }

    function get_datapekerjaan() {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('ID_PEKERJAAN,DESC1,DESC2');
        $qryget = $implus->get('data_pekerjaan');
        $implus->close();
        return $qryget;
    }

    function get_user($username, $password) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username,nama');
        $pass = md5($password);
        $implus->where('username', $username);
        $implus->where('password', $pass);
        $qryget = $implus->get('user');
        $implus->close();
        return $qryget;
    }

    function getAllUser() {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('*');
        $qryget = $implus->get('user');
        $implus->close();
        return $qryget;
    }

    function getUser($username) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('*');
        $implus->where('username', $username);
        $qryget = $implus->get('user');
        $implus->close();
        return $qryget;
    }

    function postUser($id_user) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('*');
        $implus->where('id_user', $id_user);
        $qryget = $implus->get('user');
        $implus->close();
        return $qryget;
    }

    function cek_user($username) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username');
        $implus->where('username', $username);
        $qryget = $implus->get('user');
        $implus->close();
        return $qryget;
    }

    function cek_nohp($nohp) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('phone');
        $implus->where('phone', $nohp);
        $qryget = $implus->get('user');
        $implus->close();
        return $qryget;
    }

    function create_user($username, $nama, $password, $email, $phone) {
        $implus = $this->load->database('implus', TRUE);
        $pass = md5($password);
        $data = array(
            'username' => $username,
            'nama' => $nama,
            'password' => $pass,
            'email' => $email,
            'phone' => $phone
        );
        $implus->insert('user', $data);
        $implus->close();
        return $implus;
    }

}
