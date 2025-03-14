<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioSetorRequest extends FormRequest
{
    /**
     * Regras de validação para o UsuarioSetor
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            'codigo_setor' => ['required'],
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
