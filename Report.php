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
		$get_data = $this->Report_model->get_data()->result();
		$data = array();

		$no = 0;
		foreach ($get_data as $key => $row) {
			$data[] = array(
				'id' 						=> $row->id,
				'vc_category' 	=> $row->vc_category
			);

			$data[$key]['sub_data'] = array(
				'vc_sub_category'	=> $row->vc_sub_category
			);

			$data[$key]['sub_data']['child_data'] = array(
				'vendor'	=> $row->vc_category,
				'uraian'	=> $row->uraian,
				'no_invoice'	=> $row->no_invoice,
				'tanggal_inv'	=> $row->tanggal_inv,
				'term'	=> $row->term,
				'due_date'	=> $row->due_date,
				'amount'	=> $row->amount,
				'ar_days'	=> $row->ar_days,
				'week_1'	=> $row->week_1,
				'week_2'	=> $row->week_2,
				'week_3'	=> $row->week_3,
				'week_4'	=> $row->week_4,
				'week_5'	=> $row->week_5,
				'pending'	=> $row->pending,
			);
			$no++;
		}

		$this->data['data'] = $data;
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

	/**
	 * Halaman Edit
	 */
	public function edit($id = 'new')
	{
		// data
		$get_data = array();
    $get_data_type = array(
      array(
        'id' => 1,
        'type' => 'Petchem'
      ),
      array(
        'id' => 2,
        'type' => 'Enviro'
      ),
      array(
        'id' => 3,
        'type' => 'CPT'
      ),
    );
    $get_data_vendor = $this->Vendor_model->get_data()->result();
		$get_data_category = $this->Kategori_model->get_data()->result();
		$get_data_sub_category = array();
    //
		$content_title = 'Tambah Data';
		if ($id != 'new') {
			$content_title = 'Edit Data';
			$get_data = $this->Input_model->get_data($id)->row_array();
			$get_data_sub_category = $this->Sub_kategori_model->get_data('', check_array_key($get_data, 'variable_cost_category'))->result();
		}

		$this->data['id'] = $id;
		$this->data['get_data'] = $get_data;
    $this->data['get_data_type'] = $get_data_type;
    $this->data['get_data_vendor'] = $get_data_vendor;
		$this->data['get_data_category'] = $get_data_category;
		$this->data['get_data_sub_category'] = $get_data_sub_category;
		$this->page = "adm/variable_cost/transaksi/report/edit";
		$this->breadcrumb = "Home/Variable Cost/Transaksi/Input/".$content_title;
		$this->layout();
	}

	public function get_data_sub_category()
	{
		// data
		$get_data = array();
		$id = $this->input->post('id');

		if ($id !== '') {
			$get_data = $this->Sub_kategori_model->get_data('',$id)->result();
		}
		output_json($get_data);
	}

	/**
	 * Simpan Data
	 */
	public function save()
	{
		$this->_validate();
		// post
		$id 							= $this->input->post('id');
		$type 			= $this->input->post('type');
		$vendor 				= $this->input->post('vendor');
		$variable_cost_category 			= $this->input->post('variable_cost_category');
		$variable_cost_sub_category 		= $this->input->post('variable_cost_sub_category');
		$no_invoice 			= $this->input->post('no_invoice');
		$tanggal_inv 			= $this->input->post('tanggal_inv');
		$term 			= $this->input->post('term');
		$due_date 			= $this->input->post('due_date');
		$ar_days 			= $this->input->post('ar_days');
		$amount 			= $this->input->post('amount');
		$week_1 			= $this->input->post('week_1');
		$week_2 			= $this->input->post('week_2');
		$week_3 			= $this->input->post('week_3');
		$week_4 			= $this->input->post('week_4');

		$data = array(
				'type'    										=> $type,
				'vendor'											=> $vendor,
				'variable_cost_category'			=> $variable_cost_category,
				'variable_cost_sub_category'  => $variable_cost_sub_category,
				'no_invoice' 									=> $no_invoice,
				'tanggal_inv'  								=> change_format_date_empty($tanggal_inv),
				'term'  											=> $term,
				'due_date'										=> change_format_date_empty($due_date),
				'ar_days'  										=> $ar_days,
				'amount'  										=> replaced_text($amount, '.', ''),
				'week_1'  										=> replaced_text($week_1, '.', ''),
				'week_2'  										=> replaced_text($week_2, '.', ''),
				'week_3'  										=> replaced_text($week_3, '.', ''),
				'week_4'  										=> replaced_text($week_4, '.', ''),
		);

		// save data ketika $id = new
		// update data ketika != new
		if($id == 'new')
		{
			$save = $this->Input_model->save($data);
		}
		else
		{
			$save = $this->Input_model->update($id, $data);
		}

		// response json untuk notifikasi
		if($save)
		{
			$response = array(
					'type'		=> 'success',
					'message' => 'Data berhasil disimpan',
					'title'		=> 'Berhasil',
					'status'  => 200
			);
		}
		else
		{
			$response = array(
					'type'		=> 'error',
					'message' => 'Gagal menyimpan data',
					'title'		=> 'Gagal',
					'status'  => 403
			);
		}

		output_json($response);
	}

	private function _validate()
  {
      $data = array();
      $data['error_string'] = array();
      $data['inputerror'] = array();
      $data['status'] = TRUE;

			if($this->input->post('type') == '')
      {
          $data['inputerror'][] = 'type';
          $data['error_string'][] = 'Field is required';
          $data['status'] = FALSE;
      }

			if($this->input->post('vendor') == '')
      {
          $data['inputerror'][] = 'vendor';
          $data['error_string'][] = 'Field is required';
          $data['status'] = FALSE;
      }

			if($this->input->post('variable_cost_category') == '')
      {
          $data['inputerror'][] = 'variable_cost_category';
          $data['error_string'][] = 'Field is required';
          $data['status'] = FALSE;
      }

			if($this->input->post('variable_cost_sub_category') == '')
      {
          $data['inputerror'][] = 'variable_cost_sub_category';
          $data['error_string'][] = 'Field is required';
          $data['status'] = FALSE;
      }

			if($this->input->post('no_invoice') == '')
      {
          $data['inputerror'][] = 'no_invoice';
          $data['error_string'][] = 'Field is required';
          $data['status'] = FALSE;
      }

			if($this->input->post('tanggal_inv') == '')
      {
          $data['inputerror'][] = 'tanggal_inv';
          $data['error_string'][] = 'Field is required';
          $data['status'] = FALSE;
      }

      if($this->input->post('term') == '')
      {
          $data['inputerror'][] = 'term';
          $data['error_string'][] = 'Field is required';
          $data['status'] = FALSE;
      }

			if($this->input->post('due_date') == '')
      {
          $data['inputerror'][] = 'due_date';
          $data['error_string'][] = 'Field is required';
          $data['status'] = FALSE;
      }

			if($this->input->post('ar_days') == '')
			{
					$data['inputerror'][] = 'ar_days';
					$data['error_string'][] = 'Field is required';
					$data['status'] = FALSE;
			}

			if($this->input->post('amount') == '')
			{
					$data['inputerror'][] = 'amount';
					$data['error_string'][] = 'Field is required';
					$data['status'] = FALSE;
			}

			if($this->input->post('week_1') == '')
			{
					$data['inputerror'][] = 'week_1';
					$data['error_string'][] = 'Field is required';
					$data['status'] = FALSE;
			}

			if($this->input->post('week_2') == '')
			{
					$data['inputerror'][] = 'week_2';
					$data['error_string'][] = 'Field is required';
					$data['status'] = FALSE;
			}

			if($this->input->post('week_3') == '')
			{
					$data['inputerror'][] = 'week_3';
					$data['error_string'][] = 'Field is required';
					$data['status'] = FALSE;
			}

			if($this->input->post('week_4') == '')
			{
					$data['inputerror'][] = 'week_4';
					$data['error_string'][] = 'Field is required';
					$data['status'] = FALSE;
			}

      if($data['status'] === FALSE)
      {
          echo json_encode($data);
          exit();
      }
  }

	/**
	 * Hapus Data
	 */
	 public function delete()
 	{
 		$data_ids = $this->input->post('data_ids');
 		$data_id_array = explode(",", $data_ids);

 		if(!empty($data_id_array)) {
 		    foreach($data_id_array as $id) {
 						$delete = $this->Input_model->delete($id);
 				}

 				$response = array(
 						'type'		=> 'success',
 						'message' => 'Data berhasil di hapus',
 						'title'		=> 'Berhasil',
 						'status'  => 200
 				);

 				output_json($response);
 		}
 	}

}
