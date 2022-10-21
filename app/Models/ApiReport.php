<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ApiReport extends Model
{
    protected $table = 'api_reports';

    // constant slugs
    const GET_BOOK_LIST = 'get_book_lists';

    protected $fillable = [
        'slug',
        'count',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function make_api_report() {
        $check_report = self::whereDate('created_at', Carbon::today())
        ->where('user_id', auth()->user()->id)
        ->last();

        if(!empty($check_report)) {
            $check_report->count = $check_report->count + 1;
            $check_report->save();
        }else {
            self::create([
                'slug' => self::GET_BOOK_LIST,
                'count' => 1,
                'user_id' => auth()->user()->id
            ]);
        }
    }
}
