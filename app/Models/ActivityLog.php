<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $connection = 'logs'; // <-- every query this model runs automatically goes to the logs connection, permanently. You never have to specify it again.

    protected $fillable = ['user_id', 'action', 'description'];
}
