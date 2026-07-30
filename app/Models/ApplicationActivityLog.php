<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationActivityLog extends Model
{
    public $timestamps = false;
    public $updated_at = false;

    protected $fillable = [
        'oauth_client_id',
        'admin_id',
        'action',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
