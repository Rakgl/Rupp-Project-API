<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		DB::table('faqs')->insert([
            // [
            //     'id' => Str::uuid(),
            //     'question' => json_encode(['en' => 'How can I top up my wallet?', 'kh' => 'តើខ្ញុំអាចបញ្ចូលទឹកប្រាក់នៅក្នុងប៉ោងរបស់ខ្ញុំដូចម្តេច?']),
            //     'answer' => json_encode(['en' => 'To top up your wallet, select the Top Up option and choose your preferred payment method.', 'kh' => 'ដើម្បីបញ្ចូលប៉ោងរបស់អ្នក សូមជ្រើសរើសជម្រើស បញ្ចូល និងជ្រើសរើសវិធីបង់ប្រាក់ដែលអ្នកចូលចិត្ត។']),
            //     'category' => 'FAQ',
			// 	'image' => null,
            //     'platform' => 'MOBILE',
            //     'status' => 'ACTIVE',
            //     'created_by' => 'admin',
            //     'updated_by' => null,
            //     'update_num' => 0,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'id' => Str::uuid(),
            //     'question' => json_encode(['en' => 'How do I check my charging history?', 'kh' => 'តើខ្ញុំអាចពិនិត្យប្រវត្តិការចរចារបស់ខ្ញុំបានដូចម្តេច?']),
            //     'answer' => json_encode(['en' => 'To check your charging history, go to the Wallet section and select Charging History.', 'kh' => 'ដើម្បីពិនិត្យប្រវត្តិការចរចារបស់អ្នក សូមចូលទៅផ្នែក ប៉ោង រួចជ្រើសប្រវត្តិការចរចា។']),
            //     'category' => 'FAQ',
			// 	'image' => null,
            //     'platform' => 'MOBILE',
            //     'status' => 'ACTIVE',
            //     'created_by' => 'admin',
            //     'updated_by' => null,
            //     'update_num' => 0,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'id' => Str::uuid(),
            //     'question' => json_encode(['en' => 'What is the purpose of this app?', 'kh' => 'តើគោលបំណងនៃកម្មវិធីនេះគឺជាអ្វី?']),
            //     'answer' => json_encode(['en' => 'This app is designed to help you charge your electric vehicle and manage your wallet for EV charging sessions.', 'kh' => 'កម្មវិធីនេះត្រូវបានរចនាឡើងដើម្បីជួយអ្នកក្នុងការចរចាររថយន្តអគ្គិសនី និងគ្រប់គ្រងប៉ោងរបស់អ្នកសម្រាប់ការចរចាររថយន្តអគ្គិសនី។']),
            //     'category' => 'ABOUT',
			// 	'image' => null,
            //     'platform' => 'MOBILE',
            //     'status' => 'ACTIVE',
            //     'created_by' => 'admin',
            //     'updated_by' => null,
            //     'update_num' => 0,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'id' => Str::uuid(),
            //     'question' => json_encode(['en' => 'What payment methods are supported?', 'kh' => 'តើវិធីបង់ប្រាក់ណាខ្លះដែលត្រូវបានគាំទ្រ?']),
            //     'answer' => json_encode(['en' => 'We support a variety of payment methods including credit cards, ABA Bank, and more.', 'kh' => 'យើងគាំទ្រវិធីបង់ប្រាក់ជាច្រើន រួមទាំងកាតឥណទាន, អេប៊ីអេ បែង និងអ្វីៗជាច្រើនទៀត។']),
            //     'category' => 'ABOUT',
			// 	'image' => null,
            //     'platform' => 'MOBILE',
            //     'status' => 'ACTIVE',
            //     'created_by' => 'admin',
            //     'updated_by' => null,
            //     'update_num' => 0,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'id' => Str::uuid(),
            //     'question' => json_encode(['en' => 'How do I contact customer support?', 'kh' => 'តើខ្ញុំអាចទាក់ទងជាមួយការគាំទ្រប្រាក់កាច់ត្រូវបានដូចម្តេច?']),
            //     'answer' => json_encode(['en' => 'To contact customer support, go to the Support section and submit a ticket or chat with us live.', 'kh' => 'ដើម្បីទាក់ទងការគាំទ្រប្រាក់កាច់សូមចូលទៅផ្នែកជំនួយ រួចបញ្ជូនសំបុត្រឬជជែកផ្ទាល់ជាមួយយើង។']),
            //     'category' => 'SUPPORT',
			// 	'image' => null,
            //     'platform' => 'MOBILE',
            //     'status' => 'ACTIVE',
            //     'created_by' => 'admin',
            //     'updated_by' => null,
            //     'update_num' => 0,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
            // [
            //     'id' => Str::uuid(),
            //     'question' => json_encode(['en' => 'How do I reset my password?', 'kh' => 'តើខ្ញុំអាចកំណត់ពាក្យសម្ងាត់ឡើងវិញដូចម្តេច?']),
            //     'answer' => json_encode(['en' => 'To reset your password, go to the Account Settings and select Reset Password.', 'kh' => 'ដើម្បីកំណត់ពាក្យសម្ងាត់ឡើងវិញ សូមចូលទៅផ្នែកការកំណត់គណនី រួចជ្រើសការកំណត់ពាក្យសម្ងាត់។']),
            //     'category' => 'SUPPORT',
			// 	'image' => null,
            //     'platform' => 'MOBILE',
            //     'status' => 'ACTIVE',
            //     'created_by' => 'admin',
            //     'updated_by' => null,
            //     'update_num' => 0,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
        ]);
    }
}
