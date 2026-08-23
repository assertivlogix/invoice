<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function getSettings(): self
    {
        return self::firstOrCreate([], [
            'company_name' => 'Assertiv Logix',
            'email' => 'info@assertivlogix.com',
            'phone' => '+1 (555) 019-2834',
            'website' => 'https://assertivlogix.com',
            'address' => '100 Innovation Way, Suite 400',
            'city' => 'Tech City',
            'state' => 'CA',
            'country' => 'United States',
            'zip_code' => '94016',
            'tax_number' => 'US987654321',
            'registration_number' => 'REG-2024-889',
            'currency' => 'USD',
            'default_tax_rate' => 0.00,
            'invoice_prefix' => 'INV',
            'invoice_starting_number' => 1,
            'payment_terms' => 'Net 30',
            'bank_name' => 'Silicon Valley Commercial Bank',
            'account_name' => 'Assertiv Logix Inc.',
            'account_number' => '987654321012',
            'ifsc_swift' => 'SVCBUS66',
            'bank_address' => '450 Financial District, San Francisco, CA',
            'upi_id' => 'assertivlogix@upi',
            'default_invoice_notes' => 'Thank you for your business. Please make payments by the due date.',
            'default_invoice_footer' => 'Assertiv Logix — Premium Web Development & Digital Solutions',
            'default_template' => 'modern',
        ]);
    }
}
