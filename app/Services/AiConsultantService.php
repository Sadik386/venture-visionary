<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiConsultantService
{
    /**
     * Generate an intelligent, context-aware WhatsApp consultation message
     * tailored to the prospect's destination, service type, and language preference.
     */
    public function generateWelcomeMessage(Lead|array $lead, string $language = 'bilingual'): string
    {
        $name = is_array($lead) ? ($lead['name'] ?? 'Valued Client') : ($lead->name ?? 'Valued Client');
        $destination = is_array($lead) ? ($lead['destination_country'] ?? null) : ($lead->destination_country ?? null);
        $service = is_array($lead) ? ($lead['visa_or_service_type'] ?? null) : ($lead->visa_or_service_type ?? null);
        $leadNumber = is_array($lead) ? ($lead['lead_number'] ?? null) : ($lead->lead_number ?? null);
        $agentName = 'Venture Visionary Advisory Desk';

        if (! is_array($lead) && $lead->assignedTo) {
            $agentName = $lead->assignedTo->name;
        }

        // Fallback defaults
        $destination = $destination ?: 'International Visa & Immigration';
        $service = $service ?: 'Comprehensive Advisory Consultation';

        // Context-specific advice generation
        $advice = $this->buildContextualAdvice($destination, $service);

        if ($language === 'bn') {
            return $this->formatBengaliMessage($name, $destination, $service, $leadNumber, $agentName, $advice);
        }

        if ($language === 'en') {
            return $this->formatEnglishMessage($name, $destination, $service, $leadNumber, $agentName, $advice);
        }

        // Default: Bilingual (English + Bengali summary for maximum clarity)
        return $this->formatBilingualMessage($name, $destination, $service, $leadNumber, $agentName, $advice);
    }

    /**
     * Determine specific guidance points based on destination country and service category.
     */
    protected function buildContextualAdvice(string $destination, string $service): array
    {
        $destLower = strtolower($destination);
        $servLower = strtolower($service);

        // 1. Schengen Countries (Germany, France, Italy, Spain, etc.)
        if (str_contains($destLower, 'schengen') || str_contains($destLower, 'europe') || str_contains($destLower, 'germany') || str_contains($destLower, 'france') || str_contains($destLower, 'italy') || str_contains($destLower, 'spain')) {
            return [
                'type' => 'schengen_visa',
                'highlights' => [
                    'Schengen Type-C Tourist & Business Visa file lodgment',
                    'Slot monitoring for TLScontact / BLS / VFS Global appointment slots',
                    'Compliant travel insurance policy & flight/hotel confirmation vouchers',
                ],
                'highlights_bn' => [
                    'শেনজেন টাইপ-সি ভিসা অ্যাপ্লিকেশন, ট্রাভেল ইন্স্যুরেন্স ও হোটেল বুকিং ভাউচার',
                    'ভিএফএস / টিএলএস কনট্যাক্ট স্লট বুকিং সাপোর্ট',
                ],
            ];
        }

        // 2. United Kingdom (UK) Visas
        if (str_contains($destLower, 'uk') || str_contains($destLower, 'kingdom') || str_contains($destLower, 'britain')) {
            return [
                'type' => 'uk_visa',
                'highlights' => [
                    'UK Standard Visitor / Tourist Visa documentation audit (financial sufficiency & ties)',
                    'Official VFS Global priority appointment booking & biometric scheduling',
                    'Tailored itinerary, sponsor letter drafting, and refusal mitigation QC',
                ],
                'highlights_bn' => [
                    'ইউকে স্ট্যান্ডার্ড ভিজিটর ভিসা ডকুমেন্ট ভেরিফিকেশন ও স্পন্সর লেটার ড্রাফটিং',
                    'ভিএফএস গ্লোবাল দ্রুত অ্যাপয়েন্টমেন্ট শিডিউলিং ও ফাইল অডিট',
                ],
            ];
        }

        // 3. Qatar Business Setup / Corporate Formation
        if (str_contains($destLower, 'qatar') && (str_contains($servLower, 'business') || str_contains($servLower, 'company') || str_contains($servLower, 'llc') || str_contains($servLower, 'formation') || str_contains($servLower, 'setup'))) {
            return [
                'type' => 'business_setup',
                'highlights' => [
                    '100% foreign ownership eligible structures under Qatar Law No. 1/2019',
                    'Commercial Registration (CR) & Trade License processing within 5-7 business days',
                    'Dedicated corporate bank account introduction & tax card setup',
                ],
                'highlights_bn' => [
                    'কাতারে ১০০% ফরেন ওনারশিপ এলএলসি ও কমার্শিয়াল রেজিস্ট্রেশন (CR) প্রসেসিং',
                    'ট্রেড লাইসেন্স, কর্পোরেট ব্যাংক অ্যাকাউন্ট ও ইনভেস্টর রেসিডেন্সি সহায়তা',
                ],
            ];
        }

        // 4. USA / Canada
        if (str_contains($destLower, 'usa') || str_contains($destLower, 'america') || str_contains($destLower, 'canada')) {
            return [
                'type' => 'north_america',
                'highlights' => [
                    'DS-160 / IRCC portal accurate filing with zero discrepancy guarantee',
                    'Embassy interview preparation & mock interview simulation',
                    'Detailed proof of funds evaluation to ensure embassy compliance',
                ],
                'highlights_bn' => [
                    'ডিএস-১৬০ / কানাডা পোর্টাল নিখুঁত আবেদন ও এম্বাসি ইন্টারভিউ প্রিপারেশন',
                    'ব্যাংক স্টেটমেন্ট ও টাইস প্রুফ মূল্যায়ন',
                ],
            ];
        }

        // 5. Default General Visa
        return [
            'type' => 'general',
            'highlights' => [
                "Full pre-submission compliance audit for {$destination} ({$service})",
                'Direct embassy checklist verification to prevent refusals',
                'Assigned personal consultant tracking your milestone timeline',
            ],
            'highlights_bn' => [
                "{$destination}-এর জন্য পূর্ণাঙ্গ ডকুমেন্ট অডিট ও এম্বাসি রিকয়ারমেন্ট যাচাই",
                'ডেডিকেটেড কনসালট্যান্ট দ্বারা ফাইল প্রসেসিং সাপোর্ট',
            ],
        ];
    }

    /**
     * Format bilingual message (English + Bengali).
     */
    protected function formatBilingualMessage(string $name, string $destination, string $service, ?string $leadNumber, string $agentName, array $advice): string
    {
        $refTag = $leadNumber ? " (Ref: *{$leadNumber}*)" : "";

        $msg = "✨ *Assalamu Alaikum / Greetings, {$name}!* ✨\n\n";
        $msg .= "Thank you for reaching out to *Venture Visionary Visa & Business Consultancy, Doha*. We have received your consultation inquiry for *{$destination} — {$service}*{$refTag}.\n\n";

        $msg .= "🏛️ *Key Highlights of our Service:*\n";
        foreach ($advice['highlights'] as $item) {
            $msg .= "• {$item}\n";
        }

        $msg .= "\n🇧🇩 *আপনার জন্য গুরুত্বপূর্ণ তথ্য:*\n";
        foreach ($advice['highlights_bn'] as $item) {
            $msg .= "• {$item}\n";
        }

        $msg .= "\n👤 *Assigned Consultant:* {$agentName}\n";
        $msg .= "📍 *Office:* C-Ring Road, Doha, Qatar\n";
        $msg .= "💬 *Next Step:* Please reply directly to this message with your preferred consultation time or questions. We are here to ensure your seamless approval!";

        return $msg;
    }

    /**
     * Format purely English message.
     */
    protected function formatEnglishMessage(string $name, string $destination, string $service, ?string $leadNumber, string $agentName, array $advice): string
    {
        $refTag = $leadNumber ? " [Ref: #{$leadNumber}]" : "";

        $msg = "Dear *{$name}*,\n\n";
        $msg .= "Thank you for contacting *Venture Visionary Visa & Business Consultancy, Doha*. Your consultation inquiry for *{$destination} ({$service})* has been registered{$refTag}.\n\n";

        $msg .= "💼 *Our Professional Advisory Scope:*\n";
        foreach ($advice['highlights'] as $item) {
            $msg .= "✔ {$item}\n";
        }

        $msg .= "\n👨‍💼 *Assigned Senior Consultant:* {$agentName}\n";
        $msg .= "📞 *Office:* Doha, State of Qatar\n\n";
        $msg .= "Please reply to this WhatsApp message to schedule a call or office meeting.";

        return $msg;
    }

    /**
     * Format purely Bengali message.
     */
    protected function formatBengaliMessage(string $name, string $destination, string $service, ?string $leadNumber, string $agentName, array $advice): string
    {
        $refTag = $leadNumber ? " (রেফারেন্স: *{$leadNumber}*)" : "";

        $msg = "আসসালামু আলাইকুম *{$name}*,\n\n";
        $msg .= "*ভেঞ্চার ভিশনারি ভিসা ও বিজনেস কনসালটেন্সি (দোহা, কাতার)*-এ যোগাযোগের জন্য ধন্যবাদ। *{$destination} ({$service})* সংক্রান্ত আপনার অনুসন্ধানটি গ্রহণ করা হয়েছে{$refTag}।\n\n";

        $msg .= "📋 *আমাদের সার্ভিস সুবিধাসমূহ:*\n";
        foreach ($advice['highlights_bn'] as $item) {
            $msg .= "• {$item}\n";
        }

        $msg .= "\n👨‍💼 *আপনার কনসালট্যান্ট:* {$agentName}\n";
        $msg .= "সরাসরি বিস্তারিত আলোচনা বা ফাইল প্রসেসিং শুরুর জন্য এই নম্বরেই রিপ্লাই দিন।";

        return $msg;
    }
}
