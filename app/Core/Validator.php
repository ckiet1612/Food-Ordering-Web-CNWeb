<?php

declare(strict_types=1);

namespace App\Core;

// Rule viết dạng chuỗi: 'required|email|min:6|max:100|phone|numeric|in:cod,bank|same:password'
class Validator
{
    private const MESSAGES = [
        'required' => 'Vui lòng nhập :field.',
        'email'    => ':Field không đúng định dạng.',
        'phone'    => ':Field phải gồm 10 chữ số, bắt đầu bằng 0.',
        'numeric'  => ':Field phải là số.',
        'min'      => ':Field phải có ít nhất :param ký tự.',
        'max'      => ':Field không được vượt quá :param ký tự.',
        'in'       => ':Field không hợp lệ.',
        'same'     => ':Field không khớp.',
    ];

    public static function validate(array $data, array $rules, array $labels = []): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = trim((string) ($data[$field] ?? ''));
            $label = $labels[$field] ?? $field;

            foreach (explode('|', $ruleString) as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);

                if ($name !== 'required' && $value === '') {
                    continue;
                }

                if (!self::passes($name, $value, $param, $data)) {
                    $errors[$field] = strtr(self::MESSAGES[$name], [
                        ':field' => mb_strtolower($label),
                        ':Field' => mb_strtoupper(mb_substr($label, 0, 1)) . mb_substr($label, 1),
                        ':param' => (string) $param,
                    ]);
                    break;
                }
            }
        }

        return $errors;
    }

    private static function passes(string $rule, string $value, ?string $param, array $data): bool
    {
        return match ($rule) {
            'required' => $value !== '',
            'email'    => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'phone'    => (bool) preg_match('/^0\d{9}$/', $value),
            'numeric'  => is_numeric($value),
            'min'      => mb_strlen($value) >= (int) $param,
            'max'      => mb_strlen($value) <= (int) $param,
            'in'       => in_array($value, explode(',', (string) $param), true),
            'same'     => $value === (string) ($data[$param] ?? ''),
        };
    }
}
