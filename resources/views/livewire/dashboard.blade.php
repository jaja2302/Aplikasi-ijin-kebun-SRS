<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="col-span-1">
        <div class="w-full lg:max-w-xl p-6 space-y-8 sm:p-8 bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Form Ijin Keluar
            </h2>

            @livewire('inputformijin')


        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-span-1 md:col-span-2 ">
        <div class="p-4 bg-white rounded shadow-xl dark:bg-gray-800">
            <h2 class="text-lg font-bold mb-4">History Keluar Kebun</h2>
            @livewire('tablehistoryijin')

        </div>
    </div>
</div>