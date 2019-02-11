<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Feb, 2018
* @author Irham Ciptadi <icip1998@gmail.com>
*/

class Report_model extends CI_Model {

    var $table = 'variable_cost';
    var $column_order = array(null, 'vendor','variable_cost_category');
    var $column_search = array('vendor');
    var $order = array('id' => 'asc');

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_data($id = '')
    {
    	// $sql = "SELECT
      //   variable_cost.*,
      //   variable_cost_category.name as vc_category,
      //   variable_cost_sub_category.name as vc_sub_category
      // FROM
      //   `variable_cost`
      // INNER JOIN
      //   variable_cost_category ON variable_cost_category.id = variable_cost.variable_cost_category
      // LEFT JOIN
      //   variable_cost_sub_category ON variable_cost_sub_category.id = variable_cost.variable_cost_sub_category
      // ORDER BY variable_cost.vendor";

      $sql = "SELECT MIN(variable_cost.id) AS id, variable_cost.variable_cost_category as vc_category, variable_cost_category.name as vc_category_name
FROM variable_cost
INNER JOIN variable_cost_category ON variable_cost.variable_cost_category = variable_cost_category.id
GROUP BY variable_cost.variable_cost_category";

      $query = $this->db->query($sql);
      return $query;
    }

    public function get_data_sub($id = '')
    {
        $sql = "SELECT MIN(variable_cost.id) AS id, variable_cost.variable_cost_sub_category as vc_sub_category, variable_cost_category.name as vc_category_name
FROM variable_cost
INNER JOIN variable_cost_category ON variable_cost.variable_cost_category = variable_cost_category.id
WHERE variable_cost.variable_cost_category = $id
GROUP BY variable_cost.variable_cost_sub_category
";
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_data_child($id = '')
    {
        $sql = "SELECT * FROM `variable_cost` WHERE variable_cost.id = $id";
        $query = $this->db->query($sql);
        return $query;
    }

    public function save($data)
    {
    	return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
    	$this->db->where("id", $id);
    	return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
    	$this->db->where("id", $id);
    	$this->db->delete($this->table);
    }

}

/* End of file Report_model.php */
/* Location: ./application/models/ar/Report_model.php */
