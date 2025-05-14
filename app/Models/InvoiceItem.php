<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InvoiceItem  extends Model
{
    protected $fillable = ['invoice_id','type','procedure_id', 'description', 'amount','medication_request_id','quantity','unit_proce','total_price'];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function procedure(): BelongsTo { return $this->belongsTo(Procedure::class); }
    public function medicationRequest(): BelongsTo { return $this->belongsTo(MedicationRequest::class); }
}
