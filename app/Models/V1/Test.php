<?php 

namespace App\Models\V1;

use App\Models\BaseModel;

class Test extends BaseModel
{
    /**
     * 不可被批量赋值的属性.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = 1;

}