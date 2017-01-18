<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Repositories\Eloquent\Repository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use session;

class UserRepository extends Repository
{

    protected $UserRepository;
    

    /**
     * Relationship
     *
     * @return void [description]
     */
    public function model()
    {
        return 'App\Models\User';
    }

    /**
    * Method save  file into folder
    *
    * @param file $file file get from form.
    *
    * @return picture name to save into database
    */
    public function saveFile($file)
    {
        $now = Carbon::now();
        $image = $now->toDateTimeString().$file->getClientOriginalName();
        $path=config('path.path_avatar');
        $file->move($path, $image);
        return $image;
    }
}
