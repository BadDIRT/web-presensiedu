<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTemplateItem extends Model
{
    protected $fillable = ['template_id', 'phase', 'order', 'question_text', 'answer_key', 'points'];

    public function template()
    {
        // ⚠️ HARUS specify 'template_id' sebagai argumen kedua
        return $this->belongsTo(QuestionTemplate::class, 'template_id');
    }
}
