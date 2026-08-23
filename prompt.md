# Laravel Client & Invoice Management System — Complete Development Prompt

Build a production-ready **Laravel-based Client & Invoice Management System** for an IT/web development company.

The system must allow the company administrator to manage clients, create professional invoices, track invoice status and payments, view complete client history, and generate reports.

The application should have a clean, modern, responsive admin dashboard and should be designed so additional features can be added later.

---

## 1. Technology Stack

Use the following stack:

* Laravel 12 or latest stable Laravel version
* PHP 8.3+
* MySQL 8+
* Blade templates
* Bootstrap 5 or Tailwind CSS
* Laravel Eloquent ORM
* Laravel Authentication
* Laravel Validation
* Laravel Notifications
* Laravel Queues where appropriate
* DomPDF or another reliable Laravel PDF package for invoice PDFs
* JavaScript/AJAX where useful
* Chart.js for dashboard charts
* REST-friendly architecture
* Use migrations, seeders, factories, policies, services, and repositories where appropriate

Follow Laravel best practices.

Do NOT put all business logic inside controllers.

Use:

* Models
* Form Requests
* Services
* Policies
* Notifications
* Jobs where required
* Resources/API structure if APIs are added

---

# 2. Main Objective

The application is for an IT company that works with multiple clients.

The administrator should be able to:

1. Create clients
2. Store complete client information
3. Create invoices for specific clients
4. Add multiple products/services to invoices
5. Automatically calculate subtotal, tax, discount, and grand total
6. Generate professional invoice numbers
7. Generate invoice PDFs
8. Download invoices
9. Email invoices to clients
10. Track paid/unpaid/partial/overdue invoices
11. Record payments
12. Track outstanding balances
13. View all invoices
14. Filter invoices by client
15. View complete history of a particular client
16. See all invoices generated for a client
17. See total invoiced amount for a client
18. See total paid amount
19. See total outstanding amount
20. See overdue amount
21. Track projects/services provided to each client
22. Generate financial reports
23. Search and filter records
24. Export invoice/client/payment data

---

# 3. User Roles

Initially implement two roles.

## Admin

Full access:

* Dashboard
* Clients
* Invoices
* Payments
* Services
* Projects
* Reports
* Company Settings
* Users
* Invoice Settings

## Staff

Optional restricted role.

Staff can:

* View clients
* Create invoices
* View invoices
* Update invoice information
* Record payments

Staff cannot:

* Delete important financial records
* Change company settings
* Manage users

Use Laravel Policies/Middleware for authorization.

Design the system so additional roles can be added later.

---

# 4. Authentication

Create secure authentication.

Features:

* Login
* Logout
* Forgot password
* Reset password
* Remember me
* Password hashing
* Session protection
* Role-based access

Dashboard should only be accessible to authenticated users.

---

# 5. Company Profile

Create a company settings section.

Fields:

* Company Name
* Company Logo
* Email
* Phone
* Website
* Address
* City
* State
* Country
* ZIP/Postal Code
* Tax/VAT/GST Number
* Registration Number
* Currency
* Default Tax Rate
* Invoice Prefix
* Invoice Starting Number
* Payment Terms
* Bank Name
* Account Name
* Account Number
* IFSC/SWIFT
* Bank Address
* UPI ID
* Default Invoice Notes
* Default Invoice Footer

Example company:

Assertiv Logix

The company information should automatically appear on generated invoices.

---

# 6. Client Management

Create a complete Client Management module.

## Client Fields

* Client ID

* Client Type

  * Individual
  * Company

* Company Name

* Contact Person

* Email

* Secondary Email

* Phone

* WhatsApp

* Website

* Address

* City

* State

* Country

* ZIP/Postal Code

* Tax/VAT/GST Number

* Company Registration Number

* Currency

* Payment Terms

* Notes

* Status

  * Active
  * Inactive

* Created Date

* Updated Date

---

# 7. Client Detail Page

This is one of the most important pages.

When clicking:

Clients → Specific Client

show a complete client dashboard.

Example:

Client: ABC Technologies

Display:

### Client Overview

* Company name
* Contact person
* Email
* Phone
* Website
* Country
* Tax ID
* Status

### Financial Summary

Show cards:

* Total Invoices
* Total Invoiced
* Total Paid
* Total Outstanding
* Total Overdue
* Last Payment
* Last Invoice

Example:

Total Invoices: 18

Total Invoiced: $25,500

Total Paid: $21,000

Outstanding: $4,500

Overdue: $1,200

---

# 8. Client Invoice History

On the client detail page show all invoices belonging to that client.

Columns:

* Invoice Number
* Invoice Date
* Due Date
* Amount
* Paid Amount
* Balance
* Status
* Actions

Actions:

* View
* Edit
* Download PDF
* Email
* Record Payment
* Duplicate

Add filters:

* All
* Draft
* Sent
* Paid
* Partial
* Overdue
* Cancelled

---

# 9. Client Payment History

Display all payments made by the client.

Columns:

* Payment Date
* Payment ID
* Invoice Number
* Payment Method
* Reference Number
* Amount
* Notes

Payment methods:

* Bank Transfer
* Credit Card
* Debit Card
* PayPal
* Stripe
* Razorpay
* UPI
* Cash
* Other

---

# 10. Client Projects

Allow projects to be associated with clients.

Project fields:

* Project ID
* Client
* Project Name
* Description
* Start Date
* End Date
* Project Status
* Project Budget
* Notes

Statuses:

* Planning
* In Progress
* On Hold
* Completed
* Cancelled

Invoices can optionally be linked to a project.

Example:

Client:

ABC Technologies

Projects:

* Corporate Website
* WooCommerce Development
* Mobile Application
* Website Maintenance

---

# 11. Services / Products

Create a Services module.

Example services:

* WordPress Development
* WooCommerce Development
* Laravel Development
* Website Design
* Plugin Development
* Theme Development
* Website Maintenance
* SEO Services
* Website Speed Optimization
* Mobile App Development
* Custom Web Application
* UI/UX Design
* API Development

Service fields:

* Service Name
* SKU
* Description
* Unit
* Default Price
* Tax Rate
* Status

Units:

* Hour
* Day
* Project
* Month
* License
* Item

Allow services to be selected while creating an invoice.

---

# 12. Invoice Creation

Create a professional invoice creation interface.

Invoice form:

## Client

Select client from searchable dropdown.

When client is selected, automatically load:

* Company name
* Contact person
* Billing address
* Email
* Tax number
* Currency
* Payment terms

---

## Invoice Information

Fields:

* Invoice Number
* Invoice Date
* Due Date
* Client
* Project
* Currency
* Payment Terms
* Reference Number
* Purchase Order Number

Invoice number should be automatically generated.

Example:

INV-2026-0001

Next:

INV-2026-0002

etc.

Allow configurable prefixes.

---

# 13. Invoice Items

Create dynamic invoice line items.

Each item should contain:

* Service/Product
* Description
* Quantity
* Unit
* Rate
* Discount
* Tax
* Amount

Example:

WordPress Development

Quantity: 20 Hours

Rate: $25

Amount: $500

Allow:

* Add Item

- Remove Item

Use JavaScript/AJAX for dynamic calculations.

---

# 14. Invoice Calculations

Automatically calculate:

Subtotal

Discount

Taxable Amount

Tax

Additional Charges

Round Off

Grand Total

Paid Amount

Balance Due

Formula:

Subtotal = Sum of invoice items

Taxable Amount = Subtotal - Discount

Tax = Taxable Amount × Tax Rate

Grand Total = Taxable Amount + Tax + Additional Charges

Balance = Grand Total - Paid Amount

Calculations should update instantly without page reload.

---

# 15. Discounts

Support:

* Item-level discount
* Invoice-level discount

Discount types:

* Fixed amount
* Percentage

Example:

Subtotal: $1,000

Discount: 10%

Discount Amount: $100

Taxable Amount: $900

---

# 16. Taxes

Support configurable tax rates.

Examples:

* GST
* VAT
* Sales Tax
* Custom Tax

Allow:

* No Tax
* Single Tax
* Multiple Taxes

For India, support:

* CGST
* SGST
* IGST

Example:

Subtotal: ₹10,000

CGST 9%: ₹900

SGST 9%: ₹900

Total: ₹11,800

Tax settings should be configurable.

---

# 17. Invoice Status

Invoice statuses:

* Draft
* Sent
* Viewed
* Partially Paid
* Paid
* Overdue
* Cancelled

Automatically mark invoice as:

Overdue

when:

Current Date > Due Date

and balance > 0.

---

# 18. Invoice Details Page

Create a professional invoice preview page.

Display:

Company:

Assertiv Logix

Invoice:

INV-2026-0001

Client:

ABC Technologies

Invoice Date:

August 23, 2026

Due Date:

September 22, 2026

Invoice table:

| Description | Qty | Rate | Tax | Amount |

Then:

Subtotal

Discount

Tax

Grand Total

Paid

Balance Due

Payment information

Notes

Terms & Conditions

Company footer

Actions:

* Edit
* Download PDF
* Print
* Email Invoice
* Record Payment
* Duplicate
* Cancel Invoice

---

# 19. Invoice PDF

Generate a professional PDF invoice.

The PDF should include:

* Company logo
* Company information
* Client information
* Invoice number
* Invoice date
* Due date
* Project
* Items
* Quantity
* Rate
* Tax
* Discount
* Subtotal
* Grand total
* Paid amount
* Balance due
* Payment instructions
* Bank details
* UPI information
* Notes
* Terms and conditions
* Footer

The PDF should look professional enough to send directly to an international client.

Support:

* USD
* EUR
* GBP
* INR
* CAD
* AUD
* Other currencies

Use proper currency formatting.

---

# 20. Invoice Email

Add:

"Send Invoice"

button.

When clicked:

Send an email to the client's email address.

Email should contain:

Subject:

Invoice INV-2026-0001 from Assertiv Logix

Body:

Dear [Client Name],

Please find attached invoice INV-2026-0001 for [Project/Service].

Invoice Total: $5,000

Due Date: September 22, 2026

Thank you for your business.

Attach generated PDF.

Use Laravel Mailables.

Store email sending history.

---

# 21. Payment Management

Create a payment module.

Admin can record payments manually.

Payment fields:

* Payment ID
* Client
* Invoice
* Payment Date
* Amount
* Payment Method
* Transaction ID
* Reference Number
* Notes
* Created By

When payment is recorded:

Update invoice:

Paid Amount

and

Balance Due

automatically.

---

# 22. Payment Logic

Example:

Invoice Total:

$5,000

Payment 1:

$2,000

Invoice Status:

Partially Paid

Balance:

$3,000

Payment 2:

$3,000

Invoice Status:

Paid

Balance:

$0

Prevent payment amount from exceeding invoice balance unless overpayments are intentionally supported.

---

# 23. Invoice Listing

Create:

Invoices → All Invoices

Table:

* Invoice #
* Client
* Project
* Date
* Due Date
* Total
* Paid
* Balance
* Status
* Actions

Search by:

* Invoice number
* Client name
* Email
* Project

Filters:

* Client
* Status
* Date range
* Currency
* Project

Sorting:

* Newest
* Oldest
* Highest amount
* Lowest amount
* Due date

Pagination required.

---

# 24. Dashboard

Create a professional admin dashboard.

Top cards:

### Total Clients

Number of clients

### Total Invoiced

Total invoice value

### Total Paid

Total received

### Outstanding

Amount currently due

### Overdue

Overdue amount

### This Month

Invoices generated this month

---

# 25. Dashboard Charts

Add charts.

## Revenue Chart

Monthly:

January
February
March
April
etc.

Show:

* Invoiced
* Paid
* Outstanding

## Invoice Status Chart

Show:

* Draft
* Sent
* Paid
* Partial
* Overdue

## Top Clients

Show clients based on total invoiced amount.

Example:

1. ABC Technologies — $25,000
2. XYZ Solutions — $18,000
3. Demo Corporation — $12,500

---

# 26. Dashboard Recent Activity

Show:

Recent invoices

Recent payments

Recent clients

Recent projects

Example:

"Invoice INV-2026-0042 created for ABC Technologies"

"Payment $2,500 received from XYZ Solutions"

---

# 27. Reports

Create Reports module.

Reports:

## Invoice Report

Filters:

* Date range
* Client
* Status
* Currency

Show:

* Number of invoices
* Total invoiced
* Total paid
* Outstanding
* Overdue

## Payment Report

Show:

* Payment count
* Total payments
* Payment methods
* Client-wise payments

## Client Revenue Report

Show:

Client

Total Invoices

Total Revenue

Paid

Outstanding

## Tax Report

Show:

* Taxable amount
* Tax collected
* Tax type
* Tax amount

Allow CSV/Excel export.

---

# 28. Client Statement

Create:

"Generate Client Statement"

When selecting a client and date range, generate a statement containing:

Client details

Opening Balance

Invoices

Payments

Credits/Adjustments

Closing Balance

Example:

Date | Type | Reference | Debit | Credit | Balance

This is extremely important for maintaining client accounts.

Allow:

* View
* Print
* Download PDF
* Email

---

# 29. Invoice Numbering

Create automatic invoice numbering.

Example:

INV-2026-0001

INV-2026-0002

INV-2026-0003

Settings:

Prefix:

INV

Year:

2026

Starting Number:

1

Allow administrator to change prefix.

Do not allow duplicate invoice numbers.

Use database-level uniqueness.

---

# 30. Database Design

Create proper normalized database tables.

Recommended tables:

users

clients

client_contacts

projects

services

invoices

invoice_items

payments

taxes

invoice_taxes

company_settings

invoice_settings

payment_methods

activity_logs

email_logs

currencies

client_statements

---

# 31. Important Database Relationships

Client:

hasMany Projects

hasMany Invoices

hasMany Payments

Invoice:

belongsTo Client

belongsTo Project

hasMany InvoiceItems

hasMany Payments

InvoiceItem:

belongsTo Invoice

belongsTo Service

Payment:

belongsTo Client

belongsTo Invoice

Project:

belongsTo Client

hasMany Invoices

Service:

hasMany InvoiceItems

---

# 32. Database Integrity

Use:

* Foreign keys
* Indexes
* Unique constraints
* Soft deletes where appropriate
* Decimal database fields for money

Never use floating point for financial amounts.

Use:

DECIMAL(15,2)

for monetary values.

---

# 33. Invoice Audit Trail

Create an activity log.

Track:

* Invoice created
* Invoice updated
* Invoice sent
* Invoice viewed
* Payment added
* Payment updated
* Invoice cancelled
* Invoice downloaded

Example:

August 23, 2026 — Invoice INV-2026-0045 created by Admin

August 23, 2026 — Invoice emailed to client

August 25, 2026 — Payment of $2,000 recorded

---

# 34. Search

Global search should support:

Clients

Invoices

Projects

Payments

Services

Search by:

* Name
* Email
* Invoice number
* Phone
* Transaction ID

---

# 35. Filters

Every major listing page should have useful filters.

Clients:

* Active
* Inactive
* Country
* Date created

Invoices:

* Client
* Status
* Date
* Due date
* Amount

Payments:

* Client
* Invoice
* Payment method
* Date range

Projects:

* Client
* Status
* Date

---

# 36. Client Dashboard UX

Make the client detail page especially useful.

Layout:

Client Header

↓

Financial Summary Cards

↓

Outstanding Invoice Alert

↓

Revenue Chart

↓

Invoice History

↓

Payment History

↓

Projects

↓

Activity Timeline

The administrator should be able to understand the entire relationship with the client from one page.

---

# 37. Recurring Invoices

Implement recurring invoices.

Options:

* Weekly
* Monthly
* Quarterly
* Yearly

Recurring invoice fields:

* Client
* Project
* Services
* Amount
* Start Date
* End Date
* Frequency
* Next Invoice Date
* Status

Use Laravel Scheduler/Queue to generate invoices automatically.

Example:

Client pays $1,000 monthly for maintenance.

System automatically generates:

INV-2026-0001

INV-2026-0002

INV-2026-0003

etc.

---

# 38. Invoice Templates

Create an invoice template system.

Initially create 2 professional templates:

### Template 1 — Modern

Minimal and clean.

### Template 2 — Corporate

Professional business-style invoice.

Allow admin to select default invoice template.

Structure the code so additional templates can be added later.

---

# 39. Currency Support

Create currency management.

Default:

USD

Support:

INR

USD

GBP

EUR

CAD

AUD

AED

Allow admin to add currencies.

Invoice currency should be stored independently so historical invoices do not change when the global currency setting changes.

---

# 40. Client-Specific Currency

When creating a client:

Set preferred currency.

Example:

Client A:

USD

Client B:

GBP

Client C:

INR

When creating an invoice, automatically select the client's currency.

Allow manual override.

---

# 41. Client-Specific Payment Terms

Support:

Due on Receipt

Net 7

Net 15

Net 30

Net 45

Net 60

Custom

When creating an invoice:

Invoice Date = August 23

Payment Terms = Net 30

Automatically calculate:

Due Date = September 22

---

# 42. Security

Implement:

* CSRF protection
* XSS protection
* SQL injection protection
* Authorization policies
* Secure password hashing
* File upload validation
* MIME validation
* Rate limiting
* Secure session configuration
* Authorization for invoice access
* Authorization for payment modification
* Prevent unauthorized invoice deletion

Never trust client-side calculations.

Recalculate all invoice totals on the server.

---

# 43. File Uploads

Allow:

Company logo upload

Client documents

Invoice attachments

Validate:

* File type
* File size
* File name

Store files securely.

---

# 44. Email Notifications

Create email notifications for:

* Invoice created
* Invoice sent
* Payment received
* Invoice overdue
* Payment reminder

Example reminder:

"Invoice INV-2026-0015 is due in 3 days."

---

# 45. Automatic Payment Reminder

Create scheduled reminders.

Example:

Invoice due date:

September 30

Reminder:

September 27

If unpaid:

October 1 — overdue notification

October 7 — second overdue reminder

Make reminder timing configurable.

---

# 46. Client Portal — Future Ready

Structure the system so a client portal can be added later.

Potential client portal:

Client login

Dashboard

Invoices

Payments

Projects

Statements

Download invoices

Make payments

Update profile

Do not expose admin data.

---

# 47. API Architecture

Structure the application so APIs can be added later.

Potential API endpoints:

GET /api/clients

POST /api/clients

GET /api/clients/{id}

GET /api/clients/{id}/invoices

GET /api/invoices

POST /api/invoices

GET /api/invoices/{id}

POST /api/invoices/{id}/payments

GET /api/payments

Use Laravel Sanctum if API authentication is implemented.

---

# 48. UI Requirements

The UI should look like a modern SaaS admin dashboard.

Sidebar:

Dashboard

Clients

Projects

Invoices

Payments

Services

Reports

Recurring Invoices

Settings

Users

Logout

Top bar:

Search

Notifications

User profile

Responsive mobile menu.

Use cards, tables, badges, modals, dropdowns, alerts, and charts.

Status badges:

Draft

Sent

Paid

Partial

Overdue

Cancelled

Use consistent styling throughout the application.

---

# 49. Important UX Features

Add:

* Confirmation modal before deleting
* Toast notifications
* Loading states
* Empty states
* Pagination
* Search
* Filters
* Sort
* Date pickers
* Currency formatting
* Form validation
* Server-side validation
* AJAX where useful
* Responsive design

Example empty state:

"No invoices found for this client."

---

# 50. Invoice Duplicate Feature

Add:

"Duplicate Invoice"

When clicked:

Create a new draft invoice using the existing invoice information.

New invoice number must be generated automatically.

The new invoice date should default to today's date.

---

# 51. Invoice Cancellation

Do not physically delete finalized invoices.

Instead:

Status = Cancelled

Store:

Cancellation Date

Cancelled By

Cancellation Reason

This preserves financial history.

---

# 52. Financial Accuracy

This is critical.

Never calculate financial totals only using JavaScript.

The server must recalculate:

* Item totals
* Discounts
* Taxes
* Subtotal
* Grand total
* Paid amount
* Balance

before saving.

Use database transactions when:

Creating invoice

Updating invoice

Recording payment

Cancelling invoice

---

# 53. Dashboard Example

Create a dashboard like:

---

Good Morning, Admin

[ Total Clients ] [ Total Invoices ]

[ Total Revenue ] [ Outstanding ]

[ Overdue ]

---

Revenue Overview

[Line/Bar Chart]

---

Invoice Status

[Donut Chart]

---

Recent Invoices

Invoice | Client | Date | Amount | Status

---

Recent Payments

Payment | Client | Invoice | Amount | Date

---

Top Clients

Client | Invoices | Revenue | Outstanding

---

# 54. Recommended Laravel Structure

Use a clean structure:

app/

Models/

Services/

Repositories/

Http/

Controllers/

Requests/

Policies/

Notifications/

Mail/

Jobs/

Console/

Providers/

resources/

views/

layouts/

components/

dashboard/

clients/

projects/

invoices/

payments/

services/

reports/

settings/

pdf/

routes/

web.php

api.php

database/

migrations/

seeders/

factories/

storage/

---

# 55. Service Classes

Create dedicated services.

Example:

InvoiceService

Responsibilities:

* Create invoice
* Update invoice
* Calculate totals
* Generate invoice number
* Calculate taxes
* Calculate discounts
* Update balance
* Cancel invoice

PaymentService

Responsibilities:

* Create payment
* Update invoice payment status
* Calculate outstanding balance
* Generate payment reference

PdfInvoiceService

Responsibilities:

* Generate invoice PDF
* Select invoice template
* Save PDF
* Download PDF

EmailInvoiceService

Responsibilities:

* Send invoice email
* Attach PDF
* Store email log

ClientStatementService

Responsibilities:

* Generate client statement
* Calculate opening balance
* Calculate closing balance

---

# 56. Automated Tests

Create Laravel Feature and Unit tests.

Test:

* Client creation
* Client update
* Invoice creation
* Invoice calculation
* Tax calculation
* Discount calculation
* Invoice numbering
* Payment creation
* Partial payment
* Full payment
* Overdue invoice
* Invoice cancellation
* Client invoice filtering
* Client statement calculation
* PDF generation
* Authorization

Example:

Invoice:

Subtotal = $1,000

Tax = 10%

Total = $1,100

Payment = $500

Expected:

Paid = $500

Balance = $600

Status = Partially Paid

---

# 57. Seed Demo Data

Create seeders for development.

Create:

Admin user

5 demo clients

10 demo services

10 demo projects

20 demo invoices

Payment records

Example clients:

ABC Technologies

XYZ Solutions

Global Web Agency

Demo Corporation

Digital Marketing Ltd

Use realistic sample data.

---

# 58. Settings

Create settings pages:

### Company Settings

Company details

### Invoice Settings

Prefix

Numbering

Default terms

Default notes

### Tax Settings

Tax rates

### Currency Settings

Currencies

### Payment Settings

Payment methods

### Email Settings

SMTP configuration

### User Settings

Users and roles

---

# 59. Export

Allow exports:

Clients CSV

Invoices CSV

Payments CSV

Revenue report CSV

Tax report CSV

Client statement PDF

Invoice PDF

---

# 60. Important Business Rule

The most important relationship in the system is:

CLIENT → PROJECT → INVOICE → PAYMENT

Example:

Client:

ABC Technologies

↓

Project:

WooCommerce Development

↓

Invoices:

INV-2026-0001 — $2,000

INV-2026-0008 — $3,000

INV-2026-0015 — $1,500

↓

Payments:

$2,000

$2,000

$1,000

↓

Client Financial Summary:

Total Invoiced: $6,500

Total Paid: $5,000

Outstanding: $1,500

This relationship must work throughout the application.

---

# 61. Final Deliverable

Build the complete Laravel application, not a prototype.

The application must include:

* Authentication
* Admin dashboard
* Client management
* Client detail/history
* Project management
* Service management
* Invoice creation
* Invoice editing
* Invoice preview
* Invoice PDF
* Invoice email
* Payment tracking
* Partial payments
* Overdue invoices
* Recurring invoices
* Client statements
* Reports
* Tax management
* Currency management
* Company settings
* Invoice settings
* Activity logs
* Search
* Filters
* Pagination
* Export
* Role permissions
* Notifications
* Automated reminders
* Tests
* Seed data

---

# 62. Development Instructions

Build the project incrementally.

First create:

1. Laravel project
2. Database configuration
3. Authentication
4. User roles
5. Database migrations
6. Models and relationships
7. Seeders
8. Dashboard
9. Client module
10. Project module
11. Service module
12. Invoice module
13. Payment module
14. PDF generation
15. Email system
16. Reports
17. Recurring invoices
18. Settings
19. Notifications
20. Tests

After each major module:

* Run migrations
* Run tests
* Check Laravel logs
* Check validation
* Check database relationships
* Check authorization
* Check responsive UI

Do not leave TODO placeholders for core functionality.

Do not use fake/static invoice data.

All dashboard numbers must come from the database.

All invoice totals must be calculated from actual database records.

Use proper Laravel conventions and clean reusable code.

The final application should be deployable on a production Linux server with MySQL.

Provide:

* Installation instructions
* `.env.example`
* Migration commands
* Seeder commands
* Storage setup
* Queue setup
* Scheduler setup
* Cron configuration
* Production deployment instructions
* Default admin credentials for local development
* Testing instructions
* Database relationship documentation



# 63. Online Payment Link & Automatic Payment Workflow

Add a complete **Online Payment Link System** to the invoice module.

The objective is:

When an invoice is generated and has an outstanding balance, the system should automatically generate a secure payment link for the **remaining due amount** and send that payment link to the client's email.

When the client completes the payment successfully:

1. Payment gateway confirms the transaction
2. Laravel verifies the payment using the gateway webhook/API
3. Payment is automatically recorded against the invoice
4. Invoice paid amount is updated
5. Outstanding balance is recalculated
6. Invoice status changes to `Paid` if the complete balance has been paid
7. A new invoice/receipt PDF is generated with a large `PAID` watermark
8. The paid invoice PDF is automatically emailed to the client
9. Payment confirmation email is sent
10. Payment and invoice activity are recorded in the audit log

---

# 64. Payment Gateway Architecture

Build the payment system using a gateway abstraction so multiple gateways can be supported.

Create a structure such as:

PaymentGatewayInterface

Implement gateway services such as:

* Razorpay
* Stripe
* PayPal
* Other gateways later

The application should not tightly couple invoice logic to one payment gateway.

Example:

PaymentGatewayInterface

Methods:

* createPaymentLink()
* verifyPayment()
* verifyWebhook()
* getPaymentStatus()
* refundPayment()

Then create:

RazorpayPaymentGateway

StripePaymentGateway

PayPalPaymentGateway

This will make it possible to add additional gateways later.

---

# 65. Payment Gateway Settings

Add:

Settings → Payment Gateways

Allow admin to enable/disable payment gateways.

For each gateway provide:

* Gateway Name
* API Key
* Secret Key
* Webhook Secret
* Environment

  * Test
  * Live
* Currency
* Status

Never expose secret keys in the frontend.

Store sensitive credentials securely.

---

# 66. Generate Payment Link When Invoice Is Created

When an invoice is finalized/sent and has an outstanding balance:

Example:

Invoice:

INV-2026-0045

Invoice Total:

$5,000

Paid:

$0

Balance:

$5,000

System should create a payment link.

Example:

Pay Invoice

https://yourdomain.com/pay/invoice/secure-token

The client should be able to click the link without creating an account.

---

# 67. Payment Link Security

Do NOT expose the invoice ID directly in a publicly accessible payment URL.

Do not use:

/pay/invoice/45

Instead generate a secure random token.

Example:

/pay/invoice/8d9f7c2e...secure-token...

Store the hashed token in the database where appropriate.

Payment links should:

* Be unpredictable
* Be unique
* Be revocable
* Have optional expiration
* Be associated with exactly one invoice
* Only allow payment up to the outstanding balance

---

# 68. Payment Link Database Table

Create:

payment_links

Fields:

* id
* invoice_id
* client_id
* token
* gateway
* amount
* currency
* expires_at
* status
* gateway_payment_id
* created_at
* updated_at

Statuses:

* Active
* Used
* Expired
* Cancelled

Add indexes for:

* invoice_id
* client_id
* token
* status

Token must be unique.

---

# 69. Payment Link Amount

The payment link should always represent the current outstanding balance.

Example:

Invoice:

$5,000

Client already paid:

$2,000

Outstanding:

$3,000

Payment link:

Pay $3,000

Do not allow the client to accidentally pay more than the outstanding balance.

However, optionally allow an administrator to configure whether overpayments are allowed.

---

# 70. Public Payment Page

Create a professional public payment page.

URL:

/pay/invoice/{secure-token}

Display:

Company Logo

Company Name

Invoice Number

Client Name

Invoice Date

Due Date

Invoice Total

Already Paid

Amount Due

Payment Gateway

"Pay Now"

Example:

---

ASSERTIV LOGIX

Invoice INV-2026-0045

Client:
ABC Technologies

Invoice Total:
$5,000

Paid:
$2,000

Amount Due:
$3,000

[ Pay $3,000 ]

---

Do not expose sensitive client information.

---

# 71. Payment Link Email

When sending an invoice, automatically send an email containing:

Subject:

Invoice INV-2026-0045 — Payment Required

Email content should include:

* Client name
* Invoice number
* Invoice amount
* Paid amount
* Outstanding amount
* Due date
* Payment link
* Invoice PDF attachment

Example:

Dear John,

Please find attached invoice INV-2026-0045.

Invoice Amount: $5,000

Paid Amount: $2,000

Amount Due: $3,000

Due Date: September 22, 2026

You can securely pay the outstanding amount using the button below.

[ PAY INVOICE ]

Thank you for your business.

Assertiv Logix

---

# 72. Payment Gateway Checkout

When the client clicks:

PAY INVOICE

open the configured payment gateway checkout.

The checkout should receive:

* Invoice number
* Client name
* Client email
* Amount due
* Currency
* Invoice reference
* Secure payment metadata

Do not trust the amount returned from the browser.

The server must determine the amount from the invoice.

---

# 73. Payment Verification

This is extremely important.

Never mark an invoice as paid merely because the frontend says payment succeeded.

After payment:

1. Receive gateway response
2. Verify payment signature
3. Verify gateway transaction
4. Verify payment amount
5. Verify currency
6. Verify invoice reference
7. Verify payment has not already been processed
8. Store gateway transaction ID
9. Record payment
10. Update invoice

Use the payment gateway webhook as the authoritative payment confirmation mechanism where supported.

---

# 74. Webhook System

Create secure webhook endpoints.

Example:

POST /webhooks/razorpay

POST /webhooks/stripe

POST /webhooks/paypal

Webhook processing must:

* Validate signature
* Validate event
* Validate invoice reference
* Validate amount
* Prevent duplicate processing
* Store webhook event
* Update payment
* Update invoice
* Trigger paid invoice generation

Create a:

payment_webhooks

table.

Fields:

* id
* gateway
* event_id
* event_type
* payment_id
* payload
* signature
* processed
* processed_at
* error_message
* created_at
* updated_at

`event_id` should be unique to prevent duplicate webhook processing.

---

# 75. Automatic Payment Recording

After successful payment:

Create a payment record.

Example:

Payment ID:

PAY-2026-00125

Invoice:

INV-2026-0045

Client:

ABC Technologies

Amount:

$3,000

Payment Method:

Stripe

Transaction ID:

gateway_transaction_id

Payment Date:

August 23, 2026

Status:

Completed

---

# 76. Invoice Status After Payment

Automatically determine status.

### Full Payment

Invoice:

Total = $5,000

Paid = $5,000

Balance = $0

Status:

`Paid`

### Partial Payment

Invoice:

Total = $5,000

Paid = $2,000

Balance = $3,000

Status:

`Partially Paid`

### Overdue

If:

Due Date < Current Date

and:

Balance > 0

Status:

`Overdue`

---

# 77. Automatic Paid Invoice Generation

When the invoice becomes fully paid:

Automatically generate a new PDF version of the invoice.

The PDF should contain a prominent:

# PAID

watermark.

The watermark should be visually obvious but should not make the invoice content unreadable.

Example:

PAID

Payment Received

Payment Date:

August 23, 2026

Payment Reference:

PAY-2026-00125

---

# 78. Paid Invoice PDF Information

The paid invoice should display:

Company information

Client information

Invoice number

Invoice date

Due date

Original invoice total

Payment information

Total Paid

Balance Due: $0.00

Payment Date

Payment Method

Transaction ID

Payment Reference

PAID watermark

Notes

Terms

Company footer

---

# 79. Paid Invoice Filename

Generate a clean filename.

Example:

INV-2026-0045-PAID.pdf

Do not overwrite the original draft invoice PDF if historical versioning is required.

Optionally store:

Original Invoice PDF

Paid Invoice PDF

---

# 80. Automatically Email Paid Invoice

Immediately after successful payment verification:

Send email to client.

Subject:

Payment Received — Invoice INV-2026-0045 Paid

Email:

Dear John,

Thank you for your payment.

We have successfully received your payment for invoice INV-2026-0045.

Invoice Total: $5,000

Payment Received: $3,000

Total Paid: $5,000

Balance: $0.00

Payment Date: August 23, 2026

Payment Reference: PAY-2026-00125

Your invoice has been marked as PAID.

Please find the paid invoice attached.

Thank you for your business.

Assertiv Logix

Attach:

INV-2026-0045-PAID.pdf

---

# 81. Payment Success Page

After successful payment, redirect the client to:

/payment/success/{secure-token}

Display:

Payment Successful

✓ Payment Received

Invoice:

INV-2026-0045

Amount Paid:

$3,000

Payment Reference:

PAY-2026-00125

Invoice Status:

Paid

A button:

[ Download Paid Invoice ]

Another button:

[ Return to Invoice ]

---

# 82. Payment Failure Page

Create:

/payment/failed/{secure-token}

Display:

Payment could not be completed.

Invoice:

INV-2026-0045

Amount:

$3,000

Provide:

[ Try Payment Again ]

Do not mark the invoice as paid.

---

# 83. Payment Pending

Some gateways may return a pending status.

Create:

/payment/pending/{secure-token}

Display:

Payment is being processed.

Do not mark the invoice as paid until the gateway confirms the payment through the webhook/API.

---

# 84. Duplicate Payment Protection

This is mandatory.

If a webhook is received twice for the same payment:

Do NOT create two payment records.

Use:

Gateway

*

Gateway Transaction ID

*

Webhook Event ID

to prevent duplicate processing.

Database should enforce uniqueness where appropriate.

---

# 85. Payment Amount Validation

Before recording a payment:

Verify:

Gateway amount == expected payment amount

or, if partial/custom payments are supported:

Gateway amount <= outstanding invoice amount

Reject suspicious payments.

Never trust:

* Browser amount
* JavaScript amount
* Hidden form amount

The backend must calculate the invoice balance.

---

# 86. Partial Payment Workflow

Support multiple payments.

Example:

Invoice:

$10,000

Payment 1:

$3,000

Remaining:

$7,000

Generate a new payment link for:

$7,000

Client pays:

$4,000

Remaining:

$3,000

Generate/update payment link:

$3,000

Client pays:

$3,000

Invoice:

PAID

Paid invoice PDF generated automatically.

---

# 87. Payment Link Regeneration

Admin should have:

"Generate Payment Link"

button.

If an existing active payment link exists:

Allow:

* View
* Copy
* Send
* Regenerate
* Cancel

When regenerating:

Invalidate the previous payment link.

---

# 88. Payment Link Dashboard

On invoice details, display:

Payment Status

Payment Link:

Active

Amount:

$3,000

Expires:

September 22, 2026

Actions:

[ Copy Link ]

[ Send Email ]

[ Regenerate ]

[ Cancel ]

---

# 89. Payment Timeline

On invoice detail page:

Show:

Invoice Created

Invoice Sent

Payment Link Generated

Payment Link Opened

Payment Attempted

Payment Successful

Payment Recorded

Invoice Marked Paid

Paid Invoice Generated

Paid Invoice Emailed

This provides complete transaction visibility.

---

# 90. Payment Link Analytics

Track:

* Link generated
* Link sent
* Link opened
* Payment attempted
* Payment successful
* Payment failed

Optionally store:

* IP address
* User agent
* Timestamp

Only collect what is necessary and follow applicable privacy requirements.

---

# 91. Automatic Payment Link on Invoice Email

When an invoice is sent:

If:

Outstanding Balance > 0

automatically generate a payment link.

The email should include:

[ PAY NOW ]

If:

Balance = 0

do not generate a payment link.

---

# 92. Automatic Overdue Payment Link

If an invoice becomes overdue:

The system can send an automated reminder email.

Example:

Subject:

Payment Reminder — Invoice INV-2026-0045 Overdue

Include:

Outstanding Amount:

$3,000

Due Date:

September 22, 2026

Days Overdue:

7

[ PAY NOW ]

---

# 93. Payment Reminder Schedule

Allow administrator to configure:

Before due date:

3 days

On due date:

1 reminder

After due date:

3 days

7 days

14 days

30 days

Use Laravel Scheduler + Queues.

Do not send duplicate reminders.

Track every reminder in:

invoice_reminders

table.

---

# 94. Invoice State Machine

Implement controlled invoice state transitions.

Example:

Draft

↓

Sent

↓

Partially Paid

↓

Paid

or:

Sent

↓

Overdue

↓

Partially Paid

↓

Paid

Cancelled invoices should not return to active payment states without explicit administrator action.

---

# 95. Database Additions

Add the following tables:

payment_links

payment_transactions

payment_webhooks

invoice_reminders

payment_gateway_settings

email_logs

Add appropriate foreign keys and indexes.

---

# 96. Payment Transaction Table

Fields:

* id
* invoice_id
* client_id
* payment_id
* gateway
* gateway_transaction_id
* gateway_order_id
* amount
* currency
* status
* payment_method
* transaction_date
* raw_response
* created_at
* updated_at

Statuses:

* Pending
* Processing
* Completed
* Failed
* Refunded
* Cancelled

---

# 97. Email Logs

Store every important email.

Fields:

* id
* client_id
* invoice_id
* payment_id
* recipient_email
* subject
* email_type
* status
* sent_at
* error_message

Email types:

* Invoice
* Payment Reminder
* Payment Confirmation
* Paid Invoice
* Overdue Reminder

This allows the admin to verify whether the client received the invoice/payment confirmation.

---

# 98. Automatic Workflow Summary

The complete workflow should work like this:

ADMIN

↓

Creates Client

↓

Creates Invoice

↓

Invoice Total = $5,000

↓

Invoice Status = Sent

↓

System generates secure payment link

↓

System emails client

↓

CLIENT

↓

Opens email

↓

Clicks "Pay Invoice"

↓

Secure payment page

↓

Clicks "Pay $5,000"

↓

Payment Gateway

↓

Payment Successful

↓

Gateway Webhook

↓

Laravel verifies webhook

↓

Laravel verifies amount

↓

Laravel verifies transaction

↓

Payment automatically recorded

↓

Invoice updated

↓

Paid Amount = $5,000

↓

Balance = $0

↓

Invoice Status = PAID

↓

Generate Paid Invoice PDF

↓

Add PAID watermark

↓

Add payment information

↓

Email paid invoice to client

↓

CLIENT receives:

"Payment Received — Invoice Paid"

↓

Attached:

INV-2026-0045-PAID.pdf

---

# 99. Partial Payment Workflow

Example:

Invoice:

$10,000

↓

Payment Link:

Pay $10,000

↓

Client pays:

$4,000

↓

Webhook confirms payment

↓

Payment recorded:

$4,000

↓

Invoice:

Total = $10,000

Paid = $4,000

Balance = $6,000

Status = Partially Paid

↓

Generate payment link for:

$6,000

↓

Send updated payment link

↓

Client pays $6,000

↓

Webhook confirms

↓

Paid = $10,000

Balance = $0

Status = Paid

↓

Generate:

INV-2026-0045-PAID.pdf

↓

Add PAID watermark

↓

Email PDF to client

---

# 100. Security Requirements for Payments

Payment functionality must follow strict security practices.

Never store:

* Card number
* CVV
* Full payment credentials

Use the payment gateway's hosted checkout/tokenization.

Always verify:

* Webhook signature
* Transaction ID
* Payment amount
* Currency
* Invoice ID
* Gateway status

Use HTTPS in production.

Do not expose API secrets.

Use Laravel encrypted configuration/secure environment variables.

Implement idempotency for payment processing.

---

# 101. Admin Payment Dashboard

Create:

Payments → Dashboard

Display:

Total Payments

Today's Payments

This Month

Pending

Failed

Refunded

Revenue Chart

Payment Gateway Breakdown

Payment Method Breakdown

Recent Transactions

---

# 102. Invoice Page Payment Section

Every invoice detail page should contain:

## Payment Summary

Invoice Total:

$5,000

Paid:

$2,000

Outstanding:

$3,000

Status:

Partially Paid

### Online Payment

Payment Link:

[ Copy Payment Link ]

[ Send Payment Link ]

[ Regenerate ]

### Payment History

Payment 1

$2,000

Stripe

August 20, 2026

Completed

---

# 103. Final Acceptance Criteria

The implementation is complete only when the following workflow works successfully:

### Test 1 — New Invoice

Admin creates invoice.

Expected:

Invoice generated.

Payment link generated.

Invoice email sent.

### Test 2 — Full Payment

Client opens payment link.

Client pays full amount.

Expected:

Payment verified.

Payment recorded.

Invoice becomes Paid.

Paid PDF generated.

PAID watermark appears.

Paid invoice emailed automatically.

### Test 3 — Partial Payment

Client pays partial amount.

Expected:

Payment recorded.

Invoice becomes Partially Paid.

Balance recalculated.

New payment link generated for remaining balance.

### Test 4 — Duplicate Webhook

Send the same webhook twice.

Expected:

Only one payment record.

No duplicate payment.

### Test 5 — Invalid Webhook

Send invalid signature.

Expected:

Payment rejected.

Invoice remains unchanged.

### Test 6 — Payment Failure

Gateway reports failure.

Expected:

Invoice remains unpaid/partially paid.

No paid invoice generated.

### Test 7 — Overdue Invoice

Invoice reaches due date with balance remaining.

Expected:

Invoice becomes Overdue.

Payment reminder can be sent.

Payment link remains available.

---

# 104. Final Goal

The finished application should operate as a complete:

**Client Management + Project Management + Invoice Management + Online Payment + Payment Tracking + Automated Receipt System**

for an IT company.

The ideal workflow is:

**Client → Project → Invoice → Payment Link → Online Payment → Automatic Payment Verification → Payment Record → Paid Invoice → PAID Watermark → Automatic Email**

The entire workflow should happen automatically after the client completes a successful online payment.
