<?php

namespace Database\Seeders;

use App\Models\AppDownloadLink;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppDownloadLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $links = [
            [
                'platform' => 'android',
                'name' => 'Google Play Store',
                'url' => 'https://play.google.com/store/apps/details?id=com.yourcompany.petshop',
            ],
            [
                'platform' => 'ios',
                'name' => 'Apple App Store',
                'url' => 'https://apps.apple.com/app/your-app-id',
            ],
        ];

        $writer = new SvgWriter();

        foreach ($links as $linkData) {
            $qrCode = new QrCode($linkData['url']);
            $svgResult = $writer->write($qrCode);
            $qrCodeSvg = $svgResult->getString();

            AppDownloadLink::updateOrCreate(
                ['platform' => $linkData['platform']],
                [
                    'id' => Str::uuid(), 
                    'name' => $linkData['name'],
                    'url' => $linkData['url'],
                    'qr_code_svg' => $qrCodeSvg,
                    'is_active' => true,
                ]
            );
        }
    }
}