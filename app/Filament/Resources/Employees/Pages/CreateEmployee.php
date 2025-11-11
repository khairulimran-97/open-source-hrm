<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\LeavePolicy;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $leavePoliciesData = $data['leave_policies_data'] ?? [];
        unset($data['leave_policies_data']);

        $this->leavePoliciesData = $leavePoliciesData;

        return $data;
    }

    protected function afterCreate(): void
    {
        $currentYear = now()->year;

        foreach ($this->leavePoliciesData ?? [] as $policyData) {
            LeavePolicy::create([
                'employee_id' => $this->record->id,
                'leave_type' => $policyData['leave_type'],
                'allowed_days' => $policyData['allowed_days'] ?? 0,
                'year' => $currentYear,
                'is_enabled' => $policyData['is_enabled'] ?? true,
            ]);
        }
    }
}
