<?php

return [
    'accepted' => ':attribute harus disetujui.',
    'boolean' => ':attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'exists' => 'Pilihan :attribute tidak valid.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'max' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :max item.',
        'file' => 'Ukuran :attribute tidak boleh lebih dari :max kilobita.',
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'min' => [
        'array' => ':attribute minimal harus memiliki :min item.',
        'file' => 'Ukuran :attribute minimal :min kilobita.',
        'numeric' => ':attribute minimal harus bernilai :min.',
        'string' => ':attribute minimal harus terdiri dari :min karakter.',
    ],
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.',
    'attributes' => [
        'name' => 'nama',
        'email' => 'alamat email',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'title' => 'judul artikel',
        'excerpt' => 'ringkasan',
        'body' => 'isi artikel',
        'category_id' => 'kategori',
        'status' => 'status publikasi',
    ],
];
