<?php

class Dashboard extends CI_Controller{
	public function __construct(){
		parent::__construct();
		if($this->session->login['role'] != 'petugas' && $this->session->login['role'] != 'admin') redirect();
		$this->data['aktif'] = 'dashboard';
		$this->load->model('M_barang', 'm_barang');
		$this->load->model('M_customer', 'm_customer');
		$this->load->model('M_supplier', 'm_supplier');
		$this->load->model('M_petugas', 'm_petugas');
		$this->load->model('M_pengeluaran', 'm_pengeluaran');
		$this->load->model('M_penerimaan', 'm_penerimaan');
		$this->load->model('M_pengguna', 'm_pengguna');
		$this->load->model('M_toko', 'm_toko');
		$this->load->model('M_checking', 'm_checking');
	}

	public function index(){
		$user_id 				= $this->session->login['id'];
		$this->data['title'] = 'Dashboard';
		

		if ($this->session->login['role'] == 'petugas') {
			$this->data['checkings'] 				= $this->m_checking->getAll($user_id, 2, date('Y'));
			$this->data['avg_kinerja_checking']		= number_format($this->m_checking->getAvgKinerja($user_id), 2);
			$this->data['barang'] 					= $this->m_checking->uniqueRelatedBarang($user_id);
			$this->data['working_days']				= $this->m_checking->getWorkingDays();

			$this->load->view('dashboard_petugas', $this->data);
		} else {
			$this->data['petugas']					= $this->m_petugas->lihat();
			$this->data['checkings'] 				= $this->m_checking->getAll(null, 2, date('Y'));
			$this->data['avg_kinerja_checking']		= number_format($this->m_checking->getAvgKinerja(null), 2);
			$this->data['barang'] 					= $this->m_checking->uniqueRelatedBarang(null);
			$this->data['working_days']				= $this->m_checking->getWorkingDays();

			$this->load->view('dashboard', $this->data);
		}		
	}
}