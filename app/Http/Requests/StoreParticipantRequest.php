<?php

namespace App\Http\Requests;

class StoreParticipantRequest extends BaseRequest {
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        return [
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:100',
        ];
    }
}
