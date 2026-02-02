<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConversationUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['id', 'conversation_id', 'user_id', 'deleted_at'];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
