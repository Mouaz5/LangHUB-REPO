<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Academy;


class Offer extends Model
{
    use HasFactory;

    protected $table = 'offers';
    protected $fillable = [
        'name', 'price', 'hours', 'start_date', 'end_date', 'description',
        'academy_id','teacher_id'
    ];
    protected $hidden = [
        'active' , 'rate_id'  , 'student_id' ,'teacher_id' , 'academy_id' ,'created_at' , 'updated_at' , 'pivot'
    ];
    public function academy() {
        return $this->belongsTo(Academy::class);
    }
    public function notifications(){
        return $this->hasMany(OfferNotification::class) ;
    }
    public function students(){
        return $this->belongsToMany(Student::class);
    }
    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
    public function annualSchedules(){
        return $this->hasMany(OfferAnnualScadual2::class);
    }
}
