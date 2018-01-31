<?php
/*
Template Name: Export New Orders
*/
if (!is_user_logged_in() || !current_user_can('manage_options')) wp_die('This page is private.');

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
$query = new WC_Order_Query( array(
//    'limit' => 10,
    'orderby' => 'date',
    'order' => 'ASC',
    'status' => 'processing',
) );
$new_orders = $query->get_orders();
$order_lines = [];

foreach ($new_orders as $the_order) {
	$order_data = $the_order->get_data();
	$user_data = $the_order->get_user();
	$coupons = [];
	
//	$order_data['currency'];
//	$order_data['prices_include_tax'];
//	$order_data['discount_total'];
//	$order_data['customer_user_agent'];
//	$order_data['customer_ip_address'];
	
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

    $product = new WC_Product($item_data['product_id']);
		$order_lines[] = [
				'Order ID'=>$order_data['id'],
				'Order Status'=>$order_data['status'],
				'Sales Channel Code' => 'ANCPAUS',
				'Sales Channel Fulfilment Warehouse Code' => 'ANCPAUS_1',
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
				'Shipping Method' => 'Standard',
				'Shipping Cost - Aus' => $order_data['shipping_total'],
				'Shipping Cost - RMB' => '',
				'Shipping Discount Amount - Aus' => '',
				'Shipping Discount Amount - RMB' => '',
				'Customer Instructions' => $order_data['customer_note'],
				'Internal Notes' => '',
				'Amount Paid - Aus' => $order_data['total'],
				'Amount Paid - RMB' => '',
				'Date Paid' => $order_data['date_paid']->date_i18n('n/j/Y'), //*WC_DateTime Object
				'Order Line SKU' => $product->get_sku(),
				'Order Line Qty' => $item_data['quantity'],
				'Order Line Description - English' => '',
				'Order Line Unit Price - Aus' => $item_data['subtotal']/$item_data['quantity'],
				'Order Line Extended Value - Aus' => $item_data['subtotal'],
				'Order Line Discount Amount - Aus' => $item_disc_amt,
				'Order Line Description - Chinese' => $product->get_title(),
				'Order Line Unit Price - RMB' => '',
				'Order Line Extended Value - RMB' => '',
				'Order Line Discount Amount - RMB' => '',
				'RMB Conversion Rate' => ''
		];
	}
}
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$l = 'A';
foreach ($fields as $fld) {
	$sheet->setCellValue("{$l}1", $fld);
	$l++;
}

$i=2;
foreach ($order_lines as $odr) {
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
$writer->save('/var/www/batchorders/exports_from_website/'.date('Ymd_His').'.xlsx');