<?php
require_once('vendor/autoload.php');
include_once("connect.php");

use TCPDF as TCPDF;

// Lấy mã lớp từ GET
$maLop = isset($_GET['maLop']) ? $_GET['maLop'] : '';

if (empty($maLop)) {
    die("Không có mã lớp được cung cấp.");
}

// Truy vấn thông tin sinh viên của lớp
$sql = "SELECT * FROM sinhvien WHERE maLop = ? ORDER BY tenSV";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maLop);
$stmt->execute();
$result = $stmt->get_result();

// Khởi tạo đối tượng TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Thiết lập thông tin tài liệu
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Danh sách Sinh viên Lớp ' . $maLop);
$pdf->SetSubject('Student List');
$pdf->SetKeywords('TCPDF, PDF, Student, List');

// Thiết lập header và footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Thiết lập font mặc định
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Thiết lập margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// Thiết lập auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Thiết lập font Unicode
$pdf->SetFont('dejavusans', '', 10);

// Thêm một trang
$pdf->AddPage();

// Tạo nội dung HTML
$html = '
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
<h1>Danh sách Sinh viên Lớp ' . $maLop . '</h1>
<table>
    <tr>
        <th>STT</th>
        <th>Mã SV</th>
        <th>Họ và tên</th>
        <th>Ngày sinh</th>
        <th>Giới tính</th>
    </tr>';

$stt = 1;
while ($row = $result->fetch_assoc()) {
    $html .= '
    <tr>
        <td>' . $stt . '</td>
        <td>' . $row['maSV'] . '</td>
        <td>' . $row['hoLot'] . ' ' . $row['tenSV'] . '</td>
        <td>' . date('d/m/Y', strtotime($row['ngaySinh'])) . '</td>
        <td>' . $row['gioiTinh'] . '</td>
    </tr>';
    $stt++;
}

$html .= '</table>';

// Ghi nội dung HTML vào PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Đặt tên file output
$filename = 'danh_sach_sinh_vien_lop_' . $maLop . '.pdf';

// Xuất file PDF
$pdf->Output($filename, 'D');
exit;
?>