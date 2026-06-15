<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoldTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route('action')) {
            'sell' => [
                'member_id' => 'required|exists:family_members,id',
                'gram' => 'required|numeric|min:0.0001',
                'price_per_gram' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string',
            ],
            'transfer' => [
                'from_member_id' => 'required|exists:family_members,id',
                'to_member_id' => 'required|exists:family_members,id|different:from_member_id',
                'gram' => 'required|numeric|min:0.0001',
                'price_per_gram' => 'nullable|numeric|min:0',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string',
            ],
            'adjustment' => [
                'member_id' => 'required|exists:family_members,id',
                'gram' => 'required|numeric',
                'price_per_gram' => 'nullable|numeric|min:0',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string',
            ],
            default => [
                'member_id' => 'required|exists:family_members,id',
                'gram' => 'required|numeric|min:0.0001',
                'price_per_gram' => 'required|numeric|min:0',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string',
            ],
        };
    }
}
