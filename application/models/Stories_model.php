<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stories_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function insert_img($description, $user, $data_insert) {
        $implus = $this->load->database('implus', TRUE);
        $data = array(
            'username' => $user,
            'description' => $description,
            'image' => $data_insert
        );
        $implus->set('date', 'NOW()', FALSE);
        $implus->insert('stories', $data);
        $implus->close();
        return $implus;
    }
    
    function getIDStories($username, $data_insert){
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id');
        $implus->where('username', $username);
        $implus->where('image', $data_insert);
        $qryget = $implus->get('stories');
        $implus->close();
        return $qryget;
    }

    function getStories($username) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('user.foto as userImage, stories.username, stories.image');
        $implus->where_in('stories.username', $username);
        $where = 'DATE(stories.date) > DATE(NOW() - INTERVAL 1 DAY)';
        $implus->where($where);
        // $implus->from('stories');
        $implus->join('user', 'stories.username = user.username');
        $implus->group_by('stories.username');
        $implus->order_by('stories.date', 'DESC');
        $qryget = $implus->get('stories');
        $implus->close();
        return $qryget;
    }
    
    function cekID($id){
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id, image');
        $implus->where('id', $id);
        $qryget = $implus->get('stories');
        $implus->close();
        return $qryget;
    }
    
    function deleteStories($id){
        $implus = $this->load->database('implus', TRUE);
        $implus->where('id', $id);
        $qryget = $implus->delete('stories');
        $implus->close();
        return $qryget;
    }
    
    function getPostByID($username, $offset, $limit) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username,description,tag,location,image,date');
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
