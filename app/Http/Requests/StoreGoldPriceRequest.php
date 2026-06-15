<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoldPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:BUY,SELL',
            'price' => 'required|numeric|min:0',
            'provider' => 'nullable|string|max:255',
            'price_date' => 'required|date',
        ];
    }
}
