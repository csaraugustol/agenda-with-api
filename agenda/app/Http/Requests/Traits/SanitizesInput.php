<?php

namespace App\Http\Requests\Traits;

use Illuminate\Support\Arr;
use App\Http\Requests\Traits\Sanitizer;

trait SanitizesInput
{
    /**
     * Sanitize input before validating.
     *
     *  @return void
     */
    public function validateResolved()
    {
        $filters = $this->filters();

        if ($this->hasBefore()) {
            $this->sanitize($filters['before']);
        }

        parent::validateResolved();

        if ($this->hasAfter()) {
            $this->sanitize($filters['after']);
        }

        if (!$this->hasAfter() && !$this->hasBefore()) {
            $this->sanitize($filters);
        }
    }

    /**
     * Verifica a existência da chave "before"
     * para ver qual momento validação
     *
     * @return bool
     */
    private function hasBefore()
    {
        $filters = $this->filters();

        return key_exists('before', $filters) && is_array($filters['before']);
    }

    /**
     * Verifica a existência da chave "after"
     * para ver qual momento validação
     *
     * @return bool
     */
    private function hasAfter()
    {
        $filters = $this->filters();

        return key_exists('after', $filters) && is_array($filters['after']);
    }

    /**
     * Recebe o array vindo do input
     * para preparar sanitização
     *
     *  @return void
     */
    public function sanitize($filters)
    {
        $filters = Arr::only($filters, array_keys($this->input()));


        // Filtra todos os campos de inputs recebidos do form
        $this->sanitizer = Sanitizer::make($this->input(), $filters);

        dd('chega aqui');
        // Recebe os inputs que exitem no form para efetuar a sanitização
        $keysBefore = array_keys($this->all());

        $sanitizedInputs = $this->sanitizer->sanitize();

        $result = [];

        foreach ($keysBefore as $key) {
            $result[$key] = $sanitizedInputs[$key];
        }

        $this->replace($result);
    }

    /**
     * Filters to be applied to the input.
     *
     *  @return void
     */
    public function filters()
    {
        return [];
    }
}
