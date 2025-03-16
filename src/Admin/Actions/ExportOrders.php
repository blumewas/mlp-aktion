<?php

namespace Blumewas\MlpAktion\Admin\Actions;

use Blumewas\MlpAktion\Helper\Logger;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportOrders
{
    /**
     *
     *
     * @var ?Spreadsheet
     */
    private $spreadsheet = null;

    public function __invoke($meta_key, $meta_value)
    {
        $orders = $this->get_orders($meta_key, $meta_value);

        $sheet = $this->create_sheet();

        $row = 2; // Start from the second row

        foreach ($orders as $order) {
            $sheet->setCellValue("A$row", $order->get_id());
            $sheet->setCellValue("B$row", $order->get_date_created()->format('Y-m-d H:i:s'));
            $sheet->setCellValue("C$row", $order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
            $sheet->setCellValue("D$row", $order->get_billing_email());
            $sheet->setCellValue("E$row", $order->get_meta('_mlp_aktion_contact_phone'));
            $sheet->setCellValue("F$row", $order->get_billing_address_1() . ' ' . ($order->get_billing_address_2() ?? ''));
            $sheet->setCellValue("G$row", $order->get_billing_city());
            $sheet->setCellValue("H$row", $order->get_billing_postcode());
            $sheet->setCellValue("I$row", $order->get_meta($meta_key));
            $row++;
        }

        $this->start_download();
    }

    private function start_download()
    {
        // Prepare file for download
        $filename = 'orders_export_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function create_sheet()
    {
        // Create a new spreadsheet
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Bestell-Nr.');
        $sheet->setCellValue('B1', 'Datum');
        $sheet->setCellValue('C1', 'Name');
        $sheet->setCellValue('D1', 'E-Mail');
        $sheet->setCellValue('E1', 'Telefon');
        $sheet->setCellValue('F1', 'Adressse');
        $sheet->setCellValue('G1', 'Stadt');
        $sheet->setCellValue('H1', 'PLZ');
        $sheet->setCellValue('I1', 'Bestätigung');

        return $sheet;
    }


    private function get_orders($meta_key, $meta_value)
    {
        $args = [
            'limit' => -1,
            'status' => ['completed', 'processing'],
            'meta_query' => [
                [
                    'key' => $meta_key,
                    'value' => $meta_value,
                    'compare' => '='
                ]
            ]
        ];

        $orders = wc_get_orders($args);

        if (empty($orders)) {
            wp_die('No orders found with the specified meta value.');
        }

        return $orders;
    }

}

// Usage example
if (isset($_GET['export_orders_xlsx'])) {
    export_orders_to_excel('_custom_meta_key', 'custom_value');
}
