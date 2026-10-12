<?php

declare(strict_types=1);

namespace App\Core;

class Upload
{
    private const IMAGE_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const MAX_SIZE = 3 * 1024 * 1024;

    // Lưu ảnh upload vào public/uploads/<dir>; trả về đường dẫn tính từ public/, null nếu không chọn file
    public static function image(string $field, string $dir): ?string
    {
        $file = $_FILES[$field] ?? null;

        if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Tải ảnh lên thất bại, vui lòng thử lại.');
        }
        if ($file['size'] > self::MAX_SIZE) {
            throw new \RuntimeException('Ảnh không được lớn hơn 3MB.');
        }

        // Kiểm tra nội dung file thật, không tin phần mở rộng do người dùng gửi
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extension = self::IMAGE_TYPES[$mime] ?? throw new \RuntimeException('Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.');

        $relative = "uploads/{$dir}/" . bin2hex(random_bytes(8)) . ".{$extension}";
        if (!move_uploaded_file($file['tmp_name'], BASE_PATH . '/public/' . $relative)) {
            throw new \RuntimeException('Không lưu được ảnh lên máy chủ.');
        }

        return $relative;
    }

    // Chỉ xoá ảnh do admin upload, không xoá ảnh mẫu trong assets/
    public static function delete(?string $relative): void
    {
        if ($relative && str_starts_with($relative, 'uploads/') && is_file(BASE_PATH . '/public/' . $relative)) {
            unlink(BASE_PATH . '/public/' . $relative);
        }
    }
}
