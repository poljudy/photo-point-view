<?php

namespace App\Http\Controllers;

use App\Supports\DateConvertor;
use App\Supports\MessageClass;
use Illuminate\Routing\Controller;

class AppController extends Controller
{
    use MessageClass, DateConvertor;

    /** Check Required Validation */
    public function requiredValidation($requiredKeys, $allDetails)
    {
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $allDetails) || !isset($allDetails[$key])) {
                return $this->makeError(null, __('validation.required', ['attribute' => str_replace("_", " ", $key)]));
            }
        }
    }

    /** Check Required Validation */
    public function requiredAllKeysValidation($requiredKeys, $allDetails)
    {
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $allDetails) || !isset($allDetails[$key])) {
                $validationsErrors[] = __('validation.required', ['attribute' => str_replace("_", " ", $key)]);
                // return $this->makeError(null, __('validation.required', ['attribute' => str_replace("_", " ", $key)]));
            }
        }
        if (isset($validationsErrors)) {
            return $this->makeError(null, $validationsErrors);
        }
    }
}
