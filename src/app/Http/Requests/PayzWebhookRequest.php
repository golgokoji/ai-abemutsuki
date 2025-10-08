<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayzWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可は別途必要ならミドルウェアで
    }

    public function rules(): array
    {
        $status = $this->input('status');
        $base = [
            'user_uid'         => ['nullable','string','max:191'],
            'email'            => ['required','email','max:255'],
            'status'           => ['required','string','in:purchased,subscribing,void'],
            'product_uid'      => ['required','string','max:191'],
            'name_last'        => ['nullable','string','max:191'],
            'name_first'       => ['nullable','string','max:191'],
            'kana_last'        => ['nullable','string','max:191'],
            'kana_first'       => ['nullable','string','max:191'],
            'prefecture'       => ['nullable','string','max:191'],
            'city'             => ['nullable','string','max:191'],
            'address1'         => ['nullable','string','max:191'],
            'address2'         => ['nullable','string','max:191'],
            'zip'              => ['nullable','string','max:20'],
            'tel'              => ['nullable','string','max:50'],
            'fax'              => ['nullable','string','max:50'],
            'dob'              => ['nullable','string','max:50'],
            'client_ip'        => ['nullable','ip'],
            'client_ua'        => ['nullable','string','max:1024'],
        ];
        if ($status === 'purchased') {
            $base['purchase_uid'] = ['required','string','max:191'];
        }
        return $base;
    }
}
