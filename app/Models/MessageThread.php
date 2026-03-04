<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class MessageThread extends Model
{
    use Multitenantable;

    protected $fillable = ['user_one_id', 'user_two_id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    // Helper to get the "other" person in the chat
    public function getOtherUserAttribute()
    {
        return $this->user_one_id === auth()->user()->id ? $this->userTwo : $this->userOne;
    }
}
