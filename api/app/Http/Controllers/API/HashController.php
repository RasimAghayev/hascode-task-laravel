<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hash;
use Illuminate\Http\Request;
use Validator;

class HashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hashs = Hash::all();
        return response(['hashs' => HashResource::collection($hashs)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [$request->all(),hash('sha1',$request->input('data'))];
        
        $validator = Validator::make($data, [
            'data' => 'required',
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $hash = Hash::create($data);

        return response(['hash' => new HashResource($hash)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hash  $hash
     * @return \Illuminate\Http\Response
     */
    public function show(Hash $hash)
    {
        return response(['hash' => new HashResource($hash)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hash  $hash
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hash $hash)
    {
        $data = [$request->all(),hash('sha1',$request->input('data'))];
        
        $validator = Validator::make($data, [
            'data' => 'required',
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $hash->update($data);

        return response(['hash' => new HashResource($hash)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hash  $hash
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hash $hash)
    {
        $hash->delete();

        return response(['message' => 'Hash deleted successfully']);
    }
}
