<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Feb, 2018
* @author Irham Ciptadi <icip1998@gmail.com>
*/

class Report extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
		// $this->auth->check_auth();
		$this->load->model('adm/master/Vendor_model', 'Vendor_model');
		$this->load->model('adm/variable_cost/master/Kategori_model', 'Kategori_model');
		$this->load->model('adm/variable_cost/master/Sub_kategori_model', 'Sub_kategori_model');
		$this->load->model('adm/variable_cost/transaksi/Report_model', 'Report_model');
	}

	public function index()
	{
		$this->page = "adm/variable_cost/transaksi/report/index";
		$this->breadcrumb = "Home/Variable Cost/Transaksi/Variable Cost Report";
		$this->layout();
	}

	/**
	 * Ambil data dari
	 */
	public function get_data()
	{
		$get_data = $this->Report_model->get_data()->result();
		$data = array();

		$no = 0;
		foreach ($get_data as $key => $row) {
			$data[] = array(
				'id' 						=> $row->id,
				'vc_category' 	=> $row->vc_category_name
			);

			$get_data_sub = $this->Report_model->get_data_sub($row->vc_category)->result();
			foreach ($get_data_sub as $key_sub => $row_sub) {
				$data[$key]['sub_data'][] = array(
					'vc_sub_category'	=> $row_sub->vc_sub_category
				);

				$get_data_child = $this->Report_model->get_data_child($row_sub->id)->result();
				foreach ($get_data_child as $key_child => $row_child) {
					$data[$key]['sub_data'][$key_sub]['child_data'][$key_child] = array(
						'vendor'	=> $row_child->vendor,
						'uraian'	=> $row_child->uraian,
						'no_invoice'	=> $row_child->no_invoice,
						'tanggal_inv'	=> $row_child->tanggal_inv,
						'term'	=> $row_child->term,
						'due_date'	=> $row_child->due_date,
						'amount'	=> $row_child->amount,
						'ar_days'	=> $row_child->ar_days,
						'week_1'	=> $row_child->week_1,
						'week_2'	=> $row_child->week_2,
						'week_3'	=> $row_child->week_3,
						'week_4'	=> $row_child->week_4,
						'week_5'	=> $row_child->week_5,
						'pending'	=> $row_child->pending,
					);
				}
			}
			$no++;
		}

		$output = array(
		// // 	"draw" => (isset($_POST['draw']) ? $_POST['draw'] : ''),
		// // 	"recordsTotal" => $this->Input_model->count_all(),
		// // 	"recordsFiltered" => $this->Input_model->count_filtered(),
			"data" => $data,
		);

		//output to json format
		output_json($output);
	} 

}
