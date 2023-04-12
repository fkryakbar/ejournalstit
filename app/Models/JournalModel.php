<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "journal";
    protected $dates = ['deleted_at'];
    protected $guarded = [];
}
