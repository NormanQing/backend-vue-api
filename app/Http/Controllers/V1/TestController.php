<?php 

namespace App\Http\Controllers\V1;

use Dingo\Api\Http\Request;
use App\Common\Helper;
use App\Transformers\TestTransformer;
use App\Store\TestStore;

class TestController extends BaseController
{

    public function index () {
		$obj = TestStore::getList();
		return $this->response->collection($obj, new TestTransformer);
	}

    public function store(Request $request)
    {
        $formData = [array('user','email','sex','hoby','status',)];
        if (!$request->filled($formData)) {
            return $this->errorRequest('参数错误', $request->all());
        }
        $input = $request->only($formData);
        $obj = TestStore::insert($input);
        return $this->created($obj, new TestTransformer);
    }

    public function update(Request $request, $id)
    {
        $formData = [array('user','email','sex','hoby','status',)];
        if (!$request->filled($formData)) {
            return $this->errorRequest('参数错误', $request->all());
        }
        $input = $request->only($formData);
        TestStore::update($id, $input);
        return $this->updated();
    }
    
    public function destroy($id)
    {
        $ids = Helper::strToArrayIds($id);
        TestStore::destroy($ids);
        return $this->noContent();
    }
    

    public function show($id)
    {
        $obj = TestStore::show($id);
        return $this->item($obj, new TestTransformer);
    }
    
}