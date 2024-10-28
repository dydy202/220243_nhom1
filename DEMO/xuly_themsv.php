<?php
include_once("connect.php"); // Kết nối cơ sở dữ liệu

$error = ""; // Biến lưu thông báo lỗi
$maSV = $hoLot = $tenSV = $ngaySinh = $gioiTinh = $tenLop = $hinhAnh = ""; // Biến lưu thông tin nhập vào

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy và xử lý thông tin từ form
    $maSV = isset($_POST['maSV']) ? $conn->real_escape_string(trim($_POST['maSV'])) : '';
    $hoLot = isset($_POST['hoLot']) ? $conn->real_escape_string(trim($_POST['hoLot'])) : '';
    $tenSV = isset($_POST['tenSV']) ? $conn->real_escape_string(trim($_POST['tenSV'])) : '';
    $ngaySinh = isset($_POST['ngaySinh']) ? $conn->real_escape_string(trim($_POST['ngaySinh'])) : '';
    $gioiTinh = isset($_POST['gioiTinh']) ? $conn->real_escape_string(trim($_POST['gioiTinh'])) : '';
    $tenLop = isset($_POST['tenLop']) ? $conn->real_escape_string(trim($_POST['tenLop'])) : '';

    // Kiểm tra các trường thông tin bắt buộc
    if (empty($maSV) || empty($hoLot) || empty($tenSV) || empty($ngaySinh) || empty($gioiTinh) || empty($tenLop)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Chuyển đổi định dạng ngày sinh và tính toán tuổi
        $ngaySinhFormatted = date('Y-m-d', strtotime($ngaySinh));
        $tuoi = floor((time() - strtotime($ngaySinh)) / (365 * 24 * 60 * 60));

        // Kiểm tra tuổi sinh viên
        if ($tuoi < 18) {
            $error = "Sinh viên phải đủ 18 tuổi!";
        } else {
            // Kiểm tra trùng mã sinh viên
            $checkSql = "SELECT maSV FROM sinhvien WHERE maSV = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("s", $maSV);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $error = "Mã sinh viên đã tồn tại!";
            } else {
                // Kiểm tra và xử lý upload hình ảnh
                if (isset($_FILES['hinhAnh']) && $_FILES['hinhAnh']['error'] == 0) {
                    $hinhAnh = 'uploads/' . basename($_FILES['hinhAnh']['name']);

                    // Tạo thư mục nếu chưa tồn tại
                    if (!file_exists('uploads')) {
                        mkdir('uploads', 0777, true);
                    }

                    // Di chuyển hình ảnh vào thư mục uploads
                    if (move_uploaded_file($_FILES['hinhAnh']['tmp_name'], $hinhAnh)) {
                        // Chuẩn bị câu lệnh chèn dữ liệu
                        $stmt = $conn->prepare("INSERT INTO sinhvien (maSV, hoLot, tenSV, ngaySinh, gioiTinh, maLop, hinhAnh) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssss", $maSV, $hoLot, $tenSV, $ngaySinhFormatted, $gioiTinh, $tenLop, $hinhAnh);

                        if ($stmt->execute()) {
                            header("Location: sinhvien.php?message=Thêm sinh viên thành công");
                            exit();
                        } else {
                            $error = "Lỗi: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $error = "Lỗi upload hình ảnh!";
                    }
                } else {
                    $error = "Vui lòng chọn hình ảnh!";
                }
            }
            $checkStmt->close(); // Đóng statement kiểm tra mã sinh viên
        }
    }

    // Nếu có lỗi, chuyển hướng với thông báo lỗi
    if (!empty($error)) {
        header("Location: form_sv.php?error=" . urlencode($error));
        exit();
    }
}

// Truy vấn danh sách mã lớp từ cơ sở dữ liệu
$sqlLop = "SELECT maLop, tenLop FROM lophoc";
$resultLop = $conn->query($sqlLop);

// Đảm bảo kết nối cơ sở dữ liệu đóng sau khi không sử dụng nữa
if ($conn && !$conn->connect_errno) {
    $conn->close();
}
?>

<!-- Thông báo thành công -->
<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>