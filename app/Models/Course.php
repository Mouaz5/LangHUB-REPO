<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Lesson;
use App\Models\Academy;
use App\Models\Exam as ModelsExam;
use App\Models\Exam;
use App\Models\Rate;
class Course extends Model
{
    use HasFactory;
    protected $table = 'courses';
    protected $fillable = [
        'name', 'description', 'price', 'course_image', 'seats','language','course_image',
        'hours', 'status', 'start_date', 'end_date','teacher_id' , 'actice',
    ];
    protected $hidden = [
        'active' , 'rate_id'  , 'student_id' ,'teacher_id' , 'academy_id' ,'created_at' , 'updated_at' , 'pivot'
    ];
    // The courses that belong to the Student
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
    // The teachers that belong to the Course
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function annualSchedule(){
        return $this->hasMany(CourseAnnualScadual::class);
    }
    // Get all of the lessons for the Course
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    public function academy() {
        return $this->belongsTo(Academy::class);
    }
    public function exam() {
        return $this->hasOne(Exam::class);
    }
    public function rate(){
        return $this->belongsTo(Rate::class);
    }
    public function group(){
        return $this->hasMany(Group::class);
    }
}
