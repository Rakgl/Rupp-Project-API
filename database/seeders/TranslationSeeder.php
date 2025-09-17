<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all translations by merging the data from all dedicated functions.
        $translations = $this->getAllTranslations();

        // Loop through each translation and create or update it in the database.
        foreach ($translations as $translation) {
            Translation::updateOrCreate(
                [
                    'key' => $translation['key'], // The unique key to find the record
                    'platform' => $translation['platform'] ?? 'ADMIN', // Default to 'ADMIN' if not specified
                ],
                [
                    // The values to insert or update
                    'value' => json_encode([
                        'en' => $translation['en'],
                        'km' => $translation['km'],
                        'zh' => $translation['zh'] ?? $translation['en'], // Optional Chinese translation
                    ]),
                    'status' => $translation['status'] ?? 'ACTIVE', // Default to 'ACTIVE' if not specified
                ]
            );
        }
    }

    private function getAllTranslations(): array
    {
        return array_merge(
            $this->getGlobalTranslations(),
            $this->getNavMenuTranslations(),
            $this->getProfileTranslations(),
            $this->getSettingTranslations(),
            $this->getLanguageTranslations(),
            $this->getAccountTranslations(),
            $this->getAppearanceTranslations(),
            $this->getRolesManagementTranslations(),
            $this->getUserManagementTranslations(),
            $this->getPaginationTranslations(),
            $this->getTranslationManagementTranslations(),
        );
    }

    private function getGlobalTranslations(): array
    {
        return [
            ['key' => 'account_settings', 'en' => 'Account Settings', 'km' => 'ការកំណត់គណនី', 'zh' => '账户设置', 'platform' => 'MOBILE', 'status' => 'ACTIVE'],
            ['key' => 'welcome', 'en' => 'Welcome to our application!', 'km' => 'សូមស្វាគមន៍មកកាន់កម្មវិធីរបស់យើង!', 'zh' => '欢迎使用我们的应用程序！'],
            ['key' => 'hello_name', 'en' => 'Hello, {name}!', 'km' => 'ជំរាបសួរ, {name}!', 'zh' => '你好，{name}！'],
            ['key' => 'select_language', 'en' => 'Select Language', 'km' => 'ជ្រើសរើសភាសា', 'zh' => '选择语言'],
            ['key' => 'dashboard', 'en' => 'Dashboard', 'km' => 'ផ្ទាំងគ្រប់គ្រង', 'zh' => '仪表板'],
            ['key' => 'users', 'en' => 'Users', 'km' => 'អ្នកប្រើប្រាស់', 'zh' => '用户'],
            ['key' => 'loading', 'en' => 'Loading...', 'km' => 'កំពុងផ្ទុក...', 'zh' => '正在加载...'],
            ['key' => 'error', 'en' => 'An error occurred.', 'km' => 'មានកំហុសកើតឡើង។', 'zh' => '发生错误。'],
            ['key' => 'success', 'en' => 'Success!', 'km' => 'ជោគជ័យ!', 'zh' => '成功！'],
            ['key' => 'actions.edit', 'en' => 'Edit', 'km' => 'កែសម្រួល', 'zh' => '编辑'],
            ['key' => 'actions.delete', 'en' => 'Delete', 'km' => 'លុប', 'zh' => '删除'],
            ['key' => 'themeModal.title', 'en' => 'Customize', 'km' => 'ប្ដូរតាមបំណង', 'zh' => '自定义'],
            ['key' => 'themeModal.description', 'en' => 'Customize & Preview in Real Time', 'km' => 'ប្ដូរតាមបំណង និងមើលជាមុនក្នុងពេលជាក់ស្ដែង', 'zh' => '实时自定义和预览'],

            // Menu Items
            ['key' => 'sidebar.menu.profile', 'en' => 'Profile', 'km' => 'ប្រវត្តិរូប', 'zh' => '个人资料'],
            ['key' => 'sidebar.menu.account', 'en' => 'Account', 'km' => 'គណនី', 'zh' => '帐户'],
            ['key' => 'sidebar.menu.settings', 'en' => 'Settings', 'km' => 'ការកំណត់', 'zh' => '设置'],
            ['key' => 'sidebar.menu.theme', 'en' => 'Theme', 'km' => 'ផ្ទៃ', 'zh' => '主题'],
            ['key' => 'sidebar.menu.logout', 'en' => 'Log out', 'km' => 'ចាកចេញ', 'zh' => '登出'],
        ];
    }

    private function getProfileTranslations(): array
    {
        return [
            ['key' => 'profile.loadingText', 'en' => 'Loading profile data...', 'km' => 'កំពុងផ្ទុកទិន្នន័យប្រវត្តិរូប...', 'zh' => '正在加载个人资料数据...'],

            // Profile Picture Section
            ['key' => 'profile.picture.label', 'en' => 'Profile Picture', 'km' => 'រូបភាពប្រវត្តិរូប', 'zh' => '个人资料图片'],
            ['key' => 'profile.picture.alt', 'en' => '{name}\'s profile picture', 'km' => 'រូបភាពប្រវត្តិរូបរបស់ {name}', 'zh' => '{name}的个人资料图片'],
            ['key' => 'profile.picture.changeAppearanceButton', 'en' => 'Change Appearance', 'km' => 'ផ្លាស់ប្តូររូបរាង', 'zh' => '更改外观'],
            ['key' => 'profile.picture.setAppearanceButton', 'en' => 'Set Appearance', 'km' => 'កំណត់រូបរាង', 'zh' => '设置外观'],
            ['key' => 'profile.picture.removeImageButton', 'en' => 'Remove Image', 'km' => 'លុបរូបភាព', 'zh' => '移除图片'],
            ['key' => 'profile.picture.uploadTitle', 'en' => 'Upload Custom Image', 'km' => 'បង្ហោះរូបភាពផ្ទាល់ខ្លួន', 'zh' => '上传自定义图片'],
            ['key' => 'profile.picture.processingButton', 'en' => 'Processing...', 'km' => 'កំពុងដំណើរការ...', 'zh' => '处理中...'],
            ['key' => 'profile.picture.chooseFileButton', 'en' => 'Choose File', 'km' => 'ជ្រើសរើសឯកសារ', 'zh' => '选择文件'],
            ['key' => 'profile.picture.fileRequirements', 'en' => 'Supports JPG, PNG, GIF, WEBP. Max file size: 5MB.', 'km' => 'គាំទ្រ JPG, PNG, GIF, WEBP។ ទំហំឯកសារអតិបរមា៖ 5MB។', 'zh' => '支持 JPG、PNG、GIF、WEBP。最大文件大小：5MB。'],
            ['key' => 'profile.picture.selectColorTitle', 'en' => 'Or Select an Initials Background Color', 'km' => 'ឬជ្រើសរើសពណ៌ផ្ទៃខាងក្រោយសម្រាប់អក្សរកាត់', 'zh' => '或为首字母选择背景颜色'],
            ['key' => 'profile.picture.selectColorAria', 'en' => 'Select {colorName} background', 'km' => 'ជ្រើសរើសផ្ទៃខាងក្រោយពណ៌ {colorName}', 'zh' => '选择 {colorName} 背景'],
            ['key' => 'profile.picture.description', 'en' => 'Upload an image or choose a background color for your initials.', 'km' => 'បង្ហោះរូបភាព ឬជ្រើសរើសពណ៌ផ្ទៃខាងក្រោយសម្រាប់អក្សរកាត់របស់អ្នក។', 'zh' => '上传图片或为您的首字母选择背景颜色。'],

            // Form Fields
            ['key' => 'profile.name.label', 'en' => 'Display Name', 'km' => 'ឈ្មោះបង្ហាញ', 'zh' => '显示名称'],
            ['key' => 'profile.name.placeholder', 'en' => 'Your display name', 'km' => 'ឈ្មោះបង្ហាញរបស់អ្នក', 'zh' => '您的显示名称'],
            ['key' => 'profile.name.description', 'en' => 'This is your public display name. (Used for initials if no image)', 'km' => 'នេះគឺជាឈ្មោះបង្ហាញជាសាធារណៈរបស់អ្នក។ (ប្រើសម្រាប់អក្សរកាត់ប្រសិនបើគ្មានរូបភាព)', 'zh' => '这是您的公开显示名称。（如果没有图片，将用于首字母）'],
            ['key' => 'profile.email.label', 'en' => 'Email', 'km' => 'អ៊ីមែល', 'zh' => '电子邮件'],
            ['key' => 'profile.email.placeholder', 'en' => 'you@example.com', 'km' => 'you@example.com', 'zh' => 'you@example.com'],
            ['key' => 'profile.email.description', 'en' => 'Your primary email address.', 'km' => 'អាសយដ្ឋានអ៊ីមែលចម្បងរបស់អ្នក។', 'zh' => '您的主要电子邮件地址。'],
            ['key' => 'profile.language.label', 'en' => 'Preferred Language', 'km' => 'ភាសាដែលពេញចិត្ត', 'zh' => '首选语言'],
            ['key' => 'profile.language.placeholder', 'en' => 'Select a language', 'km' => 'ជ្រើសរើសភាសា', 'zh' => '选择一种语言'],
            ['key' => 'profile.language.description', 'en' => 'Choose your preferred language for the interface.', 'km' => 'ជ្រើសរើសភាសាដែលអ្នកពេញចិត្តសម្រាប់កម្មវិធី។', 'zh' => '选择您界面的首选语言。'],

            // Buttons
            ['key' => 'profile.updateButton.update', 'en' => 'Update Profile', 'km' => 'ធ្វើបច្ចុប្បន្នភាពប្រវត្តិរូប', 'zh' => '更新个人资料'],
            ['key' => 'profile.updateButton.updating', 'en' => 'Updating...', 'km' => 'កំពុងធ្វើបច្ចុប្បន្នភាព...', 'zh' => '正在更新...'],
            ['key' => 'profile.resetButton', 'en' => 'Reset Form', 'km' => 'កំណត់ទម្រង់ឡើងវិញ', 'zh' => '重置表单'],

            // Toasts & Notifications
            ['key' => 'profile.toast.invalidFile.title', 'en' => 'Invalid File', 'km' => 'ឯកសារមិនត្រឹមត្រូវ', 'zh' => '无效文件'],
            ['key' => 'profile.toast.invalidFile.description', 'en' => 'Please select an image file.', 'km' => 'សូមជ្រើសរើសឯកសាររូបភាព។', 'zh' => '请选择一个图片文件。'],
            ['key' => 'profile.toast.fileTooLarge.title', 'en' => 'File Too Large', 'km' => 'ឯកសារធំពេក', 'zh' => '文件太大'],
            ['key' => 'profile.toast.fileTooLarge.description', 'en' => 'Please select an image smaller than 5MB.', 'km' => 'សូមជ្រើសរើសរូបភាពដែលមានទំហំតូចជាង 5MB។', 'zh' => '请选择小于5MB的图片。'],
            ['key' => 'profile.toast.imageReady.title', 'en' => 'Image Ready', 'km' => 'រូបភាពរួចរាល់', 'zh' => '图片已准备好'],
            ['key' => 'profile.toast.imageReady.description', 'en' => 'Image selected and ready for profile update.', 'km' => 'រូបភាពបានជ្រើសរើស និងរៀបចំរួចរាល់សម្រាប់ធ្វើបច្ចុប្បន្នភាពប្រវត្តិរូប។', 'zh' => '图片已选择并准备好用于更新个人资料。'],
            ['key' => 'profile.toast.readError.title', 'en' => 'Read Error', 'km' => 'កំហុសក្នុងការអាន', 'zh' => '读取错误'],
            ['key' => 'profile.toast.readError.description', 'en' => 'Could not read the selected file.', 'km' => 'មិនអាចអានឯកសារដែលបានជ្រើសរើសបានទេ។', 'zh' => '无法读取所选文件。'],
            ['key' => 'profile.toast.processingFailed.title', 'en' => 'Processing Failed', 'km' => 'ដំណើរការបរាជ័យ', 'zh' => '处理失败'],
            ['key' => 'profile.toast.processingFailed.description', 'en' => 'Failed to process image.', 'km' => 'បរាជ័យក្នុងការដំណើរការរូបភាព។', 'zh' => '处理图片失败。'],
            ['key' => 'profile.toast.fallbackColorSelected.title', 'en' => 'Fallback Color Selected', 'km' => 'បានជ្រើសរើសពណ៌ជំនួស', 'zh' => '已选择备用颜色'],
            ['key' => 'profile.toast.fallbackColorSelected.description', 'en' => '{colorName} color chosen for initials.', 'km' => 'បានជ្រើសរើសពណ៌ {colorName} សម្រាប់អក្សរកាត់។', 'zh' => '已为首字母选择 {colorName} 颜色。'],
            ['key' => 'profile.toast.avatarRemoved.title', 'en' => 'Avatar Image Removed', 'km' => 'បានលុបរូបភាពតំណាង', 'zh' => '头像图片已移除'],
            ['key' => 'profile.toast.avatarRemoved.description', 'en' => 'Fallback color or default will be used for initials.', 'km' => 'ពណ៌ជំនួសឬលំនាំដើមនឹងត្រូវបានប្រើសម្រាប់អក្សរកាត់។', 'zh' => '将使用备用颜色或默认颜色作为首字母。'],
            ['key' => 'profile.toast.loadError.title', 'en' => 'Load Error', 'km' => 'កំហុសក្នុងការផ្ទុក', 'zh' => '加载错误'],
            ['key' => 'profile.toast.loadError.defaultMessage', 'en' => 'Failed to load user data.', 'km' => 'បរាជ័យក្នុងការផ្ទុកទិន្នន័យអ្នកប្រើប្រាស់។', 'zh' => '加载用户数据失败。'],
            ['key' => 'profile.toast.unexpectedError', 'en' => 'An unexpected error occurred.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើង។', 'zh' => '发生意外错误。'],
            ['key' => 'profile.toast.apiError.title', 'en' => 'API Error', 'km' => 'កំហុស API', 'zh' => 'API 错误'],
            ['key' => 'profile.toast.missingUserId', 'en' => 'User ID is missing.', 'km' => 'លេខសម្គាល់អ្នកប្រើប្រាស់បានបាត់។', 'zh' => '缺少用户ID。'],
            ['key' => 'profile.toast.updateSuccess', 'en' => 'Profile updated successfully!', 'km' => 'បានធ្វើបច្ចុប្បន្នភាពប្រវត្តិរូបដោយជោគជ័យ!', 'zh' => '个人资料更新成功！'],
            ['key' => 'profile.toast.updateFailed.title', 'en' => 'Update Failed', 'km' => 'ការធ្វើបច្ចុប្បន្នភាពបានបរាជ័យ', 'zh' => '更新失败'],
            ['key' => 'profile.toast.updateFailed.defaultMessage', 'en' => 'Failed to update profile.', 'km' => 'បរាជ័យក្នុងការធ្វើបច្ចុប្បន្នភាពប្រវត្តិរូប។', 'zh' => '更新个人资料失败。'],

            // Validation Messages
            ['key' => 'profile.validation.nameMinLength', 'en' => 'Display name must be at least 2 characters.', 'km' => 'ឈ្មោះបង្ហាញត្រូវតែមានយ៉ាងហោចណាស់ 2 តួអក្សរ។', 'zh' => '显示名称必须至少包含2个字符。'],
            ['key' => 'profile.validation.nameMaxLength', 'en' => 'Display name must not be longer than 50 characters.', 'km' => 'ឈ្មោះបង្ហាញមិនត្រូវវែងជាង 50 តួអក្សរទេ។', 'zh' => '显示名称不得超过50个字符。'],
            ['key' => 'profile.validation.usernameMinLength', 'en' => 'Login username must be at least 2 characters.', 'km' => 'ឈ្មោះអ្នកប្រើប្រាស់សម្រាប់ចូលត្រូវតែមានយ៉ាងហោចណាស់ 2 តួអក្សរ។', 'zh' => '登录用户名必须至少包含2个字符。'],
            ['key' => 'profile.validation.usernameMaxLength', 'en' => 'Login username must not be longer than 30 characters.', 'km' => 'ឈ្មោះអ្នកប្រើប្រាស់សម្រាប់ចូលមិនត្រូវវែងជាង 30 តួអក្សរទេ។', 'zh' => '登录用户名不得超过30个字符。'],
            ['key' => 'profile.validation.emailInvalid', 'en' => 'Please enter a valid email address.', 'km' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែលដែលត្រឹមត្រូវ។', 'zh' => '请输入有效的电子邮件地址。'],

        ];
    }


    private function getNavMenuTranslations(): array
    {
        return [
            ['key' => 'nav.core_administration', 'en' => 'Core Administration', 'km' => 'ការកំណត់គណនី', 'zh' => '核心管理'],
            ['key' => 'nav.authentication', 'en' => 'Authentication', 'km' => 'ការផ្ទៀងផ្ទាត់ភាពត្រឹមត្រូវ', 'zh' => '身份验证'],
            ['key' => 'nav.role_permission', 'en' => 'Role & Permission', 'km' => 'តួនាទី និងសិទ្ធិ', 'zh' => '角色与权限'],
            ['key' => 'nav.system_users', 'en' => 'System Users', 'km' => 'អ្នកប្រើប្រាស់ប្រព័ន្ធ', 'zh' => '系统用户'],
            ['key' => 'nav.platform_settings', 'en' => 'Platform Settings', 'km' => 'ការកំណត់វេទិកា', 'zh' => '平台设置'],
            ['key' => 'nav.general_settings', 'en' => 'General Settings', 'km' => 'ការកំណត់ទូទៅ', 'zh' => '通用设置'],
            ['key' => 'nav.items.account_recovery', 'en' => 'Account Recovery', 'km' => 'ការស្តារគណនី', 'zh' => '账户恢复'],
            ['key' => 'nav.payment_gateways', 'en' => 'Payment Gateways', 'km' => 'ច្រកទូទាត់ប្រាក់', 'zh' => '支付网关'],
            ['key' => 'nav.items.payment_methods', 'en' => 'Payment Methods', 'km' => 'វិធីសាស្រ្តទូទាត់', 'zh' => '支付方式'],
            ['key' => 'nav.notification_templates', 'en' => 'Notification Templates', 'km' => 'គំរូការជូនដំណឹង', 'zh' => '通知模板'],
            ['key' => 'nav.store_notifications', 'en' => 'Store Notifications', 'km' => 'ការជូនដំណឹងហាង', 'zh' => '店铺通知'],
            ['key' => 'nav.app_management', 'en' => 'App Management', 'km' => 'ការគ្រប់គ្រងកម្មវិធី', 'zh' => '应用管理'],
            ['key' => 'nav.app_banner', 'en' => 'App Banner', 'km' => 'បាដាកម្មវិធី', 'zh' => '应用横幅'],
            ['key' => 'nav.app_configuration', 'en' => 'App Configuration', 'km' => 'ការកំណត់រចនាសម្ព័ន្ធកម្មវិធី', 'zh' => '应用配置'],
            ['key' => 'nav.push_notifications_log', 'en' => 'Push Notifications Log', 'km' => 'កំណត់ហេតុការជូនដំណឹងជំរុញ', 'zh' => '推送通知日志'],
            ['key' => 'nav.app_download_links', 'en' => 'App Download Links', 'km' => 'តំណទាញយកកម្មវិធី', 'zh' => '应用下载链接'],
            ['key' => 'nav.translation', 'en' => 'Translations', 'km' => 'ការគ្រប់គ្រងការបកប្រែ', 'zh' => '翻译管理'],
            ['key' => 'nav.technician_management', 'en' => 'Technician Management', 'km' => 'ការគ្រប់គ្រងអ្នកបច្ចេកទេស', 'zh' => '技术员管理'],
            ['key' => 'nav.technicians_staff', 'en' => 'Technicians (Staff)', 'km' => 'អ្នកបច្ចេកទេស (បុគ្គលិក)', 'zh' => '技术员 (员工)'],
            ['key' => 'nav.list_all_technicians', 'en' => 'List All Technicians', 'km' => 'បញ្ជីអ្នកបច្ចេកទេសទាំងអស់', 'zh' => '所有技术员列表'],
            ['key' => 'nav.manage_services', 'en' => 'Manage Services', 'km' => 'គ្រប់គ្រងសេវាកម្ម', 'zh' => '管理服务'],
            ['key' => 'nav.product_sales', 'en' => 'Product Sales', 'km' => 'ការលក់ផលិតផល', 'zh' => '产品销售'],
            ['key' => 'nav.store', 'en' => 'Store', 'km' => 'ហាង', 'zh' => '店铺'],
            ['key' => 'nav.product_inventory', 'en' => 'Product Inventory', 'km' => 'សារពើភ័ណ្ឌផលិតផល', 'zh' => '产品库存'],
            ['key' => 'nav.product_list', 'en' => 'Product List', 'km' => 'បញ្ជីផលិតផល', 'zh' => '产品列表'],
            ['key' => 'nav.store_inventories', 'en' => 'Store Inventories', 'km' => 'សារពើភ័ណ្ឌហាង', 'zh' => '店铺库存'],
            ['key' => 'nav.manage_categories', 'en' => 'Manage Categories', 'km' => 'គ្រប់គ្រងប្រភេទ', 'zh' => '管理分类'],
            ['key' => 'nav.manage_brands', 'en' => 'Manage Brands', 'km' => 'គ្រប់គ្រងម៉ាកយីហោ', 'zh' => '管理品牌'],
            ['key' => 'nav.orders_sales', 'en' => 'Orders & Sales', 'km' => 'ការបញ្ជាទិញ និងការលក់', 'zh' => '订单与销售'],
            ['key' => 'nav.all_orders', 'en' => 'All Orders', 'km' => 'ការបញ្ជាទិញទាំងអស់', 'zh' => '所有订单'],
            ['key' => 'nav.pending_orders', 'en' => 'Pending Orders', 'km' => 'ការបញ្ជាទិញដែលកំពុងរង់ចាំ', 'zh' => '待处理订单'],
            ['key' => 'nav.sales_reports', 'en' => 'Sales Reports', 'km' => 'របាយការណ៍លក់', 'zh' => '销售报告'],
            ['key' => 'nav.help_support', 'en' => 'Help & Support', 'km' => 'ជំនួយ និងការគាំទ្រ', 'zh' => '帮助与支持'],
            ['key' => 'nav.feedback', 'en' => 'Feedback', 'km' => 'មតិកែលម្អ', 'zh' => '反馈'],
            ['key' => 'nav.get_our_app', 'en' => 'Get Our App', 'km' => 'ទាញយកកម្មវិធីរបស់យើង', 'zh' => '获取我们的应用'],
            ['key' => 'nav.items.conversations', 'en' => 'Conversations', 'km' => 'ការសន្ទនា', 'zh' => '会话'],
            ['key' => 'nav.items.ai_assitant', 'en' => 'AI Assistant', 'km' => 'ជំនួយការ AI', 'zh' => 'AI助手'],
            ['key' => 'nav.items.ocr', 'en' => 'OCR', 'km' => 'OCR', 'zh' => 'OCR'],
        ];
    }

    private function getSettingTranslations(): array
    {
        return [
            ['key' => 'settings.layout.title', 'en' => 'Settings', 'km' => 'ការកំណត់', 'zh' => '设置'],
            ['key' => 'settings.layout.description', 'en' => 'Manage your account settings and set e-mail preferences.', 'km' => 'គ្រប់គ្រងការកំណត់គណនីរបស់អ្នក និងកំណត់ចំណូលចិត្តអ៊ីមែល។', 'zh' => '管理您的帐户设置和电子邮件偏好。'],

            // SidebarNav.vue
            ['key' => 'settings.sidebar.profile', 'en' => 'Profile', 'km' => 'ប្រវត្តិរូប', 'zh' => '个人资料'],
            ['key' => 'settings.sidebar.account', 'en' => 'Account', 'km' => 'គណនី', 'zh' => '帐户'],
            ['key' => 'settings.sidebar.security', 'en' => 'Security', 'km' => 'សុវត្ថិភាព', 'zh' => '安全'],
            ['key' => 'settings.sidebar.appearance', 'en' => 'Appearance', 'km' => 'រូបរាង', 'zh' => '外观'],
            ['key' => 'settings.sidebar.notifications', 'en' => 'Notifications', 'km' => 'ការជូនដំណឹង', 'zh' => '通知'],

            // Security Settings
            ['key' => 'settings.security.title', 'en' => 'Security Settings', 'km' => 'ការកំណត់សុវត្ថិភាព', 'zh' => '安全设置'],
            ['key' => 'settings.security.description', 'en' => 'Manage your account security and two-factor authentication.', 'km' => 'គ្រប់គ្រងសុវត្ថិភាពគណនីរបស់អ្នក និងការផ្ទៀងផ្ទាត់ពីរកត្តា។', 'zh' => '管理您的帐户安全和双重身份验证。'],

            // Two-Factor Authentication
            ['key' => 'settings.security.two_factor_auth', 'en' => 'Two-Factor Authentication', 'km' => 'ការផ្ទៀងផ្ទាត់ពីរកត្តា', 'zh' => '双重身份验证'],
            ['key' => 'settings.security.two_factor_description', 'en' => 'Add an extra layer of security to your account.', 'km' => 'បន្ថែមស្រទាប់សុវត្ថិភាពបន្ថែមទៅលើគណនីរបស់អ្នក។', 'zh' => '为您的帐户添加额外的安全层。'],
            ['key' => 'settings.security.enabled', 'en' => 'Enabled', 'km' => 'បានបើក', 'zh' => '已启用'],
            ['key' => 'settings.security.disabled', 'en' => 'Disabled', 'km' => 'បានបិទ', 'zh' => '已禁用'],
            ['key' => 'settings.security.enable', 'en' => 'Enable', 'km' => 'បើក', 'zh' => '启用'],
            ['key' => 'settings.security.disable', 'en' => 'Disable', 'km' => 'បិទ', 'zh' => '禁用'],

            // Setup Dialog
            ['key' => 'settings.security.setup_title', 'en' => 'Setup Two-Factor Authentication', 'km' => 'រៀបចំការផ្ទៀងផ្ទាត់ពីរកត្តា', 'zh' => '设置双重身份验证'],
            ['key' => 'settings.security.setup_description', 'en' => 'Follow the steps below to secure your account.', 'km' => 'អនុវត្តតាមជំហានខាងក្រោមដើម្បីធានាសុវត្ថិភាពគណនីរបស់អ្នក។', 'zh' => '按照以下步骤来保护您的帐户。'],
            ['key' => 'settings.security.qr_instruction', 'en' => 'Scan this QR code with your authenticator app.', 'km' => 'ស្កេនកូដ QR នេះជាមួយកម្មវិធីផ្ទៀងផ្ទាត់របស់អ្នក។', 'zh' => '使用您的身份验证器应用扫描此二维码。'],

            // Recovery Codes
            ['key' => 'settings.security.recovery_codes', 'en' => 'Recovery Codes', 'km' => 'កូដស្តារឡើងវិញ', 'zh' => '恢复代码'],
            ['key' => 'settings.security.recovery_codes_description', 'en' => 'Store these codes safely. They can be used to access your account if you lose your authenticator device.', 'km' => 'រក្សាទុកកូដទាំងនេះឱ្យបានសុវត្ថិភាព។ ពួកវាអាចប្រើដើម្បីចូលទៅគណនីរបស់អ្នក ប្រសិនបើអ្នកបាត់ឧបករណ៍ផ្ទៀងផ្ទាត់របស់អ្នក។', 'zh' => '安全地存储这些代码。如果您丢失了身份验证器设备，可以使用它们来访问您的帐户。'],
            ['key' => 'settings.security.recovery_codes_warning', 'en' => 'Store these codes safely. Each code can only be used once.', 'km' => 'រក្សាទុកកូដទាំងនេះឱ្យបានសុវត្ថិភាព។ កូដនីមួយៗអាចប្រើបានតែម្តង។', 'zh' => '安全地存储这些代码。每个代码只能使用一次。'],
            ['key' => 'settings.security.copy', 'en' => 'Copy', 'km' => 'ចម្លង', 'zh' => '复制'],
            ['key' => 'settings.security.regenerate', 'en' => 'Regenerate', 'km' => 'បង្កើតឡើងវិញ', 'zh' => '重新生成'],

            // Confirmation
            ['key' => 'settings.security.confirmation_code', 'en' => 'Confirmation Code', 'km' => 'កូដបញ្ជាក់', 'zh' => '确认代码'],
            ['key' => 'settings.security.confirmation_instruction', 'en' => 'Enter the 6-digit code from your authenticator app.', 'km' => 'បញ្ចូលកូដ ៦ ខ្ទង់ពីកម្មវិធីផ្ទៀងផ្ទាត់របស់អ្នក។', 'zh' => '输入您的身份验证器应用中的6位数代码。'],
            ['key' => 'settings.security.confirm', 'en' => 'Confirm', 'km' => 'បញ្ជាក់', 'zh' => '确认'],

            // Messages & Toasts
            ['key' => 'settings.security.success', 'en' => 'Success', 'km' => 'ជោគជ័យ', 'zh' => '成功'],
            ['key' => 'settings.security.error', 'en' => 'Error', 'km' => 'កំហុស', 'zh' => '错误'],
            ['key' => 'settings.security.enabled_success', 'en' => 'Two-factor authentication has been enabled successfully.', 'km' => 'ការផ្ទៀងផ្ទាត់ពីរកត្តាត្រូវបានបើកដោយជោគជ័យ។', 'zh' => '双重身份验证已成功启用。'],
            ['key' => 'settings.security.disabled_success', 'en' => 'Two-factor authentication has been disabled.', 'km' => 'ការផ្ទៀងផ្ទាត់ពីរកត្តាត្រូវបានបិទ។', 'zh' => '双重身份验证已被禁用。'],
            ['key' => 'settings.security.codes_regenerated', 'en' => 'Recovery codes have been regenerated.', 'km' => 'កូដស្តារឡើងវិញត្រូវបានបង្កើតឡើងវិញ។', 'zh' => '恢复代码已重新生成。'],
            ['key' => 'settings.security.codes_copied', 'en' => 'Recovery codes copied to clipboard.', 'km' => 'កូដស្តារឡើងវិញត្រូវបានចម្លងទៅក្ដារឆ្នៀត។', 'zh' => '恢复代码已复制到剪贴板。'],
            ['key' => 'settings.security.enable_error', 'en' => 'Failed to enable two-factor authentication.', 'km' => 'បរាជ័យក្នុងការបើកការផ្ទៀងផ្ទាត់ពីរកត្តា។', 'zh' => '启用双重身份验证失败。'],
            ['key' => 'settings.security.disable_error', 'en' => 'Failed to disable two-factor authentication.', 'km' => 'បរាជ័យក្នុងការបិទការផ្ទៀងផ្ទាត់ពីរកត្តា។', 'zh' => '禁用双重身份验证失败。'],
            ['key' => 'settings.security.confirmation_error', 'en' => 'Invalid confirmation code. Please try again.', 'km' => 'កូដបញ្ជាក់មិនត្រឹមត្រូវ។ សូមព្យាយាមម្តងទៀត។', 'zh' => '确认代码无效。请重试。'],
            ['key' => 'settings.security.regenerate_error', 'en' => 'Failed to regenerate recovery codes.', 'km' => 'បរាជ័យក្នុងការបង្កើតកូដស្តារឡើងវិញ។', 'zh' => '重新生成恢复代码失败。'],
            ['key' => 'settings.security.disable_title', 'en' => 'Disable Title', 'km' => 'បិទចំណងជើង', 'zh' => '禁用标题'],
            ['key' => 'settings.security.disable_confirmation', 'en' => 'Disable Confirmation', 'km' => 'បិទការបញ្ជាក់', 'zh' => '禁用确认'],
            ['key' => 'settings.security.authentication_code', 'en' => 'Authentication Code', 'km' => 'លេខកូដផ្ទៀងផ្ទាត់', 'zh' => '认证码'],
            ['key' => 'settings.security.disable_instruction', 'en' => 'Disable Instruction', 'km' => 'បិទការណែនាំ', 'zh' => '禁用说明'],
            ['key' => 'chat.errors.geolocation_failed', 'en' => 'Unable to retrieve your location.', 'km' => 'មិនអាចទាញយកទីតាំងរបស់អ្នកបានទេ។', 'zh' => '无法获取您的位置。'],
        ];
    }

    private function getLanguageTranslations(): array
    {
        return[
            ['key' => 'languages.en', 'en' => 'English', 'km' => 'ភាសាអង់គ្លេស', 'zh' => '英语'],
            ['key' => 'languages.km', 'en' => 'ភាសាខ្មែរ (Khmer)', 'km' => 'ភាសាខ្មែរ (Khmer)', 'zh' => '高棉语 (Khmer)'],
            ['key' => 'languages.zh', 'en' => '中文 (Chinese)', 'km' => '中文 (Chinese)', 'zh' => '中文 (Chinese)'],
            ['key' => 'languages.select.placeholder', 'en' => 'Select Language', 'km' => 'ជ្រើសរើសភាសា', 'zh' => '选择语言'],
            ['key' => 'languages.select.label', 'en' => 'Language', 'km' => 'ភាសា', 'zh' => '语言'],
        ];
    }


    private function getAccountTranslations(): array
    {
        return [
            ['key' => 'account.title', 'en' => 'Account', 'km' => 'គណនី', 'zh' => '帐户'],
            ['key' => 'account.description', 'en' => 'Change the details of your profile here.', 'km' => 'ផ្លាស់ប្តូរព័ត៌មានលម្អិតនៃប្រវត្តិរូបរបស់អ្នកនៅទីនេះ។', 'zh' => '在此处更改您的个人资料详细信息。'],
            ['key' => 'account.loadingText', 'en' => 'Loading...', 'km' => 'កំពុងផ្ទុក...', 'zh' => '正在加载...'],
            ['key' => 'account.errorLoadingUsername', 'en' => 'Error loading username', 'km' => 'កំហុសក្នុងការផ្ទុកឈ្មោះអ្នកប្រើប្រាស់', 'zh' => '加载用户名时出错'],

            // Form Fields
            ['key' => 'account.usernameLabel', 'en' => 'Username', 'km' => 'ឈ្មោះអ្នកប្រើប្រាស់', 'zh' => '用户名'],
            ['key' => 'account.usernamePlaceholder', 'en' => 'Your username', 'km' => 'ឈ្មោះអ្នកប្រើប្រាស់របស់អ្នក', 'zh' => '您的用户名'],
            ['key' => 'account.currentPasswordLabel', 'en' => 'Current Password', 'km' => 'ពាក្យសម្ងាត់បច្ចុប្បន្ន', 'zh' => '当前密码'],
            ['key' => 'account.currentPasswordPlaceholder', 'en' => 'Current Password', 'km' => 'ពាក្យសម្ងាត់បច្ចុប្បន្ន', 'zh' => '当前密码'],
            ['key' => 'account.newPasswordLabel', 'en' => 'New Password', 'km' => 'ពាក្យសម្ងាត់ថ្មី', 'zh' => '新密码'],
            ['key' => 'account.newPasswordPlaceholder', 'en' => 'New Password', 'km' => 'ពាក្យសម្ងាត់ថ្មី', 'zh' => '新密码'],
            ['key' => 'account.showCurrentPassword', 'en' => 'Show current password', 'km' => 'បង្ហាញពាក្យសម្ងាត់បច្ចុប្បន្ន', 'zh' => '显示当前密码'],
            ['key' => 'account.hideCurrentPassword', 'en' => 'Hide current password', 'km' => 'លាក់ពាក្យសម្ងាត់បច្ចុប្បន្ន', 'zh' => '隐藏当前密码'],
            ['key' => 'account.showNewPassword', 'en' => 'Show new password', 'km' => 'បង្ហាញពាក្យសម្ងាត់ថ្មី', 'zh' => '显示新密码'],
            ['key' => 'account.hideNewPassword', 'en' => 'Hide new password', 'km' => 'លាក់ពាក្យសម្ងាត់ថ្មី', 'zh' => '隐藏新密码'],

            // Buttons
            ['key' => 'account.updateButton', 'en' => 'Update Account', 'km' => 'ធ្វើបច្ចុប្បន្នភាពគណនី', 'zh' => '更新帐户'],
            ['key' => 'account.updatingButton', 'en' => 'Updating Account...', 'km' => 'កំពុងធ្វើបច្ចុប្បន្នភាពគណនី...', 'zh' => '正在更新帐户...'],

            // Toasts & Notifications
            ['key' => 'account.toast.loadError.title', 'en' => 'Load Error', 'km' => 'កំហុសក្នុងការផ្ទុក', 'zh' => '加载错误'],
            ['key' => 'account.toast.loadError.defaultMessage', 'en' => 'Failed to load user data. API response was not successful or data was missing.', 'km' => 'បរាជ័យក្នុងការផ្ទុកទិន្នន័យអ្នកប្រើប្រាស់។ ការឆ្លើយតបរបស់ API មិនបានជោគជ័យ ឬទិន្នន័យបានបាត់។', 'zh' => '加载用户数据失败。API 响应不成功或数据丢失。'],
            ['key' => 'account.toast.unexpectedError', 'en' => 'An unexpected error occurred while fetching user data.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើងខណៈពេលកំពុងទាញយកទិន្នន័យអ្នកប្រើប្រាស់។', 'zh' => '获取用户数据时发生意外错误。'],
            ['key' => 'account.toast.apiError.title', 'en' => 'API Error', 'km' => 'កំហុស API', 'zh' => 'API 错误'],
            ['key' => 'account.toast.missingUserId', 'en' => 'User ID is missing. Cannot update password. Please refresh.', 'km' => 'លេខសម្គាល់អ្នកប្រើប្រាស់បានបាត់។ មិនអាចធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់បានទេ។ សូមផ្ទុកឡើងវិញ។', 'zh' => '缺少用户ID。无法更新密码。请刷新。'],
            ['key' => 'account.toast.updateSuccess.defaultMessage', 'en' => 'Password updated successfully.', 'km' => 'បានធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់ដោយជោគជ័យ។', 'zh' => '密码更新成功。'],
            ['key' => 'account.toast.updateFailed.title', 'en' => 'Update Failed', 'km' => 'ការធ្វើបច្ចុប្បន្នភាពបានបរាជ័យ', 'zh' => '更新失败'],
            ['key' => 'account.toast.updateFailed.defaultMessage', 'en' => 'Could not update password.', 'km' => 'មិនអាចធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់បានទេ។', 'zh' => '无法更新密码。'],
            ['key' => 'account.toast.unexpectedUpdateError', 'en' => 'An unexpected error occurred during password update.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើងកំឡុងពេលធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់។', 'zh' => '密码更新期间发生意外错误。'],

            // Validation
            ['key' => 'account.validation.currentPasswordRequired', 'en' => 'Current password is required.', 'km' => 'ត្រូវតែបញ្ចូលពាក្យសម្ងាត់បច្ចុប្បន្ន។', 'zh' => '当前密码为必填项。'],
            ['key' => 'account.validation.newPasswordMinLength', 'en' => 'New password must be at least 8 characters.', 'km' => 'ពាក្យសម្ងាត់ថ្មីត្រូវតែមានយ៉ាងហោចណាស់ 8 តួអក្សរ។', 'zh' => '新密码必须至少包含8个字符。'],

        ];
    }

    private function getAppearanceTranslations(): array
    {
        return[
            ['key' => 'appearance.title', 'en' => 'Appearance', 'km' => 'រូបរាង', 'zh' => '外观'],
            ['key' => 'appearance.description', 'en' => 'Customize the appearance of the app. Automatically switch between day and night themes.', 'km' => 'ប្ដូររូបរាងរបស់កម្មវិធីតាមបំណង។ ប្ដូររវាងផ្ទៃថ្ងៃនិងយប់ដោយស្វ័យប្រវត្តិ។', 'zh' => '自定义应用程序的外观。在白天和夜晚主题之间自动切换。'],

            // Font Section
            ['key' => 'appearance.font.label', 'en' => 'Font', 'km' => 'ពុម្ពអក្សរ', 'zh' => '字体'],
            ['key' => 'appearance.font.description', 'en' => 'Set the font you want to use in the dashboard.', 'km' => 'កំណត់ពុម្ពអក្សរដែលអ្នកចង់ប្រើក្នុងផ្ទាំងគ្រប់គ្រង។', 'zh' => '设置您想在仪表板中使用的字体。'],
            ['key' => 'appearance.font.inter', 'en' => 'Inter', 'km' => 'អ៊ីនធឺ', 'zh' => 'Inter'],
            ['key' => 'appearance.font.manrope', 'en' => 'Manrope', 'km' => 'មែនរ៉ូប', 'zh' => 'Manrope'],
            ['key' => 'appearance.font.system', 'en' => 'System', 'km' => 'ប្រព័ន្ធ', 'zh' => '系统'],

            // Theme Section
            ['key' => 'appearance.theme.label', 'en' => 'Theme', 'km' => 'ផ្ទៃ', 'zh' => '主题'],
            ['key' => 'appearance.theme.description', 'en' => 'Select the theme for the dashboard.', 'km' => 'ជ្រើសរើសផ្ទៃសម្រាប់ផ្ទាំងគ្រប់គ្រង។', 'zh' => '为仪表板选择主题。'],
            ['key' => 'appearance.theme.light', 'en' => 'Light', 'km' => 'ពន្លឺថ្ងៃ', 'zh' => '浅色'],
            ['key' => 'appearance.theme.dark', 'en' => 'Dark', 'km' => 'ពន្លឺយប់', 'zh' => '深色'],

            // Button
            ['key' => 'appearance.updateButton', 'en' => 'Update preferences', 'km' => 'ធ្វើបច្ចុប្បន្នភាពចំណូលចិត្ត', 'zh' => '更新偏好设置'],

            // Toast
            ['key' => 'appearance.toast.submittedTitle', 'en' => 'You submitted the following values:', 'km' => 'អ្នកបានបញ្ជូនតម្លៃដូចខាងក្រោម៖', 'zh' => '您提交了以下值：'],

            // Validation
            ['key' => 'appearance.validation.themeRequired', 'en' => 'Please select a theme.', 'km' => 'សូមជ្រើសរើសផ្ទៃ។', 'zh' => '请选择一个主题。'],
            ['key' => 'appearance.validation.fontInvalid', 'en' => 'Select a font', 'km' => 'ជ្រើសរើសពុម្ពអក្សរ', 'zh' => '选择一种字体'],
            ['key' => 'appearance.validation.fontRequired', 'en' => 'Please select a font.', 'km' => 'សូមជ្រើសរើសពុម្ពអក្សរ។', 'zh' => '请选择一种字体。'],
        ];
    }

    private function getRolesManagementTranslations(): array
    {
        return [
            ['key' => 'roles.title', 'en' => 'Role & Permissions', 'km' => 'តួនាទី និងការអនុញ្ញាត', 'zh' => '角色与权限'],
            ['key' => 'roles.description', 'en' => 'Here\'s a list of roles!', 'km' => 'នេះគឺជាបញ្ជីនៃតួនាទី!', 'zh' => '这是角色列表！'],
            ['key' => 'roles.table.noResults', 'en' => 'No results.', 'km' => 'គ្មានលទ្ធផល។', 'zh' => '无结果。'],
            ['key' => 'roles.toolbar.filterByName', 'en' => 'Filter by name...', 'km' => 'ត្រងតាមឈ្មោះ...', 'zh' => '按名称筛选...'],
            ['key' => 'roles.toolbar.status', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'roles.toolbar.reset', 'en' => 'Reset', 'km' => 'កំណត់ឡើងវិញ', 'zh' => '重置'],
            ['key' => 'roles.toolbar.newRole', 'en' => 'New Role', 'km' => 'តួនាទីថ្មី', 'zh' => '新角色'],
            ['key' => 'roles.dialog.create.title', 'en' => 'Create New Role', 'km' => 'បង្កើតតួនាទីថ្មី', 'zh' => '创建新角色'],
            ['key' => 'roles.dialog.create.description', 'en' => 'Define the properties for the new role. Required fields are marked with an asterisk (*).', 'km' => 'កំណត់លក្ខណសម្បត្តិសម្រាប់តួនាទីថ្មី។ វាលដែលត្រូវការត្រូវបានសម្គាល់ដោយសញ្ញាផ្កាយ (*).', 'zh' => '定义新角色的属性。必填字段标有星号 (*)。'],
            ['key' => 'roles.dialog.form.name.label', 'en' => 'Role Name', 'km' => 'ឈ្មោះតួនាទី', 'zh' => '角色名称'],
            ['key' => 'roles.dialog.form.name.placeholder', 'en' => 'e.g., Content Editor', 'km' => 'ឧទាហរណ៍៖ អ្នកកែសម្រួលមាតិកា', 'zh' => '例如：内容编辑'],
            ['key' => 'roles.dialog.form.status.label', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'roles.dialog.form.status.placeholder', 'en' => 'Select a status', 'km' => 'ជ្រើសរើសស្ថានភាព', 'zh' => '选择状态'],
            ['key' => 'roles.dialog.form.description.label', 'en' => 'Description', 'km' => 'ការពិពណ៌នា', 'zh' => '描述'],
            ['key' => 'roles.dialog.form.description.optional', 'en' => '(Optional)', 'km' => '(ស្រេចចិត្ត)', 'zh' => '(可选)'],
            ['key' => 'roles.dialog.form.description.placeholder', 'en' => "Provide a brief summary of the role's responsibilities and permissions.", 'km' => 'ផ្តល់ការសង្ខេបខ្លីៗអំពីការទទួលខុសត្រូវ និងការអនុញ្ញាតរបស់តួនាទី។', 'zh' => '提供角色职责和权限的简要摘要。'],
            ['key' => 'roles.dialog.buttons.cancel', 'en' => 'Cancel', 'km' => 'បោះបង់', 'zh' => '取消'],
            ['key' => 'roles.dialog.buttons.save', 'en' => 'Save Role', 'km' => 'រក្សាទុកតួនាទី', 'zh' => '保存角色'],
            ['key' => 'roles.toast.validation.title', 'en' => 'Validation Error', 'km' => 'កំហុសសុពលភាព', 'zh' => '验证错误'],
            ['key' => 'roles.toast.validation.nameRequired', 'en' => 'Role name cannot be empty.', 'km' => 'ឈ្មោះតួនាទីមិនអាចទទេបានទេ។', 'zh' => '角色名称不能为空。'],
            ['key' => 'roles.toast.validation.statusRequired', 'en' => 'Please select a role status.', 'km' => 'សូមជ្រើសរើសស្ថានភាពតួនាទី។', 'zh' => '请选择角色状态。'],
            ['key' => 'roles.toast.create.success.title', 'en' => 'Role Created Successfully!', 'km' => 'បានបង្កើតតួនាទីដោយជោគជ័យ!', 'zh' => '角色创建成功！'],
            ['key' => 'roles.toast.create.success.description', 'en' => 'The role "{roleName}" has been added.', 'km' => 'តួនាទី "{roleName}" ត្រូវបានបន្ថែម។', 'zh' => '角色 "{roleName}" 已添加。'],
            ['key' => 'roles.toast.create.error.title', 'en' => 'Error Creating Role', 'km' => 'កំហុសក្នុងការបង្កើតតួនាទី', 'zh' => '创建角色时出错'],
            ['key' => 'roles.toast.create.error.description', 'en' => 'An unexpected error occurred. Please try again.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើង។ សូមព្យាយាមម្តងទៀត។', 'zh' => '发生意外错误。请重试。'],
            ['key' => 'roles.columns.index', 'en' => '#', 'km' => '#', 'zh' => '序号'],
            ['key' => 'roles.columns.name', 'en' => 'Name', 'km' => 'ឈ្មោះ', 'zh' => '名称'],
            ['key' => 'roles.columns.description', 'en' => 'Description', 'km' => 'ការពិពណ៌នា', 'zh' => '描述'],
            ['key' => 'roles.columns.status', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'roles.columns.actions', 'en' => 'Actions', 'km' => 'សកម្មភាព', 'zh' => '操作'],
            ['key' => 'roles.status.active', 'en' => 'Active', 'km' => 'សកម្ម', 'zh' => '有效'],
            ['key' => 'roles.status.inactive', 'en' => 'Inactive', 'km' => 'អសកម្ម', 'zh' => '无效'],
            ['key' => 'roles.status.unknown', 'en' => 'Unknown', 'km' => 'មិនស្គាល់', 'zh' => '未知'],
            ['key' => 'roles.table.noDescription', 'en' => 'N/A', 'km' => 'គ្មាន', 'zh' => '无'],
            ['key' => 'roles.rowActions.openMenu', 'en' => 'Open menu', 'km' => 'បើកម៉ឺនុយ', 'zh' => '打开菜单'],
            ['key' => 'roles.rowActions.edit', 'en' => 'Edit', 'km' => 'កែសម្រួល', 'zh' => '编辑'],
            ['key' => 'roles.rowActions.managePermissions', 'en' => 'Manage Permissions', 'km' => 'គ្រប់គ្រងសិទ្ធិ', 'zh' => '管理权限'],
            ['key' => 'roles.rowActions.delete', 'en' => 'Delete', 'km' => 'លុប', 'zh' => '删除'],
            ['key' => 'roles.editDialog.title', 'en' => 'Edit Role', 'km' => 'កែសម្រួលតួនាទី', 'zh' => '编辑角色'],
            ['key' => 'roles.editDialog.description', 'en' => 'Make changes to the role details. Click save when you\'re done.', 'km' => 'ធ្វើការផ្លាស់ប្តូរព័ត៌មានលម្អិតតួនាទី។ ចុចរក្សាទុកនៅពេលអ្នករួចរាល់។', 'zh' => '更改角色详细信息。完成后点击保存。'],
            ['key' => 'roles.editDialog.loading', 'en' => 'Loading...', 'km' => 'កំពុងផ្ទុក...', 'zh' => '加载中...'],
            ['key' => 'roles.editDialog.error', 'en' => 'Error:', 'km' => 'កំហុស:', 'zh' => '错误：'],
            ['key' => 'roles.editDialog.form.name.label', 'en' => 'Name', 'km' => 'ឈ្មោះ', 'zh' => '名称'],
            ['key' => 'roles.editDialog.form.name.placeholder', 'en' => 'Enter role name', 'km' => 'បញ្ចូលឈ្មោះតួនាទី', 'zh' => '输入角色名称'],
            ['key' => 'roles.editDialog.form.description.label', 'en' => 'Description', 'km' => 'ការពិពណ៌នា', 'zh' => '描述'],
            ['key' => 'roles.editDialog.form.description.placeholder', 'en' => 'Enter role description (optional)', 'km' => 'បញ្ចូលការពិពណ៌នាតួនាទី (ស្រេចចិត្ត)', 'zh' => '输入角色描述（可选）'],
            ['key' => 'roles.editDialog.form.status.label', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'roles.editDialog.form.status.placeholder', 'en' => 'Select a status', 'km' => 'ជ្រើសរើសស្ថានភាព', 'zh' => '选择状态'],
            ['key' => 'roles.editDialog.buttons.cancel', 'en' => 'Cancel', 'km' => 'បោះបង់', 'zh' => '取消'],
            ['key' => 'roles.editDialog.buttons.saving', 'en' => 'Saving...', 'km' => 'កំពុងរក្សាទុក...', 'zh' => '保存中...'],
            ['key' => 'roles.editDialog.buttons.saveChanges', 'en' => 'Save changes', 'km' => 'រក្សាទុកការផ្លាស់ប្តូរ', 'zh' => '保存更改'],
            ['key' => 'roles.editDialog.toast.error.missingId', 'en' => 'Role ID is missing.', 'km' => 'លេខសម្គាល់តួនាទីបាត់។', 'zh' => '缺少角色ID。'],
            ['key' => 'roles.editDialog.toast.error.invalidResponse', 'en' => 'Failed to load role details: Invalid response structure.', 'km' => 'បរាជ័យក្នុងការផ្ទុកព័ត៌មានលម្អិតតួនាទី៖ រចនាសម្ព័ន្ធការឆ្លើយតបមិនត្រឹមត្រូវ។', 'zh' => '加载角色详细信息失败：无效的响应结构。'],
            ['key' => 'roles.editDialog.toast.error.unexpected', 'en' => 'An unexpected error occurred.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើង។', 'zh' => '发生意外错误。'],
            ['key' => 'roles.editDialog.toast.error.noDataToSave', 'en' => 'No role data to save.', 'km' => 'គ្មានទិន្នន័យតួនាទីដើម្បីរក្សាទុកទេ។', 'zh' => '没有要保存的角色数据。'],
            ['key' => 'roles.editDialog.toast.error.nameEmpty', 'en' => 'Role name cannot be empty.', 'km' => 'ឈ្មោះតួនាទីមិនអាចទទេបានទេ។', 'zh' => '角色名称不能为空。'],
            ['key' => 'roles.editDialog.toast.success.title', 'en' => 'Role Updated Successfully!', 'km' => 'បានធ្វើបច្ចុប្បន្នភាពតួនាទីដោយជោគជ័យ!', 'zh' => '角色更新成功！'],
            ['key' => 'roles.editDialog.toast.success.description', 'en' => 'The role "{roleName}" has been updated.', 'km' => 'តួនាទី "{roleName}" ត្រូវបានធ្វើបច្ចុប្បន្នភាព។', 'zh' => '角色 "{roleName}" 已更新。'],
            ['key' => 'roles.editDialog.toast.error.failedToSave', 'en' => 'Failed to save changes.', 'km' => 'បរាជ័យក្នុងការរក្សាទុកការផ្លាស់ប្តូរ។', 'zh' => '保存更改失败。'],
            ['key' => 'roles.permissionsDialog.title', 'en' => 'Manage Permissions for {roleName}', 'km' => 'គ្រប់គ្រងសិទ្ធិសម្រាប់ {roleName}', 'zh' => '管理 {roleName} 的权限'],
            ['key' => 'roles.permissionsDialog.description', 'en' => 'Select the permissions to assign to this role.', 'km' => 'ជ្រើសរើសសិទ្ធិដើម្បីកំណត់ទៅតួនាទីនេះ។', 'zh' => '选择要分配给此角色的权限。'],
            ['key' => 'roles.permissionsDialog.loading', 'en' => 'Loading permissions...', 'km' => 'កំពុងផ្ទុកសិទ្ធិ...', 'zh' => '正在加载权限...'],
            ['key' => 'roles.permissionsDialog.error', 'en' => 'Error:', 'km' => 'កំហុស:', 'zh' => '错误：'],
            ['key' => 'roles.permissionsDialog.error.loadPermissions', 'en' => 'Could not load available permissions.', 'km' => 'មិនអាចផ្ទុកសិទ្ធិដែលមាន។', 'zh' => '无法加载可用权限。'],
            ['key' => 'roles.permissionsDialog.error.unexpected', 'en' => 'An error occurred.', 'km' => 'មានកំហុសកើតឡើង។', 'zh' => '发生错误。'],
            ['key' => 'roles.permissionsDialog.noPermissions', 'en' => 'No permissions available to assign.', 'km' => 'គ្មានសិទ្ធិដែលអាចកំណត់បានទេ។', 'zh' => '没有可分配的权限。'],
            ['key' => 'roles.permissionsDialog.buttons.cancel', 'en' => 'Cancel', 'km' => 'បោះបង់', 'zh' => '取消'],
            ['key' => 'roles.permissionsDialog.buttons.saving', 'en' => 'Saving...', 'km' => 'កំពុងរក្សាទុក...', 'zh' => '保存中...'],
            ['key' => 'roles.permissionsDialog.buttons.savePermissions', 'en' => 'Save Permissions', 'km' => 'រក្សាទុកសិទ្ធិ', 'zh' => '保存权限'],
            ['key' => 'roles.permissionsDialog.toast.success.title', 'en' => 'Permissions Updated!', 'km' => 'បានធ្វើបច្ចុប្បន្នភាពសិទ្ធិ!', 'zh' => '权限已更新！'],
            ['key' => 'roles.permissionsDialog.toast.success.description', 'en' => 'Permissions for role "{roleName}" have been saved.', 'km' => 'សិទ្ធិសម្រាប់តួនាទី "{roleName}" ត្រូវបានរក្សាទុក។', 'zh' => '角色 "{roleName}" 的权限已保存。'],
            ['key' => 'roles.permissionsDialog.toast.error.failedToSave', 'en' => 'Failed to save permissions.', 'km' => 'បរាជ័យក្នុងការរក្សាទុកសិទ្ធិ។', 'zh' => '保存权限失败。'],
            ['key' => 'roles.permissionsDialog.toast.error.unexpectedSave', 'en' => 'An unexpected error occurred while saving permissions.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើងនៅពេលរក្សាទុកសិទ្ធិ។', 'zh' => '保存权限时发生意外错误。'],
            ['key' => 'roles.deleteDialog.title', 'en' => 'Are you absolutely sure?', 'km' => 'តើអ្នកប្រាកដទេ?', 'zh' => '您确定吗？'],
            ['key' => 'roles.deleteDialog.description', 'en' => 'This action cannot be undone. This will permanently delete the role "<strong>{roleName}</strong>" and remove its data from our servers.', 'km' => 'សកម្មភាពនេះមិនអាចត្រឡប់វិញបានទេ។ វានឹងលុបតួនាទី "<strong>{roleName}</strong>" ជាអចិន្ត្រៃយ៍ និងលុបទិន្នន័យរបស់វាពីម៉ាស៊ីនមេរបស់យើង។', 'zh' => '此操作无法撤销。这将永久删除角色 "<strong>{roleName}</strong>" 并从我们的服务器中删除其数据。'],
            ['key' => 'roles.deleteDialog.buttons.cancel', 'en' => 'Cancel', 'km' => 'បោះបង់', 'zh' => '取消'],
            ['key' => 'roles.deleteDialog.buttons.confirm', 'en' => 'Yes, delete role', 'km' => 'បាទ/ចាស លុបតួនាទី', 'zh' => '是的，删除角色'],
            ['key' => 'roles.deleteDialog.toast.error.missingId', 'en' => 'Role ID is missing, cannot delete.', 'km' => 'លេខសម្គាល់តួនាទីបាត់ មិនអាចលុបបានទេ។', 'zh' => '角色ID丢失，无法删除。'],
            ['key' => 'roles.deleteDialog.toast.error.title', 'en' => 'Error', 'km' => 'កំហុស', 'zh' => '错误'],
            ['key' => 'roles.deleteDialog.toast.success.title', 'en' => 'Role Deleted Successfully!', 'km' => 'បានលុបតួនាទីដោយជោគជ័យ!', 'zh' => '角色删除成功！'],
            ['key' => 'roles.deleteDialog.toast.success.description', 'en' => 'The role "{roleName}" has been deleted.', 'km' => 'តួនាទី "{roleName}" ត្រូវបានលុប។', 'zh' => '角色 "{roleName}" 已被删除。'],
            ['key' => 'roles.deleteDialog.toast.error.failed', 'en' => 'Deletion Failed', 'km' => 'បរាជ័យក្នុងការលុប', 'zh' => '删除失败'],
            ['key' => 'roles.deleteDialog.toast.error.couldNotDelete', 'en' => 'Could not delete the role.', 'km' => 'មិនអាចលុបតួនាទីបានទេ។', 'zh' => '无法删除该角色。'],
            ['key' => 'roles.deleteDialog.toast.error.unexpected', 'en' => 'An unexpected error occurred.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើង។', 'zh' => '发生意外错误。'],
            ['key' => 'roles.permissionsDialog.searchPlaceholder', 'en' => 'Search permissions', 'km' => 'ស្វែងរកសិទ្ធិ', 'zh' => '搜索权限'],
            ['key' => 'roles.permissionsDialog.deselectAll', 'en' => 'Deselect all', 'km' => 'ឈប់ជ្រើសរើសទាំងអស់', 'zh' => '取消全选'],
            ['key' => 'roles.permissionsDialog.selectAll', 'en' => 'Select all', 'km' => 'ជ្រើសរើសទាំងអស់', 'zh' => '全选'],
            ['key' => 'roles.permissionsDialog.selectedPermissions', 'en' => 'Selected Permissions', 'km' => 'សិទ្ធិដែលបានជ្រើសរើស', 'zh' => '已选权限'],
            ['key' => 'roles.toolbar.suggest', 'en' => 'AI Suggestion', 'km' => 'ការផ្ដល់យោបល់ដោយ AI', 'zh' => 'AI建议'],
            ['key' => 'roles.dialog.ai.title', 'en' => 'Get AI Suggestions', 'km' => 'ទទួលការផ្ដល់យោបល់ដោយ AI', 'zh' => '获取AI建议'],
            ['key' => 'roles.dialog.ai.description', 'en' => 'Enter a description or context for the role, and our AI will suggest relevant permissions.', 'km' => 'បញ្ចូលការពិពណ៌នា ឬបរិបទសម្រាប់តួនាទី ហើយ AI របស់យើងនឹងផ្ដល់យោបល់សិទ្ធិពាក់ព័ន្ធ។', 'zh' => '输入角色描述或上下文，我们的AI将建议相关权限。'],
            ['key' => 'roles.dialog.ai.placeholder', 'en' => 'e.g., "A role for a junior technician"', 'km' => 'ឧទាហរណ៍៖ "តួនាទីសម្រាប់អ្នកបច្ចេកទេសក្មេង"', 'zh' => '例如：“一个为初级技术员设计的角色”'],
            ['key' => 'roles.dialog.ai.loading', 'en' => 'Generating suggestions...', 'km' => 'កំពុងបង្កើតការផ្ដល់យោបល់...', 'zh' => '正在生成建议...'],
            ['key' => 'roles.dialog.ai.button', 'en' => 'Generate Suggestions', 'km' => 'បង្កើតការផ្ដល់យោបល់', 'zh' => '生成建议'],
            ['key' => 'roles.dialog.ai.results', 'en' => 'Suggested Permissions', 'km' => 'សិទ្ធិដែលបានផ្ដល់យោបល់', 'zh' => '建议的权限'],
            ['key' => 'roles.dialog.ai.noResults', 'en' => 'No suggestions were generated. Please try a different description.', 'km' => 'មិនមានការផ្ដល់យោបល់ទេ។ សូមព្យាយាមពិពណ៌នាផ្សេង។', 'zh' => '未生成任何建议。请尝试不同的描述。'],
            ['key' => 'roles.dialog.ai.validation.contextRequired', 'en' => 'Context is required to generate suggestions.', 'km' => 'ត្រូវតែមានបរិបទដើម្បីបង្កើតការផ្ដល់យោបល់។', 'zh' => '需要上下文才能生成建议。'],
            ['key' => 'roles.dialog.ai.error.fetchFailed', 'en' => 'Failed to fetch AI suggestions. Please try again.', 'km' => 'បរាជ័យក្នុងការទាញយកការផ្ដល់យោបល់ពី AI។ សូមព្យាយាមម្តងទៀត។', 'zh' => '获取AI建议失败。请重试。'],
            ['key' => 'roles.dialog.ai.error.unexpected', 'en' => 'An unexpected error occurred while generating suggestions.', 'km' => 'មានបញ្ហាមិនបានរំពឹងទុកបានកើតឡើងក្នុងអំឡុងពេលបង្កើតការផ្ដល់យោបល់។', 'zh' => '生成建议时发生意外错误。'],
            ['key' => 'roles.dialog.buttons.createSelected', 'en' => 'Create Selected Roles', 'km' => 'បង្កើតតួនាទីដែលបានជ្រើសរើស', 'zh' => '创建选定的角色'],
            ['key' => 'roles.toast.validation.selectionRequired', 'en' => 'Please select at least one permission to create a role.', 'km' => 'សូមជ្រើសរើសសិទ្ធិយ៉ាងតិចមួយដើម្បីបង្កើតតួនាទី។', 'zh' => '请至少选择一个权限以创建角色。'],
            ['key' => 'roles.toast.bulkCreate.title', 'en' => 'Roles Created', 'km' => 'តួនាទីត្រូវបានបង្កើត', 'zh' => '角色已创建'],
            ['key' => 'roles.toast.bulkCreate.description', 'en' => 'The selected roles have been created successfully.', 'km' => 'តួនាទីដែលបានជ្រើសរើសត្រូវបានបង្កើតដោយជោគជ័យ។', 'zh' => '选定的角色已成功创建。'],
        ];
    }


    private function getUserManagementTranslations(): array
    {
        return [
            // User Page
            ['key' => 'users.title', 'en' => 'Users', 'km' => 'អ្នកប្រើប្រាស់', 'zh' => '用户'],
            ['key' => 'users.description', 'en' => 'Here\'s a list of users!', 'km' => 'នេះគឺជាបញ្ជីអ្នកប្រើប្រាស់!', 'zh' => '这是用户列表！'],
            ['key' => 'users.table.noResults', 'en' => 'No results.', 'km' => 'គ្មានលទ្ធផល។', 'zh' => '无结果。'],

            // User Toolbar & Create Dialog
            ['key' => 'users.toolbar.filterByName', 'en' => 'Filter by name...', 'km' => 'ត្រងតាមឈ្មោះ...', 'zh' => '按名称筛选...'],
            ['key' => 'users.toolbar.status', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'users.toolbar.reset', 'en' => 'Reset', 'km' => 'កំណត់ឡើងវិញ', 'zh' => '重置'],
            ['key' => 'users.toolbar.new', 'en' => 'New', 'km' => 'ថ្មី', 'zh' => '新建'],
            ['key' => 'users.dialog.create.title', 'en' => 'Create New User', 'km' => 'បង្កើតអ្នកប្រើប្រាស់ថ្មី', 'zh' => '创建新用户'],
            ['key' => 'users.dialog.create.description', 'en' => 'Fields marked with * are required.', 'km' => 'វាលដែលសម្គាល់ដោយ * គឺតម្រូវឱ្យបំពេញ។', 'zh' => '标有 * 的字段是必填项。'],
            ['key' => 'users.dialog.create.error.title', 'en' => 'Error:', 'km' => 'កំហុស:', 'zh' => '错误：'],
            ['key' => 'users.dialog.create.form.avatar', 'en' => 'Avatar', 'km' => 'រូបតំណាង', 'zh' => '头像'],
            ['key' => 'users.dialog.create.form.name.label', 'en' => 'Name', 'km' => 'ឈ្មោះ', 'zh' => '姓名'],
            ['key' => 'users.dialog.create.form.name.placeholder', 'en' => 'Enter full name', 'km' => 'បញ្ចូលឈ្មោះពេញ', 'zh' => '输入全名'],
            ['key' => 'users.dialog.create.form.username.label', 'en' => 'Username', 'km' => 'ឈ្មោះអ្នកប្រើប្រាស់', 'zh' => '用户名'],
            ['key' => 'users.dialog.create.form.username.placeholder', 'en' => 'Enter username', 'km' => 'បញ្ចូលឈ្មោះអ្នកប្រើប្រាស់', 'zh' => '输入用户名'],
            ['key' => 'users.dialog.create.form.email.label', 'en' => 'Email', 'km' => 'អ៊ីមែល', 'zh' => '电子邮件'],
            ['key' => 'users.dialog.create.form.email.placeholder', 'en' => 'Enter email', 'km' => 'បញ្ចូលអ៊ីមែល', 'zh' => '输入电子邮件'],
            ['key' => 'users.dialog.create.form.role.label', 'en' => 'Role', 'km' => 'តួនាទី', 'zh' => '角色'],
            ['key' => 'users.dialog.create.form.role.loading', 'en' => 'Loading roles...', 'km' => 'កំពុងផ្ទុកតួនាទី...', 'zh' => '正在加载角色...'],
            ['key' => 'users.dialog.create.form.role.placeholder', 'en' => 'Select a role', 'km' => 'ជ្រើសរើសតួនាទី', 'zh' => '选择角色'],
            ['key' => 'users.dialog.create.form.role.groupLabel', 'en' => 'Available Roles', 'km' => 'តួនាទីដែលមាន', 'zh' => '可用角色'],
            ['key' => 'users.dialog.create.form.role.noRoles', 'en' => 'No roles available. A role is required.', 'km' => 'គ្មានតួនាទីទេ។ តួនាទីត្រូវបានទាមទារ។', 'zh' => '无可用角色。角色是必填项。'],
            ['key' => 'users.dialog.create.form.status.label', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'users.dialog.create.form.status.active', 'en' => 'ACTIVE', 'km' => 'សកម្ម', 'zh' => '有效'],
            ['key' => 'users.dialog.create.form.status.inactive', 'en' => 'INACTIVE', 'km' => 'អសកម្ម', 'zh' => '无效'],
            ['key' => 'users.dialog.create.form.password.title', 'en' => 'Set Password', 'km' => 'កំណត់ពាក្យសម្ងាត់', 'zh' => '设置密码'],
            ['key' => 'users.dialog.create.form.password.label', 'en' => 'Password', 'km' => 'ពាក្យសម្ងាត់', 'zh' => '密码'],
            ['key' => 'users.dialog.create.form.password.minChars', 'en' => '(Min. 6 chars)', 'km' => '(អប្បបរមា ៦ តួអក្សរ)', 'zh' => '（最少6个字符）'],
            ['key' => 'users.dialog.create.form.password.placeholder', 'en' => 'Enter password', 'km' => 'បញ្ចូលពាក្យសម្ងាត់', 'zh' => '输入密码'],
            ['key' => 'users.dialog.create.form.confirmPassword.label', 'en' => 'Confirm Password', 'km' => 'បញ្ជាក់ពាក្យសម្ងាត់', 'zh' => '确认密码'],
            ['key' => 'users.dialog.create.form.confirmPassword.placeholder', 'en' => 'Confirm password', 'km' => 'បញ្ជាក់ពាក្យសម្ងាត់', 'zh' => '确认密码'],
            ['key' => 'users.dialog.create.buttons.cancel', 'en' => 'Cancel', 'km' => 'បោះបង់', 'zh' => '取消'],
            ['key' => 'users.dialog.create.buttons.saving', 'en' => 'Saving...', 'km' => 'កំពុងរក្សាទុក...', 'zh' => '保存中...'],
            ['key' => 'users.dialog.create.buttons.saveUser', 'en' => 'Save User', 'km' => 'រក្សាទុកអ្នកប្រើប្រាស់', 'zh' => '保存用户'],
            ['key' => 'users.dialog.create.toast.validation.passwordsMismatch', 'en' => 'Passwords do not match.', 'km' => 'ពាក្យសម្ងាត់មិនត្រូវគ្នា។', 'zh' => '密码不匹配。'],
            ['key' => 'users.dialog.create.toast.validation.passwordMinLength', 'en' => 'Password must be at least 6 characters.', 'km' => 'ពាក្យសម្ងាត់ត្រូវតែមានយ៉ាងហោចណាស់ ៦ តួអក្សរ។', 'zh' => '密码必须至少为6个字符。'],
            ['key' => 'users.dialog.create.toast.validation.requiredFields', 'en' => 'Please fill all required fields.', 'km' => 'សូមបំពេញគ្រប់វាលដែលត្រូវការ។', 'zh' => '请填写所有必填字段。'],
            ['key' => 'users.dialog.create.toast.success.title', 'en' => 'User Created Successfully!', 'km' => 'បានបង្កើតអ្នកប្រើប្រាស់ដោយជោគជ័យ!', 'zh' => '用户创建成功！'],
            ['key' => 'users.dialog.create.toast.success.description', 'en' => 'The user {userName} has been created.', 'km' => 'អ្នកប្រើប្រាស់ {userName} ត្រូវបានបង្កើត។', 'zh' => '用户 {userName} 已创建。'],
            ['key' => 'users.dialog.create.toast.error.failed', 'en' => 'Failed to create user.', 'km' => 'បរាជ័យក្នុងការបង្កើតអ្នកប្រើប្រាស់។', 'zh' => '创建用户失败。'],
            ['key' => 'users.dialog.create.toast.error.unexpected', 'en' => 'An unexpected error occurred.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើង។', 'zh' => '发生意外错误。'],


            // User Table & Row Actions
            ['key' => 'users.columns.index', 'en' => '#', 'km' => '#', 'zh' => '序号'],
            ['key' => 'users.columns.image', 'en' => 'Image', 'km' => 'រូបភាព', 'zh' => '图片'],
            ['key' => 'users.columns.name', 'en' => 'Name', 'km' => 'ឈ្មោះ', 'zh' => '姓名'],
            ['key' => 'users.columns.role', 'en' => 'Role', 'km' => 'តួនាទី', 'zh' => '角色'],
            ['key' => 'users.columns.roleNA', 'en' => 'N/A', 'km' => 'គ្មាន', 'zh' => '无'],
            ['key' => 'users.columns.status', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'users.columns.actions', 'en' => 'Actions', 'km' => 'សកម្មភាព', 'zh' => '操作'],
            ['key' => 'users.rowActions.openMenu', 'en' => 'Open menu', 'km' => 'បើកម៉ឺនុយ', 'zh' => '打开菜单'],
            ['key' => 'users.rowActions.edit', 'en' => 'Edit', 'km' => 'កែសម្រួល', 'zh' => '编辑'],
            ['key' => 'users.rowActions.delete', 'en' => 'Delete', 'km' => 'លុប', 'zh' => '删除'],

            // View User Dialog
            ['key' => 'users.viewDialog.title', 'en' => 'User Details', 'km' => 'ព័ត៌មានលម្អិតអ្នកប្រើប្រាស់', 'zh' => '用户详细信息'],
            ['key' => 'users.viewDialog.description', 'en' => 'Viewing details for {userName}.', 'km' => 'កំពុងមើលព័ត៌មានលម្អិតសម្រាប់ {userName}។', 'zh' => '正在查看 {userName} 的详细信息。'],
            ['key' => 'users.viewDialog.loading', 'en' => 'Loading user details...', 'km' => 'កំពុងផ្ទុកព័ត៌មានលម្អិតអ្នកប្រើប្រាស់...', 'zh' => '正在加载用户详细信息...'],
            ['key' => 'users.viewDialog.error.title', 'en' => 'Error:', 'km' => 'កំហុស៖', 'zh' => '错误：'],
            ['key' => 'users.viewDialog.error.loadFailed', 'en' => 'Could not load user details.', 'km' => 'មិនអាចផ្ទុកព័ត៌មានលម្អិតអ្នកប្រើប្រាស់បានទេ។', 'zh' => '无法加载用户详细信息。'],
            ['key' => 'users.viewDialog.form.email.label', 'en' => 'Email', 'km' => 'អ៊ីមែល', 'zh' => '电子邮件'],
            ['key' => 'users.viewDialog.form.role.label', 'en' => 'Role', 'km' => 'តួនាទី', 'zh' => '角色'],
            ['key' => 'users.viewDialog.form.status.label', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'users.viewDialog.form.lastUpdated.label', 'en' => 'Last Updated', 'km' => 'បានធ្វើបច្ចុប្បន្នភាពចុងក្រោយ', 'zh' => '上次更新'],
            ['key' => 'users.viewDialog.buttons.close', 'en' => 'Close', 'km' => 'បិទ', 'zh' => '关闭'],
            ['key' => 'users.rowActions.openMenu', 'en' => 'Open menu', 'km' => 'បើកម៉ឺនុយ', 'zh' => '打开菜单'],
            ['key' => 'users.rowActions.view', 'en' => 'View', 'km' => 'មើល', 'zh' => '查看'],
            ['key' => 'users.rowActions.edit', 'en' => 'Edit', 'km' => 'កែសម្រួល', 'zh' => '编辑'],
            ['key' => 'users.rowActions.delete', 'en' => 'Delete', 'km' => 'លុប', 'zh' => '删除'],

            // Edit User Dialog
            ['key' => 'users.editDialog.title', 'en' => 'Edit User Profile', 'km' => 'កែសម្រួលប្រវត្តិរូបអ្នកប្រើប្រាស់', 'zh' => '编辑用户资料'],
            ['key' => 'users.editDialog.description', 'en' => 'Update details. Fields with * are required.', 'km' => 'ធ្វើបច្ចុប្បន្នភាពព័ត៌មានលម្អិត។ វាលដែលសម្គាល់ដោយ * គឺតម្រូវឱ្យបំពេញ។', 'zh' => '更新详细信息。标有 * 的字段是必填项。'],
            ['key' => 'users.editDialog.loading', 'en' => 'Loading user data...', 'km' => 'កំពុងផ្ទុកទិន្នន័យអ្នកប្រើប្រាស់...', 'zh' => '正在加载用户数据...'],
            ['key' => 'users.editDialog.error.title', 'en' => 'Error:', 'km' => 'កំហុស:', 'zh' => '错误：'],
            ['key' => 'users.editDialog.error.loadFailed', 'en' => 'Failed to load user details: Invalid response structure.', 'km' => 'បរាជ័យក្នុងការផ្ទុកព័ត៌មានលម្អិតអ្នកប្រើប្រាស់៖ រចនាសម្ព័ន្ធការឆ្លើយតបមិនត្រឹមត្រូវ។', 'zh' => '加载用户详细信息失败：无效的响应结构。'],
            ['key' => 'users.editDialog.error.unexpected', 'en' => 'An unexpected error occurred.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើង។', 'zh' => '发生意外错误。'],
            ['key' => 'users.editDialog.form.avatar', 'en' => 'Avatar', 'km' => 'រូបតំណាង', 'zh' => '头像'],
            ['key' => 'users.editDialog.form.name.label', 'en' => 'Name', 'km' => 'ឈ្មោះ', 'zh' => '姓名'],
            ['key' => 'users.editDialog.form.name.placeholder', 'en' => 'Enter full name', 'km' => 'បញ្ចូលឈ្មោះពេញ', 'zh' => '输入全名'],
            ['key' => 'users.editDialog.form.username.label', 'en' => 'Username', 'km' => 'ឈ្មោះអ្នកប្រើប្រាស់', 'zh' => '用户名'],
            ['key' => 'users.editDialog.form.username.placeholder', 'en' => 'Enter username', 'km' => 'បញ្ចូលឈ្មោះអ្នកប្រើប្រាស់', 'zh' => '输入用户名'],
            ['key' => 'users.editDialog.form.email.label', 'en' => 'Email', 'km' => 'អ៊ីមែល', 'zh' => '电子邮件'],
            ['key' => 'users.editDialog.form.email.placeholder', 'en' => 'Enter email', 'km' => 'បញ្ចូលអ៊ីមែល', 'zh' => '输入电子邮件'],
            ['key' => 'users.editDialog.form.role.label', 'en' => 'Role', 'km' => 'តួនាទី', 'zh' => '角色'],
            ['key' => 'users.editDialog.form.role.loading', 'en' => 'Loading roles...', 'km' => 'កំពុងផ្ទុកតួនាទី...', 'zh' => '正在加载角色...'],
            ['key' => 'users.editDialog.form.role.placeholder', 'en' => 'Select a role', 'km' => 'ជ្រើសរើសតួនាទី', 'zh' => '选择角色'],
            ['key' => 'users.editDialog.form.role.availableRoles', 'en' => 'Available Roles', 'km' => 'តួនាទីដែលមាន', 'zh' => '可用角色'],
            ['key' => 'users.editDialog.form.status.label', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'users.editDialog.form.status.active', 'en' => 'ACTIVE', 'km' => 'សកម្ម', 'zh' => '有效'],
            ['key' => 'users.editDialog.form.status.inactive', 'en' => 'INACTIVE', 'km' => 'អសកម្ម', 'zh' => '无效'],
            ['key' => 'users.editDialog.form.password.title', 'en' => 'Update Password', 'km' => 'ធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់', 'zh' => '更新密码'],
            ['key' => 'users.editDialog.form.password.optional', 'en' => '(optional)', 'km' => '(ស្រេចចិត្ត)', 'zh' => '（可选）'],
            ['key' => 'users.editDialog.form.newPassword.label', 'en' => 'New Password', 'km' => 'ពាក្យសម្ងាត់ថ្មី', 'zh' => '新密码'],
            ['key' => 'users.editDialog.form.newPassword.placeholder', 'en' => 'Min. 6 characters', 'km' => 'អប្បបរមា ៦ តួអក្សរ', 'zh' => '最少6个字符'],
            ['key' => 'users.editDialog.form.confirmNewPassword.label', 'en' => 'Confirm New Password', 'km' => 'បញ្ជាក់ពាក្យសម្ងាត់ថ្មី', 'zh' => '确认新密码'],
            ['key' => 'users.editDialog.form.confirmNewPassword.placeholder', 'en' => 'Confirm password', 'km' => 'បញ្ជាក់ពាក្យសម្ងាត់', 'zh' => '确认密码'],
            ['key' => 'users.editDialog.buttons.cancel', 'en' => 'Cancel', 'km' => 'បោះបង់', 'zh' => '取消'],
            ['key' => 'users.editDialog.buttons.saving', 'en' => 'Saving...', 'km' => 'កំពុងរក្សាទុក...', 'zh' => '保存中...'],
            ['key' => 'users.editDialog.buttons.saveChanges', 'en' => 'Save Changes', 'km' => 'រក្សាទុកការផ្លាស់ប្តូរ', 'zh' => '保存更改'],
            ['key' => 'users.editDialog.toast.validation.correctErrors', 'en' => 'Please correct the errors before saving.', 'km' => 'សូមកែតម្រូវកំហុសមុនពេលរក្សាទុក។', 'zh' => '请在保存前更正错误。'],
            ['key' => 'users.editDialog.toast.success.title', 'en' => 'User Updated', 'km' => 'បានធ្វើបច្ចុប្បន្នភាពអ្នកប្រើប្រាស់', 'zh' => '用户已更新'],
            ['key' => 'users.editDialog.toast.success.description', 'en' => 'User {userName} has been updated.', 'km' => 'អ្នកប្រើប្រាស់ {userName} ត្រូវបានធ្វើបច្ចុប្បន្នភាព។', 'zh' => '用户 {userName} 已更新。'],
            ['key' => 'users.editDialog.toast.error.failed', 'en' => 'Failed to save changes.', 'km' => 'បរាជ័យក្នុងការរក្សាទុកការផ្លាស់ប្តូរ។', 'zh' => '保存更改失败。'],
            ['key' => 'users.editDialog.toast.error.unexpected', 'en' => 'An unexpected error occurred.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើង។', 'zh' => '发生意外错误。'],

            // Delete User Dialog
            ['key' => 'users.deleteDialog.title', 'en' => 'Are you absolutely sure?', 'km' => 'តើអ្នកប្រាកដទេ?', 'zh' => '您确定吗？'],
            ['key' => 'users.deleteDialog.description', 'en' => 'This action will permanently delete the user "{userName}". This cannot be undone.', 'km' => 'សកម្មភាពនេះនឹងលុបអ្នកប្រើប្រាស់ "{userName}" ជាអចិន្ត្រៃយ៍។ វាមិនអាចត្រឡប់វិញបានទេ។', 'zh' => '此操作将永久删除用户 "{userName}"。此操作无法撤销。'],
            ['key' => 'users.deleteDialog.buttons.cancel', 'en' => 'Cancel', 'km' => 'បោះបង់', 'zh' => '取消'],
            ['key' => 'users.deleteDialog.buttons.deleting', 'en' => 'Deleting...', 'km' => 'កំពុងលុប...', 'zh' => '删除中...'],
            ['key' => 'users.deleteDialog.buttons.confirm', 'en' => 'Yes, delete user', 'km' => 'បាទ/ចាស លុបអ្នកប្រើប្រាស់', 'zh' => '是的，删除用户'],
            ['key' => 'users.deleteDialog.toast.success.title', 'en' => 'User Deleted', 'km' => 'បានលុបអ្នកប្រើប្រាស់', 'zh' => '用户已删除'],
            ['key' => 'users.deleteDialog.toast.success.description', 'en' => 'User "{userName}" has been deleted.', 'km' => 'អ្នកប្រើប្រាស់ "{userName}" ត្រូវបានលុប។', 'zh' => '用户 "{userName}" 已被删除。'],
            ['key' => 'users.deleteDialog.toast.error.failed', 'en' => 'Deletion Failed', 'km' => 'បរាជ័យក្នុងការលុប', 'zh' => '删除失败'],
            ['key' => 'users.deleteDialog.toast.error.title', 'en' => 'Deletion Error', 'km' => 'កំហុសក្នុងការលុប', 'zh' => '删除错误'],
            ['key' => 'users.deleteDialog.toast.error.unexpected', 'en' => 'An unexpected error occurred.', 'km' => 'មានកំហុសដែលមិនបានរំពឹងទុកកើតឡើង។', 'zh' => '发生意外错误。'],
        ];
    }

    private function getPaginationTranslations(): array
    {
        return [
            ['key' => 'pagination.selected', 'en' => '{selected} of {total} row(s) selected.', 'km' => 'បានជ្រើសរើស {selected} នៃ {total} ជួរ។', 'zh' => '已选择 {total} 行中的 {selected} 行。'],
            ['key' => 'pagination.rowsPerPage', 'en' => 'Rows per page', 'km' => 'ជួរក្នុងមួយទំព័រ', 'zh' => '每页行数'],
            ['key' => 'pagination.page', 'en' => 'Page {current} of {total}', 'km' => 'ទំព័រ {current} នៃ {total}', 'zh' => '第 {current} 页，共 {total} 页'],
            ['key' => 'pagination.goToFirst', 'en' => 'Go to first page', 'km' => 'ទៅទំព័រដំបូង', 'zh' => '前往第一页'],
            ['key' => 'pagination.goToPrevious', 'en' => 'Go to previous page', 'km' => 'ទៅទំព័រមុន', 'zh' => '前往上一页'],
            ['key' => 'pagination.goToNext', 'en' => 'Go to next page', 'km' => 'ទៅទំព័របន្ទាប់', 'zh' => '前往下一页'],
            ['key' => 'pagination.goToLast', 'en' => 'Go to last page', 'km' => 'ទៅទំព័រចុងក្រោយ', 'zh' => '前往最后一页'],
            ['key' => 'pagination.showing', 'en' => 'Showing', 'km' => 'កំពុងបង្ហាញ', 'zh' => '正在显示'],
            ['key' => 'pagination.to', 'en' => 'to', 'km' => 'ទៅ', 'zh' => '到'],
            ['key' => 'pagination.of', 'en' => 'of', 'km' => 'នៃ', 'zh' => '的'],
            ['key' => 'pagination.translations', 'en' => 'translations', 'km' => 'ការបកប្រែ', 'zh' => '翻译'],
            ['key' => 'pagination.previous', 'en' => 'Previous', 'km' => 'មុន', 'zh' => '上一页'],
            ['key' => 'pagination.next', 'en' => 'Next', 'km' => 'បន្ទាប់', 'zh' => '下一页'],
        ];
    }


    private function getTranslationManagementTranslations(): array
    {
        return [
            ['key' => 'translationManagement.title', 'en' => 'Translation Management', 'km' => 'ការគ្រប់គ្រងការបកប្រែ', 'zh' => '翻译管理'],
            ['key' => 'translationManagement.description', 'en' => 'Manage translations across all platforms and locales', 'km' => 'គ្រប់គ្រងការបកប្រែនៅគ្រប់វេទិកា និងគ្រប់ភាសា', 'zh' => '管理所有平台和语言区域的翻译'],
            ['key' => 'translationManagement.addTranslation', 'en' => 'Add Translation', 'km' => 'បន្ថែមការបកប្រែ', 'zh' => '添加翻译'],
            ['key' => 'translationManagement.search', 'en' => 'Search', 'km' => 'ស្វែងរក', 'zh' => '搜索'],
            ['key' => 'translationManagement.searchPlaceholder', 'en' => 'Search by key or value...', 'km' => 'ស្វែងរកតាមកូនសោរ ឬតម្លៃ...', 'zh' => '按键或值搜索...'],
            ['key' => 'translationManagement.platform', 'en' => 'Platform', 'km' => 'វេទិកា', 'zh' => '平台'],
            ['key' => 'translationManagement.allPlatforms', 'en' => 'All Platforms', 'km' => 'គ្រប់វេទិកា', 'zh' => '所有平台'],
            ['key' => 'translationManagement.adminPanel', 'en' => 'Admin Panel', 'km' => 'ផ្ទាំងគ្រប់គ្រង', 'zh' => '管理面板'],
            ['key' => 'translationManagement.mobileApp', 'en' => 'Mobile App', 'km' => 'កម្មវិធីទូរស័ព្ទ', 'zh' => '移动应用'],
            ['key' => 'translationManagement.table.key', 'en' => 'Translation Key', 'km' => 'កូនសោបកប្រែ', 'zh' => '翻译键'],
            ['key' => 'translationManagement.table.locales', 'en' => 'Locales & Values', 'km' => 'ភាសា និងតម្លៃ', 'zh' => '语言区域与值'],
            ['key' => 'translationManagement.table.platform', 'en' => 'Platform', 'km' => 'វេទិកា', 'zh' => '平台'],
            ['key' => 'translationManagement.table.status', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'translationManagement.table.actions', 'en' => 'Actions', 'km' => 'សកម្មភាព', 'zh' => '操作'],
            ['key' => 'translationManagement.noTranslations.title', 'en' => 'No translations found', 'km' => 'រកមិនឃើញការបកប្រែ', 'zh' => '未找到翻译'],
            ['key' => 'translationManagement.noTranslations.description', 'en' => 'Try adjusting your search or create a new translation', 'km' => 'សាកល្បងកែតម្រូវការស្វែងរករបស់អ្នក ឬបង្កើតការបកប្រែថ្មី', 'zh' => '尝试调整您的搜索或创建新翻译'],
            ['key' => 'translationManagement.noTranslation', 'en' => 'No translation', 'km' => 'គ្មានការបកប្រែ', 'zh' => '无翻译'],
            ['key' => 'translationManagement.edit.title', 'en' => 'Edit Translation', 'km' => 'កែសម្រួលការបកប្រែ', 'zh' => '编辑翻译'],
            ['key' => 'translationManagement.create.title', 'en' => 'Create New Translation', 'km' => 'បង្កើតការបកប្រែថ្មី', 'zh' => '创建新翻译'],
            ['key' => 'translationManagement.edit.description', 'en' => 'Update the translation key and values for different locales.', 'km' => 'ធ្វើបច្ចុប្បន្នភាពកូនសោបកប្រែ និងតម្លៃសម្រាប់ភាសាផ្សេងៗ។', 'zh' => '为不同语言区域更新翻译键和值。'],
            ['key' => 'translationManagement.create.description', 'en' => 'Add a new translation key with values for different locales.', 'km' => 'បន្ថែមការបកប្រែថ្មីមួយជាមួយនឹងតម្លៃសម្រាប់ភាសាផ្សេងៗ។', 'zh' => '为不同语言区域添加新的翻译键和值。'],
            ['key' => 'translationManagement.form.key.label', 'en' => 'Translation Key', 'km' => 'កូនសោបកប្រែ', 'zh' => '翻译键'],
            ['key' => 'translationManagement.form.key.placeholder', 'en' => 'e.g., nav.dashboard, buttons.save', 'km' => 'ឧទាហរណ៍៖ nav.dashboard, buttons.save', 'zh' => '例如：nav.dashboard, buttons.save'],
            ['key' => 'translationManagement.form.platform.label', 'en' => 'Platform', 'km' => 'វេទិកា', 'zh' => '平台'],
            ['key' => 'translationManagement.form.platform.placeholder', 'en' => 'Select a platform', 'km' => 'ជ្រើសរើសវេទិកា', 'zh' => '选择平台'],
            ['key' => 'translationManagement.form.status.label', 'en' => 'Status', 'km' => 'ស្ថានភាព', 'zh' => '状态'],
            ['key' => 'translationManagement.form.status.placeholder', 'en' => 'Select a status', 'km' => 'ជ្រើសរើសស្ថានភាព', 'zh' => '选择状态'],
            ['key' => 'translationManagement.form.status.active', 'en' => 'Active', 'km' => 'សកម្ម', 'zh' => '有效'],
            ['key' => 'translationManagement.form.status.inactive', 'en' => 'Inactive', 'km' => 'អសកម្ម', 'zh' => '无效'],
        ];
    }
}