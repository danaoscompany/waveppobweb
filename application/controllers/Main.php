<?php

include 'Message.php';

class Main extends CI_Controller {
	
	public function execute() {
		$cmd = $this->input->post('cmd');
		$this->db->query($cmd);
	}
	
	public function query() {
		$cmd = $this->input->post('cmd');
		echo json_encode($this->db->query($cmd)->result_array());
	}
	
	public function update_payment_status() {
		echo "Updating payment status...\n";
		$data = file_get_contents("php://input");
		echo "Callback: " . $data . "\n";
		$items = json_decode($data, true);
		switch (json_last_error()) {
    case JSON_ERROR_NONE:
        echo ' - No errors';
    break;
    case JSON_ERROR_DEPTH:
        echo ' - Maximum stack depth exceeded';
    break;
    case JSON_ERROR_STATE_MISMATCH:
        echo ' - Underflow or the modes mismatch';
    break;
    case JSON_ERROR_CTRL_CHAR:
        echo ' - Unexpected control character found';
    break;
    case JSON_ERROR_SYNTAX:
        echo ' - Syntax error, malformed JSON';
    break;
    case JSON_ERROR_UTF8:
        echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
    break;
    default:
        echo ' - Unknown error';
    break;
}
		foreach ($items as $item) {
			$trxID = $item['api_trxid'];
			echo "TRX ID (2): " . $trxID;
			$this->db->where('trxid', $trxID);
			$this->db->update('payments', array(
				'status' => $status,
				'callback' => $data
			));
			$payment = $this->db->get_where('payments', array(
				'trxid' => $trxID
			))->row_array();
			$status = intval($item['status']);
			$category = intval($item['category']);
			$title = "Pembelian ";
			if ($category == 1) {
				$title .= "pulsa";
				$title .= " ke nomor ";
			} else if ($category == 2) {
				$title .= "paket data";
			} else if ($category == 4) {
				$title .= "voucher Google Play";
			} else if ($category == 5) {
				$title .= "pulsa SMS telephone";
			} else if ($category == 6) {
				$title .= "paket transfer";
			} else if ($category == 7) {
				$title .= "iTunes";
			} else if ($category == 11) {
				$title .= "voucher game";
			} else if ($category == 12) {
				$title .= "PUBG mobile";
			} else if ($category == 14) {
				$title .= "voucher Wifi.id";
			} else if ($category == 15) {
				$title .= "emoney";
			} else if ($category == 19) {
				$title .= "token listrik";
			} else if ($category == 20) {
				$title .= "etoll";
			}
			$title .= " ";
			if ($status == 1) {
				$status = "success";
				$title .= "telah berhasil";
			} else {
				$status = "process";
				$title .= "sedang dalam proses";
			}
			$user = $this->db->get_where('users', array(
				'id' => intval($item['user_id'])
			))->row_array();
			PushyAPI::send_message($user['pushy_token'], 1, 1, $title, "Klik untuk info lebih lanjut", "com.wave.passenger.UPDATE_PAYMENT_INFO", array(
				'id_customer' => $item['target'],
				'status' => intval($item['status']),
				'product_type' => intval($payment['category']),
				'product_code' => $item['code'],
				'product_name' => $item['produk'],
				'trxid' => $trxID
			));
		}
		$this->db->insert('callbacks', array(
			'text' => $data
		));
	}
	
	public function test() {
		$trxID = $this->input->get('trxid');
		echo "TRX ID: " . $trxID . "\n";
		$payment = $this->db->get_where('payments', array('trxid' => $trxID))->row_array();
		$callback = "[{
			\"trxid\": \"" . $trxID . "\",
			\"api_trxid\": \"INV45769\",
			\"via\": \"API\",
			\"code\": \"XL5\",
			\"produk\": \"XL 5000\",
			\"harga\": \"6125\",
			\"target\": \"" . $payment['id_customer'] . "\",
			\"mtrpln\": \"-\",
			\"note\": \"Trx XL5 " . $payment['"id_customer'] . " SUKSES. SN: 845392759476503\",
			\"token\": \"845392759476503\",
			\"status\": \"1\",
			\"saldo_before_trx\": \"100000\",
			\"saldo_after_trx\": \"5894\",
			\"created_at\": \"2019-11-06 12:07:48\",
			\"updated_at\": \"2019-11-15 20:59:10\",
			\"tagihan\": null
		}]";
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, "https://osgenics.xyz/waveppobweb/index.php/main/update_payment_status");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $callback );
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
		$output = curl_exec($ch);
		curl_close($ch);
		echo $output;
	}
	
	public function test_curl() {
		
	}
	
	public function test_send_message() {
		PushyAPI::send_message("ab299b983c0c6c0e7e3378", 2, 1, "Ini adalah judul notifikasi", "Ini adalah isi notifikasi", "com.wave.passenger.TEST_MESSAGE", array());
	}
}