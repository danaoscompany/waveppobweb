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
}