<?php

namespace App\Http\Requests;

use App\Enums\ProfileStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in('update', 'delete')],
            'last_name' => ['required_if:action,update', 'string', 'max:255'],
            'first_name' => ['required_if:action,update', 'string', 'max:255'],
            'status' => ['required_if:action,update', Rule::enum(ProfileStatus::class)],
            'image' => ['sometimes', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }
}
