<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->web = $this->db->get('web')->row();
		$this->load->library('Pdf');
		if ($this->session->userdata('level') != 'admin') {
			$this->session->set_flashdata('message', 'swal("Ops!", "Anda harus login sebagai admin", "error");');
			redirect('auth');
		}
	}

	public function index()
	{
		$tahun 			= date('Y');
		$bulan 			= date('m');
		$hari 			= date('d');
		$data['web']	= $this->web;
		$data['pegawai'] = $this->M_data->pegawai()->num_rows();
		$data['hadir']	= $this->M_data->hadirtoday($tahun, $bulan, $hari)->num_rows();
		$data['cuti']	= $this->M_data->cutitoday($tahun, $bulan, $hari)->num_rows();
		$data['izin']	= $this->M_data->izintoday($tahun, $bulan, $hari)->num_rows() + $this->M_data->sakittoday($tahun, $bulan, $hari)->num_rows();
		$data['absensi'] = $this->M_data->absen()->num_rows();
		$data['departemen'] = $this->db->get('departemen')->num_rows();
		$data['title']	= 'Dashboard';
		$data['body']	= 'admin/home';
		$this->load->view('template', $data);
	}
	//CURD Departemen
	public function departemen()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get('departemen')->result();
		$data['title']	= 'Data Departemen';
		$data['body']	= 'admin/departemen';
		$this->load->view('template', $data);
	}
	public function departemen_add()
	{
		$data['web']	= $this->web;
		$data['title']	= 'Tambah Data Departemen';
		$data['body']	= 'admin/departemen_add';
		$this->load->view('template', $data);
	}
	public function departemen_simpan()
	{
		$this->db->insert('departemen', ['departemen' => $this->input->post('departemen')]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah departemen", "success");');
		redirect('admin/departemen');
	}
	public function departemen_edit($id)
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get_where('departemen', ['departemen_id' => $id])->row();
		$data['title']	= 'Update Data Departemen';
		$data['body']	= 'admin/departemen_edit';
		$this->load->view('template', $data);
	}
	public function departemen_update($id)
	{
		$this->db->update('departemen', ['departemen' => $this->input->post('departemen')], ['departemen_id' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update departemen", "success");');
		redirect('admin/departemen');
	}
	public function departemen_delete($id)
	{
		$this->db->delete('departemen', ['departemen_id' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete departemen", "success");');
		redirect('admin/departemen');
	}
	//EDN CURD Departemen
	//CURD Pegawai
	public function pegawai()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->pegawai()->result();
		$data['title']	= 'Data Pegawai';
		$data['body']	= 'admin/pegawai';
		$this->load->view('template', $data);
	}
	public function pegawai_add()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get('departemen')->result();
		$data['title']	= 'Tambah Data Pegawai';
		$data['body']	= 'admin/pegawai_add';
		$this->load->view('template', $data);
	}
	public function pegawai_simpan()
	{
		$p = $this->input->post();
		$user = [
			'nama'		=> $p['nama'],
			'username'		=> $p['username'],
			'password'	=> md5($p['nip']),
			'level'		=> 'pegawai',
			'nip'		=> $p['nip']
		];
		$pgw = [
			'nip'			=> $p['nip'],
			'jenis_kelamin'	=> $p['jenis_kelamin'],
			'id_departemen'	=> $p['departemen'],
			'waktu_masuk'	=> $p['masuk'],
			'gaji'			=> $p['gaji'],
		];
		$this->db->trans_start();
		$this->db->insert('user', $user);
		$this->db->insert('pegawai', $pgw);
		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah Data Pegawai", "success");');
		redirect('admin/pegawai');
	}
	public function pegawai_edit($id)
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get('departemen')->result();
		$data['detail']	= $this->M_data->pegawaiid($id)->row();
		$data['title']	= 'Update Data Pegawai';
		$data['body']	= 'admin/pegawai_edit';
		$this->load->view('template', $data);
	}
	public function pegawai_update($id)
	{
		$p = $this->input->post();
		$user = [
			'nama'		=> $p['nama'],
			'email'		=> $p['email'],
		];
		$pgw = [
			'jenis_kelamin'	=> $p['jenis_kelamin'],
			'id_departemen'	=> $p['departemen'],
			'waktu_masuk'	=> $p['masuk'],
			'gaji'			=> $p['gaji'],
		];
		$this->db->trans_start();
		$this->db->update('user', $user, ['nip' => $id]);
		$this->db->update('pegawai', $pgw, ['nip' => $id]);
		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update Data Pegawai", "success");');
		redirect('admin/pegawai');
	}
	public function pegawai_delete($id)
	{
		$this->db->trans_start();
		$this->db->delete('user', ['nip' => $id]);
		$this->db->delete('pegawai', ['nip' => $id]);
		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete Data Pegawai", "success");');
		redirect('admin/pegawai');
	}
	//end CURD pegawai
	//Data Absensi
	public function absensi()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->absen()->result();
		$data['title']	= 'Data Absen Pegawai';
		$data['body']	= 'admin/absen';
		$this->load->view('template', $data);
	}
	//Data pengajuan cuti
	public function cuti()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->cuti()->result();
		$data['title']	= 'Data Cuti Pegawai';
		$data['body']	= 'admin/cuti';
		$this->load->view('template', $data);
	}
	public function cuti_terima($id)
	{
		$this->db->update('cuti', ['status' => 'diterima'], ['id_cuti' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Menerima pengajuan cuti", "success");');
		redirect('admin/cuti');
	}
	public function cuti_tolak($id)
	{
		$this->db->update('cuti', ['status' => 'ditolak'], ['id_cuti' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Menolak pengajuan cuti", "success");');
		redirect('admin/cuti');
	}
	//laporan bulanan
	function laporan()
	{
		function bulan($bln)
		{
			$bulan = $bln;
			switch ($bulan) {
				case 1:
					$bulan = "Januari";
					break;
				case 2:
					$bulan = "Februari";
					break;
				case 3:
					$bulan = "Maret";
					break;
				case 4:
					$bulan = "April";
					break;
				case 5:
					$bulan = "Mei";
					break;
				case 6:
					$bulan = "Juni";
					break;
				case 7:
					$bulan = "Juli";
					break;
				case 8:
					$bulan = "Agustus";
					break;
				case 9:
					$bulan = "September";
					break;
				case 10:
					$bulan = "Oktober";
					break;
				case 11:
					$bulan = "November";
					break;
				case 12:
					$bulan = "Desember";
					break;
			}
			return $bulan;
		}
		$bulan  = $this->input->post('bulan');
		$web 	= $this->web;
		$data   = $this->M_data->laporan($bulan)->result();

		$pdf = new FPDF('P', 'mm', 'A4');
		// membuat halaman baru
		$pdf->AddPage();
		// setting jenis font yang akan digunakan
		$pdf->SetFont('Arial', 'B', 22);
		// mencetak string 
		$pdf->Image('assets/img/' . $web->logo, 10, 5, 25);
		$pdf->Cell(190, 7, $web->nama, 0, 1, 'C');
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(190, 5, $web->alamat, 0, 1, 'C');
		$pdf->Cell(190, 3, 'Phone : ' . $web->nohp . ' - Email : ' . $web->email, 0, 1, 'C');
		$pdf->Cell(10, 7, '', 0, 1);
		$pdf->Cell(190, 1, '', 'B', 1, 'L');
		$pdf->Cell(190, 1, '', 'B', 0, 'L');
		// Memberikan space kebawah agar tidak terlalu rapat
		$pdf->Cell(10, 5, '', 0, 1);
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(190, 7, 'Laporan Absensi Pegawai', 0, 1, 'C');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(10, 5, 'Bulan : ' . bulan($bulan), 0, 1);
		$pdf->Cell(10, 1, '', 0, 1);
		$pdf->Cell(10, 7, 'No ', 1, 0, 'C');
		$pdf->Cell(80, 7, 'Nama ', 1, 0, 'C');
		$pdf->Cell(50, 7, 'Waktu ', 1, 0, 'C');
		$pdf->Cell(50, 7, 'Keterangan ', 1, 1, 'C');
		$no = 1;
		foreach ($data as $a) {
			$pdf->Cell(10, 7, $no++, 1, 0, 'C');
			$pdf->Cell(80, 7, $a->nama, 1, 0, 'C');
			$pdf->Cell(50, 7, $a->waktu, 1, 0, 'C');
			$pdf->Cell(50, 7, ucfirst($a->ket), 1, 1, 'C');
		}
		$pdf->Cell(10, 5, '', 0, 1, 'C');
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(170, 5, ucfirst($web->kabupaten) . ', ' . date('d-m-Y'), 0, 1, 'R');
		$pdf->Cell(190, 15, '', 0, 1, 'C');
		$pdf->Cell(160, 5, $web->author, 0, 1, 'R');


		$pdf->Output();
	}
	public function profile()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get_where('user', ['user_id' => $this->session->userdata('user_id')])->row();
		$data['title']	= 'Profile Pengguna';
		$data['body']	= 'admin/profile';
		$this->load->view('template', $data);
	}
	public function profile_update($id)
	{
		$usr = [
			'nama'	=> $this->input->post('nama'),
			'email'	=> $this->input->post('email'),
		];
		$this->db->update('user', $usr, ['user_id' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update profile", "success");');
		redirect('admin/profile');
	}
	public function ganti_password()
	{
		$data['web']	= $this->web;
		$data['title']	= 'Ganti Password';
		$data['body']	= 'admin/ganti password';
		$this->load->view('template', $data);
	}
	public function password_update($id)
	{
		$p = $this->input->post();
		$cek = $this->db->get_where('user', ['user_id' => $id]);
		if ($cek->num_rows() > 0) {
			$a = $cek->row();
			if (md5($p['pw_lama']) == $a->password) {
				$this->db->update('user', ['password' => md5($p['pw_baru'])], ['user_id' => $id]);
				$this->session->set_flashdata('message', 'swal("Berhasil!", "Update password", "success");');
				redirect('admin/ganti_password');
			} else {
				$this->session->set_flashdata('message', 'swal("Ops!", "Password lama yang anda masukan salah", "error");');
				redirect('admin/ganti_password');
			}
		} else {
			$this->session->set_flashdata('message', 'swal("Ops!", "Anda harus login", "error");');
			redirect('auth');
		}
	}
	//penggajian
	public function penggajian()
	{
		$data['list']	= $this->M_data->pegawai()->result();
		$data['web']	= $this->web;
		$data['title']	= 'Daftar Hadir';
		$data['body']	= 'admin/penggajian';
		$this->load->view('template', $data);
	}
}
