<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'price_date' => 'nullable|date',
        ];
    }
}
