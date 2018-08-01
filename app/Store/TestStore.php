<?php

namespace App\Store;

use App\Models\V1\Test;

class TestStore extends BaseStore
{
    public static function getList () 
	{
		return Test::all();
	}
    
    public static function insert($input)
    {
        return Test::create($input);
    }
    
    public static function update($id, $input)
    {
        return Test::where('id', $id)->update($input);
    }
    
    public static function destroy($id)
    {
        return Test::destroy($id);
    }
    
    public static function show($id)
    {
        return Test::findOrFail($id);
    }
}