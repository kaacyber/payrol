<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_data extends CI_Model
{

	function pegawai()
	{
		$this->db->select('*');
		$this->db->from('user');
		$this->db->join('pegawai', 'user.nip = pegawai.nip');
		$this->db->join('departemen', 'pegawai.id_departemen = departemen.departemen_id');
		return $this->db->get();
	}
	function pegawaiid($id)
	{
		$this->db->select('*');
		$this->db->from('user');
		$this->db->join('pegawai', 'user.nip = pegawai.nip');
		$this->db->join('departemen', 'pegawai.id_departemen = departemen.departemen_id');
		$this->db->where('user.nip', $id);
		return $this->db->get();
	}
	function absendaily($id, $tahun, $bulan, $hari)
	{
		$this->db->select('*');
		$this->db->from('absen');
		$this->db->where('nip', $id);
		$this->db->where('year(waktu)', $tahun);
		$this->db->where('month(waktu)', $bulan);
		$this->db->where('day(waktu)', $hari);
		return $this->db->get();
	}
	public function absen()
	{
		$this->db->select('*');
		$this->db->from('absen');
		$this->db->join('pegawai', 'absen.nip = pegawai.nip');
		$this->db->join('user', 'pegawai.nip = user.nip');
		$this->db->order_by('absen.waktu', 'desc');
		return $this->db->get();
	}
	public function absensi_pegawai($id)
	{
		$this->db->select('*');
		$this->db->from('absen');
		$this->db->join('pegawai', 'absen.nip = pegawai.nip');
		$this->db->join('user', 'pegawai.nip = user.nip');
		$this->db->where('pegawai.nip', $id);
		$this->db->order_by('absen.waktu', 'desc');
		return $this->db->get();
	}
	public function cuti()
	{
		$this->db->select('*');
		$this->db->from('cuti');
		$this->db->join('pegawai', 'cuti.nip = pegawai.nip');
		$this->db->join('user', 'pegawai.nip = user.nip');
		$this->db->order_by('cuti.id_cuti', 'desc');
		return $this->db->get();
	}
	public function cuti_pegawai($id)
	{
		$this->db->select('*');
		$this->db->from('cuti');
		$this->db->join('pegawai', 'cuti.nip = pegawai.nip');
		$this->db->join('user', 'pegawai.nip = user.nip');
		$this->db->where('pegawai.nip', $id);
		$this->db->order_by('cuti.id_cuti', 'desc');
		return $this->db->get();
	}
	public function laporan($bulan)
	{
		$this->db->select('*');
		$this->db->from('absen');
		$this->db->join('pegawai', 'absen.nip = pegawai.nip');
		$this->db->join('user', 'pegawai.nip = user.nip');
		$this->db->where('month(waktu)', $bulan);
		$this->db->order_by('absen.waktu', 'desc');
		return $this->db->get();
	}
	function absenbulan($id, $tahun, $bulan)
	{
		$this->db->select('*');
		$this->db->from('absen');
		$this->db->where('nip', $id);
		$this->db->where('keterangan', 'masuk');
		$this->db->where('year(waktu)', $tahun);
		$this->db->where('month(waktu)', $bulan);
		return $this->db->get();
	}
	function cutibulan($id, $tahun, $bulan)
	{

		$this->db->select('* ');
		$this->db->from('cuti');
		$this->db->join('detailcuti', 'cuti.id_cuti = detailcuti.id_cuti');
		$this->db->where('nip', $id);
		$this->db->where('jenis_cuti', 'cuti');
		$this->db->where('status', 'diterima');
		$this->db->where('year(tanggal)', $tahun);
		$this->db->where('month(tanggal)', $bulan);
		return $this->db->get();
	}
	function sakitbulan($id, $tahun, $bulan)
	{
		$this->db->select('*');
		$this->db->from('cuti');
		$this->db->join('detailcuti', 'cuti.id_cuti = detailcuti.id_cuti');
		$this->db->where('nip', $id);
		$this->db->where('jenis_cuti', 'sakit');
		$this->db->where('status', 'diterima');
		$this->db->where('year(tanggal)', $tahun);
		$this->db->where('month(tanggal)', $bulan);
		return $this->db->get();
	}
	function izinbulan($id, $tahun, $bulan)
	{
		$this->db->select('*');
		$this->db->from('cuti');
		$this->db->join('detailcuti', 'cuti.id_cuti = detailcuti.id_cuti');
		$this->db->where('nip', $id);
		$this->db->where('jenis_cuti', 'izin');
		$this->db->where('status', 'diterima');
		$this->db->where('year(tanggal)', $tahun);
		$this->db->where('month(tanggal)', $bulan);
		return $this->db->get();
	}
	function cutitoday($tahun, $bulan, $hari)
	{
		$this->db->select('*');
		$this->db->from('cuti');
		$this->db->join('detailcuti', 'cuti.id_cuti = detailcuti.id_cuti');
		$this->db->where('jenis_cuti', 'cuti');
		$this->db->where('status', 'diterima');
		$this->db->where('year(tanggal)', $tahun);
		$this->db->where('month(tanggal)', $bulan);
		$this->db->where('day(tanggal)', $hari);
		return $this->db->get();
	}
	function izintoday($tahun, $bulan, $hari)
	{
		$this->db->select('*');
		$this->db->from('cuti');
		$this->db->join('detailcuti', 'cuti.id_cuti = detailcuti.id_cuti');
		$this->db->where('jenis_cuti', 'izin');
		$this->db->where('status', 'diterima');
		$this->db->where('year(tanggal)', $tahun);
		$this->db->where('month(tanggal)', $bulan);
		$this->db->where('day(tanggal)', $hari);
		return $this->db->get();
	}
	function sakittoday($tahun, $bulan, $hari)
	{
		$this->db->select('*');
		$this->db->from('cuti');
		$this->db->join('detailcuti', 'cuti.id_cuti = detailcuti.id_cuti');
		$this->db->where('jenis_cuti', 'sakit');
		$this->db->where('status', 'diterima');
		$this->db->where('year(tanggal)', $tahun);
		$this->db->where('month(tanggal)', $bulan);
		$this->db->where('day(tanggal)', $hari);
		return $this->db->get();
	}

	function hari($hari)
	{

		switch ($hari) {
			case 'Sun':
				$hari_ini = "Minggu";
				break;

			case 'Mon':
				$hari_ini = "Senin";
				break;

			case 'Tue':
				$hari_ini = "Selasa";
				break;

			case 'Wed':
				$hari_ini = "Rabu";
				break;

			case 'Thu':
				$hari_ini = "Kamis";
				break;

			case 'Fri':
				$hari_ini = "Jumat";
				break;

			case 'Sat':
				$hari_ini = "Sabtu";
				break;

			default:
				$hari_ini = "Tidak di ketahui";
				break;
		}

		return $hari_ini;
	}
	function tgl_indo($tanggal)
	{
		$bulan = array(
			1 =>   'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$pecahkan = explode('-', $tanggal);

		// variabel pecahkan 0 = tanggal
		// variabel pecahkan 1 = bulan
		// variabel pecahkan 2 = tahun

		return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
	}
	function hadirtoday($tahun, $bulan, $hari)
	{
		$this->db->select('*');
		$this->db->from('absen');
		$this->db->where('keterangan', 'masuk');
		$this->db->where('year(waktu)', $tahun);
		$this->db->where('month(waktu)', $bulan);
		$this->db->where('day(waktu)', $hari);
		return $this->db->get();
	}
}

/* End of file M_data.php */
/* Location: ./application/models/M_data.php */