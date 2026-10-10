<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    protected $fillable = ['user_id', 'day_of_week', 'shift'];
}