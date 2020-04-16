<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable =['name','total', 'client_id', 'created_at'];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

}
