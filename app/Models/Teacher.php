<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;
use App\Models\Group;
use App\Models\TeacherPost;

class Teacher extends Model
{
    use HasFactory;
    protected $table = 'teachers';
    protected $fillable = [
        'first_name', 'last_name', 'phone_number', 'photo', 'user_id'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function rate(){
        return $this->belongsTo(Rate::class) ;
    }
    // The courses that belong to the Teacher
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
    // Get all of the groups for the Teacher
    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function posts()
    {
        return $this->hasMany(TeacherPost::class);
    }
    public function offers(){
        return $this->hasMany(Offer::class);
    }
    public function academies(){
        return $this->belongsToMany(Academy::class) ;
    }
}
