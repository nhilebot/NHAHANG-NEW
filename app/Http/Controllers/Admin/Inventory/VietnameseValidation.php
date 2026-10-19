<?php

namespace App\Http\Controllers\Admin\Inventory;

/** Thông báo lỗi validate tiếng Việt dùng chung cho các controller kho. */
trait VietnameseValidation
{
    protected function messages(): array
    {
        return [
            'required'   => ':attribute không được để trống.',
            'unique'     => ':attribute đã tồn tại, vui lòng chọn giá trị khác.',
            'max'        => ':attribute không được vượt quá :max ký tự.',
            'min'        => ':attribute không được nhỏ hơn :min.',
            'numeric'    => ':attribute phải là số.',
            'email'      => ':attribute không đúng định dạng email.',
            'exists'     => ':attribute không hợp lệ.',
            'alpha_dash' => ':attribute chỉ gồm chữ, số, dấu gạch ngang và gạch dưới.',
        ];
    }

    protected function attributes(): array
    {
        return [
            'name'            => 'Tên',
            'code'            => 'Mã',
            'description'     => 'Mô tả',
            'phone'           => 'Số điện thoại',
            'email'           => 'Email',
            'address'         => 'Địa chỉ',
            'note'            => 'Ghi chú',
            'category_id'     => 'Nhóm nguyên liệu',
            'unit'            => 'Đơn vị tính',
            'min_stock'       => 'Mức tồn tối thiểu',
            'reference_price' => 'Giá nhập tham khảo',
        ];
    }

    /** Dò lỗi trùng khóa unique (MySQL 1062, SQLite 19/UNIQUE) khi hai người tạo cùng lúc. */
    protected function isDuplicateKey(\Illuminate\Database\QueryException $e): bool
    {
        $msg = $e->getMessage();

        return ($e->errorInfo[1] ?? null) === 1062
            || str_contains($msg, 'UNIQUE constraint failed')
            || str_contains($msg, 'Duplicate entry');
    }
}