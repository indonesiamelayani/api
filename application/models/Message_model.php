<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Message_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function insert_img($description, $user, $tag, $location, $data_insert) {
        $implus = $this->load->database('implus', TRUE);
        $data = array(
            'username' => $user,
            'description' => $description,
            'tag' => $tag,
            'location' => $location,
            'image' => $data_insert
        );
        $implus->set('date', 'NOW()', FALSE);
        $implus->insert('post', $data);
        $implus->close();
        return $implus;
    }

    function getPost($username, $offset, $limit) {
        $implus = $this->load->database('implus', TRUE);
        $implus->select('username,description,tag,location,image,date');
        $implus->where_in('username', $username);
        $implus->order_by('date', 'DESC');
        $implus->limit($limit, $offset);
        $qryget = $implus->get('post');
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
    
        public function job_generated_referal() {
        $result = $this->db->query("SELECT * FROM mst_prakarsa WHERE tipe_referal IS NOT NULL AND tipe_referal<>'' AND referal IS NOT NULL AND referal<>'' AND tp_produk IN (1,2,28,10,23,30,69,36) AND status=200 AND status_referal=0 LIMIT 10");
        if ($result->num_rows() > 0) {
            foreach ($result->result() as $row) {
                if (in_array($row->tipe_referal, array('employee', 'brilink', 'cust_exist'))) {
                    $datarekomendasi = json_decode($row->content_datarekomendasi);
                    $datafinansial = json_decode($row->content_datafinansial);
                    $referal = array();
                    if ($row->tipe_referal == 'employee') {
                        $this->db->select('SNAME as nama_referal, REKENING as norek_referal');
                        $this->db->where('PERNR', $row->referal);
                        $res = $this->db->get('man_pa0001_eof');
                        $referal = $res->num_rows() > 0 ? $res->row() : array();
                        $res->free_result();
                    } else if ($row->tipe_referal == 'brilink') {
                        $this->db->select('mid_name as nama_referal, account as norek_referal');
                        $this->db->where('mid_code', $row->referal);
                        $res = $this->db->get('mst_mid');
                        $referal = $res->num_rows() > 0 ? $res->row() : array();
                        $res->free_result();
                    } else if ($row->tipe_referal == 'cust_exist') {
                        // masih kosong
                    }

                    $result = $this->db->query("select count(*) as total_referal from mst_referal where no_referal=" . $this->db->escape($row->referal) . " and concat(year(insdate), lpad(month(insdate), 2, '0'))='" . date('Ym') . "' ");
                    if ($referal && $row->tipe_referal == 'brilink' && in_array($row->tp_produk, array('30', '69', '36')) && floatval($datarekomendasi->plafond_tambahan) >= floatval(1000000) && floatval($datarekomendasi->plafond_tambahan) <= floatval(10000000) && $result->row()->total_referal < 4) {
                        // rule 1 KUR, KUPRA range 1 juta - 10 juta fee : Rp 25.000
                        $data['refno'] = $row->refno;
                        $data['branch'] = $row->branch;
                        $data['no_referal'] = $row->referal;
                        $data['tipe_referal'] = $row->tipe_referal;
                        $data['nama_referal'] = $referal->nama_referal;
                        $data['norek_referal'] = $referal->norek_referal;
                        $data['norek_gl'] = $this->config->item('norek_gl_referal');
                        $data['norek_pinjaman'] = $row->norek_pinjaman;
                        $data['plafond_pinjaman'] = $datarekomendasi->plafond_tambahan;
                        $data['komisi'] = 25000;
                        $data['insdate'] = date('Y-m-d H:i:s');
                        $data['counter'] = 0;
                        $data['status'] = 1;
                        $data['tp_produk'] = $row->tp_produk;

                        $this->db->insert('mst_referal', $data);
                        echo "Berhasil membentuk referal fee untuk refno " . $row->refno . "\r\n";
                        $this->db->query("UPDATE mst_prakarsa SET status_referal=1 WHERE id=" . $row->id . " and status_referal=0");
                    } else if ($referal && $row->tipe_referal == 'brilink' && in_array($row->tp_produk, array('30', '69', '36')) && floatval($datarekomendasi->plafond_tambahan) > floatval(10000000) && floatval($datarekomendasi->plafond_tambahan) <= floatval(20000000) && $result->row()->total_referal < 4) {
                        // rule 2 KUR, KUPRA range 10 juta - 20 juta fee : Rp 50.000
                        $data['refno'] = $row->refno;
                        $data['branch'] = $row->branch;
                        $data['no_referal'] = $row->referal;
                        $data['tipe_referal'] = $row->tipe_referal;
                        $data['nama_referal'] = $referal->nama_referal;
                        $data['norek_referal'] = $referal->norek_referal;
                        $data['norek_gl'] = $this->config->item('norek_gl_referal');
                        $data['norek_pinjaman'] = $row->norek_pinjaman;
                        $data['plafond_pinjaman'] = $datarekomendasi->plafond_tambahan;
                        $data['komisi'] = 50000;
                        $data['insdate'] = date('Y-m-d H:i:s');
                        $data['counter'] = 0;
                        $data['status'] = 1;
                        $data['tp_produk'] = $row->tp_produk;
                        $this->db->insert('mst_referal', $data);
                        echo "Berhasil membentuk referal fee untuk refno " . $row->refno . "\r\n";
                        $this->db->query("UPDATE mst_prakarsa SET status_referal=1 WHERE id=" . $row->id . " and status_referal=0");
                    } else if ($referal && $row->tipe_referal == 'brilink' && in_array($row->tp_produk, array('10', '30', '69', '36')) && floatval($datarekomendasi->plafond_tambahan) >= floatval(20000000) && floatval($datarekomendasi->plafond_tambahan) <= floatval(200000000) && $result->row()->total_referal < 4) {
                        // rule 3 KUR, KUPRA range 20 juta - 200 juta fee : Rp 50.000
                        $data['refno'] = $row->refno;
                        $data['branch'] = $row->branch;
                        $data['no_referal'] = $row->referal;
                        $data['tipe_referal'] = $row->tipe_referal;
                        $data['nama_referal'] = $referal->nama_referal;
                        $data['norek_referal'] = $referal->norek_referal;
                        $data['norek_gl'] = $this->config->item('norek_gl_referal');
                        $data['norek_pinjaman'] = $row->norek_pinjaman;
                        $data['plafond_pinjaman'] = $datarekomendasi->plafond_tambahan;
                        $data['komisi'] = 50000;
                        $data['insdate'] = date('Y-m-d H:i:s');
                        $data['counter'] = 0;
                        $data['status'] = 1;
                        $data['tp_produk'] = $row->tp_produk;
                        $this->db->insert('mst_referal', $data);
                        echo "Berhasil membentuk referal fee untuk refno " . $row->refno . "\r\n";
                        $this->db->query("UPDATE mst_prakarsa SET status_referal=1 WHERE id=" . $row->id . " and status_referal=0");
                    } else if ($referal && $row->tipe_referal == 'brilink' && in_array($row->tp_produk, array('1', '2', '28')) && floatval($datafinansial->Plafond_usulan) >= floatval(20000000) && floatval($datafinansial->Plafond_usulan) <= floatval(200000000) && $result->row()->total_referal < 4) {
                        // rule 4 Briguna Mikro range 20 juta - 200 juta fee : Rp 50.000
                        $data['refno'] = $row->refno;
                        $data['branch'] = $row->branch;
                        $data['no_referal'] = $row->referal;
                        $data['tipe_referal'] = $row->tipe_referal;
                        $data['nama_referal'] = $referal->nama_referal;
                        $data['norek_referal'] = $referal->norek_referal;
                        $data['norek_gl'] = $this->config->item('norek_gl_referal');
                        $data['norek_pinjaman'] = $row->norek_pinjaman;
                        $data['plafond_pinjaman'] = $datafinansial->Plafond_usulan;
                        $data['komisi'] = 50000;
                        $data['insdate'] = date('Y-m-d H:i:s');
                        $data['counter'] = 0;
                        $data['status'] = 1;
                        $data['tp_produk'] = $row->tp_produk;
                        $this->db->insert('mst_referal', $data);
                        echo "Berhasil membentuk referal fee untuk refno " . $row->refno . "\r\n";
                        $this->db->query("UPDATE mst_prakarsa SET status_referal=1 WHERE id=" . $row->id . " and status_referal=0");
                    } else if ($referal && $row->tipe_referal == 'brilink' && in_array($row->tp_produk, array('23'))) {
                        // rule 4 Kupedes Extra Cepat
                        if ($datarekomendasi->plafond_tambahan > 0 && $datarekomendasi->plafond_tambahan <= 2000000) {
                            $komisi = 25000;
                        } else if ($datarekomendasi->plafond_tambahan > 2000000 && $datarekomendasi->plafond_tambahan <= 4000000) {
                            $komisi = 50000;
                        } else if ($datarekomendasi->plafond_tambahan > 4000000) {
                            $komisi = 75000;
                        } else {
                            $komisi = 0;
                        }
                        $data['refno'] = $row->refno;
                        $data['branch'] = $row->branch;
                        $data['no_referal'] = $row->referal;
                        $data['tipe_referal'] = $row->tipe_referal;
                        $data['nama_referal'] = $referal->nama_referal;
                        $data['norek_referal'] = $referal->norek_referal;
                        $data['norek_gl'] = $this->config->item('norek_gl_referal');
                        $data['norek_pinjaman'] = $row->norek_pinjaman;
                        $data['plafond_pinjaman'] = $datarekomendasi->plafond_tambahan;
                        $data['komisi'] = $komisi;
                        $data['insdate'] = date('Y-m-d H:i:s');
                        $data['counter'] = 0;
                        $data['status'] = 1;
                        $data['tp_produk'] = $row->tp_produk;
                        $this->db->insert('mst_referal', $data);
                        echo "Berhasil membentuk referal fee untuk refno " . $row->refno . "\r\n";
                        $this->db->query("UPDATE mst_prakarsa SET status_referal=1 WHERE id=" . $row->id . " and status_referal=0");
                    } else {
                        echo "Tidak memenuhi persyaratan referal untuk refno " . $row->refno . "\r\n";
                        $this->db->query("UPDATE mst_prakarsa SET status_referal=2 WHERE id=" . $row->id . " and status_referal=0");
                    }
                }
                sleep(2);
            }
        }
    }

}
