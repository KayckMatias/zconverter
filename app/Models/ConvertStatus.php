<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvertStatus extends Model
{
    use HasFactory;

    protected $table = 'files_convert';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_id',
        'status',
        'progress',
        'message',
        'job_id',
        'to_extension',
        'options'
    ];

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }

}
