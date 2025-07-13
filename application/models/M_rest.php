<?php
  
Class M_rest extends CI_Model
{
   public function get_nama_siswa()
   {
      $this->db->select('nama,peserta_didik_id,nama_rombel');
      $this->db->from('data_siswa');
      $query = $this->db->get();
      return $query->result();

   }

   public function cek_api_token($app,$token)
   {
      $this->db->select('*');
      $this->db->from('api_token');
      $this->db->where('app',$app);
      $this->db->where('token',$token);
      $query = $this->db->get();

      if ($query->row()==null){
          return 'not_allowed';
       }else{
         return 'valid'; // if valid password and username and allowed
       }
   }



   public function get_detail_siswa($id)
   {
      $this->db->select('*');
      $this->db->from('data_siswa');
      $this->db->join('data_tambahan_siswa', 'data_siswa.peserta_didik_id = data_tambahan_siswa.peserta_didik_id');
      $this->db->where('data_siswa.peserta_didik_id',$id);
      $query = $this->db->get();
         return $query->row();

   }
}