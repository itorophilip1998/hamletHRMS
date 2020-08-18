<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name', 'company_email', 'company_address', 'company_phone', 'no_of_employees', 'city', 'state', 'zip_code',
        'company_website', 'company_logo', 'services'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    
}
