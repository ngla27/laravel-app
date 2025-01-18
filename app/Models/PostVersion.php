<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostVersion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'post_id',
        'title',
        'description',
        'meta_title',
        'meta_description',
        'keywords',
        'updated_at',
        'start_timestamp',
        'end_timestamp'
    ];
}
