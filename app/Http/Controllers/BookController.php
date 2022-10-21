<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function list() {
        return new BookCollection(Book::all());
    }

    public function show($id) {
        return new BookResource(Book::findOrFail($id));
    }
}
