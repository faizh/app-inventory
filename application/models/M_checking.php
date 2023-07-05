<?php

class M_checking extends CI_Model{
	protected $_table = 'checking';

	public function insert($data)
	{
		$query = $this->db->insert($this->_table, $data);

		return $query;
	}

	public function getById($id)
	{
		$this->db->select('*');
		$this->db->where('checking_id', $id);

		return $this->db->get($this->_table)->row();
	}

	public function getAll($user_id = null, $month = null, $year = null)
	{
		$this->db->select('*');
		$this->db->join('barang', 'barang.id = checking.barang_id');
		$this->db->join('petugas', 'petugas.id = checking.petugas_id');

		if ($user_id != null) {
			$this->db->where('checking.petugas_id', $user_id);
			$this->db->where('checking.hasil IS NOT NULL');
		}

		if ($month != null) {
			$this->db->where('MONTH(checking.date)', $month);
		}

		if ($year != null) {
			$this->db->where('YEAR(checking.date)', $year);
		}

		$this->db->where('checking.hasil IS NOT NULL');
		$this->db->order_by('checking.date ASC');

		$results  = $this->db->get($this->_table)->result();

		return $results;
	}

	public function update_kinerja($checking_id, $data)
	{
		$this->db->where("checking_id", $checking_id);
		return $this->db->update($this->_table, $data);
	}

	public function getRelatedBarang($user_id, $tgl_checking)
	{
		$query = "
		SELECT *,
		b.id as barang_id
		FROM checking c 
		JOIN barang b ON b.id = c.`barang_id`
		WHERE c.`hasil` IS NULL AND c.`petugas_id` = ? AND  c.`date` = ?";

		return $this->db->query($query, array($user_id, $tgl_checking))->result();
	}

	public function getAvgKinerja($user_id = null)
	{
		$this->db->select('AVG(kinerja) as avg_kinerja');
		$this->db->where('hasil IS NOT NULL');
		if ($user_id != NULL) {
			$this->db->where('petugas_id', $user_id);
		}

		return $this->db->get($this->_table)->row()->avg_kinerja;
	}

	public function uniqueRelatedBarang($user_id = null)
	{
		$this->db->select('DISTINCT(barang_id) AS barang_id, b.nama_barang');
		$this->db->where('hasil IS NOT NULL');
		$this->db->join('barang b', 'b.id = checking.`barang_id`');
		if ($user_id != NULL) {
			$this->db->where('petugas_id', $user_id);
		}

		return $this->db->get($this->_table)->result();
	}

	public function getCompleteDataChecking($checking_id)
	{
		$query = "
		SELECT 
		c.`date`,
		c.`hasil`,
		c.`kinerja`,
		c.`target`,
		b.`nama_barang`,
		p.`nama` AS nama_petugas,
		p.`kode` AS kode_petugas
		FROM `checking` c 
		JOIN `barang` b ON b.`id` = c.`barang_id`
		JOIN `petugas` p ON p.`id` = c.`petugas_id`
		WHERE c.`checking_id` = ?";

		return $this->db->query($query, array($checking_id))->row();
	}

	public function getWorkingDays()
	{
		$workdays = array();
		$type = CAL_GREGORIAN;
		$month = date(2); // Month ID, 1 through to 12.
		$year = date('Y'); // Year in 4 digit 2009 format.
		$day_count = cal_days_in_month($type, $month, $year); // Get the amount of days

		//loop through all days
		for ($i = 1; $i <= $day_count; $i++) {

		        $date = $year.'/'.$month.'/'.$i; //format date
		        $get_name = date('l', strtotime($date)); //get week day
		        $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars

		        //if not a weekend add day to array
		        if($day_name != 'Sun' && $day_name != 'Sat'){
		            $workdays[] = $i;
		        }

		}
		
		return $workdays;
	}
}