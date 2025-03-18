<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemContratacaoRequest extends FormRequest
{
    /**
     * Regras de validação para o ItemContratacao
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            // Adicione suas regras de validação aqui, como
            // 'nome' => ['required', 'string', 'max:128'],
        ];
    }

    /**
     * Determina se o usuário está autorizado a fazer essa requisição
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mensagens personalizadas de validação
     * @return string[]
     */
    public function messages(): array
    {
        return [];
    }
}
