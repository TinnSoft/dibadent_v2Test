<?php

namespace App\Http\Controllers;
use App\Models\Comments;
use Illuminate\Http\Request;
use Auth;
use DB;

class CommentsController extends Controller
{
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function get_CommentsByImageId($imageId)
    {
        $data = Comments::join('users', function ($join) {
            $join->on('users.id', '=', 'comments.user_id');
        })
        ->join('profiles', function ($join) {
            $join->on('profiles.id', '=', 'users.profile_id');
        }) 
        ->select('comments.id','comments.image_id','comments.comment','comments.created_at',
        DB::raw("CONCAT(IFNULL(profiles.description,''),': ',CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,''))) as title")) 
        ->where('comments.image_id', $imageId)   
        ->orderBy('comments.id', 'desc')      
        ->get();      

        return response()
        ->json([
        'comments' => $data
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $companyId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {                
        $this->validate($request, [
            'comment' => 'required',
            'image_id' => 'required'
        ]);             
        
        $data = $request->all();           
        $data['user_id'] = Auth::user()->id;  
        
        $item = Comments::create($data);
    
        return response()
            ->json([
                'created' => true,
                'id' => $item->id
            ]);
    }

   
}
