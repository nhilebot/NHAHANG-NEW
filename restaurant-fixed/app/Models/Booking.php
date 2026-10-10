<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
   protected $fillable = [
    'name', 
    'email', 
    'phone', 
    'booking_date', 
    'booking_time', 
    'table_id', // Thêm dòng này
    'guests', 
    'note'
];
}