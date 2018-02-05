<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
  use SoftDeletes;

   /**
    * Fields that can be mass assigned.
    *
    * @var array
    */
  protected $fillable = [
  	'name'
  ];

  protected $dates = [
  	'deleted_at'
  ];

  public function questions()
  {
  	return $this->belongsToMany('App\Question')->withTimestamps();
  }
}
