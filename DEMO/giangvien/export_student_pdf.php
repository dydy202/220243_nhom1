<?php
require __DIR__ . '/../vendor/autoload.php';
include_once("../connect.php");

// Tạo đối tượng TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Thiết lập thông tin tài liệu
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Student Information');
$pdf->SetSubject('Student Details');
$pdf->SetKeywords('TCPDF, PDF, Student, Information');


// Thiết lập font
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Thiết lập mặc định monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Thiết lập margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Thiết lập auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Thiết lập image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Thêm một trang
$pdf->AddPage();

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

// Đặt font Unicode
$pdf->SetFont('dejavusans', '', 10);

// Tạo nội dung PDF
$html = '
<h1>Thông tin Sinh viên</h1>
<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <th>Mã SV</th>
        <th>Họ và tên</th>
        <th>Ngày sinh</th>
        <th>Giới tính</th>
        <th>Mã lớp</th>
    </tr>
    <tr>
        <td>' . $sinhVien['maSV'] . '</td>
        <td>' . $sinhVien['hoLot'] . ' ' . $sinhVien['tenSV'] . '</td>
        <td>' . date('d/m/Y', strtotime($sinhVien['ngaySinh'])) . '</td>
        <td>' . $sinhVien['gioiTinh'] . '</td>
        <td>' . $sinhVien['maLop'] . '</td>
    </tr>
</table>';

// Thêm nội dung HTML vào PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Thêm hình ảnh sinh viên (nếu có)
if (!empty($sinhVien['hinhAnh']) && file_exists($sinhVien['hinhAnh'])) {
    $pdf->Image($sinhVien['hinhAnh'], 15, 50, 30, 40, 'JPG', '', '', true, 150, '', false, false, 1, false, false, false);
}

// Đặt tên file output
$filename = 'student_info_' . $sinhVien['maSV'] . '.pdf';

// Xuất file PDF
$pdf->Output($filename, 'D');
exit;
