<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\BSON\UTCDateTime;

class Management extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'managements';


    protected $guarded = [];

}
