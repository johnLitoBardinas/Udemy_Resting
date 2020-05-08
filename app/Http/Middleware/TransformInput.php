<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {
        $transformedInput = [];
        foreach ($request->request->all() as $input => $value) {
            $transformedInput[$transformer::originalAttribute($input)] = $value;
        }
        $request->replace($transformedInput);

        // 23:47 05-08-2020 (Modify the Response)
        $response = $next($request);

        // 23:48 05-08-2020 (Only act on validation error response)
        if (isset($response->exception) && $response->exception instanceof ValidationException) {

            $data = $response->getData();

            $transformedErrors = [];

            foreach ($data->error as $field => $errorMessage) {
                // Get the transformed field
                $transformedField = $transformer::transformedAttribute($field);

                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $errorMessage);

                $data->error = $transformedErrors;

                $response->setData($data);
            }
        }

        return $response;

    }

}
