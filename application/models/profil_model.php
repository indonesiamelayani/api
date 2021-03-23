<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profil_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getProfilByID($username) {
        $implus = $this->load->database('implus', TRUE);
        $qryget = $implus->query('SELECT `nama`, `username`, COUNT(`followerUsername`) as jumlahFollower FROM `followers` WHERE `username`= "'.$username.'" GROUP BY `nama`');
        $implus->close();
        return $qryget;
    }

    function getProfilDetail($username) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username,nama,tag,location,image');
        $implus->where_in('username', $username);
        $implus->order_by('date', 'DESC');
        $implus->limit($limit, $offset);
        $qryget = $implus->get('follower');
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
