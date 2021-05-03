<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Post_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function insert_img($judul, $description, $user, $tag, $location, $kategori, $data_insert) {
        $implus = $this->load->database('implus', TRUE);
        $data = array(
            'username' => $user,
            'judul' => $judul,
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
    
    function insertLikeComment($id_comment, $id_post, $user) {
        $implus = $this->load->database('implus', TRUE);
        $data = array(
            'userlike' => $user,
            'id_post' => $id_post,
            'id_comment' => $id_comment
        );
        $implus->insert('comment', $data);
        $implus->close();
        return $implus;
    }
    
    function unLikeComment($id_comment, $id_post, $user){
        $implus = $this->load->database('implus', TRUE);
        $implus->where('id_comment', $$id_comment);
        $implus->where('id_post', $id_post);
        $implus->where('userlike', $user);
        $qryget = $implus->delete('comment');
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
    function countLikeComment($id_comment,$id_post){
        $implus = $this->load->database('implus', TRUE);
        $qryget = $implus->query('SELECT COUNT(*) as count FROM `commentLike` WHERE id_post='.$id_post.'');
        $implus->close();
        return $qryget;
    }
    
    function updateLikeComment($id_post, $count){
        $implus = $this->load->database('implus', TRUE);
        $implus->set('countLikeComment', $count);
        $implus->where('id', $id_post);
        $implus->update('post');
        $implus->close();
        return $implus;
    }
    
    function getPost($username, $offset, $limit, $user) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id, (SELECT foto FROM user WHERE username="'.$user.'") AS profileImg ,username,judul,description,tag,kategori,location,image,date,countLike');
        array_push($username, $user);
        $implus->where_in('username', $username);
        $implus->order_by('date', 'DESC');
        $implus->limit($limit, $offset);
        $qryget = $implus->get('post');
        $implus->close();
        return $qryget;
    }

    function getAllPost($limit, $offset) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id,username,judul,description,tag,kategori,location,image,date,countLike');
        $where = 'DATE(`date`) > DATE(NOW() - INTERVAL 2 DAY)';
        $implus->where($where);
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
    
    function getPostByUser($username, $offset, $limit) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username,judul,description,tag,kategori,location,image,date');
        $implus->where('username', $username);
        $implus->order_by('date', 'DESC');
        $implus->limit($limit, $offset);
        $qryget = $implus->get('post');
        $implus->close();
        return $qryget;
    }
    
    function getPostByID($id) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id,username,judul,description,tag,kategori,location,image,date');
        $implus->where('id', $id);
        $qryget = $implus->get('post');
        $implus->close();
        return $qryget;
    }

    function addComment($user, $id_post, $comment) {
        $implus = $this->load->database('implus', TRUE);
        $data = array(
            'username' => $user,
            'id_post' => $id_post,
            'comment' => $coment
        );
        $implus->set('date', 'NOW()', FALSE);
        $implus->insert('comment', $data);
        $implus->close();
        return $implus;
    }
    
    function getCommentID($username, $id_post){
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id');
        $implus->where('username', $username);
        $implus->where('id_post', $id_post);
        $qryget = $implus->get('comment');
        $implus->close();
        return $qryget;
    }
    
        function cekUsernameComment($id_post) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username');
        $implus->where('id_post', $id_post);
        $qryget = $implus->get('comment');
        $implus->close();
        return $qryget;
    }
    
   function getComment($username, $id_post) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('id, user.name as commentName, user.foto as commentPhoto, comment as commentText, date as CommentTime,countLike as commentLike');
        $implus->where('id_post', $id_post);
        $implus->order_by('date', 'DESC');
        $implus->join('user', 'user.username = comment.username');
        $qryget = $implus->get('comment');
        $implus->close();
        return $qryget;
    }


}
