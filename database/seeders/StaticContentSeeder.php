<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StaticContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            [
                'id' => (string) Str::uuid(),
                'title' => json_encode(['en' => 'Privacy Policy']),
                'content' => json_encode(['en' => $this->getPrivacyPolicyContent()]),
                'type' => 'PRIVACY_POLICY',
                'status' => 'ACTIVE',
                'created_by' => 'system',
                'updated_by' => 'system',
                'update_num' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'title' => json_encode(['en' => 'Terms and Conditions']),
                'content' => json_encode(['en' => $this->getTermsContent()]),
                'type' => 'TERMS_AND_CONDITIONS',
                'status' => 'ACTIVE',
                'created_by' => 'system',
                'updated_by' => 'system',
                'update_num' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'title' => json_encode(['en' => 'About Us']),
                'content' => json_encode(['en' => $this->getAboutUsContent()]),
                'type' => 'ABOUT_US',
                'status' => 'ACTIVE',
                'created_by' => 'system',
                'updated_by' => 'system',
                'update_num' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];


        DB::table('static_contents')->insert($contents);
    }

    /**
     * Get sample privacy policy content
     */
    private function getPrivacyPolicyContent(): string
    {
        return <<<EOT
# Privacy Policy

Last updated: November 21, 2024

## 1. Introduction

Welcome to our Privacy Policy. Your privacy is critically important to us. This Privacy Policy document contains types of information that is collected and recorded by us and how we use it.

## 2. Information We Collect

### 2.1 Personal Information
- Name and email address
- Contact information
- Usage data and preferences
- Device information

### 2.2 Log Data
We collect information that your browser sends whenever you visit our website. This Log Data may include:
- Internet Protocol (IP) address
- Browser type and version
- Pages visited
- Time and date of your visit
- Time spent on pages

## 3. How We Use Your Information

We use the collected data for various purposes:
- To provide and maintain our service
- To notify you about changes to our service
- To provide customer support
- To detect, prevent and address technical issues

## 4. Data Security

We implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet is 100% secure.

## 5. Contact Us

If you have any questions about this Privacy Policy, please contact us.
EOT;
    }

    /**
     * Get sample terms and conditions content
     */
    private function getTermsContent(): string
    {
        return <<<EOT
# Terms and Conditions

Last updated: November 21, 2024

## 1. Agreement to Terms

By accessing our website, you agree to be bound by these Terms and Conditions and agree that you are responsible for compliance with any applicable local laws.

## 2. Use License

Permission is granted to temporarily download one copy of the materials (information or software) on our website for personal, non-commercial transitory viewing only.

## 3. Disclaimer

The materials on our website are provided on an 'as is' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including, without limitation:
- Merchantability
- Fitness for a particular purpose
- Non-infringement of intellectual property

## 4. Limitations

In no event shall we or our suppliers be liable for any damages arising out of the use or inability to use the materials on our website.

## 5. Revisions and Errata

The materials appearing on our website could include technical, typographical, or photographic errors.

## 6. Governing Law

These terms and conditions are governed by and construed in accordance with applicable laws.
EOT;
    }

    /**
     * Get sample about us content
     */
    private function getAboutUsContent(): string
    {
        return <<<EOT
# About Us

## Our Story

Founded with a vision to innovate and excel, our company has been at the forefront of delivering exceptional services and solutions. We believe in creating value through technology and human expertise.

## Our Mission

Our mission is to empower businesses and individuals with innovative solutions that drive growth and success. We strive to:
- Deliver excellence in everything we do
- Foster innovation and creativity
- Build lasting relationships with our clients
- Contribute positively to society

## Our Team

Our team consists of passionate professionals who bring diverse expertise and experiences to the table. We believe in:
- Continuous learning and improvement
- Collaborative problem-solving
- Customer-centric approach
- Excellence in execution

## Our Values

### Innovation
We constantly push boundaries and explore new possibilities.

### Integrity
We conduct our business with the highest ethical standards.

### Excellence
We strive for excellence in every aspect of our work.

### Collaboration
We believe in the power of teamwork and partnerships.
EOT;
    }
}
