<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
	use SoftDeletes;

	/**
   * Fields that can be mass assigned.
   *
   * @var array
   */
  protected $fillable = [
  	'title', 'body', 'user_id'
  ];

  protected $dates = [
  	'deleted_at'
  ];

  protected $appends = [
  	'solved_by',
  	'view_count'
  ];

  /**
   * Relationships
   */

  public function user()
  {
  	return $this->belongsTo('App\User');
  }

  /**
   * The question answers as comments
   * 
   * @return \App\Comment [description]
   */
  public function answers()
  {
  	return $this->hasMany('App\Comment');
  }

  public function tags()
  {
  	return $this->belongsToMany('App\Tag')->withTimestamps();
  }

  public function views()
  {
    return $this->belongsToMany('App\User')->withTimestamps();
  }

  /**
   * Accesors
   */

  public function getSolvedByAttribute()
  {
  	return $this->answers->where('solution', 'YES')->first();
  }

  public function getViewCountAttribute()
  {
  	return $this->views->count();
  }

  
}
