<?php

namespace App\Support;

class PasswordPolicy
{
    public static function rules(bool $required = true): array
    {
        return [
            $required ? 'required' : 'nullable',
            'string',
            'min:8',
            'max:64',
            'regex:/\A(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9\s])[\s\S]+\z/',
        ];
    }

    public static function messages(): array
    {
        return [
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.string' => 'Mật khẩu không hợp lệ.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu không được dài quá 64 ký tự.',
            'password.regex' => 'Mật khẩu phải có chữ hoa, chữ thường, chữ số và ít nhất một ký tự đặc biệt.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ];
    }
}
