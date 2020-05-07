<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiResponser;

    /**
     * Register the middleware
     */
    public function __construct()
    {
    }

}
