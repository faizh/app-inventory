<?php

use Dompdf\Dompdf;

class Checking extends CI_Controller{
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		$this->data['aktif'] = 'checking';
		$this->load->model('M_checking', 'm_checking');
		$this->load->model('M_petugas', 'm_petugas');
		$this->load->model('M_barang', 'm_barang');
		$this->load->model('M_supplier', 'm_supplier');
		$this->load->model('M_penerimaan', 'm_penerimaan');
		$this->load->model('M_detail_terima', 'm_detail_terima');

		if (empty($this->session->login)) {
			redirect('login');
		}
	}

	public function index(){
		$user_id = $this->session->login['id'];

		$this->data['title'] 			= 'Transaksi Checking';
		$this->data['all_checking'] 	= $this->m_checking->getAll($user_id);
		$this->data['no'] 				= 1;

		$this->load->view('checking/lihat', $this->data);
	}

	public function report(){
		$this->data['title'] 			= 'Transaksi Checking';
		$this->data['all_barang'] 		= $this->m_barang->lihat_stok();
		$this->data['all_supplier'] 	= $this->m_supplier->lihat_spl();

		$this->load->view('checking/tambah', $this->data);
	}

	public function process_report(){
		$hasil 			= $this->input->post('hasil');
		$kinerja 		= $this->input->post('kinerja');
		$checking_id 	= $this->input->post('checking_id');

		$data_update 	= array(
			'hasil'		=> $hasil,
			'kinerja'	=> $kinerja
		);

		if ($this->m_checking->update_kinerja($checking_id, $data_update)) {
			$this->session->set_flashdata('success', 'Data Target Kinerja Berhasil Di Update!');
			redirect('checking');
		}
	}

	public function target()
	{
		$this->data['title'] 			= 'Target Checking';
		$this->data['all_target'] 		= $this->m_checking->getAll();
		$this->data['no'] 				= 1;

		$this->load->view('checking/master/list_target_checking', $this->data);
	
	}

	public function add_target()
	{
		$this->data['title'] 			= 'Transaksi Checking';
		$this->data['all_petugas']		= $this->m_petugas->lihat();
		$this->data['all_barang'] 		= $this->m_barang->lihat_stok();
		$this->data['all_supplier'] 	= $this->m_supplier->lihat_spl();

		$this->load->view('checking/master/add_target', $this->data);
	}

	public function add_target_monthly()
	{
		$this->data['title'] 			= 'Transaksi Checking Bulanan';
		$this->data['all_petugas']		= $this->m_petugas->lihat();
		$this->data['all_barang'] 		= $this->m_barang->lihat_stok();
		$this->data['all_supplier'] 	= $this->m_supplier->lihat_spl();

		$this->load->view('checking/master/add_target_monthly', $this->data);
	}

	public function view_detail($checking_id)
	{
		$this->data['title'] 			= 'View Transaksi Checking';
		$this->data['checking']			= $this->m_checking->getCompleteDataChecking($checking_id);

		$this->load->view('checking/view_detail', $this->data);
	}

	public function process_add_target()
	{
		$tgl_checking 	= $this->input->post('tgl_checking');
		$petugas 		= $this->input->post('petugas');
		$barang 		= $this->input->post('barang');
		$target 		= $this->input->post('target');

		$data_target 	= array(
			'date'			=> $tgl_checking,
			'petugas_id'	=> $petugas,
			'barang_id'		=> $barang,
			'target'		=> $target
		);

		if ($this->m_checking->insert($data_target)) {
			$this->session->set_flashdata('success', 'Data Target Kinerja Berhasil Dibuat!');
			redirect('checking/target');
		}
	}

	public function process_add_target_monthly()
	{
		$data_target 	= $this->input->post('target');
		$barang 		= $this->input->post('barang');
		$status 		= true;
		foreach ($data_target as $petugas_id => $target_month) {
			foreach ($target_month as $tanggal => $target_day) {
				if (!empty($target_day)) {
					$data_target = array(
						'date'			=> date_format(date_create(date('Y').'-'.date('m').'-'.$tanggal), 'Y-m-d'),
						'barang_id'		=> $barang,
						'target'		=> $target_day,
						'petugas_id'	=> $petugas_id
					);

					if (! $this->m_checking->insert($data_target)) {
						$status = false;
					}
				}
			}
		}

		if ($status) {
			$this->session->set_flashdata('success', 'Data Target Kinerja Berhasil Dibuat!');
			redirect('checking/target');
		}else{
			$this->session->set_flashdata('error', 'Data Target Kinerja Gagal Dibuat!');
			redirect('checking/target');
		}
	}

	public function get_related_barang()
	{
		$tgl_checking 	= $this->input->post('tgl_checking');
		$user 			= $this->session->login['id'];

		$data_checking 	= $this->m_checking->getRelatedBarang($user, $tgl_checking);

		$list_barang 	= '<select name="nama_barang" id="nama_barang" class="form-control" onChange="getTarget(this)">';
		$list_barang 	.= '<option value="" disabled selected>Pilih Barang</option>';
		foreach ($data_checking as $key) {
			$list_barang .= '<option value="'.$key->checking_id.'"> '.$key->nama_barang.' </option>';
		}
		$list_barang 	.= '</select>';

		echo $list_barang;
	}

	public function get_target_checking()
	{
		$checking_id 	= $this->input->post('checking_id');
		$checking_data 	= $this->m_checking->getById($checking_id);

		echo json_encode(array(
			'target' => $checking_data->target
		));
	}

	public function load_form_target_monthly()
	{
		$month 	= $this->input->post('month');
		$year 	= date('Y');
		
		$data['working_days'] 	= $this->m_checking->getWorkingDays();
		$data['all_petugas']	= $this->m_petugas->getAll();

		$this->load->view('checking/master/form_target_monthly', $data);
	}
}