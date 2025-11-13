<?php
// actions/bulk_upload_images_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if user is admin
if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Admin privileges required']);
    exit;
}

// Check if files were uploaded
// When using FormData with product_images[], PHP receives files in $_FILES['product_images']
// But the structure depends on how many files were sent
if (!isset($_FILES['product_images'])) {
    // Debug: show what we received
    $debug = array('POST' => array_keys($_POST), 'FILES' => array_keys($_FILES));
    echo json_encode(['success' => false, 'message' => 'No files received', 'debug' => $debug]);
    exit;
}

$user_id = get_user_id();
$product_id = intval($_POST['product_id'] ?? 0);

if (!$product_id || $product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Uploads directory is a co-subdirectory on the server (assumed to exist)
// From actions/ directory: go up to register_sample_one/, then up to parent, then into uploads/
$base_upload_dir = realpath(__DIR__ . '/../../uploads');

if (!$base_upload_dir || !is_dir($base_upload_dir)) {
    echo json_encode(['success' => false, 'message' => 'Uploads directory not found at expected location']);
    exit;
}

// Directory structure: uploads/u{user_id}/p{product_id}/
$upload_dir = $base_upload_dir . '/u' . $user_id;
$product_dir = $upload_dir . '/p' . $product_id;

// Create subdirectories if they don't exist (base uploads directory is assumed to exist)
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create user directory: ' . $upload_dir]);
        exit;
    }
}

if (!is_dir($product_dir)) {
    if (!mkdir($product_dir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create product directory: ' . $product_dir]);
        exit;
    }
}


// Process multiple files
// When using multiple file input with name="product_images[]", PHP restructures $_FILES array
// Check different possible structures
$files = null;
if (isset($_FILES['product_images'])) {
    // Standard multiple file structure (when using name="product_images[]")
    if (is_array($_FILES['product_images']['name'])) {
        $files = $_FILES['product_images'];
    }
    // Single file or alternative structure
    else if (!empty($_FILES['product_images']['name'])) {
        // Convert single file to array format for consistent processing
        $files = array(
            'name' => array($_FILES['product_images']['name']),
            'type' => array($_FILES['product_images']['type']),
            'tmp_name' => array($_FILES['product_images']['tmp_name']),
            'error' => array($_FILES['product_images']['error']),
            'size' => array($_FILES['product_images']['size'])
        );
    }
}

if (!$files || empty($files['name']) || (is_array($files['name']) && count($files['name']) == 0)) {
    // Better error message with debug info
    $error_msg = 'No files uploaded or invalid file format.';
    if (isset($_FILES['product_images'])) {
        $error_msg .= ' Received: ' . (is_array($_FILES['product_images']['name']) ? count($_FILES['product_images']['name']) : 1) . ' file(s)';
        // Log the structure for debugging
        error_log('Bulk upload debug: ' . json_encode($_FILES['product_images']));
    }
    echo json_encode(['success' => false, 'message' => $error_msg]);
    exit;
}

$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB per file
$uploaded_images = [];
$errors = [];

// Count number of files - ensure it's an array
if (!is_array($files['name'])) {
    echo json_encode(['success' => false, 'message' => 'Files array structure error']);
    exit;
}

$file_count = count($files['name']);

// Process each file
for ($i = 0; $i < $file_count; $i++) {
    try {
        // Skip empty file slots
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $error_code = $files['error'][$i];
                $error_msg = 'File ' . ($i + 1);
                switch ($error_code) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $error_msg .= ': File too large';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $error_msg .= ': File partially uploaded';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $error_msg .= ': Missing temporary folder';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $error_msg .= ': Failed to write file';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $error_msg .= ': File upload blocked';
                        break;
                    default:
                        $error_msg .= ': Upload error code ' . $error_code;
                }
                $errors[] = $error_msg;
            }
            continue;
        }
    } catch (Exception $e) {
        $errors[] = 'File ' . ($i + 1) . ': Processing error - ' . $e->getMessage();
        continue;
    }
    
    // Validate file type
    if (!in_array($files['type'][$i], $allowed_types)) {
        $errors[] = $files['name'][$i] . ': Invalid file type. Only JPEG, PNG, and GIF allowed';
        continue;
    }
    
    // Validate file size
    if ($files['size'][$i] > $max_size) {
        $errors[] = $files['name'][$i] . ': File size exceeds 5MB limit';
        continue;
    }
    
    // Generate unique filename
    $file_extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
    $timestamp = time() . '_' . $i;
    $new_filename = 'image_' . $timestamp . '.' . $file_extension;
    $upload_path = $product_dir . '/' . $new_filename;
    
    // Move uploaded file
    if (!move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
        $errors[] = $files['name'][$i] . ': Failed to upload';
        continue;
    }
    
    // Store relative path
    $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $new_filename;
    $uploaded_images[] = [
        'filename' => $files['name'][$i],
        'path' => $relative_path,
        'size' => $files['size'][$i]
    ];
}

// Return results
if (empty($uploaded_images)) {
    echo json_encode([
        'success' => false,
        'message' => 'No images were uploaded successfully',
        'errors' => $errors
    ]);
    exit;
}

// Update product_image field in database with first uploaded image (for all cases)
require_once __DIR__ . '/../settings/db_class.php';
if (!empty($uploaded_images)) {
    try {
        $db = new Database();
        $first_image_path = $uploaded_images[0]['path'];
        $sql = "UPDATE products SET product_image = ? WHERE product_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('si', $first_image_path, $product_id);
        $stmt->execute();
    } catch (Exception $e) {
        // Log error but don't fail the upload
        error_log('Error updating product_image: ' . $e->getMessage());
    }
}

// If some succeeded and some failed, return partial success
if (!empty($errors)) {
    echo json_encode([
        'success' => true,
        'message' => count($uploaded_images) . ' image(s) uploaded successfully. ' . count($errors) . ' error(s)',
        'images' => $uploaded_images,
        'errors' => $errors,
        'partial' => true,
        'first_image_path' => !empty($uploaded_images) ? $uploaded_images[0]['path'] : null
    ]);
    exit;
}

// All successful
try {
    echo json_encode([
        'success' => true,
        'message' => count($uploaded_images) . ' image(s) uploaded successfully',
        'images' => $uploaded_images,
        'count' => count($uploaded_images),
        'first_image_path' => !empty($uploaded_images) ? $uploaded_images[0]['path'] : null
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error encoding response: ' . $e->getMessage(),
        'uploaded_count' => count($uploaded_images)
    ]);
}
?>

