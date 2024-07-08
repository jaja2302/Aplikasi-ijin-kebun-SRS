@section('title', 'Dashboard')

<div class="bg-white w-full h-screen scrollbar-none overflow-y-auto">
    <style>
        .btn-approve-fi {
            width: 100%;
            height: 100%;
            border-radius: 0px;
            font-size: 18px;
        }

        .fi-btn:disabled {
            cursor: not-allowed;
        }
    </style>

    <div class="sticky top-0 z-10">
        <div class="navbar bg-emerald-600 shadow-xl">
            <div class="ps-1 navbar-start">
                <div tabindex="0" role="button" class="ms-2 btn btn-ghost btn-circle avatar hover:bg-slate-200/25">
                    <div class="w-10 rounded-full border-2 border-slate-100">
                        <i class="fas fa-user text-2xl mt-0.5 text-slate-100"></i>
                    </div>
                </div>
                <a class="mx-3 text-xl text-slate-100 font-semibold transition-all duration-500 transform">
                    <span class="hidden md:inline">{{ auth()->user()->nama_lengkap }}</span>
                    <span class="inline md:hidden">{{ explode(' ', auth()->user()->nama_lengkap)[0] }}</span>
                </a>
            </div>
            <div class="navbar-end pe-4">
                {{ $this->logoutAction }}
            </div>
        </div>
    </div>

    <div class="flex w-full h-auto px-5 pt-5">
        <div
            class="flex w-full p-5 text-2xl font-bold h-auto bg-gradient-to-r from-emerald-500 to-amber-500 shadow-md rounded-md">
            <div class="flex-grow flex font-normal">
                <div class="flex flex-col">
                    <div class="flex flex-row">
                        <h1 class="text-slate-700 text-3xl font-bold">{{ $this->greeting }},
                            {{ $allUser[auth()->user()->user_id]['nama_lengkap'] }}.</h1>
                        <img src="{{ asset('images/others/wave-hand.png') }}" alt="wave-hand"
                            class="hidden sm:block ms-2 w-8 h-8">
                    </div>
                    <div>
                        <a class=" text-slate-700 text-lg font-normal">{{ !$this->review ? 'Berikut ini adalah riwayat pengajuan anda:' : 'Berikut ini adalah pengajuan yang membutuhkan persetujuan anda:' }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="w-0 lg:w-96 flex-none flex justify-end text-center font-semibold">
                <img src="{{ asset('images/others/star.svg') }}" alt="star"
                    class="hidden lg:block w-20 h-20 transform rotate-45 hover:scale-150 transition-all duration-250">
                <img src="{{ asset('images/others/star.svg') }}" alt="star"
                    class="hidden lg:block w-20 h-20 transform rotate-45 hover:scale-150 transition-all duration-250">
            </div>
        </div>
    </div>

    <div class="flex w-full h-auto p-5">
        <div
            class="w-full max-w-full bg-white rounded-2xl border border-slate-200 shadow-md scrollbar-none overflow-auto">

            {{ $this->table }}
        </div>
    </div>

    <input type="checkbox" id="modalDetailHistory" class="modal-toggle invisible" />
    <div class="modal justify-center z-10" role="dialog">
        <div
            class="modal-box w-screen max-w-sm md:max-w-xl lg:max-w-3xl left-0 right-0 p-0 h-5/6 bg-slate-100 flex flex-col">
            <div class="sticky top-0 bg-emerald-700 px-5 py-3 justify-center items-center z-10 rounded-none">
                <label for="modalDetailHistory"
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-2.5 text-white">✕</label>
                <h3 class="font-semibold text-lg text-white">Detail Izin Keluar</h3>
            </div>

            <div class="p-5 z-0 pb-10 text-slate-800 flex-grow scrollbar-none overflow-y-auto">
                @php
                    $status = 'Data tidak ditemukan';
                    if (isset($record['status'])) {
                        switch ($record['status']) {
                            case 1:
                                $status =
                                    isset($record['atasan_1']) && $record['atasan_1'] == auth()->user()->user_id
                                        ? 'MENUNGGU PERSETUJUAN ANDA'
                                        : 'MENUNGGU PERSETUJUAN ATASAN 1';
                                break;
                            case 2:
                                $status =
                                    isset($record['atasan_2']) && $record['atasan_2'] == auth()->user()->user_id
                                        ? 'Menunggu Persetujuan Anda'
                                        : 'MENUNGGU PERSETUJUAN ATASAN 2';
                                break;
                            case 3:
                                $status = 'DITOLAK';
                                break;
                            default:
                                $status = 'DISETUJUI';
                                break;
                        }
                    }
                @endphp
                <h3 class="font-semibold text-base">Status:
                    <span
                        class="{{ isset($record['status']) ? ($record['status'] == 1 ? 'text-amber-500' : ($record['status'] == 2 ? 'text-blue-500' : ($record['status'] == 3 ? 'text-red-500' : 'text-green-500'))) : 'text-black' }}">{{ $status }}</span>
                </h3>
                @if (isset($record['catatan']) && isset($record['status']) && $record['status'] == 3)
                    <h5 class="font-normal text-sm italic">Catatan:
                        {{ ucwords(strtolower($record['catatan'])) }}
                    </h5>
                @endif
                <hr class="h-px mt-2 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent">
                <div class="mt-3 flex flex-col w-full text-sm font-semibold text-slate-800">
                    <a>Nama Karyawan</a>
                    <div class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between">
                        {{ isset($record['user_id']) && isset($allUser[$record['user_id']]) ? $allUser[$record['user_id']]['nama_lengkap'] : 'Data tidak ditemukan' }}
                    </div>
                </div>
                <div class="mt-3 flex flex-col md:flex-row w-full text-sm font-semibold text-slate-800">
                    <div class="flex flex-col md:w-1/2 md:pe-5">
                        <a>Tanggal Keluar</a>
                        <div
                            class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between md:justify-center">
                            {{ isset($record['tanggal_keluar']) ? formattedDate($record['tanggal_keluar'], IntlDateFormatter::LONG) : 'Data tidak ditemukan' }}
                        </div>
                    </div>
                    <div class="flex flex-col mt-3 md:mt-0 md:w-1/2">
                        <a>Tanggal Kembali</a>
                        <div
                            class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between md:justify-center">
                            {{ isset($record['tanggal_kembali']) ? formattedDate($record['tanggal_kembali'], IntlDateFormatter::LONG) : 'Data tidak ditemukan' }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex flex-col md:flex-row w-full text-sm font-semibold text-slate-800">
                    <div class="flex flex-col md:w-1/2 md:pe-5">
                        <a>Jenis Kendaraan</a>
                        <div
                            class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between md:justify-center">
                            {{ isset($record['kendaraan']) ? $record['kendaraan'] : 'Data tidak ditemukan' }}
                        </div>
                    </div>
                    <div class="flex flex-col mt-3 md:mt-0 md:w-1/2">
                        <a>Plat Nomor</a>
                        <div
                            class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between md:justify-center">
                            {{ isset($record['plat_nomor']) ? $record['plat_nomor'] : 'Data tidak ditemukan' }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex flex-col w-full text-sm font-semibold text-slate-800">
                    <a>Tujuan</a>
                    <div class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between">
                        {{ isset($record['lokasi_tujuan']) ? $record['lokasi_tujuan'] : 'Data tidak ditemukan' }}
                    </div>
                </div>
                <div class="mt-3 flex flex-col w-full text-sm font-semibold text-slate-800">
                    <a>Keperluan</a>
                    <div class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between">
                        {{ isset($record['keperluan']) ? $record['keperluan'] : 'Data tidak ditemukan' }}
                    </div>
                </div>
                <div class="mt-3 flex flex-col w-full text-sm font-semibold text-slate-800">
                    <a>Atasan 1</a>
                    <div class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between">
                        {{ isset($record['atasan_1']) && isset($allUser[$record['atasan_1']]['new_jabatan']) ? $allUser[$record['atasan_1']]['new_jabatan']['nama'] . ' - ' : '' }}{{ isset($record['atasan_1']) && isset($allUser[$record['atasan_1']]) ? $allUser[$record['atasan_1']]['nama_lengkap'] : 'Data tidak ditemukan' }}
                    </div>
                </div>
                <div class="mt-3 flex flex-col w-full text-sm font-semibold text-slate-800">
                    <a>Atasan 2</a>
                    <div class="w-full mt-1 p-2 rounded-lg border border-slate-500/40 flex justify-between">
                        {{ isset($record['atasan_2']) && isset($allUser[$record['atasan_2']]['new_jabatan']) ? $allUser[$record['atasan_2']]['new_jabatan']['nama'] . ' - ' : '' }}{{ isset($record['atasan_2']) && isset($allUser[$record['atasan_2']]) ? $allUser[$record['atasan_2']]['nama_lengkap'] : 'Data tidak ditemukan' }}
                    </div>
                </div>
            </div>

            @if (!$review && isset($record['status']) && $record['status'] == 4)
                <div class="flex flex-row bg-slate-300/30 justify-center items-center z-10 rounded-none">
                    {{ $this->pdfAction }}
                </div>
            @endif

            @if (
                $review &&
                    isset($record['status']) &&
                    ((isset($record['atasan_1']) && $record['status'] == 1 && $record['atasan_1'] == auth()->user()->user_id) ||
                        (isset($record['atasan_2']) && $record['status'] == 2 && $record['atasan_2'] == auth()->user()->user_id)))
                <div class="flex flex-row bg-slate-300/30 justify-center items-center z-10 rounded-none">
                    <div class="w-full flex flex-row justify-center items-center border-t border-e border-slate-300">
                        {{ ($this->approveAction)(['value' => 'yes']) }}
                    </div>
                    <div class="w-full flex flex-row justify-center items-center border-t border-slate-300">
                        {{ ($this->approveAction)(['value' => 'no']) }}
                    </div>
                </div>
            @endif

        </div>
        <label class="modal-backdrop w-screen" for="modalDetailHistory">Close</label>
    </div>

    <input type="checkbox" id="modalFormIzin" class="modal-toggle" />
    <div class="modal justify-center z-10" role="dialog">
        <div
            class="modal-box w-screen max-w-sm md:max-w-xl lg:max-w-3xl left-0 right-0 p-0 h-5/6 bg-slate-100 flex flex-col">
            <div class="sticky top-0 bg-emerald-700 px-5 py-3 justify-center items-center z-10 rounded-none">
                <label id="closeModalFormIzin" for="modalFormIzin"
                    class="btn btn-sm btn-circle btn-ghost absolute right-4 top-2.5 text-white">✕</label>
                <h3 class="font-semibold text-lg text-white">Formulir Permohonan Izin</h3>
            </div>

            <div class="p-5 z-0 pb-10 flex-grow scrollbar-none overflow-y-auto">
                {{ $this->form }}
            </div>

            <div class="flex flex-row bg-slate-300/30 justify-center items-center z-10 rounded-none">
                {{ $this->submitAction }}
            </div>
        </div>
    </div>

    <x-filament-actions::modals />
</div>

@script
    <script>
        window.onload = function() {
            const closeModalFormIzin = document.getElementById('closeModalFormIzin');
            closeModalFormIzin.addEventListener('click', () => {
                @this.isEdit = false;
                @this.idDataRecord = @this.tanggal_keluar = @this.tanggal_kembali = @this
                    .kendaraan = @this
                    .plat_nomor = @this.lokasi_tujuan = @this.keperluan = @this.atasan_1 = @this.atasan_2 =
                    null;
            });

            @this.on('openModalHistory', () => {
                document.getElementById("modalDetailHistory").checked = true;
            })

            @this.on('closeModalHistory', () => {
                document.getElementById("modalDetailHistory").checked = false;
            })

            document.addEventListener('keydown', function(event) {
                if (event.key === "Escape" && document.getElementById("modalDetailHistory").checked) {
                    document.getElementById("modalDetailHistory").checked = false;
                }
            });

            @this.on('openModalSubmission', () => {
                document.getElementById("modalFormIzin").checked = true;
            })

            @this.on('closeModalSubmission', () => {
                document.getElementById("closeModalFormIzin").click();
            })
        }
    </script>
@endscript
