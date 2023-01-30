<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->web = $this->db->get('web')->row();
		if ($this->session->userdata('level') != 'pegawai') {
			$this->session->set_flashdata('message', 'swal("Ops!", "Anda haru login sebagai pegawai", "error");');
			redirect('auth');
		}
		date_default_timezone_set('asia/jakarta');
	}

	public function index()
	{
		$tahun 			= date('Y');
		$bulan 			= date('m');
		$hari 			= date('d');
		$absen			= $this->M_data->absendaily($this->session->userdata('nip'), $tahun, $bulan, $hari);
		if ($absen->num_rows() == 0) {
			$data['waktu'] = 'masuk';
		} elseif ($absen->num_rows() == 1) {
			$data['waktu'] = 'pulang';
		} else {
			$data['waktu'] = 'dilarang';
		}
		$data['web']	= $this->web;
		$data['title']	= 'Dashboard';
		$data['body']	= 'pegawai/home';
		$this->load->view('template', $data);
	}
	//proses absen
	public function proses_absen()
	{
		$id = $this->session->userdata('nip');
		$p = $this->input->post();
		$data = [
			'nip'	=> $id,
			'keterangan' => $p['ket']
		];
		$this->db->insert('absen', $data);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Melakukan absen", "success");');
		redirect('pegawai');
	}
	//data absen
	public function absensi()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->absensi_pegawai($this->session->userdata('nip'))->result();
		$data['title']	= 'Data Absen';
		$data['body']	= 'pegawai/absen';
		$this->load->view('template', $data);
	}
	//CURD data cuti
	public function cuti()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->cuti_pegawai($this->session->userdata('nip'))->result();
		$pegawai = $this->M_data->pegawaiid($this->session->userdata('nip'))->row();
		$dt1 = new DateTime($pegawai->waktu_masuk);
		$dt2 = new DateTime(date('Y-m-d'));
		$d = $dt2->diff($dt1)->days + 1;
		$data['bakti']	= $d;
		$data['title']	= 'Data Permohonan Ketidakhadiran';
		$data['body']	= 'pegawai/cuti';
		$this->load->view('template', $data);
	}
	public function cuti_add()
	{
		$data['web']	= $this->web;
		$pegawai = $this->M_data->pegawaiid($this->session->userdata('nip'))->row();
		$dt1 = new DateTime($pegawai->waktu_masuk);
		$dt2 = new DateTime(date('Y-m-d'));
		$d = $dt2->diff($dt1)->days + 1;
		$data['bakti']	= $d;
		$data['title']	= 'Tambah Data Ketidakhadiran';
		$data['body']	= 'pegawai/cuti_add';
		$this->load->view('template', $data);
	}
	public function cuti_simpan()
	{
		$this->db->trans_start();
		$data = array(
			'nip'			=> $this->session->userdata('nip'),
			'jenis_cuti'	=> $this->input->post('jenis'),
			'alasan'		=> $this->input->post('alasan'),
			'status'		=> 'diajukan'
		);

		if (isset($_FILES['bukti']['name'])) {
			$config['upload_path'] 		= './bukti/';
			$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
			$config['overwrite']  		= true;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('bukti')) {
				$this->session->set_flashdata('message', 'swal("Ops!", "Bukti gagal diupload", "erro");');
				redirect('pegawai/cuti_add');
			} else {
				$img = $this->upload->data();
				$data['bukti'] = $img['file_name'];
			}
		}

		$this->db->insert('cuti', $data);
		$cek = $this->db->query(" select * from cuti order by id_cuti desc limit 1 ")->row();
		$dt1 = new DateTime($this->input->post('mulai'));
		$dt2 = new DateTime($this->input->post('akhir'));
		$jml = $dt2->diff($dt1)->days + 1;
		$tgl1 = $this->input->post('mulai');
		$no  = 1;
		for ($i = 0; $i < $jml; $i++) {
			$insert = array(
				'id_cuti' => $cek->id_cuti,
				'tanggal' => date('Y-m-d', strtotime('+' . $i . ' days', strtotime($tgl1))),
			);
			$this->db->insert('detailcuti', $insert);
		}

		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Pengajuan cuti", "success");');
		redirect('pegawai/cuti');
	}
	public function cuti_update($id)
	{
		$data = array(
			'nip'	=> $this->session->userdata('nip'),
			'mulai'	=> $this->input->post('mulai'),
			'akhir'	=> $this->input->post('akhir'),
			'alasan' => $this->input->post('alasan')
		);
		$this->db->update('cuti', $data, ['id_cuti' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update pengajuan cuti", "success");');
		redirect('pegawai/cuti');
	}
	public function cuti_edit($id)
	{
		$data['web']	= $this->web;
		$data['title']	= 'Update Data Cuti';
		$data['data']	= $this->db->get_where('cuti', ['id_cuti' => $id])->row();
		$data['body']	= 'pegawai/cuti_edit';
		$this->load->view('template', $data);
	}
	public function cuti_delete($id)
	{
		$this->db->delete('cuti', ['id_cuti' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete pengajuan cuti", "success");');
		redirect('pegawai/cuti');
	}
	//update profile
	public function profile()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->pegawaiid($this->session->userdata('nip'))->row();
		$data['title']	= 'Profile Pengguna';
		$data['body']	= 'pegawai/profile';
		$this->load->view('template', $data);
	}
	public function profile_update($id)
	{
		$usr = [
			'nama'	=> $this->input->post('nama'),
			'email'	=> $this->input->post('email'),
		];
		$this->db->trans_start();
		$this->db->update('user', $usr, ['nip' => $id]);
		$this->db->update('pegawai', ['jenis_kelamin' => $this->input->post('jenis_kelamin')], ['nip' => $id]);
		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update profile", "success");');
		redirect('pegawai/profile');
	}
	public function ganti_password()
	{
		$data['web']	= $this->web;
		$data['title']	= 'Ganti Password';
		$data['body']	= 'pegawai/ganti password';
		$this->load->view('template', $data);
	}
	public function password_update($id)
	{
		$p = $this->input->post();
		$cek = $this->db->get_where('user', ['nip' => $id]);
		if ($cek->num_rows() > 0) {
			$a = $cek->row();
			if (md5($p['pw_lama']) == $a->password) {
				$this->db->update('user', ['password' => md5($p['pw_baru'])], ['nip' => $id]);
				$this->session->set_flashdata('message', 'swal("Berhasil!", "Update password", "success");');
				redirect('pegawai/ganti_password');
			} else {
				$this->session->set_flashdata('message', 'swal("Ops!", "Password lama yang anda masukan salah", "error");');
				redirect('pegawai/ganti_password');
			}
		} else {
			$this->session->set_flashdata('message', 'swal("Ops!", "Anda harus login", "error");');
			redirect('auth');
		}
	}
	public function slip()
	{
		$tahun 			= date('Y');
		$bulan 			= date('m');
		$data['data']	= $this->M_data->pegawaiid($this->session->userdata('nip'))->row();
		$data['absen']  = $this->M_data->absenbulan($this->session->userdata('nip'), $tahun, $bulan)->num_rows();
		$data['cuti']  	= $this->M_data->cutibulan($this->session->userdata('nip'), $tahun, $bulan)->num_rows();
		$data['sakit']  = $this->M_data->sakitbulan($this->session->userdata('nip'), $tahun, $bulan)->num_rows();
		$data['izin']  	= $this->M_data->izinbulan($this->session->userdata('nip'), $tahun, $bulan)->num_rows();
		$data['web']	= $this->web;
		$data['title']	= 'Slip Gaji';
		$data['body']	= 'pegawai/slip';
		$this->load->view('template', $data);
	}
	public function print_slip()
	{
		$tahun 			= date('Y');
		$bulan 			= date('m');
		$data['data']	= $this->M_data->pegawaiid($this->session->userdata('nip'))->row();
		$data['absen']  = $this->M_data->absenbulan($this->session->userdata('nip'), $tahun, $bulan)->num_rows();
		$data['cuti']  	= $this->M_data->cutibulan($this->session->userdata('nip'), $tahun, $bulan)->num_rows();
		$data['sakit']  = $this->M_data->sakitbulan($this->session->userdata('nip'), $tahun, $bulan)->num_rows();
		$data['izin']  	= $this->M_data->izinbulan($this->session->userdata('nip'), $tahun, $bulan)->num_rows();
		$data['web']	= $this->web;
		$data['title']	= 'Slip Gaji ' . $this->session->userdata('nama');
		$this->load->view('pegawai/slip_print', $data);
	}

	public function messageAlert($type, $title)
	{
		$messageAlert = "const Toast = Swal.mixin({
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 3000
		});
		Toast.fire({
			type: '" . $type . "',
			title: '" . $title . "'
		});";
		return $messageAlert;
	}

	function scan()
	{
		$user = $this->user;
		$data = array('user' => $user, 'users' => $this->ion_auth->user()->row());
		if ($this->agent->is_mobile('iphone')) {
			$this->template->load('template/template', 'scan/scan_mobile', $data);
		} elseif ($this->agent->is_mobile()) {
			$this->template->load('template/template', 'scan/scan_mobile', $data);
		} else {
			$this->template->load('template/template', 'scan/scan_desktop', $data);
		}
	}

	function cek_id()
	{
		$user = $this->user;
		$result_code = $this->input->post('id_karyawan');
		$tgl = date('Y-m-d');
		$jam_msk = date('h:i:s');
		$jam_klr = date('h:i:s');
		$cek_id = $this->Scan->cek_id($result_code);
		$cek_kehadiran = $this->Scan->cek_kehadiran($result_code, $tgl);
		if (!$cek_id) {
			$this->session->set_flashdata('messageAlert', $this->messageAlert('error', 'absen gagal data QR tidak ditemukan'));
			redirect($_SERVER['HTTP_REFERER']);
		} elseif ($cek_kehadiran && $cek_kehadiran->jam_msk != '00:00:00' && $cek_kehadiran->jam_klr == '00:00:00' && $cek_kehadiran->id_status == 1) {
			$data = array(
				'jam_klr' => $jam_klr,
				'id_status' => 2,
			);
			$this->Scan->absen_pulang($result_code, $data);
			$this->session->set_flashdata('messageAlert', $this->messageAlert('success', 'absen pulang'));
			redirect($_SERVER['HTTP_REFERER']);
		} elseif ($cek_kehadiran && $cek_kehadiran->jam_msk != '00:00:00' && $cek_kehadiran->jam_klr != '00:00:00' && $cek_kehadiran->id_status == 2) {
			$this->session->set_flashdata('messageAlert', $this->messageAlert('warning', 'sudah absen'));
			redirect($_SERVER['HTTP_REFERER']);
			return false;
		} else {
			$data = array(
				'id_karyawan' => $result_code,
				'tgl' => $tgl,
				'jam_msk' => $jam_msk,
				'id_khd' => 1,
				'id_status' => 1,
			);
			$this->Scan->absen_masuk($data);
			$this->session->set_flashdata('messageAlert', $this->messageAlert('success', 'absen masuk'));
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
}
