<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
	use SoftDeletes;

	/**
	 * Fields that can be mass assigned.
	 *
	 * @var array
	 */
	protected $fillable = [
		'value', 'comment_id', 'user_id'
	];

	protected $dates = [
		'deleted_at'
	];

  public function comment()
  {
  	return $this->belongsTo('App\Comment');
  }

  public function user()
  {
  	return $this->belongsTo('App\User');
  }

}
