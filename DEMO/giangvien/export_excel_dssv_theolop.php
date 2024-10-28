<?php
require __DIR__ . '/../vendor/autoload.php';
include_once("../connect.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Lấy mã lớp từ GET
$maLop = isset($_GET['maLop']) ? $_GET['maLop'] : '';

// Lấy danh sách sinh viên theo mã lớp
$sql = "SELECT * FROM sinhvien WHERE maLop = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maLop);
$stmt->execute();
$result = $stmt->get_result();

// Tạo một Spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Đặt tiêu đề cho các cột
$sheet->setCellValue('A1', 'Mã sinh viên');
$sheet->setCellValue('B1', 'Họ');
$sheet->setCellValue('C1', 'Tên sinh viên');
$sheet->setCellValue('D1', 'Ngày sinh');
$sheet->setCellValue('E1', 'Giới tính');

// Điền dữ liệu vào các ô
$row = 2;
while ($data = $result->fetch_assoc()) {
    $maSV = (int) $data['maSV'];
    $sheet->setCellValue('A' . $row, $data['maSV']);
    $sheet->setCellValue('B' . $row, $data['hoLot']);
    $sheet->setCellValue('C' . $row, $data['tenSV']);
    $sheet->setCellValue('D' . $row, date('d-m-Y', strtotime($data['ngaySinh'])));
    $sheet->setCellValue('E' . $row, $data['gioiTinh']);
    $row++;
}

foreach (range('A', 'D') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}
// Tạo writer để lưu file
$writer = new Xlsx($spreadsheet);

// Đặt headers để trình duyệt hiểu đây là file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="DanhSachSinhVien_' . $maLop . '.xlsx"');
header('Cache-Control: max-age=0');

// Lưu file Excel
$writer->save('php://output');
exit;
