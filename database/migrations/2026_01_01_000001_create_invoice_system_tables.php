<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Company Settings
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Assertiv Logix');
            $table->string('company_logo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->decimal('default_tax_rate', 5, 2)->default(0.00);
            $table->string('invoice_prefix')->default('INV');
            $table->integer('invoice_starting_number')->default(1);
            $table->string('payment_terms')->default('Net 30');
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_swift')->nullable();
            $table->text('bank_address')->nullable();
            $table->string('upi_id')->nullable();
            $table->text('default_invoice_notes')->nullable();
            $table->text('default_invoice_footer')->nullable();
            $table->string('default_template')->default('modern');
            $table->timestamps();
        });

        // 2. Clients
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->unique(); // e.g. CLI-001
            $table->enum('client_type', ['individual', 'company'])->default('company');
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('email');
            $table->string('secondary_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('payment_terms')->default('Net 30');
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // 3. Client Contacts
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('designation')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // 4. Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_id')->unique(); // e.g. PRJ-001
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('project_name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['Planning', 'In Progress', 'On Hold', 'Completed', 'Cancelled'])->default('In Progress');
            $table->decimal('budget', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Services Catalog
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->string('sku')->nullable()->unique();
            $table->text('description')->nullable();
            $table->enum('unit', ['Hour', 'Day', 'Project', 'Month', 'License', 'Item'])->default('Hour');
            $table->decimal('default_price', 15, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // 6. Taxes
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. GST 18%, VAT 20%, CGST 9%
            $table->decimal('rate', 5, 2);
            $table->string('type')->default('Standard'); // GST, CGST, SGST, IGST, VAT, Sales Tax
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Currencies
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // USD, EUR, GBP, INR, CAD, AUD, AED
            $table->string('name');
            $table->string('symbol', 10);
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 8. Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // e.g. INV-2026-0001
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('currency', 10)->default('USD');
            $table->string('payment_terms')->default('Net 30');
            $table->string('reference_number')->nullable();
            $table->string('po_number')->nullable();
            
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_value', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('taxable_amount', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('additional_charges', 15, 2)->default(0.00);
            $table->decimal('round_off', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->decimal('balance_due', 15, 2)->default(0.00);
            
            $table->enum('status', ['Draft', 'Sent', 'Viewed', 'Partially Paid', 'Paid', 'Overdue', 'Cancelled'])->default('Draft');
            $table->string('template')->default('modern');
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            
            $table->dateTime('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('cancellation_reason')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['client_id', 'status', 'due_date']);
        });

        // 9. Invoice Items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->string('unit')->default('Item');
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_value', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 10. Invoice Tax Detail Breakdown
        Schema::create('invoice_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->onDelete('set null');
            $table->string('tax_name');
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->timestamps();
        });

        // 11. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->unique(); // e.g. PAY-2026-0001
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // Bank Transfer, Credit Card, Debit Card, PayPal, Stripe, Razorpay, UPI, Cash, Other
            $table->string('transaction_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['Completed', 'Pending', 'Failed', 'Refunded'])->default('Completed');
            $table->timestamps();

            $table->index(['invoice_id', 'client_id', 'payment_date']);
        });

        // 12. Payment Links (Secure Tokens)
        Schema::create('payment_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('token', 100)->unique();
            $table->string('gateway')->default('Stripe');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->dateTime('expires_at')->nullable();
            $table->enum('status', ['Active', 'Used', 'Expired', 'Cancelled'])->default('Active');
            $table->string('gateway_payment_id')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'token', 'status']);
        });

        // 13. Payment Gateway Transactions Log
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->string('gateway');
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_order_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('status')->default('Pending'); // Pending, Processing, Completed, Failed, Refunded, Cancelled
            $table->string('payment_method')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });

        // 14. Payment Gateway Webhooks
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('event_id')->unique(); // Ensures idempotency
            $table->string('event_type');
            $table->string('payment_id')->nullable();
            $table->json('payload')->nullable();
            $table->text('signature')->nullable();
            $table->boolean('processed')->default(false);
            $table->dateTime('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // 15. Recurring Invoices
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->enum('frequency', ['Weekly', 'Monthly', 'Quarterly', 'Yearly'])->default('Monthly');
            $table->decimal('amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_invoice_date');
            $table->enum('status', ['Active', 'Paused', 'Completed'])->default('Active');
            $table->json('items_data')->nullable();
            $table->timestamps();
        });

        // 16. Invoice Reminders Log
        Schema::create('invoice_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('reminder_type'); // Due Soon, On Due Date, Overdue
            $table->integer('days_offset')->default(0);
            $table->dateTime('sent_at');
            $table->string('recipient_email');
            $table->string('status')->default('Sent');
            $table->timestamps();
        });

        // 17. Payment Gateway Settings
        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_name')->unique(); // Razorpay, Stripe, PayPal
            $table->string('api_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->enum('environment', ['test', 'live'])->default('test');
            $table->string('currency', 10)->default('USD');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 18. Email Logs
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->string('recipient_email');
            $table->string('subject');
            $table->string('email_type'); // Invoice, Payment Reminder, Payment Confirmation, Paid Invoice, Overdue Reminder
            $table->string('status')->default('Sent'); // Sent, Failed
            $table->dateTime('sent_at');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // 19. Audit Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('action'); // created, updated, deleted, sent, paid, cancelled
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('payment_gateway_settings');
        Schema::dropIfExists('invoice_reminders');
        Schema::dropIfExists('recurring_invoices');
        Schema::dropIfExists('payment_webhooks');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_links');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_taxes');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('services');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('client_contacts');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('company_settings');
    }
};
