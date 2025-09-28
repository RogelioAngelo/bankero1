<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','subtotal','discount','tax','total','name','phone','locality','address','city','state','country','landmark','zip','qr_token','qr_scanned_at','payment_status'
    ];

    protected $casts = [
        'qr_scanned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);

    }

    /**
     * Serialize dates using the application timezone so JSON/array output
     * matches what users expect in the local timezone instead of UTC.
     */

    protected function serializeDate(DateTimeInterface $date)
    {
        // Use 12-hour clock with AM/PM (non-military time) and include timezone offset.
        // Example: 2025-09-27 08:38:22 PM +08:00
        return (new \DateTimeImmutable($date->format('Y-m-d H:i:s.u')))
            ->setTimezone(new \DateTimeZone(config('app.timezone')))
            ->format('Y-m-d h:i:s A P');
    }

}
