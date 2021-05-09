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
    //     public function handle($request, Closure $next, $transformer)
    //     {
    //         //* here we take user input values and replace them with the orignal ones
    //         $transformedInput = [];
    //         foreach ($request->request->all() as $input => $value) {
    //             $transformedInput[$transformer::originalValues($input)] = $value;
    //         }
    //         $request->replace($transformedInput);
    //         $response = $next($request);

    //         //* first, , make sure the err is ONLY because of wrong attrs
    //         if (isset($response->exception) && $response->exception instanceof ValidationException) {
    //             $data = $response->getData();
    //             $transformedErrors = [];
    //             foreach ($data->error as $field => $error) {
    //                 $transformedField = $transformer::transformedValues($field);
    //                 $transformedErrors[$transformedField] = str_replace($field, $transformedField, $error);
    //             }
    //             $data->error = $transformedErrors; //* replace err[] with transformed values
    //             $response ->setData($data);
    //         }
    //         return $response;
    //     }
    // }

    public function handle($request, Closure $next, $transformer)
    {
        $transformedInput = [];

        foreach ($request->request->all() as $input => $value) {
            $transformedInput[$transformer::originalValues($input)] = $value;
        }

        $request->replace($transformedInput);

        $response = $next($request);

        if (isset($response->exception) && $response->exception instanceof ValidationException) {
            $data = $response->getData();

            $transformedErrors = [];

            foreach ($data->error as $field => $error) {
                $transformedField = $transformer::transformedValues($field);

                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $error);
            }

            $data->error = $transformedErrors;

            $response->setData($data);
        }

        return $response;
    }
}
