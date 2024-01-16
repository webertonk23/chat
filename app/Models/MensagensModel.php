<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MensagensModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mensagens';

    protected $fillable = [
        'de',
        'para',
        'mensagem'
    ];

    public function de(){
        return $this->belongsTo(User::class, 'de');
    }

    
    public function para(){
        return $this->belongsTo(User::class, 'para');
    }
}
