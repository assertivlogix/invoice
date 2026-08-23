<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($invoice->status === 'Cancelled') {
            return $user->isAdmin();
        }
        return true;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        // Only Admin can delete draft invoices. Finalized/Paid invoices cannot be hard deleted.
        if ($invoice->status !== 'Draft') {
            return false;
        }
        return $user->isAdmin();
    }

    public function cancel(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }
}
