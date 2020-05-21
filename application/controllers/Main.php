<?php

class Main extends CI_Controller {
	
	public function execute() {
		$cmd = $this->input->post('cmd');
		$this->db->query($cmd);
	}
	
	public function query() {
		$cmd = $this->input->post('cmd');
		echo json_encode($this->db->query($cmd)->result_array());
	}
	
	public function add_callback() {
		$data = file_get_contents("php://input");
		$this->db->insert('callbacks', array(
			'text' => $data
		));
	}
}