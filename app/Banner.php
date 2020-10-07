<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        "title",
        "description",
        "link",
        "image_file",
        "click_count",
        "start_date",
        "expire_date",
        "hasButton",
    ];
    protected $hidden=[
      "created_at",
      "updated_at",
    ];
}
