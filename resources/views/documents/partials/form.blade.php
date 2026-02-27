@csrf

@if ($method !== 'POST')
    @method($method)
@endif

<div class="space-y-4">
    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
        <div>
            <x-input-label for="document_date" :value="__('Document Date')" />
            <x-text-input id="document_date" name="document_date" type="date" class="mt-1 block w-full" :value="old('document_date', $document->document_date?->format('Y-m-d'))" required />
            <x-input-error class="mt-1.5" :messages="$errors->get('document_date')" />
        </div>

        <div>
            <x-input-label for="document_no" :value="__('Document No')" />
            <x-text-input id="document_no" name="document_no" type="text" class="mt-1 block w-full" :value="old('document_no', $document->document_no)" maxlength="20" required />
            <x-input-error class="mt-1.5" :messages="$errors->get('document_no')" />
        </div>

        <div>
            <x-input-label for="document_year" :value="__('Document Year')" />
            <x-text-input id="document_year" name="document_year" type="text" class="mt-1 block w-full" :value="old('document_year', $document->document_year)" maxlength="4" required />
            <x-input-error class="mt-1.5" :messages="$errors->get('document_year')" />
        </div>

        <div>
            <x-input-label for="employee_name" :value="__('Employee Name')" />
            <x-text-input id="employee_name" name="employee_name" type="text" class="mt-1 block w-full" :value="old('employee_name', $document->employee_name)" maxlength="150" required />
            <x-input-error class="mt-1.5" :messages="$errors->get('employee_name')" />
        </div>

        <div>
            <x-input-label for="position" :value="__('Position')" />
            <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $document->position)" maxlength="150" required />
            <x-input-error class="mt-1.5" :messages="$errors->get('position')" />
        </div>

        <div>
            <x-input-label for="assignment_station" :value="__('Assignment Station')" />
            <x-text-input id="assignment_station" name="assignment_station" type="text" class="mt-1 block w-full" :value="old('assignment_station', $document->assignment_station)" maxlength="200" required />
            <x-input-error class="mt-1.5" :messages="$errors->get('assignment_station')" />
        </div>

        <div class="md:col-span-2 lg:col-span-3">
            <x-input-label for="conforme_name" :value="__('Conforme Name')" />
            <x-text-input id="conforme_name" name="conforme_name" type="text" class="mt-1 block w-full" :value="old('conforme_name', $document->conforme_name)" maxlength="150" required />
            <x-input-error class="mt-1.5" :messages="$errors->get('conforme_name')" />
        </div>
    </div>

    <div class="flex flex-col gap-2 border-t border-gray-100 pt-3 sm:flex-row sm:justify-end dark:border-gray-700">
        <button
            type="submit"
            name="action"
            value="save"
            class="inline-flex items-center justify-center rounded-md bg-gray-900 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white"
        >
            {{ __('Save') }}
        </button>
        <button
            type="submit"
            name="action"
            value="save_pdf"
            class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
        >
            {{ __('Save & Download PDF') }}
        </button>
    </div>
</div>
