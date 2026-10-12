<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['name','capacity', 'status', 'cleanup_started_at'];

    public static function releaseExpiredCleaning(): void
    {
        self::where('status', 'cleaning')
            ->whereNotNull('cleanup_started_at')
            ->where('cleanup_started_at', '<=', now()->subMinute())
            ->update(['status' => 'empty', 'cleanup_started_at' => null]);
    }

    public static function lockIfAvailableForBooking(int $tableId): ?self
    {
        $table = self::whereKey($tableId)->lockForUpdate()->first();

        if (!$table || $table->status !== 'empty') {
            return null;
        }

        if (Reservation::blockingTable()->where('table_id', $tableId)->exists()) {
            return null;
        }

        return $table;
    }
}