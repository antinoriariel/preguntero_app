<?php
declare(strict_types=1);

final class Validator
{
    private array $errors = [];

    private function __construct() {}

    public static function make(array $data, array $rules): self
    {
        $v = new self();

        foreach ($rules as $field => $ruleSet) {
            $value = (string)($data[$field] ?? '');

            foreach (explode('|', $ruleSet) as $rule) {
                if (isset($v->errors[$field])) {
                    break; // one error per field
                }

                if ($rule === 'required') {
                    if (trim($value) === '') {
                        $v->errors[$field] = 'Este campo es obligatorio.';
                    }
                } elseif ($rule === 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $v->errors[$field] = 'El email no es válido.';
                    }
                } elseif ($rule === 'digits') {
                    if (!ctype_digit($value)) {
                        $v->errors[$field] = 'Solo se permiten dígitos numéricos.';
                    }
                } elseif ($rule === 'alpha_num') {
                    if (!ctype_alnum($value)) {
                        $v->errors[$field] = 'Solo se permiten letras y números.';
                    }
                } elseif (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (mb_strlen($value) < $min) {
                        $v->errors[$field] = "Mínimo $min caracteres.";
                    }
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int)substr($rule, 4);
                    if (mb_strlen($value) > $max) {
                        $v->errors[$field] = "Máximo $max caracteres.";
                    }
                } elseif (str_starts_with($rule, 'exact:')) {
                    $len = (int)substr($rule, 6);
                    if (mb_strlen($value) !== $len) {
                        $v->errors[$field] = "Debe tener exactamente $len caracteres.";
                    }
                }
            }
        }

        return $v;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function first(string $field): string
    {
        return $this->errors[$field] ?? '';
    }
}
