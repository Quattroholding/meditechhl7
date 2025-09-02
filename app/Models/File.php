<?php

namespace App\Models;

class File extends BaseModel
{
    protected $fillable = ['table_name', 'record_id', 'user_id', 'path', 'name', 'type', 'extention'];
}
