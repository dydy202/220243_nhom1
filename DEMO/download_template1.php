<?php
// Bao gồm autoload của Composer để sử dụng PHPSpreadsheet
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; // Để chuyển đổi từ chỉ số cột sang ký tự

// Kết nối cơ sở dữ liệu
include_once("connect.php");

// Tạo một đối tượng Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// **Ghi tên các cột vào hàng đầu tiên của file Excel**
$columns = ['Mã sinh viên', 'Họ lót', 'Tên sinh viên', 'Ngày sinh', 'Giới tính', 'Mã lớp']; // Danh sách các cột cần ghi
$colIndex = 1; // Bắt đầu từ cột A

// Ghi tên cột vào ô đầu tiên của cột đó
foreach ($columns as $columnName) {
    // Chuyển đổi số cột thành ký tự (A, B, C,...)
    $columnLetter = Coordinate::stringFromColumnIndex($colIndex);
    // Ghi tên cột vào ô đầu tiên của cột đó
    $sheet->setCellValue($columnLetter . '1', $columnName);
    $colIndex++;
}

// Thiết lập tiêu đề và kiểu trả về file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="template.xlsx"');

// Xuất file Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
