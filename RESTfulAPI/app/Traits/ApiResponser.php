<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

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
        $collection = $this->filterData($collection, $transformer);
        $collection = $this->sortData($collection, $transformer);
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer); //convert collection to transformed collection
        $collection = $this->cacheResponse($collection);
        return $this->successResponse($collection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {
        //we deleted 'data=> ' because fractal returns it already
        $transformer = $instance->transformer; //the property in model
        $instance = $this->transformData($instance, $transformer); //transform it
        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function filterData(Collection $collection, $transformer)
    {
        foreach (request()->query() as $query => $value) {
            $filteringAttr = $transformer::originalValues($query);
            if (isset($filteringAttr, $value)) {
                $collection = $collection->where($filteringAttr, $value);
            }
        }
        return $collection;
    }

    protected function sortData(Collection $collection, $transformer)
    {
        if (request()->has('sort_by')) {
            $sortingAttr = $transformer::originalValues(request()->sort_by);
            $collection = $collection->sortBy->{$sortingAttr};
        }
        return $collection;
    }

    protected function paginate(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50',
        ];
        Validator::validate(request()->all(), $rules);
        $pageNumber = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        if (request()->has('per_page')) {
            $perPage = request()->per_page;
        }
        $results = $collection->slice(($pageNumber - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $pageNumber, [
            'path' => LengthAwarePaginator::resolveCurrentPath(), // full link to prev and next page
        ]);
        $paginated->appends(request()->all()); //don't ignore other params such as 'sortby'
        return $paginated;
    }

    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);
        return $transformation->toArray();
    }

    //* working on the cache as the data is transformed to Array  NOT collection
    protected function cacheResponse($data)
    {
        $url = request()->url();
        $queryParams = request()->query();
        ksort($queryParams); // re-sort it as arr[] to work properly with cache
        $queryString = http_build_query($queryParams); // build query from sorted arr of qrs
        $fullUrl = "{$url}?{$queryString}";
        return Cache::remember($fullUrl, 30 / 60, function () use ($data) {
            return $data;
        });
    }
}
