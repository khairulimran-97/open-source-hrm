<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Department;
use App\Models\LeavePolicy;
use App\Models\Position;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class EditEmployee extends EditRecord implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = EmployeeResource::class;

    protected string $view = 'filament.resources.employees.pages.edit-employee';

    public ?array $basicInfoData = [];

    public ?array $contactInfoData = [];

    public ?array $emergencyContactData = [];

    public ?array $nextOfKinData = [];

    public ?array $employmentDetailsData = [];

    public ?array $leavePoliciesData = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->basicInfoData = [
            'employee_number' => $this->record->employee_number,
            'first_name' => $this->record->first_name,
            'last_name' => $this->record->last_name,
            'date_of_birth' => $this->record->date_of_birth,
            'gender' => $this->record->gender,
            'marital_status' => $this->record->marital_status,
        ];

        $this->contactInfoData = [
            'email' => $this->record->email,
            'phone' => $this->record->phone,
            'national_id' => $this->record->national_id,
            'kra_pin' => $this->record->kra_pin,
        ];

        $this->emergencyContactData = [
            'emergency_contact_name' => $this->record->emergency_contact_name,
            'emergency_contact_phone' => $this->record->emergency_contact_phone,
        ];

        $this->nextOfKinData = [
            'next_of_kin_name' => $this->record->next_of_kin_name,
            'next_of_kin_relationship' => $this->record->next_of_kin_relationship,
            'next_of_kin_phone' => $this->record->next_of_kin_phone,
            'next_of_kin_email' => $this->record->next_of_kin_email,
        ];

        $this->employmentDetailsData = [
            'department_id' => $this->record->department_id,
            'position_id' => $this->record->position_id,
            'employment_type' => $this->record->employment_type,
            'hire_date' => $this->record->hire_date,
            'termination_date' => $this->record->termination_date,
            'is_active' => $this->record->is_active,
        ];

        $this->leavePoliciesData['leavePolicies'] = $this->record->leavePolicies()
            ->where('year', now()->year)
            ->orderBy('leave_type')
            ->get()
            ->map(fn ($policy) => [
                'id' => $policy->id,
                'leave_type' => $policy->leave_type->value,
                'allowed_days' => (float) $policy->allowed_days,
                'is_enabled' => (bool) $policy->is_enabled,
            ])
            ->values()
            ->toArray();
    }

    public function generateLeavePolicies(): void
    {
        $currentYear = now()->year;

        $leaveTypes = [
            'sickLeave',
            'vacation',
            'personalLeave',
            'maternityLeave',
            'paternityLeave',
            'timeOff',
            'other',
        ];

        foreach ($leaveTypes as $leaveType) {
            LeavePolicy::create([
                'employee_id' => $this->record->id,
                'leave_type' => $leaveType,
                'allowed_days' => 0,
                'year' => $currentYear,
                'is_enabled' => true,
            ]);
        }

        Notification::make()
            ->title('Leave policies generated successfully for '.now()->year)
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
    }

    public function basicInfoForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->description('Employee personal information')
                    ->aside()
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
            ])
            ->statePath('basicInfoData')
            ->model($this->record);
    }

    public function contactInfoForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Contact Information')
                    ->description('Employee contact details')
                    ->aside()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->label('Email Address')
                                ->unique(ignoreRecord: true, table: 'employees', column: 'email'),
                            TextInput::make('phone')
                                ->tel()
                                ->required()
                                ->label('Phone Number')
                                ->unique(ignoreRecord: true, table: 'employees', column: 'phone'),
                            TextInput::make('national_id')
                                ->required()
                                ->unique(ignoreRecord: true, table: 'employees', column: 'national_id')
                                ->integer(),
                            TextInput::make('kra_pin'),
                        ]),
                    ]),
            ])
            ->statePath('contactInfoData')
            ->model($this->record);
    }

    public function emergencyContactForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Emergency Contact')
                    ->description('Emergency contact person details')
                    ->aside()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('emergency_contact_name'),
                            TextInput::make('emergency_contact_phone'),
                        ]),
                    ]),
            ])
            ->statePath('emergencyContactData')
            ->model($this->record);
    }

    public function nextOfKinForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Next of Kin')
                    ->description('Next of kin information')
                    ->aside()
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
            ])
            ->statePath('nextOfKinData')
            ->model($this->record);
    }

    public function employmentDetailsForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Employment Details')
                    ->description('Employment and job information')
                    ->aside()
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
            ])
            ->statePath('employmentDetailsData')
            ->model($this->record);
    }

    public function leavePoliciesForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Leave Policies')
                    ->description('Configure leave allowances for '.now()->year)
                    ->schema([
                        Placeholder::make('no_policies')
                            ->label('No Leave Policies Found')
                            ->content('No leave policies have been set up for '.now()->year.'. Click the button below to generate default policies.')
                            ->visible(fn () => ! $this->record->leavePolicies()->where('year', now()->year)->exists()),

                        Repeater::make('leavePolicies')
                            ->label('Leave Policies for '.now()->year)
                            ->visible(fn () => $this->record->leavePolicies()->where('year', now()->year)->exists())
                            ->schema([
                                TextInput::make('id')
                                    ->hidden()
                                    ->dehydrated(),
                                Toggle::make('is_enabled')
                                    ->default(true)
                                    ->label('Enabled')
                                    ->inline(false),
                                Select::make('leave_type')
                                    ->label('Leave Type')
                                    ->options(fn () => collect(\App\Enums\LeaveType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('allowed_days')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.5)
                                    ->default(0)
                                    ->required()
                                    ->label('Allowed Days')
                                    ->suffix('days'),
                            ])
                            ->columns(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpan('full'),
                    ])
                    ->footerActions([
                        fn () => Action::make('generatePolicies')
                            ->label('Generate Leave Policies')
                            ->action('generateLeavePolicies')
                            ->color('primary')
                            ->icon('heroicon-o-plus-circle')
                            ->visible(fn () => ! $this->record->leavePolicies()->where('year', now()->year)->exists()),
                    ]),
            ])
            ->statePath('leavePoliciesData')
            ->model($this->record);
    }

    public function savePersonalInfo(): void
    {
        $this->record->update(array_merge(
            $this->basicInfoData,
            $this->contactInfoData
        ));

        Notification::make()
            ->title('Personal information saved successfully')
            ->success()
            ->send();
    }

    public function saveEmergencyContacts(): void
    {
        $this->record->update(array_merge(
            $this->emergencyContactData,
            $this->nextOfKinData
        ));

        Notification::make()
            ->title('Emergency contacts saved successfully')
            ->success()
            ->send();
    }

    public function saveEmploymentDetails(): void
    {
        $this->record->update($this->employmentDetailsData);

        Notification::make()
            ->title('Employment details saved successfully')
            ->success()
            ->send();
    }

    public function saveLeavePolicies(): void
    {
        $state = $this->leavePoliciesForm->getState();

        // Update each leave policy by ID
        foreach ($state['leavePolicies'] ?? [] as $policyData) {
            if (isset($policyData['id'])) {
                LeavePolicy::where('id', $policyData['id'])->update([
                    'is_enabled' => $policyData['is_enabled'] ?? true,
                    'allowed_days' => $policyData['allowed_days'] ?? 0,
                ]);
            }
        }

        Notification::make()
            ->title('Leave policies saved successfully')
            ->success()
            ->send();
    }

    protected function getForms(): array
    {
        return [
            'basicInfoForm',
            'contactInfoForm',
            'emergencyContactForm',
            'nextOfKinForm',
            'employmentDetailsForm',
            'leavePoliciesForm',
        ];
    }
}
