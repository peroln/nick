<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public function products()
    {
        return $this->hasMany('App/Product');
    }
}
