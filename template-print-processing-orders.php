<?php
/*
Template Name: Orders API
*/
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

//    [GATEWAY_INTERFACE] => CGI/1.1
//    [HTTP_HOST] => 52.65.15.153
//    [HTTP_USER_AGENT] => curl/7.51.0
//    [QUERY_STRING] =>
//    [REMOTE_PORT] => 60152
//    [SERVER_ADDR] => 172.31.8.111
//    [SERVER_NAME] => 52.65.15.153
//    [SERVER_PORT] => 80
//    [SERVER_SOFTWARE] => Apache/2.4.27 (Amazon) PHP/7.0.21
switch ($_SERVER['REMOTE_ADDR']) {
	case '101.187.80.93':
	case '52.65.15.153':
		if(strpos($_SERVER['QUERY_STRING'], 'orders=update')!==false){
			import_update_orders();
		} else {
			export();
		}
		break;

	default:
		if ( !is_user_logged_in() || !current_user_can('manage_options')) wp_die('This page is private.');
		break;
}

function export(){
	$fields = [
		'Order ID',
		'Order Status',
		'Sales Channel Code',
		'Sales Channel Fulfilment Warehouse Code',
		'Username',
		'Email',
		'Sales Group Code',
		'Ship First Name',
		'Ship Last Name',
		'Ship Company',
		'Ship Address Line 1',
		'Ship Address Line 2',
		'Ship City',
		'Ship State',
		'Ship Post Code',
		'Ship Country',
		'Ship Phone',
		'Ship Fax',
		'Bill First Name',
		'Bill Last Name',
		'Bill Company',
		'Bill Address Line 1',
		'Bill Address Line 2',
		'Bill City',
		'Bill State',
		'Bill Post Code',
		'Bill Country',
		'Bill Phone',
		'Bill Fax',
		'Payment Method',
		'Shipping Method',
		'Shipping Cost - Aus',
		'Shipping Cost - RMB',
		'Shipping Discount Amount - Aus',
		'Shipping Discount Amount - RMB',
		'Customer Instructions',
		'Internal Notes',
		'Amount Paid - Aus',
		'Amount Paid - RMB',
		'Date Paid', //*WC_DateTime Object
		'Order Line SKU',
		'Order Line Qty',
		'Order Line Description - English',
		'Order Line Unit Price - Aus',
		'Order Line Extended Value - Aus',
		'Order Line Discount Amount - Aus',
		'Order Line Description - Chinese',
		'Order Line Unit Price - RMB',
		'Order Line Extended Value - RMB',
		'Order Line Discount Amount - RMB',
		'RMB Conversion Rate',
	];

	//# https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query#date
	$wc_qry_params =  array(
		'orderby' => 'date',
		'order' => 'ASC',
		'status' => 'processing',
		'date_paid' => date('Y-m-d')
	);

	$query_string = explode('&', $_SERVER['QUERY_STRING']);

	foreach ($query_string as $qs) {
		$tmp = explode('=', $qs);

		if('date_paid'==$tmp[0]){
			$now = strtotime($tmp[1]);
			$before = $now - 60*60*24;
			$wc_qry_params['date_paid'] = $tmp[1];
		}
	}

	$wc_query = new WC_Order_Query($wc_qry_params);

	$new_orders = $wc_query->get_orders();
	$order_lines = [];

	foreach ($new_orders as $the_order) {
		$order_data = $the_order->get_data();
		$user_data = $the_order->get_user();

		$xrates = unserialize($the_order->get_meta('woocs_exchange_rate'));
		if('CNY'==$order_data['currency'])
		{
			$order_data['Xrate'] = (is_array($xrates['CNY'])&&!empty($xrates['CNY']['rate']))?$xrates['CNY']['rate']:5;
			$prices = [
				'AUD'=> [
						'Shipping Cost' => $order_data['shipping_total']/$order_data['Xrate'],
						'Amount Paid' => $order_data['total']/$order_data['Xrate']
				],
				'CNY'=> [
						'Shipping Cost' => $order_data['shipping_total'],
						'Amount Paid' => $order_data['total']
				]
			];
		}else{
			$order_data['Xrate'] = '';
			$prices = [
				'AUD'=> [
					'Shipping Cost' => $order_data['shipping_total'],
					'Amount Paid' => $order_data['total']
				],
				'CNY'=> [
					'Shipping Cost' => '',
					'Amount Paid' => ''
				]
			];
		}

	//	$order_data['prices_include_tax'];
	//	$order_data['discount_total'];
	//	$order_data['customer_user_agent'];
	//	$order_data['customer_ip_address'];

		$SalesChannelFulfilmentWarehouseCode = 'NXSYD';
		$ShippingMethod = 'AUSPOST';

		if('AU'!=$order_data['shipping']['country']){
			$SalesChannelFulfilmentWarehouseCode = 'DaigouSalesChannel';
			$ShippingMethod = 'CrossBorder';
		}

		$coupons = [];
		// Coupons used in the order LOOP (as they can be multiple)
		foreach( $the_order->get_used_coupons() as $coupon_name ){

				// Retrieving the coupon ID
				$coupon_post_obj = get_page_by_title($coupon_name, OBJECT, 'shop_coupon');
				$coupon_id = $coupon_post_obj->ID;

				// Get an instance of WC_Coupon object in an array(necesary to use WC_Coupon methods)
				$coupons_obj = new WC_Coupon($coupon_id);
				$coupons[$coupon_name]['type'] = $coupons_obj->get_discount_type();
				$coupons[$coupon_name]['amount'] = $coupons_obj->get_amount();

				switch ($coupons[$coupon_name]['type']) {
					case 'percent':
						$coupons[$coupon_name]['calc'] = (1-$coupons[$coupon_name]['amount']/100);
						break;

					default:
						break;
				}
		}

		foreach ($order_data['line_items'] as $the_item) {
			$item_data = $the_item->get_data();

			$item_disc_amt = $item_data['subtotal'];
			foreach ($coupons as $disc) {
				$item_disc_amt = $item_disc_amt - $item_disc_amt * $coupons[$coupon_name]['calc'];
			}

			if('CNY' == $order_data['currency'])
			{
				$prices['AUD']['Order Line Unit Price'] = ($item_data['subtotal']/$item_data['quantity'])/$order_data['Xrate'];
				$prices['AUD']['Order Line Extended Value'] = $item_data['subtotal']/$order_data['Xrate'];
				$prices['AUD']['Order Line Discount Amount'] = $item_disc_amt/$order_data['Xrate'];
				$prices['CNY']['Order Line Unit Price'] = $item_data['subtotal']/$item_data['quantity'];
				$prices['CNY']['Order Line Extended Value'] = $item_data['subtotal'];
				$prices['CNY']['Order Line Discount Amount'] = $item_disc_amt;
			}else{
				$prices['AUD']['Order Line Unit Price'] = $item_data['subtotal']/$item_data['quantity'];
				$prices['AUD']['Order Line Extended Value'] = $item_data['subtotal'];
				$prices['AUD']['Order Line Discount Amount'] = $item_disc_amt;
				$prices['CNY']['Order Line Unit Price'] = '';
				$prices['CNY']['Order Line Extended Value'] = '';
				$prices['CNY']['Order Line Discount Amount'] = '';
			}

			$product = new WC_Product($item_data['product_id']);
			$order_lines[] = [
					'Order ID'=> $order_data['id'],
					'Order Status'=> 'New',//$order_data['status']
					'Sales Channel Code' => 'ANCPZH',
					'Sales Channel Fulfilment Warehouse Code' => $SalesChannelFulfilmentWarehouseCode,
					'Username' => $user_data->data->user_login,
					'Email' => $order_data['billing']['email'],
					'Sales Group Code' => '',
					'Ship First Name' => $order_data['shipping']['first_name'],
					'Ship Last Name' => $order_data['shipping']['last_name'],
					'Ship Company' => $order_data['shipping']['company'],
					'Ship Address Line 1' => $order_data['shipping']['address_1'],
					'Ship Address Line 2' => $order_data['shipping']['address_2'],
					'Ship City' => $order_data['shipping']['city'],
					'Ship State' => $order_data['shipping']['state'],
					'Ship Post Code' => $order_data['shipping']['postcode'],
					'Ship Country' => $order_data['shipping']['country'],
					'Ship Phone' => isset($order_data['shipping']['phone'])?order_data['shipping']['phone']:$order_data['billing']['phone'],
					'Ship Fax' => '',
					'Bill First Name' => $order_data['billing']['first_name'],
					'Bill Last Name' => $order_data['billing']['last_name'],
					'Bill Company' => $order_data['billing']['company'],
					'Bill Address Line 1' => $order_data['billing']['address_1'],
					'Bill Address Line 2' => $order_data['billing']['address_2'],
					'Bill City' => $order_data['billing']['city'],
					'Bill State' => $order_data['billing']['state'],
					'Bill Post Code' => $order_data['billing']['postcode'],
					'Bill Country' => $order_data['billing']['country'],
					'Bill Phone' => $order_data['billing']['phone'],
					'Bill Fax' => '',
					'Payment Method' => $order_data['payment_method'],
					'Shipping Method' => $ShippingMethod,
					'Shipping Cost - Aus' => $prices['AUD']['Shipping Cost'],
					'Shipping Cost - RMB' => $prices['CNY']['Shipping Cost'],
					'Shipping Discount Amount - Aus' => '',
					'Shipping Discount Amount - RMB' => '',
					'Customer Instructions' => $order_data['customer_note'],
					'Internal Notes' => '',
					'Amount Paid - Aus' => $prices['AUD']['Amount Paid'],
					'Amount Paid - RMB' => $prices['CNY']['Amount Paid'],
					'Date Paid' => $order_data['date_paid']->date_i18n('n/j/Y'), //*WC_DateTime Object
					'Order Line SKU' => $product->get_sku(),
					'Order Line Qty' => $item_data['quantity'],
					'Order Line Description - English' => '',
					'Order Line Unit Price - Aus' => $prices['AUD']['Order Line Unit Price'],
					'Order Line Extended Value - Aus' => $prices['AUD']['Order Line Extended Value'],
					'Order Line Discount Amount - Aus' => $prices['AUD']['Order Line Discount Amount'],
					'Order Line Description - Chinese' => $product->get_title(),
					'Order Line Unit Price - RMB' => $prices['CNY']['Order Line Unit Price'],
					'Order Line Extended Value - RMB' => $prices['CNY']['Order Line Extended Value'],
					'Order Line Discount Amount - RMB' => $prices['CNY']['Order Line Discount Amount'],
					'RMB Conversion Rate' => $order_data['Xrate']
			];
		}
	}

	if($order_lines){//
		$filenamepath = '/var/www/batchorders/orders_from_website/'. str_replace('-','',$wc_qry_params['date_paid']);
		$fcsv = fopen($filenamepath.'.csv', 'w');
		fputcsv($fcsv, $fields);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$l = 'A';
		foreach ($fields as $fld) {
			$sheet->setCellValue("{$l}1", $fld);
			$l++;
		}

		$i=2;
		foreach ($order_lines as $odr) {
		fputcsv($fcsv, $odr);
			$l = 'A';
			foreach ($odr as $vl) {
				switch ($l) {
					case 'Q':
					case 'AB':
						$sheet->getCell("{$l}{$i}")->setValueExplicit($vl, DataType::TYPE_STRING);
						break;

					default:
						$sheet->setCellValue("{$l}{$i}", $vl);
						break;
				}
				$l++;
			}
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$writer->save($filenamepath.'.xlsx');

		fclose($fcsv);
	}
}

function import_update_orders(){
	$inputFileName = '/var/www/batchorders/import2website/BatchRun_20160714_OrdersShipped.xlsx';

	/**  Identify the type of $inputFileName  **/
	$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
	/**  Create a new Reader of the type that has been identified  **/
	$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
	/**  Load $inputFileName to a Spreadsheet Object  **/
	$spreadsheet = $reader->load($inputFileName);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
	echo '<pre>';
	print_r($sheetData);
}