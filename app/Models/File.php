<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'original_name',
        'path_name',
        'original_url',
        'user_id',
    ];

    public function downloadStatus()
    {
        return $this->hasOne(DownloadStatus::class, 'file_id', 'id');
    }

    public function convertStatus()
    {
        return $this->hasMany(ConvertStatus::class, 'file_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeAuthUser($query){
        return $query->where('user_id', auth()->user()->id);
    }
}
