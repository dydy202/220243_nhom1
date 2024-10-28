<?php
session_start(); // Bắt đầu phiên làm việc

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: index.php?message=Bạn cần đăng nhập để truy cập trang này.");
    exit; // Dừng thực thi mã tiếp theo
}

require 'vendor/autoload.php'; // Thư viện PhpSpreadsheet
include_once("connect.php"); // Kết nối CSDL

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = ""; // Khởi tạo biến thông báo
$error = ""; // Khởi tạo biến lỗi
$duplicateStudents = []; // Mảng để lưu mã sinh viên trùng lặp

if (isset($_FILES['file']['name'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];

    // Đảm bảo file có định dạng Excel
    $allowedExtensions = ['xls', 'xlsx'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    if (in_array($fileExtension, $allowedExtensions)) {
        // Đọc file Excel
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
            if (count($cells) < 6) {
                continue; // Bỏ qua hàng nếu không đủ dữ liệu
            }

            $maSV = $cells[0];
            $hoLot = $cells[1];
            $tenSV = $cells[2];
            $ngaySinh = date('Y-m-d', strtotime($cells[3])); // Chuyển đổi định dạng ngày sang YYYY-MM-DD
            $gioiTinh = $cells[4];
            $maLop = $cells[5]; // Bạn có thể điều chỉnh mã lớp theo yêu cầu

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

            if ($count > 0) {
                // Chỉ cần thêm thông báo "Mã số sinh viên đã tồn tại"
                $error = "Mã số sinh viên đã tồn tại!";
                continue; // Bỏ qua hàng này nếu đã tồn tại
            }

            // Thêm dữ liệu vào CSDL
            $sql = "INSERT INTO sinhvien (maSV, hoLot, tenSV, ngaySinh, gioiTinh, maLop) VALUES (?, ?, ?, ?, ?, ?)";
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $maSV, $hoLot, $tenSV, $ngaySinh, $gioiTinh, $maLop);
                $stmt->execute();
            } catch (mysqli_sql_exception $e) {
                $error = "Lỗi khi nhập dữ liệu: " . $e->getMessage(); // Thông báo lỗi nếu có
            }
        }

        // Thông báo import thành công nếu không có lỗi
        if (empty($error)) {
            $message .= "Import dữ liệu thành công!";
        }
    } else {
        $error .= "Vui lòng chọn file Excel hợp lệ!";
    }
}
