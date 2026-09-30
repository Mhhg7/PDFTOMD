<?php

namespace App\Support;

/**
 * Server rules for the public forms. Field names match formDef() in
 * public/site.js; labels are only used by the dashboard inbox.
 */
class FormSpec
{
    public const KINDS = [
        'partner' => [
            'label' => 'Partnership requests',
            'fields' => [
                'company' => ['Company', 'required|string|max:200'],
                'country' => ['Country', 'required|string|max:200'],
                'name' => ['Name', 'required|string|max:200'],
                'email' => ['Email', 'required|email:rfc|max:200'],
                'phone' => ['Phone', 'nullable|string|max:60'],
                'area' => ['Therapeutic area', 'nullable|string|max:200'],
                'portfolio' => ['Portfolio', 'required|string|max:5000'],
            ],
        ],
        'inquiry' => [
            'label' => 'Inquiries',
            'fields' => [
                'name' => ['Name', 'required|string|max:200'],
                'org' => ['Organisation', 'nullable|string|max:200'],
                'email' => ['Email', 'required|email:rfc|max:200'],
                'phone' => ['Phone', 'nullable|string|max:60'],
                'subject' => ['Subject', 'required|string|max:200'],
                'message' => ['Message', 'required|string|max:5000'],
            ],
        ],
        'medical' => [
            'label' => 'Medical information',
            'fields' => [
                'name' => ['Name', 'required|string|max:200'],
                'role' => ['Profession', 'required|string|max:200'],
                'email' => ['Email', 'required|email:rfc|max:200'],
                'product' => ['Product', 'required|string|max:200'],
                'question' => ['Question', 'required|string|max:5000'],
            ],
        ],
        'apply' => [
            'label' => 'Job applications',
            'fields' => [
                'name' => ['Full name', 'required|string|max:200'],
                'email' => ['Email', 'required|email:rfc|max:200'],
                'phone' => ['Phone', 'required|string|max:60'],
                'role' => ['Area of interest', 'required|string|max:200'],
                'city' => ['Preferred hub', 'nullable|string|max:200'],
                'note' => ['Note', 'nullable|string|max:5000'],
            ],
            'file' => ['cv', 'CV', 'required|file|mimes:pdf,doc,docx|max:5120'],
        ],
        'newsletter' => [
            'label' => 'Newsletter sign-ups',
            'fields' => [
                'email' => ['Email', 'required|email:rfc|max:200'],
            ],
        ],
    ];

    public static function label(string $kind): string
    {
        return self::KINDS[$kind]['label'] ?? ucfirst($kind);
    }

    public static function fieldLabel(string $kind, string $field): string
    {
        return self::KINDS[$kind]['fields'][$field][0] ?? ucfirst($field);
    }
}
