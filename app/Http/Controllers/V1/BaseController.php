<?php

namespace App\Http\Controllers\V1;


use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    protected $inputKey = [];

    use Helpers;


    protected function destroyError($message = '', $params = '')
    {
        Log::error($message, ['params' => json_encode($params)]);
    }

    /**
     * 响应单个数据
     * @param object   $item
     * @param object   $transformer
     * @param array    $parameters
     * @param \Closure $after
     *
     * @return \Dingo\Api\Http\Response
     */
    protected function item($item, $transformer, $parameters = [], \Closure $after = null)
    {
        return $this->response->item($item, $transformer);
    }

    /**
     * 创建一个资源 {post|put|patch}
     * @param object   $item
     * @param object   $transformer
     *
     * @return \Dingo\Api\Http\Response
     */
    protected function created($item, $transformer)
    {
        $content = $this->item($item, $transformer);
        return $this->response->created(null, $content);
    }

    protected function updated()
    {
        return $this->response->accepted();
    }

    protected function errorRequest($msg, $params = [])
    {
        Log::error(json_encode($params));
        return $this->response->errorBadRequest($msg);
    }

    protected function errorInternal($msg, $err_info = '服务器错误')
    {
        Log::error($msg);
        return $this->response->errorInternal($err_info);
    }

    protected function noContent()
    {
        return $this->response->noContent();
    }

    protected function errorNotFound()
    {
        return $this->response->errorNotFound();
    }

    protected function getPageSize()
    {
        return config('app.pageSize');
    }
}