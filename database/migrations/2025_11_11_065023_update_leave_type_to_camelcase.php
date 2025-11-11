<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mapping = [
            'Sick Leave' => 'sickLeave',
            'Vacation' => 'vacation',
            'Personal Leave' => 'personalLeave',
            'Maternity Leave' => 'maternityLeave',
            'Paternity Leave' => 'paternityLeave',
            'Time Off' => 'timeOff',
            'Other' => 'other',
        ];

        foreach ($mapping as $old => $new) {
            DB::table('leave')->where('leave_type', $old)->update(['leave_type' => $new]);
            DB::table('leave_policies')->where('leave_type', $old)->update(['leave_type' => $new]);
        }
    }

    public function down(): void
    {
        $mapping = [
            'sickLeave' => 'Sick Leave',
            'vacation' => 'Vacation',
            'personalLeave' => 'Personal Leave',
            'maternityLeave' => 'Maternity Leave',
            'paternityLeave' => 'Paternity Leave',
            'timeOff' => 'Time Off',
            'other' => 'Other',
        ];

        foreach ($mapping as $old => $new) {
            DB::table('leave')->where('leave_type', $old)->update(['leave_type' => $new]);
            DB::table('leave_policies')->where('leave_type', $old)->update(['leave_type' => $new]);
        }
    }
};
