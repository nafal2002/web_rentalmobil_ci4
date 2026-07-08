<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Strings
    |--------------------------------------------------------------------------
    */

    // Core Messages
    'required' => '{field} harus diisi.',
    'isset' => '{field} harus memiliki nilai.',
    'valid_email' => '{field} harus berupa email yang valid.',
    'valid_emails' => '{field} harus berisi email yang valid.',
    'valid_url' => '{field} harus berupa URL yang valid.',
    'valid_ip' => '{field} harus berupa IP yang valid.',
    'valid_base64' => '{field} harus berupa base64 string yang valid.',
    'valid_credit_card' => '{field} harus berupa nomor kartu kredit yang valid.',
    'valid_json' => '{field} harus berupa JSON yang valid.',
    'valid_isbn' => '{field} harus berupa ISBN yang valid.',
    'valid_iban' => '{field} harus berupa IBAN yang valid.',
    'valid_bb_code' => '{field} harus berupa BB Code yang valid.',
    'valid_hex' => '{field} harus berupa hexadecimal yang valid.',
    'valid_punycode' => '{field} harus berupa Punycode yang valid.',

    // Numeric
    'integer' => '{field} harus berupa angka bulat.',
    'max_length' => '{field} tidak boleh lebih dari {param} karakter.',
    'min_length' => '{field} harus minimal {param} karakter.',
    'in_list' => '{field} harus salah satu dari: {param}.',
    'less_than' => '{field} harus kurang dari {param}.',
    'less_than_equal_to' => '{field} harus kurang dari atau sama dengan {param}.',
    'greater_than' => '{field} harus lebih dari {param}.',
    'greater_than_equal_to' => '{field} harus lebih dari atau sama dengan {param}.',

    // File
    'uploaded' => '{field} harus berupa file yang di-upload.',
    'valid_mime' => '{field} harus berupa tipe file: {param}.',
    'max_size' => '{field} tidak boleh lebih dari {param} dalam ukuran.',
    'is_image' => '{field} harus berupa file gambar yang valid.',
    'image_size' => '{field} harus berupa gambar dengan dimensi: {param}.',
    'image_mime' => '{field} harus berupa gambar dengan tipe: {param}.',
    'image_max_width' => '{field} lebar tidak boleh lebih dari {param} piksel.',
    'image_max_height' => '{field} tinggi tidak boleh lebih dari {param} piksel.',
    'image_min_width' => '{field} lebar harus minimal {param} piksel.',
    'image_min_height' => '{field} tinggi harus minimal {param} piksel.',

    // Database
    'is_unique' => '{field} sudah ada di database.',
    'is_not_unique' => '{field} tidak unik dalam database.',
    'is_natural' => '{field} harus berupa angka alami.',
    'is_natural_no_zero' => '{field} harus berupa angka alami tanpa nol.',

    // String
    'alpha' => '{field} hanya boleh berisi karakter alfabet.',
    'alpha_dash' => '{field} hanya boleh berisi karakter alphanumerik, garis bawah, dan garis.',
    'alpha_numeric' => '{field} hanya boleh berisi karakter alphanumerik.',
    'alpha_numeric_punct' => '{field} hanya boleh berisi karakter alphanumerik dan tanda baca.',
    'alpha_numeric_space' => '{field} hanya boleh berisi karakter alphanumerik dan spasi.',

    // Custom
    'regex_match' => '{field} format tidak sesuai.',
    'matches' => '{field} tidak cocok dengan field {param}.',
    'differs' => '{field} harus berbeda dengan field {param}.',
    'numeric' => '{field} harus berupa angka.',
    'decimal' => '{field} harus berupa angka desimal.',
    'hex' => '{field} harus berupa heksadesimal.',
    'json' => '{field} harus berupa JSON yang valid.',
    'timezone' => '{field} harus berupa timezone yang valid.',
    'valid_data' => '{field} berisi data yang tidak valid.',
    'valid_currency' => '{field} harus berupa mata uang yang valid.',
];
