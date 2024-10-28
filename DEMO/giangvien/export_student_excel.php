<?php
require __DIR__ . '/../vendor/autoload.php';
include_once("../connect.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Kiểm tra và lấy mã sinh viên
$maSV = isset($_GET['maSV']) ? $_GET['maSV'] : '';

if (empty($maSV)) {
    die("Không có mã sinh viên được cung cấp.");
}

// Lấy thông tin sinh viên từ database
$sql = "SELECT * FROM sinhvien WHERE maSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maSV);
$stmt->execute();
$result = $stmt->get_result();
$sinhVien = $result->fetch_assoc();

if (!$sinhVien) {
    die("Không tìm thấy thông tin sinh viên.");
}

// Tạo một đối tượng Spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Đặt tiêu đề cho các cột
$sheet->setCellValue('A1', 'Mã SV');
$sheet->setCellValue('B1', 'Họ và tên');
$sheet->setCellValue('C1', 'Ngày sinh');
$sheet->setCellValue('D1', 'Giới tính');

// Điền thông tin sinh viên
$sheet->setCellValue('A2', $sinhVien['maSV']);
$sheet->setCellValue('B2', $sinhVien['hoLot'] . ' ' . $sinhVien['tenSV']);
$sheet->setCellValue('C2', date('d/m/Y', strtotime($sinhVien['ngaySinh'])));
$sheet->setCellValue('D2', $sinhVien['gioiTinh']);

// Thêm hình ảnh nếu có
if (!empty($sinhVien['hinhAnh']) && file_exists($sinhVien['hinhAnh'])) {
    $drawing = new Drawing();
    $drawing->setName('Hình ảnh sinh viên');
    $drawing->setDescription('Hình ảnh sinh viên');
    $drawing->setPath($sinhVien['hinhAnh']); // Đường dẫn đến file hình ảnh
    $drawing->setCoordinates('E2');
    $drawing->setWidth(100); // Điều chỉnh kích thước hình ảnh
    $drawing->setHeight(100);
    $drawing->setWorksheet($sheet);

    // Điều chỉnh chiều cao hàng để phù hợp với hình ảnh
    $sheet->getRowDimension(2)->setRowHeight(75);
} else {
    $sheet->setCellValue('E2', 'Không có hình ảnh');
}
// Tự động điều chỉnh độ rộng cột
foreach (range('A', 'D') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Tạo writer để lưu file
$writer = new Xlsx($spreadsheet);

// Đặt headers để tải file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ThongTinSinhVien_' . $maSV . '.xlsx"');
header('Cache-Control: max-age=0');

// Lưu file
$writer->save('php://output');
exit;
