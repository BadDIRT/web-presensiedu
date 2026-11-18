<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTemplate extends Model
{
    protected $fillable = ['name', 'description', 'created_by'];

    public function items()
    {
        // ⚠️ Tambahkan 'template_id' sebagai argumen kedua
        return $this->hasMany(QuestionTemplateItem::class, 'template_id')
            ->orderBy('phase')
            ->orderBy('order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
