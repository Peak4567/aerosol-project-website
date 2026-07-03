<?php
include('index.php');


// --- CONFIGURATION & DATA HANDLING ---
// ตั้งค่า uploadsDir ให้ชี้ไปที่โฟลเดอร์ uploads ใน root project
$uploadsDir = '../../uploads';
$settings = [];
$successMessage = '';

// 1. ดึงข้อมูลการตั้งค่าทั้งหมดจาก Database (PDO)
try {
    $stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
    // PDO::FETCH_KEY_PAIR ดึงมาเป็น Array [key => value] ตรงตามที่ต้องการ
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    // ในกรณีที่ตารางยังไม่มี หรือมีปัญหาในการเชื่อมต่อ
    error_log("Error fetching settings: " . $e->getMessage());
}


// 2. Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // **เตรียมคำสั่ง SQL** (ใช้สำหรับ INSERT หรือ UPDATE)
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
        $stmt = $conn->prepare($sql);

        // วนลูปบันทึกข้อมูลทั่วไป
        foreach ($_POST as $key => $value) {
            // ข้าม checkbox กับไฟล์ไปก่อน
            if ($key === 'maintenance_mode' || $key === 'site_logo') continue;
            $stmt->execute([$key, $value, $value]);
        }

        // จัดการ Checkbox
        $maintenance_value = isset($_POST['maintenance_mode']) ? '1' : '0';
        $stmt->execute(['maintenance_mode', $maintenance_value, $maintenance_value]);

        // จัดการการอัปโหลดไฟล์
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            if (!empty($settings['site_logo']) && file_exists($uploadsDir . '/' . $settings['site_logo'])) {
                unlink($uploadsDir . '/' . $settings['site_logo']);
            }
            $fileName = time() . '_' . basename($_FILES['site_logo']['name']);
            $targetPath = $uploadsDir . '/' . $fileName;
            if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $targetPath)) {
                $stmt->execute(['site_logo', $fileName, $fileName]);
            }
        }

        // **สำคัญ:** POST-Redirect-GET เพื่อป้องกันการส่งซ้ำเมื่อ Refresh
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    } catch (PDOException $e) {
        // หากมีปัญหาในการบันทึก
        die("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage());
    }
}

// Reload settings และแสดงข้อความ Success
if (isset($_GET['success'])) {
    // ดึงข้อมูลใหม่หลังจาก Redirect กลับมา เพื่อให้ค่าล่าสุดแสดงในฟอร์ม
    try {
        $stmt_reload = $conn->query("SELECT setting_key, setting_value FROM settings");
        $settings = $stmt_reload->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        // ...
    }
    $successMessage = 'บันทึกการตั้งค่าสำเร็จแล้ว!';
}


// Helper function (ใช้ได้เหมือนเดิม)
function get_setting($key, $default = '')
{
    global $settings;
    return isset($settings[$key]) ? htmlspecialchars($settings[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>

<div class="p-4 sm:ml-64">
    <div class="p-4 md:p-6 bg-slate-50">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">การตั้งค่าเว็บไซต์</h1>

        <?php if ($successMessage): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $successMessage; ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">

                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-700">ทั่วไป (General)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">

                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-600">ชื่อเว็บไซต์</label>
                            <input type="text" name="site_name" id="site_name"
                                value="<?php echo get_setting('site_name'); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="site_logo" class="block text-sm font-medium text-gray-600">โลโก้</label>
                            <input type="file" name="site_logo" id="site_logo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <?php if (get_setting('site_logo')): ?>
                                <img src="../../uploads/<?php echo get_setting('site_logo'); ?>" alt="Current Logo" class="mt-3 h-16 rounded">
                            <?php endif; ?>
                        </div>

                        <div class="md:col-span-2">
                            <label for="site_description" class="block text-sm font-medium text-gray-600">คำอธิบายเว็บไซต์</slabel>
                                <textarea name="site_description" id="site_description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo get_setting('site_description'); ?></textarea>
                        </div>

                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-700">ขั้นสูง (Advanced)</h2>
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="maintenance_mode" value="1"
                                class="rounded h-4 w-4 text-indigo-600 border-gray-300"
                                <?php echo get_setting('maintenance_mode') == '1' ? 'checked' : ''; ?>>
                            <span class="ml-2 text-sm text-gray-600">เปิดโหมดซ่อมบำรุง (Maintenance Mode)</span>
                        </label>
                    </div>
                    <br>
                    <a href="/simulator.php" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        สุ่มจําลองค่า
                    </a>
                </div>

                <div class="flex justify-end mt-8 border-t border-gray-200 pt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
        </div>
        </form>
    </div>
</div>
</div>