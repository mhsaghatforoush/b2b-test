<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    protected $fillable = [
        'title',
        'description',
        'price',
        'stock',
    ];

    // Reduce inventory book
    public static function reduce_inventory($id) {
        $book = Self::findOrFail($id);
        $book->stock = $book->stock - 1;
        $book->save();
    }
}
