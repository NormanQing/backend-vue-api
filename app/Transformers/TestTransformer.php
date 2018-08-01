<?php 

namespace App\Transformers;

use League\Fractal\TransformerAbstract;


class TestTransformer extends TransformerAbstract
{
    public function transform(\App\Models\V1\Test $test)
    {
        return 		array (
  				"id" => $test->id,
  				"user" => $test->user,
  				"email" => $test->email,
  				"sex" => $test->sex,
  				"hoby" => $test->hoby,
  				"status" => $test->status,
  				"created_at" => $test->created_at->format("Y-m-d H:i:s"),
  				"updated_at" => $test->updated_at->format("Y-m-d H:i:s")
		);
    }
}