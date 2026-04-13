<?php

namespace Modules\PrSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['sub_department_id', 'department_id', 'job_id', 'category', 'amount', 'pta_amount', 'used_amount', 'year'];

    protected $casts = [
        'amount' => 'decimal:2',
        'pta_amount' => 'decimal:2',
        'used_amount' => 'decimal:2',
    ];

    public function subDepartment()
    {
        return $this->belongsTo(SubDepartment::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function totalAmount(): float
    {
        return (float) $this->amount + (float) ($this->pta_amount ?? 0);
    }

    public function remainingAmount(): float
    {
        return $this->totalAmount() - (float) ($this->used_amount ?? 0);
    }
}
