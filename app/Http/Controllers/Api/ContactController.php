<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContactService;

class ContactController extends Controller
{
    public function __construct(private ContactService $contacts) {}

    public function index()
    {
        return response()->json($this->contacts->paginateForAdmin());
    }
}
