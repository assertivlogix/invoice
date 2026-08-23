<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->state, $this->zip_code, $this->country]);
        return implode(', ', $parts);
    }

    public function getTotalInvoicedAttribute(): float
    {
        return (float) $this->invoices()->where('status', '!=', 'Cancelled')->sum('grand_total');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->where('status', 'Completed')->sum('amount');
    }

    public function getTotalOutstandingAttribute(): float
    {
        return (float) $this->invoices()->whereNotIn('status', ['Paid', 'Cancelled'])->sum('balance_due');
    }

    public function getTotalOverdueAttribute(): float
    {
        return (float) $this->invoices()->where('status', 'Overdue')->sum('balance_due');
    }
}
