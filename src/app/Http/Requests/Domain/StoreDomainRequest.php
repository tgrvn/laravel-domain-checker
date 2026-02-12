<?php

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'domain' => 'required|string|max:255|unique:domains,domain',
            'check_interval_minutes' => ['sometimes', 'integer', 'min:1', 'max:1440'],
            'request_timeout_seconds' => ['sometimes', 'integer', 'min:1', 'max:30'],
            'check_method' => ['sometimes', 'string', 'in:GET,HEAD'],
            'auto_checks_enabled' => ['sometimes', 'boolean'],
        ];
    }

    public function domainData(): array
    {
        return $this->only('domain');
    }

    public function checkSettingData(): array
    {
        return $this->only([
            'check_interval_minutes',
            'request_timeout_seconds',
            'check_method',
            'auto_checks_enabled',
        ]);
    }

    public function messages(): array
    {
        return [
            'domain.required' => 'Домен обязателен для заполнения',
            'domain.unique' => 'Такой домен уже существует',
            'domain.max' => 'Домен не должен превышать 255 символов',
            'check_interval_minutes.min' => 'Интервал проверки должен быть не менее 1 минуты',
            'check_interval_minutes.max' => 'Интервал проверки не должен превышать 1440 минут',
            'request_timeout_seconds.min' => 'Таймаут должен быть не менее 1 секунды',
            'request_timeout_seconds.max' => 'Таймаут не должен превышать 30 секунд',
            'check_method.in' => 'Метод проверки должен быть GET или HEAD',
        ];
    }
}