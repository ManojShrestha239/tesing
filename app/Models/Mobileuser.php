<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;

class Mobileuser extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    protected $table = "mobileusers";

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'areas',
        'role_id',
        'fcm_id',
        'last_seen',
        'sub_areas',
        'status',
        'is_tracking_enabled',
        'is_senior',
        'is_super_senior',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'device',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_tracking_enabled' => 'boolean',
        'is_senior' => 'boolean',
        'is_super_senior' => 'boolean',
    ];
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function area()
    {
        return $this->belongsToMany(Area::class, 'area_mobileuser', 'mobileuser_id', 'area_code');
    }

    public function session()
    {
        return $this->hasMany(Session::class, 'user_id', 'id');
    }

    public function todayVisits()
    {
        return $this->hasMany(Session::class, 'user_id', 'id')
            ->where('date', date('Y-m-d'));
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'user_id', 'id');
    }

    public function todayLocations($date = null)
    {
        return $this->hasMany(Location::class, 'user_id', 'id')
            ->where('day', $date ?? date('Y-m-d'));
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'mobileuser_id');
    }

    // Mutator to get only the latest attendance
    public function latestAttendance()
    {
        return $this->hasOne(Attendance::class, 'mobileuser_id')->latestOfMany('date');
    }

    public function todayLatestAttendance($date = null)
    {
        return $this->hasOne(Attendance::class, 'mobileuser_id')
            ->whereDate('date', $date ?? date('Y-m-d'));
    }

    public function daily_attendance()
    {
        return $this->hasOne(Attendance::class, 'mobileuser_id');
    }

    // Team Relationships (Hierarchical Structure)

    /**
     * Get the employees under this senior (direct reports)
     */
    public function teamMembers()
    {
        return $this->belongsToMany(Mobileuser::class, 'mobileuser_teams', 'senior_id', 'employee_id')
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * Get the seniors this employee reports to
     */
    public function teamLeaders()
    {
        return $this->belongsToMany(Mobileuser::class, 'mobileuser_teams', 'employee_id', 'senior_id')
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * Get ALL employees under this senior (including indirect reports - recursive)
     * This uses a closure to traverse the hierarchy
     */
    public function getAllTeamMembers()
    {
        $teamMemberIds = [];
        $this->collectTeamMembersRecursive($this->id, $teamMemberIds);

        return Mobileuser::whereIn('id', $teamMemberIds)->get();
    }

    /**
     * Recursive helper to collect all team member IDs
     */
    private function collectTeamMembersRecursive($seniorId, &$memberIds)
    {
        $directMembers = DB::table('mobileuser_teams')
            ->where('senior_id', $seniorId)
            ->pluck('employee_id')
            ->toArray();

        foreach ($directMembers as $memberId) {
            if (!in_array($memberId, $memberIds)) {
                $memberIds[] = $memberId;
                // Recursively get members of this member (if they are also a senior)
                $this->collectTeamMembersRecursive($memberId, $memberIds);
            }
        }
    }

    /**
     * Check if this user is a senior (has team members)
     */
    public function isSenior()
    {
        return $this->teamMembers()->count() > 0;
    }

    /**
     * Check if this user is a super admin/super senior (can see everyone)
     */
    public function isSuperSenior()
    {
        return $this->is_super_senior === true;
    }

    public function isJunior(int $userId): bool
    {
        if ($userId === $this->id) {
            return false;
        }

        return $this->checkJuniorRecursive($this->id, $userId);
    }

    private function checkJuniorRecursive(int $seniorId, int $targetId): bool
    {
        $directMembers = DB::table('mobileuser_teams')
            ->where('senior_id', $seniorId)
            ->pluck('employee_id')
            ->toArray();

        if (in_array($targetId, $directMembers, true)) {
            return true;
        }

        foreach ($directMembers as $memberId) {
            if ($this->checkJuniorRecursive($memberId, $targetId)) {
                return true;
            }
        }

        return false;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function todayOrders($date = null)
    {
        return $this->hasMany(Order::class, 'user_id', 'id')
            ->where('date', $date ?? date('Y-m-d'));
    }

    // ✅ Scalar helper — different name, explicit
    public function todaysOrderTotal(?string $date = null): float
    {
        return $this->todayOrders()
            ->sum('total_price');
    }

    // ✅ Real relationship — no parameters, no aggregation
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id', 'id');
    }

    public function todayPayments($date = null)
    {
        return $this->hasMany(Payment::class, 'user_id', 'id')
            ->where('collection_date', $date ?? today()->toDateString());
    }

    // ✅ Scalar helper — different name, explicit
    public function todaysPaymentTotal(?string $date = null): float
    {
        return $this->payments()
            ->sum('amount');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
