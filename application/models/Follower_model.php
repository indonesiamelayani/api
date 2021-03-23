<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Follower_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function goFollow($nama, $username, $followerUsername, $followerNama) {
        $implus = $this->load->database('implus', TRUE);
        $data = array(
            'nama' => $nama,
            'username' => $username,
            'followerUsername' => $followerUsername,
            'followerNama' => $followerNama
        );
        $implus->insert('followers', $data);
        $implus->close();
        return $implus;
    }

    function validasiFollow($user) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('followerUsername');
        $implus->where('followerUsername', $user);
        $qryget = $implus->get('followers');
        $implus->close();
        return $qryget;
    }

    function cekFollowers($username) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('followerUsername', 'followerNama');
        $implus->where('username', $username);
        $qryget = $implus->get('followers');
        $implus->close();
        return $qryget;
    }

    function cekFollowed($followerUsername) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username', 'nama');
        $implus->where('followerUsername', $followerUsername);
        $qryget = $implus->get('followers');
        $implus->close();
        return $qryget;
    }

    function getNama($username) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('nama');
        $implus->where('username', $username);
        $qryget = $implus->get('user');
        $implus->close();
        return $qryget;
    }

}
