<?php

namespace Modules\QcComplaintSystem\Http\Requests;

class UpdateQcFindingRequest extends StoreQcFindingRequest
{
	public function authorize(): bool
	{
		return true;
	}
}
