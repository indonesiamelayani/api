<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of home_model
 *
 * @author jeri
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
    function getTrendingTag() {
        $implus = $this->load->database('implus', TRUE);
//        $implus->query('SELECT `tag`, COUNT(`tag`) FROM `post` ORDER BY `date` DESC limit 10');
        $qryget = $implus->query('SELECT `tag`, COUNT(`tag`) as count FROM `post` WHERE DATE(`date`) > DATE(NOW() - INTERVAL 1 DAY) GROUP BY `tag` ORDER BY count DESC LIMIT 5');
        $implus->close();
        return $qryget;
    }
    
    function getFeature(){
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id, feature');
        $qryget = $implus->get('featured');
        $implus->close();
        return $qryget;
    }
    
}