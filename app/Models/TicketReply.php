<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TicketReply extends Model {
    protected $fillable = ['ticket_id','user_id','message','is_admin'];
    public function user() { return $this->belongsTo(User::class); }
    public function ticket() { return $this->belongsTo(Ticket::class); }
}
