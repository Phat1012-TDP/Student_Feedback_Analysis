<?php

// Nhận giá trị Tên giảng viên (full_name) và Tên học phần (subject) từ URL
$ten_giang_vien = isset($_GET['full_name']) ? $_GET['full_name'] : '';
$ten_hoc_phan = isset($_GET['subject']) ? $_GET['subject'] : '';
session_start();
$nguoi_danh_gia = $_SESSION['full_name'];

// Kết nối cơ sở dữ liệu
include '../Models/connect_pdo.php';

$idtoconnect = 1; // ID của nhóm câu hỏi cần lấy
$query = "SELECT q1, q2, q3, q4, q5, q6, q7, q8, q9, q10
          FROM question WHERE idtoconnect = :idtoconnect";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':idtoconnect', $idtoconnect, PDO::PARAM_INT);
$stmt->execute();
$questions = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$questions) {
    die("Không tìm thấy câu hỏi.");
}

// Kiểm tra nếu đã đánh giá hay chưa
$checkQuery = "SELECT COUNT(*) FROM result 
               WHERE ten_giang_vien = :ten_giang_vien 
               AND ten_hoc_phan = :ten_hoc_phan 
               AND nguoi_danh_gia = :nguoi_danh_gia";
$checkStmt = $pdo->prepare($checkQuery);
$checkStmt->bindParam(':ten_giang_vien', $ten_giang_vien, PDO::PARAM_STR);
$checkStmt->bindParam(':ten_hoc_phan', $ten_hoc_phan, PDO::PARAM_STR);
$checkStmt->bindParam(':nguoi_danh_gia', $_SESSION['full_name'], PDO::PARAM_STR);
$checkStmt->execute();
$checkResult = $checkStmt->fetchColumn();

$resultData = [];

if ($checkResult > 0) {
    // Nếu đã đánh giá rồi, thông báo
    $success_message = "Bạn đã đánh giá học phần này rồi. ";
    $formSubmitted = true;
     // Nếu đã đánh giá, lấy dữ liệu đánh giá từ bảng 'result'
    $fetchResultQuery = "SELECT cau_hoi_1, cau_hoi_2, cau_hoi_3, cau_hoi_4, cau_hoi_5, cau_hoi_6, cau_hoi_7, cau_hoi_8, cau_hoi_9, cau_hoi_10 
                         FROM result 
                         WHERE ten_giang_vien = :ten_giang_vien 
                         AND ten_hoc_phan = :ten_hoc_phan 
                         AND nguoi_danh_gia = :nguoi_danh_gia";
    $fetchStmt = $pdo->prepare($fetchResultQuery);
    $fetchStmt->bindParam(':ten_giang_vien', $ten_giang_vien, PDO::PARAM_STR);
    $fetchStmt->bindParam(':ten_hoc_phan', $ten_hoc_phan, PDO::PARAM_STR);
    $fetchStmt->bindParam(':nguoi_danh_gia', $nguoi_danh_gia, PDO::PARAM_STR);
    $fetchStmt->execute();
    $resultData = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    if ($resultData) {
        $formSubmitted = true;
    } else {
        $error_message = "Không tìm thấy dữ liệu đánh giá.";
    }
} else {

    // Biến để kiểm tra form đã được gửi
    $formSubmitted = false;

    // Kiểm tra nếu form đã được gửi
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy giá trị từ form
        $ten_giang_vien = $_POST['ten_giang_vien']; // Bạn cần thêm trường này trong form để lấy tên giảng viên
        $ten_hoc_phan = $_POST['ten_hoc_phan']; // Tương tự, thêm trường cho tên học phần
        $nguoi_danh_gia = $_POST['nguoi_danh_gia']; // Trường cho tên người đánh giá

        // Lấy các câu trả lời từ form
        $cau_hoi_1 = $_POST['q1'];
        $cau_hoi_2 = $_POST['q2'];
        $cau_hoi_3 = $_POST['q3'];
        $cau_hoi_4 = $_POST['q4'];
        $cau_hoi_5 = $_POST['q5'];
        $cau_hoi_6 = $_POST['q6'];
        $cau_hoi_7 = $_POST['q7'];
        $cau_hoi_8 = $_POST['q8'];
        $cau_hoi_9 = $_POST['q9'];
        $cau_hoi_10 = $_POST['q10'];

        // Câu lệnh SQL để chèn kết quả vào bảng 'result'
        $query = "INSERT INTO result (ten_giang_vien, ten_hoc_phan, nguoi_danh_gia, cau_hoi_1, cau_hoi_2, cau_hoi_3, cau_hoi_4, cau_hoi_5, cau_hoi_6, cau_hoi_7, cau_hoi_8, cau_hoi_9, cau_hoi_10) 
                  VALUES (:ten_giang_vien, :ten_hoc_phan, :nguoi_danh_gia, :cau_hoi_1, :cau_hoi_2, :cau_hoi_3, :cau_hoi_4, :cau_hoi_5, :cau_hoi_6, :cau_hoi_7, :cau_hoi_8, :cau_hoi_9, :cau_hoi_10)";

        // Chuẩn bị câu lệnh
        $stmt = $pdo->prepare($query);

        // Liên kết các tham số
        $stmt->bindParam(':ten_giang_vien', $ten_giang_vien, PDO::PARAM_STR);
        $stmt->bindParam(':ten_hoc_phan', $ten_hoc_phan, PDO::PARAM_STR);
        $stmt->bindParam(':nguoi_danh_gia', $nguoi_danh_gia, PDO::PARAM_STR);
        $stmt->bindParam(':cau_hoi_1', $cau_hoi_1, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_2', $cau_hoi_2, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_3', $cau_hoi_3, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_4', $cau_hoi_4, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_5', $cau_hoi_5, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_6', $cau_hoi_6, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_7', $cau_hoi_7, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_8', $cau_hoi_8, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_9', $cau_hoi_9, PDO::PARAM_INT);
        $stmt->bindParam(':cau_hoi_10', $cau_hoi_10, PDO::PARAM_INT);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            $success_message = "Đánh giá của bạn đã được gửi thành công! ";
            $formSubmitted = true;
        } else {
            $error_message = "Có lỗi xảy ra trong quá trình gửi đánh giá.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá học phần</title>
    <link rel="icon" href="../../public/images/Logo.png" type="image/png">
    <link rel="stylesheet" href="../../app/Views/css/surveytable.css">
</head>
<body>
    <div class="header">
        <img alt="Logo" height="50" src="../../public/images/Logo.png" width="50" />
        <h1> HỆ THỐNG PHÂN TÍCH PHẢN HỒI NGƯỜI HỌC </h1>
    </div>
    
    <div class="navigation">
        <ul class="nav">
            <li><a href="../Views/html/studentHomePage.html">Trang chủ</a></li>
            <li><a > Thực hiện khảo sát</a></li>
            <?php if (isset($nguoi_danh_gia)): ?>
            <li class="greeting">Chào <?php echo htmlspecialchars($nguoi_danh_gia); ?></li>
            <?php endif; ?>
            <li><a href="../../index.html" >Đăng xuất</a></li>
        </ul>
    </div>
     <!-- //Hiển thị thông báo lỗi nếu có -->
            <?php if (!empty($error_message)): ?>
            <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
            <?php endif; ?>
     <!-- //Hiển thị thông báo lỗi nếu có -->
                <?php if (!empty($success_message)): ?>
                <div style="color: green; text-align: center;"> 
                <span><?php echo $success_message; ?></span>
                <a href="../Views/html/studentHomePage.html"
                style="text-align: center; color: #7C3CB4;"> Quay lại</a>
                </div>
                <?php endif; ?>

<div class="container">
  <h2><i class="fas fa-clipboard-list"></i> Đánh giá lớp học phần</h2>

  <form action="surveytable.php" method="POST">
    <!-- Thông tin nhập liệu -->
    <div class="form-row">
      <label for="ten_giang_vien">Tên Giảng Viên:</label>
      <input type="text" id="ten_giang_vien" name="ten_giang_vien" 
             value="<?php echo htmlspecialchars($ten_giang_vien); ?>" readonly>

      <label for="ten_hoc_phan">Tên Học Phần:</label>
      <input type="text" id="ten_hoc_phan" name="ten_hoc_phan" 
             value="<?php echo htmlspecialchars($ten_hoc_phan); ?>" readonly>

      <label for="nguoi_danh_gia">Người Đánh Giá:</label>
      <input type="text" id="nguoi_danh_gia" name="nguoi_danh_gia" 
             value="<?php echo isset($nguoi_danh_gia) ? htmlspecialchars($_SESSION['full_name']) : ''; ?>" readonly>
    </div>

    <!-- Các câu hỏi -->
    <?php 
    $index = 1;
    foreach ($questions as $key => $question) { 
      // Lấy giá trị đã lưu (nếu có) từ cơ sở dữ liệu
      $value = isset($resultData['cau_hoi_' . $index]) ? $resultData['cau_hoi_' . $index] : ''; 
    ?>
    <div class="question">
      <p class= "ques" ><strong>Câu hỏi <?php echo $index; ?>:</strong> <?php echo htmlspecialchars($question); ?></p>
      <div class="options">
        <label class="option-<?php echo $value; ?>">
          <input type="radio" name="q<?php echo $index; ?>" value="5" required
                 <?php echo $value == 5 ? 'checked' : ''; ?>> Rất đồng ý
        </label><br>

        <label class="<?php echo $value == 4 ? 'bold-label' : ''; ?>">
          <input type="radio" name="q<?php echo $index; ?>" value="4" required
                 <?php echo $value == 4 ? 'checked' : ''; ?>> Đồng ý
        </label><br>

        <label class="<?php echo $value == 3 ? 'bold-label' : ''; ?>">
          <input type="radio" name="q<?php echo $index; ?>" value="3" required
                 <?php echo $value == 3 ? 'checked' : ''; ?>> Đồng ý một phần
        </label><br>

        <label class="<?php echo $value == 2 ? 'bold-label' : ''; ?>">
          <input type="radio" name="q<?php echo $index; ?>" value="2" required
                 <?php echo $value == 2 ? 'checked' : ''; ?>> Không đồng ý
        </label><br>

        <label class="<?php echo $value == 1 ? 'bold-label' : ''; ?>">
          <input type="radio" name="q<?php echo $index; ?>" value="1" required
                 <?php echo $value == 1 ? 'checked' : ''; ?>> Rất không đồng ý
        </label><br>
      </div>
    </div>

    <?php 
      $index++; 
    } 
    ?>

    <!-- Nút gửi -->
    <div class="submit-section">
      <?php if (!$formSubmitted) { ?>
        <button type="submit">Gửi đánh giá</button>
      <?php } ?>
    </div>

  </form>

</div>

</body>

<footer>
    <p>
      Liên hệ <br>
      <table id="contact">
        <tr>
          <th>Nguyễn Văn Huy</th>
          <th>Lê Quang Hoàng</th>
          <th>Trần Đức Phát</th>
        </tr>
        <tr>
          <td>21KTMT</td>
          <td>21KTMT</td>
          <td>21KTMT</td>
        </tr>
        <tr>
          <td>106210218@sv1.dut.udn.vn</td>
          <td>106210214@sv1.dut.udn.vn</td>
          <td>106210225@sv1.dut.udn.vn</td>
        </tr>
      </table>
    </p>
</footer>

</html>