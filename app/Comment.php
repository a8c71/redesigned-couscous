<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
	use SoftDeletes;
	
	/**
   * Fields that can be mass assigned.
   *
   * @var array
   */
  protected $fillable = [
  	'body', 'solution', 'question_id', 'comment_id', 'user_id'
  ];

  protected $dates = [
  	'deleted_at'
  ];

  protected $appends = [
  	'score'
  ];

  protected $with = [
  	'votes:value,user_id,comment_id'
  ];

  public function question()
  {
  	return $this->belongsTo('App\Question');
  }  

  public function answer()
  {
  	return $this->belongsTo('App\Comment');
  }  

  public function comments()
  {
  	return $this->hasMany('App\Comment');
  }

  public function votes()
  {
  	return $this->hasMany('App\Vote');
  }

  public function getScoreAttribute()
  {
  	return $this->votes->sum('value');
  }

  public function user()
  {
  	return $this->belongsTo('App\User');
  }
}
