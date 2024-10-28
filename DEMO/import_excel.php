<?php
session_start();
require 'vendor/autoload.php';
include_once("connect.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

$_SESSION['messages'] = []; // Khởi tạo mảng thông báo

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']['name'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $allowedExtensions = ['xls', 'xlsx'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    if (in_array($fileExtension, $allowedExtensions)) {
        $spreadsheet = IOFactory::load($fileTmpName);
        $worksheet = $spreadsheet->getActiveSheet();

        // Lặp qua các hàng và lưu vào CSDL
        foreach ($worksheet->getRowIterator() as $row) {
            $cells = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Lặp qua tất cả các ô
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            // Giả sử cột 1: Mã sinh viên, cột 2: Họ, cột 3: Tên sinh viên, cột 4: Ngày sinh, cột 5: Giới tính
            if (count($cells) < 5) {
                continue; // Bỏ qua hàng nếu không đủ dữ liệu
            }

            $maSV = $cells[0];
            $hoLot = $cells[1];
            $tenSV = $cells[2];
            $ngaySinh = date('Y-m-d', strtotime($cells[3])); // Chuyển đổi định dạng ngày
            $gioiTinh = $cells[4];
            $maLop = $cells[5];

            $sqlCheckLop = "SELECT COUNT(*) FROM lophoc WHERE maLop = ?";
            $stmtCheckLop = $conn->prepare($sqlCheckLop);
            $stmtCheckLop->bind_param("s", $maLop);
            $stmtCheckLop->execute();
            $stmtCheckLop->bind_result($lopCount);
            $stmtCheckLop->fetch();
            $stmtCheckLop->close();

            if ($lopCount == 0) {
                $messages[] = "Mã lớp '{$maLop}' không tồn tại trong bảng lophoc.";
                continue; // Bỏ qua sinh viên nếu mã lớp không hợp lệ
            }

            // Kiểm tra xem mã sinh viên đã tồn tại trong cơ sở dữ liệu chưa
            $sqlCheckSV = "SELECT COUNT(*) FROM sinhvien WHERE maSV = ?";
            $stmtCheckSV = $conn->prepare($sqlCheckSV);
            $stmtCheckSV->bind_param("s", $maSV);
            $stmtCheckSV->execute();
            $stmtCheckSV->bind_result($svCount);
            $stmtCheckSV->fetch();
            $stmtCheckSV->close();

            if ($svCount > 0) {
                $_SESSION['messages'][] = ["type" => "error", "text" => "Mã số sinh viên {$maSV} đã tồn tại."];
                continue; // Bỏ qua và không thêm sinh viên đã tồn tại
            }

            $cellValue = $cells[3];

            // Kiểm tra nếu ngày sinh là một số (Excel lưu ngày tháng thành số serial)
            if (is_numeric($cellValue)) {
                // Chuyển đổi số serial của Excel thành ngày tháng
                $ngaySinh = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cellValue));
            } else {
                // Ngược lại, xử lý như chuỗi ngày tháng
                $ngaySinh = date('Y-m-d', strtotime($cellValue));
            }


            // Thêm dữ liệu vào CSDL
            $sql = "INSERT INTO sinhvien (maSV, hoLot, tenSV, ngaySinh, gioiTinh, maLop) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $maSV, $hoLot, $tenSV, $ngaySinh, $gioiTinh, $maLop);

            try {
                if (!$stmt->execute()) {
                    $_SESSION['messages'][] = ["type" => "error", "text" => "Lỗi khi nhập dữ liệu cho mã số sinh viên {$maSV}: " . $stmt->error];
                }
            } catch (mysqli_sql_exception $e) {
                $_SESSION['messages'][] = ["type" => "error", "text" => "Lỗi: " . $e->getMessage()];
            }
        }

        // Thông báo thành công nếu không có lỗi xảy ra trong quá trình import
        if (empty($_SESSION['messages'])) {
            $_SESSION['messages'][] = ["type" => "success", "text" => "Nhập dữ liệu thành công!"];
        }
    } else {
        $_SESSION['messages'][] = ["type" => "error", "text" => "Vui lòng chọn file Excel hợp lệ!"];
    }
} else {
    $_SESSION['messages'][] = ["type" => "error", "text" => "Không tìm thấy tệp tin hoặc yêu cầu không hợp lệ!"];
}

// Chuyển hướng về dssv_theo_lop.php với mã lớp tương ứng
$maLop = isset($maLop) ? $maLop : ''; // Đảm bảo mã lớp không bị trống
header("Location: http://localhost/DEMO/dssv_theo_lop.php?maLop=" . urlencode($maLop));
exit;
