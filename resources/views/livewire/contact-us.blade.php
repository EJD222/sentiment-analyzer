<!-- resources/views/livewire/contact-us.blade.php -->
<<<<<<< HEAD
<div class="background">
   <span></span>
   <span></span>
   <span></span>
   <span></span>
   <span></span>



</div>

=======
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Contact Us') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form wire:submit.prevent="submit">
                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Name</label>
                        <input id="name" type="text" wire:model="name" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:ring focus:ring-opacity-50">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
                        <input id="email" type="email" wire:model="email" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:ring focus:ring-opacity-50">
                    </div>

                    <div class="mb-4">
                        <label for="message" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Message</label>
                        <textarea id="message" wire:model="message" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:ring focus:ring-opacity-50"></textarea>
                    </div>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-white dark:text-gray-200 uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring focus:ring-blue-300 dark:focus:ring-blue-600">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
>>>>>>> 3ef48af861dba66df1e809b0131f2268db28d981
