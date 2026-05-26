<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_id'   => ['required', 'uuid', 'exists:media,id'],
            'start_time' => ['required', 'numeric', 'min:0'],
            'end_time'   => ['required', 'numeric', 'gt:start_time'],
            'crop_w'     => ['nullable', 'integer', 'min:1'],
            'crop_h'     => ['nullable', 'integer', 'min:1'],
            'crop_x'     => ['nullable', 'integer', 'min:0'],
            'crop_y'     => ['nullable', 'integer', 'min:0'],
            'speed'      => ['nullable', 'numeric', 'between:0.5,4.0'],
            'mute'       => ['nullable', 'boolean'],
        ];
    }
}
