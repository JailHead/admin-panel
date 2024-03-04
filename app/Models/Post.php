<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'color',
        'categorie_id',
        'content',
        'thumbnail',
        'tags',
        'published'
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    public function categorie(){
        return $this->belongsTo(Categorie::class);
    }

    public function authors(){
        return $this->belongsToMany(User::class, 'post_users')->withPivot(['order'])->withTimestamps();
    }

    public function comments(){
        return $this->morphMany(Comment::class, 'commentable');
    }
}
