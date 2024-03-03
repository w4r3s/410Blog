<?php
session_start();

require_once '../config.php';
require_once '../connect.php';

// 确保用户已登录
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // 对于 AJAX 请求，返回 JSON 响应
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized access.']);
        exit;
    } else {
        header('Location: ../login.php');
        exit();
    }
}

$targetDirectory = "../imgs";
if (!file_exists($targetDirectory)) {
    mkdir($targetDirectory, 0755, true);
}

$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // 允许的文件扩展名

// 处理图片上传
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $filename = $_FILES['image']['name'];
    $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $uniqueFilename = uniqid() . '.' . $fileExtension; // 生成唯一文件名
        $targetFile = $targetDirectory . '/' . $uniqueFilename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imageUrl = BLOG_URL . str_replace('../', '/', $targetFile);
            
            // AJAX 请求，返回 JSON 响应
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'url' => $imageUrl]);
                exit;
            }
        } else {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'There was an error uploading your image.']);
                exit;
            }
        }
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unsupported file format. Only JPG, JPEG, PNG, and GIF are allowed.']);
            exit;
        }
    }
}


//非Ajax

// 处理图片上传
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $filename = $_FILES['image']['name'];
    $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $uniqueFilename = uniqid() . '.' . $fileExtension; // 生成唯一文件名
        $targetFile = $targetDirectory . '/' . $uniqueFilename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imageUrl = BLOG_URL . str_replace('../', '/', $targetFile);
            echo json_encode(['success' => true, 'url' => $imageUrl]); // 确保这里正确返回图片 URL
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // AJAX 请求，返回 JSON 响应
                echo json_encode(['success' => 'Image successfully uploaded.', 'url' => $imageUrl]);
                exit;
            } else {
                // 常规请求
                $message = "Image successfully uploaded.";
            }
        } else {
            $message = "There was an error uploading your image.";
        }
    } else {
        $message = "Unsupported file format. Only JPG, JPEG, PNG, and GIF are allowed.";
    }
}

// 删除图片逻辑
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $deleteFile = $targetDirectory . '/' . basename($_POST['delete']);
    if (file_exists($deleteFile)) {
        unlink($deleteFile);
        $message = "Image deleted successfully.";
    } else {
        $message = "File does not exist.";
    }
}

// 处理图片上传
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $filename = $_FILES['image']['name'];
    $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $targetFile = $targetDirectory . '/' . basename($filename);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $message = "Image successfully uploaded.";
        } else {
            $message = "There was an error uploading your image.";
        }
    } else {
        $message = "Unsupported file format. Only JPG, JPEG, PNG, and GIF are allowed.";
    }
}

// 获取已上传的图片列表
$images = glob("$targetDirectory/*.{jpg,jpeg,png,gif}", GLOB_BRACE);

// 分页逻辑
$perPage = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = ceil(count($images) / $perPage);
$offset = ($page - 1) * $perPage;
$imagesToShow = array_slice($images, $offset, $perPage);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Hosting - <?php echo BLOG_TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="../styles.css">

    <script>
function togglePreviewImage(src) {
    var preview = document.getElementById('imagePreview');
    // 检查当前预览图像是否已显示且与点击的图像相同
    if (preview.src === src && preview.style.display === 'block') {
        // 如果是，则隐藏预览
        preview.style.display = 'none';
    } else {
        // 否则，显示预览
        preview.src = src;
        preview.style.display = 'block';
    }
}
</script>


</head>
<body>
    <?php include '../menu.php'; ?>
    <main>
        <h1>Image Hosting</h1>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="imghost.php" method="post" enctype="multipart/form-data">
            <label for="image">Select image to upload:</label>
            <input type="file" name="image" id="image">
            <button type="submit">Upload Image</button>
        </form>

        <h2>Uploaded Images</h2>
        <div style="margin-bottom: 20px;">
            <img id="imagePreview" src="" alt="Preview" style="display: none; max-width: 960px; max-height: 400px;">
        </div>
        <?php foreach ($imagesToShow as $image): ?>
            <div class="image-item">

            <a href="javascript:void(0)" onclick="togglePreviewImage('<?php echo htmlspecialchars(str_replace('../', BLOG_URL . '/', $image)); ?>')">
    <?php echo htmlspecialchars(basename($image)); ?>
</a>


                <form action="imghost.php" method="post" style="display: inline;">
                    <input type="hidden" name="delete" value="<?php echo htmlspecialchars(basename($image)); ?>">
                    <button type="submit">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>

        <!-- 分页链接 -->
        <div class="pagination">
            <p>---</p>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="imghost.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </main>
    <?php include '../footer.php'; ?>
</body>
</html>
