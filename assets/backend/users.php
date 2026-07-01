<?php
// ** 1. เริ่มคำสั่ง Output Buffering **
ob_start();

// **คำเตือน:** include('index.php'); ถูกคงไว้ตามความต้องการ
// สมมติว่าไฟล์ index.php มีการเชื่อมต่อ $conn PDO database connection
include('index.php'); 

// --- CONFIGURATION & DATA HANDLING ---
$successMessage = '';
$errorMessage = '';
$users = [];
$action = $_GET['action'] ?? 'list'; // Action หลัก: list, delete
$actionUserId = $_GET['id'] ?? null; // ID สำหรับการลบ (Delete)

// ********** 1. Handle POST Submission (Add/Edit Logic) **********
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_user']) || isset($_POST['edit_user']))) {
    $isEdit = isset($_POST['edit_user']);
    $post_id = $_POST['id'] ?? null;
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $level = $_POST['level'];
    $password = $_POST['password'] ?? '';
    $redirect_message = '';
    
    // Simple Validation
    if (empty($username) || empty($email) || empty($level) || (!$isEdit && empty($password))) {
        $errorMessage = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        try {
            if ($isEdit && $post_id) {
                // UPDATE user
                $sql = "UPDATE users SET username = ?, email = ?, level = ?";
                $params = [$username, $email, $level];
                
                if (!empty($password)) {
                    // *** ข้อควรระวัง: ยังใช้ md5 ตามโค้ดเดิม ควรเปลี่ยนเป็น password_hash() ***
                    $hashed_password = md5($password); 
                    $sql .= ", password = ?";
                    $params[] = $hashed_password;
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $post_id;
                
                $stmt = $conn->prepare($sql);
                $stmt->execute($params);
                $redirect_message = 'แก้ไขผู้ใช้สำเร็จแล้ว!';
                
            } else {
                // ADD new user
                $hashed_password = md5($password); 
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, level) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $level]);
                $redirect_message = 'เพิ่มผู้ใช้สำเร็จแล้ว!';
            }
            
            // ** ล้าง Buffer และ Redirect **
            ob_end_clean(); 
            header("Location: /assets/backend/users.php?page=users&success=" . urlencode($redirect_message));
            exit();

        } catch (PDOException $e) {
            $errorMessage = 'เกิดข้อผิดพลาดของฐานข้อมูล: ' . $e->getMessage();
            $action = 'list';
        }
    }
}

// ********** 2. Handle Delete Action (GET Request) **********
if ($action === 'delete' && $actionUserId) {
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$actionUserId]);
        $redirect_message = 'ลบผู้ใช้สำเร็จแล้ว!';

        // ** ล้าง Buffer และ Redirect **
        ob_end_clean(); 
        header("Location: /assets/backend/users.php?page=users&success=" . urlencode($redirect_message));
        exit();

    } catch (PDOException $e) {
        $errorMessage = 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage();
        $action = 'list';
    }
}


// ********** 3. ดึงข้อมูลผู้ใช้ทั้งหมดสำหรับหน้า List **********
if ($action === 'list') {
    try {
        $stmt = $conn->query("SELECT id, username, email, level, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching users: " . $e->getMessage());
        $errorMessage = 'เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้: ' . $e->getMessage(); 
    }
}


// ********** 4. แสดงข้อความ Success/Error จาก GET parameter **********
if (isset($_GET['success']) && empty($successMessage)) {
    $successMessage = htmlspecialchars($_GET['success']);
}

?>

<div class="p-4 sm:ml-64">
    <div class="p-4 md:p-6 bg-slate-50">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">⚙️ จัดการผู้ใช้</h1>

        <?php if ($successMessage): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $successMessage; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $errorMessage; ?></span>
            </div>
        <?php endif; ?>

        <div class="flex justify-end mb-4">
            <button type="button" data-modal-target="user-modal" data-modal-toggle="user-modal" 
                    data-action="add"
                    class="btn-add-user inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                <i class="fa-solid fa-user-plus mr-2"></i> เพิ่มผู้ใช้ใหม่
            </button>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อผู้ใช้ (Username)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">อีเมล</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สิทธิ์ (Level)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่สร้าง</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo ($user['level'] == 'admin') ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo htmlspecialchars($user['level']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['created_at']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" data-modal-target="user-modal" data-modal-toggle="user-modal" 
                                                data-action="edit" data-user-id="<?php echo $user['id']; ?>"
                                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                data-level="<?php echo htmlspecialchars($user['level']); ?>"
                                                class="btn-edit-user text-indigo-600 hover:text-indigo-900 mr-4">แก้ไข</button>
                                        
                                        <a href="/assets/backend/users.php?page=users&action=delete&id=<?php echo $user['id']; ?>" 
                                            onclick="return confirm('ยืนยันการลบผู้ใช้: <?php echo htmlspecialchars($user['username']); ?>?')" 
                                            class="text-red-600 hover:text-red-900">ลบ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 whitespace-nowrap text-center text-gray-400 text-sm">ยังไม่มีข้อมูลผู้ใช้ในระบบ</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>

<div id="user-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modal-title">
                    เพิ่มผู้ใช้ใหม่
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="user-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <form class="space-y-4" action="/assets/backend/users.php?page=users" method="POST" id="user-form">
                    
                    <input type="hidden" name="id" id="user-id-input">
                    <input type="hidden" id="action-type-marker" value="add"> 
                    
                    <div>
                        <label for="username-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ชื่อผู้ใช้ (Username)</label>
                        <input type="text" name="username" id="username-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>

                    <div>
                        <label for="email-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">อีเมล</label>
                        <input type="email" name="email" id="email-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>

                    <div>
                        <label for="password-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" id="password-label">รหัสผ่าน</label>
                        <input type="password" name="password" id="password-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    <div>
                        <label for="level-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ระดับสิทธิ์ (Level)</label>
                        <select name="level" id="level-input" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            </select>
                    </div>
                    
                    <button type="submit" id="submit-button" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        บันทึกข้อมูล
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('user-modal');
        const form = document.getElementById('user-form');
        const modalTitle = document.getElementById('modal-title');
        
        // Input fields
        const userIdInput = document.getElementById('user-id-input');
        const actionMarker = document.getElementById('action-type-marker'); 
        const usernameInput = document.getElementById('username-input');
        const emailInput = document.getElementById('email-input');
        const levelInput = document.getElementById('level-input');
        const passwordInput = document.getElementById('password-input');
        const passwordLabel = document.getElementById('password-label');

        // *** รายการ ENUM/Level ที่มีในฐานข้อมูล (สามารถเพิ่ม/ลดได้ที่นี่) ***
        const userLevels = ['member', 'admin'];

        // ฟังก์ชันสำหรับรีเซ็ตฟอร์มและสร้าง/กำหนด Options
        function resetModal(currentLevel = 'member') {
            form.reset();
            userIdInput.value = '';
            passwordInput.required = true;
            passwordLabel.textContent = 'รหัสผ่าน';
            actionMarker.value = 'add'; 
            actionMarker.removeAttribute('name'); 
            
            // 1. เคลียร์และสร้าง Options ใหม่
            levelInput.innerHTML = ''; 
            
            userLevels.forEach(level => {
                const option = document.createElement('option');
                option.value = level;
                // ทำให้ตัวแรกเป็นตัวใหญ่: User, Admin
                option.textContent = level.charAt(0).toUpperCase() + level.slice(1); 
                
                // 2. กำหนด Selected ให้ตรงกับค่าปัจจุบันที่รับเข้ามา
                if (level === currentLevel) {
                    option.selected = true;
                }
                
                levelInput.appendChild(option);
            });
        }

        // จัดการเมื่อ Modal เปิด
        document.querySelectorAll('[data-modal-toggle="user-modal"]').forEach(button => {
            button.addEventListener('click', function(event) {
                const action = event.currentTarget.getAttribute('data-action');
                // ดึงค่า level จากปุ่ม หรือใช้ 'user' เป็นค่าเริ่มต้นหากเป็นโหมดเพิ่ม
                const userLevel = event.currentTarget.getAttribute('data-level') || 'user'; 

                // *** เรียก resetModal พร้อมส่งค่า Level ปัจจุบันเข้าไป เพื่อกำหนดค่า selected ***
                resetModal(userLevel); 

                if (action === 'add') {
                    modalTitle.textContent = '➕ เพิ่มผู้ใช้ใหม่';
                    actionMarker.value = 'add'; 
                    passwordInput.required = true;

                } else if (action === 'edit') {
                    modalTitle.textContent = '✏️ แก้ไขผู้ใช้';
                    actionMarker.value = 'edit'; 

                    // เติมข้อมูลในช่องอื่นๆ
                    userIdInput.value = event.currentTarget.getAttribute('data-user-id');
                    usernameInput.value = event.currentTarget.getAttribute('data-username');
                    emailInput.value = event.currentTarget.getAttribute('data-email');
                    
                    // Dropdown ถูกตั้งค่าใน resetModal(userLevel) ไปแล้ว
                    
                    // ปรับฟิลด์ Password
                    passwordInput.required = false;
                    passwordLabel.textContent = 'รหัสผ่าน (กรอกเมื่อต้องการเปลี่ยนเท่านั้น)';
                }
            });
        });

        // การจัดการ name attribute ก่อน submit
        form.addEventListener('submit', function(e) {
            const action = actionMarker.value;
            
            const oldActionInput = form.querySelector('[name="add_user"], [name="edit_user"]');
            if (oldActionInput) {
                 oldActionInput.removeAttribute('name');
            }

            if (action === 'add') {
                actionMarker.name = 'add_user';
            } else if (action === 'edit') {
                actionMarker.name = 'edit_user';
            }
        });

        // Reset name attribute เมื่อ Modal ถูกปิด
        document.querySelectorAll('[data-modal-hide="user-modal"]').forEach(closeButton => {
             closeButton.addEventListener('click', function() {
                 actionMarker.removeAttribute('name'); 
             });
        });
        
        // เรียกใช้ resetModal() ครั้งแรกเมื่อโหลดหน้าเว็บ เพื่อตั้งค่าเริ่มต้น
        resetModal('user'); 
    });
</script>
<?php
// ในกรณีที่เป็น GET request หรือมี Error และไม่ได้เกิด redirect
if (!headers_sent()) {
    ob_end_flush();
}
?>