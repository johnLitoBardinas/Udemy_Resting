<?php 

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponser{

	/* returning a simple json response */
	private function successResponse($data, $code)
	{
		return response()->json($data, $code);
	}

	/* return a error response */
	protected function errorResponse($message, $code)
	{
		return response()->json(['error' => $message, 'code' => $code], $code);
	}

	/* return all the collections with 200 status code as a default */
	protected function showAll(Collection $collection, $code=200)
	{
		// collection empty
		if ($collection->isEmpty()) {
			return $this->successResponse(['data' => $collection], $code);
		}

		$transformer = $collection->first()->transformer;
		$collection = $this->filterData($collection, $transformer); 
		// filtering is admin ... like that request parameter string

		$collection = $this->sortData($collection, $transformer); // sorting the given collection

		$collection = $this->paginate($collection); // paginating some collection
		
		$collection = $this->transformData($collection, $transformer); // must before this one

		$collection = $this->cacheResponse($collection); // cacheng some data for lessen db interaction

		return $this->successResponse($collection, $code);
	}

	/* return just a single collection of the specified resource */
	protected function showOne(Model $instanceModel, $code=200)
	{
		$transformer = $instanceModel->transformer;

		$instanceModel = $this->transformData($instanceModel, $transformer);
		return $this->successResponse($instanceModel, $code);
	}

	/* 4:42 PM 01-31-2019 { filtering collection using the specified attributes } */
	protected function filterData(Collection $collection, $transformer)
	{
		foreach (request()->query() as $query => $value) {
			$attribute = $transformer::originalAttribute($query);

			if (isset($attribute, $value)) {
				$collection = $collection->where($attribute, $value);
			}
		}

		return $collection;
	}

	/* 2:11 PM 01-30-2019 { Showing some message here } */
	protected function showMessage($message, $code=200)
	{
		return $this->successResponse(['data' => $message], $code);
	}

	/* 3:34 PM 01-31-2019 { Sorting some data } */
	protected function sortData(Collection $collection, $transformer)
	{
		if (request()->has('sort_by')) {
			$attribute = $transformer::originalAttribute(request()->sort_by);
			$collection = $collection->sortBy->{$attribute};
			// if the attribute does'nt exist then the collection will return random collection
		}
		return $collection;
	}

	/* 3:33 PM 01-31-2019 { Applying a fractal transformation in the collection data } */
	protected function transformData($data, $transformer)
	{
		// transformation
		$transformation = fractal($data, new $transformer);
		return $transformation->toArray();
	}

	protected function paginate(Collection $collection)
	{
		// ruling some pagination output
		$rules = [
			'per_page' => 'integer|min:2|max:50',
		];

		// applying the rules using the facades cause we where not using a controller
		Validator::validate(request()->all(), $rules);

		// page query parameter
		$page = LengthAwarePaginator::resolveCurrentPage();

		// divide the collection depends on page
		$perPage = 15;

		// if the request have a per_page parameter then changing the default
		if (request()->has('per_page')) {
			$perPage = (int) request()->per_page;
		}

		// obtaining the results
		$results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

		$paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
			'path' => LengthAwarePaginator::resolveCurrentPath(),
		]);

		$paginated->appends(request()->all());

		return $paginated;
	}

	/* 4:41 PM 01-02-2019 { Implementing the cache data from fractal } */
	/* 
		Make sure that we are cacheng unique stuff, 
		depending if it is on user cache or product cache
	*/
	public function cacheResponse($data)
	{
		// obtaining some url
		$url = request()->url();

		// implementing first the query befor the caching of data
		$queryParams = request()->query();

		// sorting via the keys act by refference
		ksort($queryParams);

		// build a new query string
		$queryString = http_build_query($queryParams);

		// building the full url === to the original url
		$fullUrl = "{$url}?{$queryString}";

		return Cache::remember($fullUrl, 30/60, function() use($data)
		{
			return $data;
		});
	}
}
