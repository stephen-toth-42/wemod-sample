<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    public static $header = [
        'user',
        'long_url'
    ];

    protected $fillable = [
        'user',
        'long_url',
        'hits',
    ];

    public function jsonSerialize(): Array {
        return [
            'short_url' => env('SHORT_LINK_BASE_URL').'/'.$this->id,
            'long_url' => $this->long_url,
            'hits' => $this->hits,
        ];
    }

    public function incrementHits(): Link {
        // Use getOriginal for hits to avoid accidental inflation prior to save
        $this->fill([
            'hits' => $this->getOriginal('hits')+1,
        ]);
        return $this;
    }
}