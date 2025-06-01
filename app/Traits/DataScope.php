<?php

namespace App\Traits;
trait DataScope
{
    public function scopeActive($q)
    {
        return $q->where('status', 'ACTIVE');
    }

    public function scopeInActive($q)
    {
        return $q->where('status', 'INACTIVE');
    }

    public function scopeDelete($q)
    {
        return $q->where('delete', 'DELETED');
    }

    public function scopeNotDelete($q)
    {
        return $q->where('status', '!=', 'DELETED');
    }

	//  public function scopeUserStation($query)
    // {
	// 	if (auth()->check() && auth()->user()->username !== 'admin') {
	// 		return $query->whereHas('users', function ($query) {
	// 			$query->where('id', auth()->id());
	// 		});
	// 	} else if (auth()->check() && auth()->user()->username !== 'developer') {
	// 		return $query->whereHas('users', function ($query) {
	// 			$query->where('id', auth()->id());
	// 		});
	// 	}
	// 	return $query;
    // }

	public function scopeUserStation($query)
	{
		if (auth()->check()) {
			$user = auth()->user();

			if ($user->username === 'developer') {
				// Developers see everything, so don't add any restrictions.
				return $query;
			} elseif ($user->username !== 'admin') {  //  Crucially, this checks if it's *not* admin
				//  Regular users (and anyone *not* admin or developer)
				return $query->whereHas('users', function ($query) {
					$query->where('id', auth()->id());
				});
			}
			//Implicitly: if the username IS admin, it also falls through here.
			// admins also get all
		}

		return $query; // Important:  Always return the query object
	}
}
