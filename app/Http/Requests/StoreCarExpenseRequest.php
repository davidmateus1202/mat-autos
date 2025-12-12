<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'concept' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'account_id' => ['required', 'exists:financial_accounts,id'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB max
        ];
    }
}
