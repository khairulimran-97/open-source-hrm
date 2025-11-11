<x-filament-panels::page>
    <div x-data="{ activeTab: 'personal-info' }" class="space-y-6">
        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                    @click="activeTab = 'personal-info'"
                    :class="activeTab === 'personal-info' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors"
                >
                    Personal Information
                </button>

                <button
                    @click="activeTab = 'emergency-contacts'"
                    :class="activeTab === 'emergency-contacts' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors"
                >
                    Emergency Contacts
                </button>

                <button
                    @click="activeTab = 'employment-details'"
                    :class="activeTab === 'employment-details' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors"
                >
                    Employment Details
                </button>

                <button
                    @click="activeTab = 'leave-policies'"
                    :class="activeTab === 'leave-policies' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors"
                >
                    Leave Policies
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="mt-6">
            {{-- Personal Information Tab (Basic + Contact) --}}
            <div x-show="activeTab === 'personal-info'" x-cloak>
                <form wire:submit="savePersonalInfo">
                    <div class="space-y-6">
                        {{ $this->basicInfoForm }}
                        {{ $this->contactInfoForm }}
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit">
                            Save Personal Information
                        </x-filament::button>
                    </div>
                </form>
            </div>

            {{-- Emergency Contacts Tab (Emergency + Next of Kin) --}}
            <div x-show="activeTab === 'emergency-contacts'" x-cloak>
                <form wire:submit="saveEmergencyContacts">
                    <div class="space-y-6">
                        {{ $this->emergencyContactForm }}
                        {{ $this->nextOfKinForm }}
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit">
                            Save Emergency Contacts
                        </x-filament::button>
                    </div>
                </form>
            </div>

            {{-- Employment Details Tab --}}
            <div x-show="activeTab === 'employment-details'" x-cloak>
                <form wire:submit="saveEmploymentDetails">
                    {{ $this->employmentDetailsForm }}

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit">
                            Save Employment Details
                        </x-filament::button>
                    </div>
                </form>
            </div>

            {{-- Leave Policies Tab --}}
            <div x-show="activeTab === 'leave-policies'" x-cloak>
                <form wire:submit="saveLeavePolicies">
                    {{ $this->leavePoliciesForm }}

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit">
                            Save Leave Policies
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
