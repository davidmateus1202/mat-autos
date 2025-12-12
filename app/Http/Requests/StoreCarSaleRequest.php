<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarSaleRequest extends FormRequest
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
            'sale_price' => ['required', 'numeric', 'min:0'],
            'sale_date' => ['required', 'date'],
            'account_id' => ['required', 'exists:financial_accounts,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_document' => ['nullable', 'string', 'max:50'],
        ];
    }
}
