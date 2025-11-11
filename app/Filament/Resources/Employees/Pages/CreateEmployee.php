<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Department;
use App\Models\LeavePolicy;
use App\Models\Position;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Database\Eloquent\Builder;

class CreateEmployee extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = EmployeeResource::class;

    protected static bool $canCreateAnother = false;

    public function getSteps(): array
    {
        return [
            Step::make('Personal Information')
                ->description('Personal and contact details')
                ->schema([
                    Section::make('Basic Information')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('employee_number')
                                    ->required()
                                    ->maxLength(50)
                                    ->label('Employee Number')
                                    ->placeholder('Enter employee number')
                                    ->columnSpan(1),
                                TextInput::make('first_name')
                                    ->required(),
                                TextInput::make('last_name')
                                    ->required(),
                                DatePicker::make('date_of_birth'),
                                Select::make('gender')
                                    ->options(['Male' => 'Male', 'Female' => 'Female']),
                                Select::make('marital_status')
                                    ->options([
                                        'Single' => 'Single',
                                        'Married' => 'Married',
                                        'Divorced' => 'Divorced',
                                        'Widowed' => 'Widowed',
                                    ]),
                            ]),
                        ]),

                    Section::make('Contact Information')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->label('Email Address')
                                    ->unique(table: 'employees', column: 'email'),
                                TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->label('Phone Number')
                                    ->unique(table: 'employees', column: 'phone'),
                                TextInput::make('nric_number')
                                    ->label('NRIC Number (MyKad)')
                                    ->placeholder('YYMMDD-PB-###G')
                                    ->maxLength(255),
                                TextInput::make('income_tax_number')
                                    ->label('Income Tax Number')
                                    ->placeholder('SG 12345678')
                                    ->maxLength(255),
                                TextInput::make('epf_number')
                                    ->label('EPF Number')
                                    ->maxLength(255),
                                TextInput::make('socso_number')
                                    ->label('SOCSO Number')
                                    ->maxLength(255),
                            ]),
                        ]),
                ]),

            Step::make('Emergency Contacts')
                ->description('Emergency contacts')
                ->schema([
                    Section::make('Emergency Contact')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('emergency_contact_name'),
                                TextInput::make('emergency_contact_phone'),
                            ]),
                        ]),

                    Section::make('Next of Kin')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('next_of_kin_name')
                                    ->label('Name')
                                    ->required(),
                                TextInput::make('next_of_kin_relationship')
                                    ->label('Relationship')
                                    ->required(),
                                TextInput::make('next_of_kin_phone')
                                    ->required()
                                    ->tel()
                                    ->label('Phone'),
                                TextInput::make('next_of_kin_email')
                                    ->label('Email')
                                    ->email(),
                            ]),
                        ]),
                ]),

            Step::make('Employment Details')
                ->description('Job and employment info')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('department_id')
                            ->relationship(
                                name: 'department',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->select('id', 'name')->orderBy('name', 'asc')
                            )
                            ->label('Department')
                            ->searchable()
                            ->placeholder('Select a department')
                            ->preload()
                            ->nullable(),
                        Select::make('position_id')
                            ->options(
                                Position::all()->pluck('title', 'id')
                            )
                            ->label('Position')
                            ->searchable()
                            ->placeholder('Select a position')
                            ->preload()
                            ->nullable()
                            ->createOptionForm([
                                TextInput::make('title')
                                    ->required()
                                    ->label('Position Title'),
                                Select::make('department_id')
                                    ->options(
                                        Department::all()->pluck('name', 'id')
                                    ),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('code')
                                            ->label('Position Code')
                                            ->unique(ignoreRecord: true)
                                            ->nullable(),
                                        TextInput::make('salary')
                                            ->label('Salary')
                                            ->numeric()
                                            ->nullable(),
                                    ]),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->nullable()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return Position::create([
                                    'title' => $data['title'],
                                    'department_id' => $data['department_id'],
                                    'code' => $data['code'] ?? null,
                                    'salary' => $data['salary'] ?? null,
                                    'description' => $data['description'] ?? null,
                                ])->id;
                            })
                            ->native(false),
                        Select::make('employment_type')
                            ->options([
                                'Permanent' => 'Permanent',
                                'Contract' => 'Contract',
                                'Casual' => 'Casual',
                            ])
                            ->required(),
                        DatePicker::make('hire_date')->required(),
                        DatePicker::make('termination_date'),
                        Toggle::make('is_active')->default(true),
                    ]),
                ]),

            Step::make('Leave Policies')
                ->description('Set leave allowances for '.now()->year)
                ->schema([
                    Repeater::make('leave_policies_data')
                        ->label('Leave Policies for '.now()->year)
                        ->schema([
                            Toggle::make('is_enabled')
                                ->default(true)
                                ->label('Enabled')
                                ->inline(false)
                                ->columnSpan(1),
                            TextInput::make('leave_type_label')
                                ->label('Leave Type')
                                ->disabled()
                                ->columnSpan(2),
                            TextInput::make('leave_type')
                                ->hidden()
                                ->dehydrated(false),
                            TextInput::make('allowed_days')
                                ->numeric()
                                ->minValue(0)
                                ->step(0.5)
                                ->default(0)
                                ->required()
                                ->label('Allowed Days')
                                ->suffix('days')
                                ->columnSpan(2),
                        ])
                        ->columns(5)
                        ->default([
                            ['leave_type' => 'sickLeave', 'leave_type_label' => 'Sick Leave', 'allowed_days' => 0, 'is_enabled' => true],
                            ['leave_type' => 'vacation', 'leave_type_label' => 'Vacation', 'allowed_days' => 0, 'is_enabled' => true],
                            ['leave_type' => 'personalLeave', 'leave_type_label' => 'Personal Leave', 'allowed_days' => 0, 'is_enabled' => true],
                            ['leave_type' => 'maternityLeave', 'leave_type_label' => 'Maternity Leave', 'allowed_days' => 0, 'is_enabled' => true],
                            ['leave_type' => 'paternityLeave', 'leave_type_label' => 'Paternity Leave', 'allowed_days' => 0, 'is_enabled' => true],
                            ['leave_type' => 'timeOff', 'leave_type_label' => 'Time Off', 'allowed_days' => 0, 'is_enabled' => true],
                            ['leave_type' => 'other', 'leave_type_label' => 'Other', 'allowed_days' => 0, 'is_enabled' => true],
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columnSpan('full'),
                ]),
        ];
    }

    protected function afterCreate(): void
    {
        $currentYear = now()->year;

        foreach ($this->data['leave_policies_data'] ?? [] as $policyData) {
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
