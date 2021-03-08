<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['message' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection], $code);
        }
        $transformer = $collection->first()->transformer; //property in models
        $collection = $this->filterData($collection,$transformer);
        $collection = $this->sortData($collection,$transformer);
        $collection = $this->transformData($collection, $transformer); //convert collection to transformed collection
        return $this->successResponse( $collection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {
        //we deleted 'data=> ' because fractal returns it already
        $transformer = $instance->transformer; //the property in model
        $instance = $this->transformData($instance,$transformer);//transform it
        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function filterData(Collection $collection, $transformer){
        foreach (request()->query() as $query => $value) {
            $filteringAttr = $transformer::originalValues($query);
            if (isset($filteringAttr,$value)) {
            $collection = $collection->where($filteringAttr,$value);
            }
        }
        return $collection;
    }

    protected function sortData(Collection $collection, $transformer){
        if(request()->has('sort_by')){
            $sortingAttr = $transformer::originalValues(request()->sort_by);
            $collection = $collection->sortBy->{$sortingAttr};
        }
        return $collection;
    }

    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);
        return $transformation->toArray();
    }
}
