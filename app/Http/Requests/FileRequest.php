<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function authorize(): bool
    {
        return true; // Set to true unless you want authorization logic
    }

    public function rules(): array
    {
        return [
            'files' => 'required|array|max:5',
            'files.*' => 'file|max:102400|mimes:jpg,png,pdf,docx,zip',
            'expires_in' => 'nullable|integer|min:1|max:30',
            'email_to_notify' => 'nullable|email',
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Please upload at least one file.',
            'files.array' => 'The uploaded files must be in an array.',
            'files.max' => 'You can upload a maximum of 5 files.',
            'files.*.file' => 'Each uploaded item must be a valid file.',
            'files.*.max' => 'Each file must not exceed 100MB.',
            'files.*.mimes' => 'Allowed file types: jpg, png, pdf, docx, zip.',
            'expires_in.integer' => 'Expiration must be a valid number of days.',
            'expires_in.min' => 'Minimum allowed expiration is 1 day.',
            'expires_in.max' => 'Maximum allowed expiration is 30 days.',
            'email_to_notify.email' => 'Please enter a valid email address.',
        ];
    }
}
