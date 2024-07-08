<?php

namespace App\Livewire;

use DateTime;
use stdClass;
use Carbon\Carbon;
use App\Models\User;
use IntlDateFormatter;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\FormSuratIzin;
use App\Models\ListDepartement;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Actions\Concerns\InteractsWithActions;

class Dashboard extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public $idDataRecord = null;
    public $tanggal_keluar = null;
    public $tanggal_kembali = null;
    public $kendaraan = null;
    public $plat_nomor = null;
    public $lokasi_tujuan = null;
    public $keperluan = null;
    public $atasan_1 = null;
    public $atasan_2 = null;

    public $record = [];
    public $allUser = [];
    public $allListDepartement = [];
    public $review = false;
    public $isEdit = false;
    public $greeting = '';

    public function mount(): void
    {
        $this->allUser = User::with('new_jabatan')->get()->keyBy('user_id')->toArray();
        $this->allListDepartement = ListDepartement::get()->keyBy('id')->toArray();

        $currentHour = Carbon::now()->hour;
        if ($currentHour < 12) {
            $this->greeting = 'Selamat pagi';
        } elseif ($currentHour < 17) {
            $this->greeting = 'Selamat siang';
        } else {
            $this->greeting = 'Selamat malam';
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(FormSuratIzin::query())
            ->modifyQueryUsing(function (Builder $query) {
                if ($this->review) {
                    return $query->whereIn('user_id', $this->listDataResultsNew('query'));
                }

                return $query->where('user_id', auth()->user()->user_id);
            })
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(fn () => ($this->review) ? 'Tidak ada pengajuan surat' : 'Tidak ada riwayat')
            ->emptyStateDescription(fn () => ($this->review) ? 'Tidak ada pengajuan surat izin yang dikirim ke anda.' : 'Tambahkan pengajuan surat izin pada halaman yang telah disediakan.')
            ->filters([
                Filter::make('tanggal_pengajuan_filters')
                    ->form([
                        DatePicker::make('tanggal_pengajuan_dari'),
                        DatePicker::make('tanggal_pengajuan_sampai')
                            ->default(now()->timezone('Asia/Jakarta')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_pengajuan_dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_pengajuan_sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['tanggal_pengajuan_sampai']) {
                            return null;
                        }

                        return 'Tanggal pengajuan sampai ' . formattedDate(Carbon::parse($data['tanggal_pengajuan_sampai'])->toFormattedDateString(), IntlDateFormatter::MEDIUM);
                    })
            ])
            ->columns([
                TextColumn::make('No')
                    ->state(static function (HasTable $livewire, stdClass $rowLoop): string {
                        return strval((intval($rowLoop->iteration) + (intval($livewire->getTableRecordsPerPage()) * (intval($livewire->getTablePage()) - 1))));
                    }),
                TextColumn::make('nama_lengkap')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn () => !$this->review)
                    ->state(fn (Model $model): string => isset($this->allUser[$model->user_id]) ? ucwords(strtolower($this->allUser[$model->user_id]['nama_lengkap'])) : ''),
                TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->searchable()
                    ->sortable()
                    ->state(fn (Model $model): string => formattedDate($model->created_at, IntlDateFormatter::LONG)),
                TextColumn::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->searchable()
                    ->sortable()
                    ->state(fn (Model $model): string => formattedDate($model->tanggal_keluar, IntlDateFormatter::LONG)),
                TextColumn::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->searchable()
                    ->sortable()
                    ->state(fn (Model $model): string => formattedDate($model->tanggal_kembali, IntlDateFormatter::LONG)),
                TextColumn::make('kendaraan')
                    ->label('Jenis Kendaraan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plat_nomor')
                    ->label('Plat Nomor')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lokasi_tujuan')
                    ->label('Tujuan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('keperluan')
                    ->label('Keperluan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->state(fn (Model $model): string => match ($model->status) {
                        '1' => ($model->status == 1 && $model->atasan_1 == auth()->id()) ? 'MENUNGGU PERSETUJUAN ANDA' : 'MENUNGGU PERSETUJUAN ATASAN 1',
                        '2' => ($model->status == 2 && $model->atasan_2 == auth()->id()) ? 'MENUNGGU PERSETUJUAN ANDA' : 'MENUNGGU PERSETUJUAN ATASAN 2',
                        '3' => 'DITOLAK',
                        '4' => 'DISETUJUI',
                    })
                    ->color(fn (Model $model): string => match ($model->status) {
                        '1' => 'warning',
                        '2' => 'info',
                        '3' => 'danger',
                        '4' => 'success',
                    }),
            ])
            ->headerActions([
                TableAction::make('add_submission')
                    ->label('Tambah Pengajuan')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color(Color::Emerald)
                    ->action(function () {
                        $this->dispatch('openModalSubmission');
                    }),
                TableAction::make('data_review')
                    ->label('Review Pengajuan')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->color(fn () => $this->review ? Color::Slate : Color::Amber)
                    ->hidden(fn () => ($this->allUser[auth()->user()->user_id]['new_jabatan']['id_level'] ?? 0) < 3)
                    ->disabled(fn () => $this->review)
                    ->action(function () {
                        $this->review = true;
                    }),
                TableAction::make('my_history')
                    ->label('Riwayat Pengajuan')
                    ->icon('heroicon-m-document-text')
                    ->color(fn () => !$this->review ? Color::Slate : Color::Amber)
                    ->hidden(fn () => ($this->allUser[auth()->user()->user_id]['new_jabatan']['id_level'] ?? 0) < 3)
                    ->disabled(fn () => !$this->review)
                    ->action(function () {
                        $this->review = false;
                    })
            ])
            ->actions([
                TableAction::make('detail_izin')
                    ->label('Detail')
                    ->icon('heroicon-m-document-magnifying-glass')
                    ->color(Color::Emerald)
                    ->action(function (Model $record) {
                        $this->record = $record->refresh()->toArray();
                        $this->dispatch('openModalHistory');
                    }),
                TableAction::make('edit_approval')
                    ->label('Perbarui')
                    ->icon('heroicon-m-pencil-square')
                    ->color(Color::Blue)
                    ->action(function (Model $record) {
                        $this->isEdit = true;

                        $this->idDataRecord = $record->id;
                        $this->tanggal_keluar = $record->tanggal_keluar;
                        $this->tanggal_kembali = $record->tanggal_kembali;
                        $this->kendaraan = $record->kendaraan;
                        $this->plat_nomor = $record->plat_nomor;
                        $this->lokasi_tujuan = $record->lokasi_tujuan;
                        $this->keperluan = $record->keperluan;
                        $this->atasan_1 = $record->atasan_1;
                        $this->atasan_2 = $record->atasan_2;

                        $this->dispatch('openModalSubmission');
                    })
                    ->hidden(function (Model $record) {
                        $date = Carbon::parse($record->tanggal_keluar);

                        $now = Carbon::now();
                        $startOfWeek = $now->startOfWeek();
                        $endOfWeek = $now->endOfWeek();

                        if ($this->review) {
                            return true;
                        }

                        return $record->status == 4 || $date->between($startOfWeek, $endOfWeek) || $date->lt($startOfWeek->copy()->subWeek()) ? true : false;
                    }),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 2
                ])
                    ->schema([
                        TextInput::make('departement')
                            ->label('Unit / Sub. Unit')
                            ->default($this->allListDepartement[auth()->user()->id_departement]['nama'] ?? 'Data tidak ditemukan')
                            ->placeholder($this->allListDepartement[auth()->user()->id_departement]['nama'] ?? 'Data tidak ditemukan')
                            ->extraInputAttributes(['class' => 'capitalize'])
                            ->columnSpanFull()
                            ->disabled(),
                        DateTimePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->live()
                            ->default(now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'))
                            ->minDate(now()->timezone('Asia/Jakarta')->startOfDay())
                            ->closeOnDateSelection()
                            ->seconds(false)
                            ->required()
                            ->afterStateUpdated(fn ($get, $set) => $set('tanggal_kembali', now()->parse($get('tanggal_keluar'))->addDay()->format('Y-m-d H:i:s'))),
                        DateTimePicker::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->default(now()->timezone('Asia/Jakarta')->addDay()->format('Y-m-d H:i:s'))
                            ->minDate(fn ($get) => $get('tanggal_keluar'))
                            ->closeOnDateSelection()
                            ->seconds(false)
                            ->required(),
                        Select::make('kendaraan')
                            ->label('Jenis Kendaraan')
                            ->options([
                                'Motor' => 'Motor',
                                'Mobil' => 'Mobil'
                            ])
                            ->native(false)
                            ->placeholder('Pilih salah satu jenis kendaraan')
                            ->required(),
                        TextInput::make('plat_nomor')
                            ->label('Plat Nomor')
                            ->placeholder('KH 1234 AA')
                            ->extraInputAttributes(['class' => 'uppercase'])
                            ->required(),
                        TextInput::make('lokasi_tujuan')
                            ->label('Tujuan')
                            ->placeholder('Pangkalan Bun')
                            ->extraInputAttributes(['class' => 'capitalize'])
                            ->columnSpanFull()
                            ->required(),
                        TextInput::make('keperluan')
                            ->label('Keperluan')
                            ->placeholder('Belanja Bulanan')
                            ->extraInputAttributes(['class' => 'capitalize'])
                            ->columnSpanFull()
                            ->required(),
                        Select::make('atasan_1')
                            ->label('Atasan 1')
                            ->live()
                            ->options(fn () => $this->listDataResultsNew())
                            ->placeholder('Pilih atasan 1')
                            ->searchable()
                            ->optionsLimit(10)
                            ->required()
                            ->columnSpanFull()
                            ->afterStateUpdated(fn ($set) => $set('atasan_2', null))
                            ->getSearchResultsUsing(fn (string $search): array => $this->listDataResultsNew('', $search)),
                        Select::make('atasan_2')
                            ->label('Atasan 2')
                            ->live()
                            ->options(fn ($get) => ($get('atasan_1') !== null) ? $this->listDataResultsNew() : [])
                            ->placeholder('Pilih atasan 2')
                            ->searchable()
                            ->optionsLimit(10)
                            ->required()
                            ->columnSpanFull()
                            ->getSearchResultsUsing(fn (string $search): array => $this->listDataResultsNew('', $search))
                            ->afterStateUpdated(function (?string $state, $get, $set) {
                                if (!empty($state) && $this->allUser[$get('atasan_1')]['new_jabatan'] !== null && $this->allUser[$state]['new_jabatan'] !== null) {
                                    if ($this->allUser[$state]['new_jabatan']['id_level'] < $this->allUser[$get('atasan_1')]['new_jabatan']['id_level']) {
                                        $set('atasan_2', null);
                                        notifyValidation('Jabatan atasan 2 harus lebih tinggi atau sama dengan atasan 1', '', 'warning', '');
                                        return null;
                                    }
                                }
                            })
                    ])
            ]);
    }

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->icon('heroicon-m-arrow-up-tray')
            ->requiresConfirmation()
            ->modalHeading('Submit formulir')
            ->modalDescription(fn () => $this->isEdit ? 'Apakah anda yakin ingin memperbarui data? Status pengajuan akan diatur ulang.' : 'Apakah anda yakin bahwa data sudah benar?')
            ->modalSubmitActionLabel('Ya, yakin')
            ->extraAttributes(['class' => 'btn-approve-fi py-5'])
            ->action(function () {
                if (!isset($this->tanggal_keluar) || !isset($this->tanggal_kembali) || !isset($this->kendaraan) || !isset($this->plat_nomor) || !isset($this->lokasi_tujuan) || !isset($this->keperluan) || !isset($this->atasan_1) || !isset($this->atasan_2)) {
                    notifyValidation('Silakan lengkapi semua data terlebih dahulu.', '', 'warning', 'admin');
                }

                $data['user_id'] = auth()->user()->user_id;
                $data['tanggal_keluar'] = (new DateTime($this->tanggal_keluar))->format('Y-m-d H:i:s');
                $data['tanggal_kembali'] = (new DateTime($this->tanggal_kembali))->format('Y-m-d H:i:s');
                $data['kendaraan'] = $this->kendaraan;
                $data['plat_nomor'] = $this->plat_nomor;
                $data['lokasi_tujuan'] = ucwords(strtolower($this->lokasi_tujuan));
                $data['keperluan'] = ucwords(strtolower($this->keperluan));
                $data['atasan_1'] = $this->atasan_1;
                $data['atasan_2'] = $this->atasan_2;
                $data['status'] = 1;
                $data['catatan'] = null;
                $data['status_bot'] = '0$0$0';

                $createForm = FormSuratIzin::updateOrCreate(
                    ['id' => $this->idDataRecord],
                    $data
                );
                if ($createForm) {
                    $this->isEdit = false;
                    $this->dispatch('closeModalSubmission');
                    notifyValidation('Berhasil menambahkan surat izin.', '', 'success', '');
                    $this->idDataRecord = $this->tanggal_keluar = $this->tanggal_kembali = $this->kendaraan = $this->plat_nomor = $this->lokasi_tujuan = $this->keperluan = $this->atasan_1 = $this->atasan_2 = null;
                } else {
                    notifyValidation('Gagal menambahkan surat izin.');
                }
            });
    }

    public function approveAction(): Action
    {
        return Action::make('approve')
            ->icon(fn (array $arguments): string => $arguments['value'] == 'yes' ? 'heroicon-m-check' : 'heroicon-m-x-mark')
            ->color(fn (array $arguments) => $arguments['value'] == 'yes' ? Color::Emerald : Color::Red)
            ->label(fn (array $arguments): string => $arguments['value'] == 'yes' ? 'Approve' : 'Reject')
            ->extraAttributes(['class' => 'btn-approve-fi py-5'])
            ->form(fn (array $arguments): array => $arguments['value'] == 'yes' ? [] : [
                TextInput::make('catatan')->required()
            ])
            ->requiresConfirmation()
            ->modalHeading('Peringatan')
            ->modalDescription(fn (array $arguments): string => $arguments['value'] == 'yes' ? 'Apakah anda yakin ingin menyetujui pengajuan ini?' : 'Apakah anda yakin ingin menolak pengajuan ini?')
            ->modalSubmitActionLabel(fn (array $arguments): string => $arguments['value'] == 'yes' ? 'Ya, setujui' : 'Ya, tolak')
            ->action(function (array $arguments, array $data) {
                $qrUpdate = FormSuratIzin::find($this->record['id']);
                if ($qrUpdate) {
                    $this->record['status'] = $this->record['atasan_1'] == $this->record['atasan_2'] ? 99 : $this->record['status'];
                    $statusMap = [
                        1 => ['yes' => 2, 'no' => 3],
                        2 => ['yes' => 4, 'no' => 3],
                        99 => ['yes' => 4, 'no' => 3]
                    ];
                    $statusBotMap = [
                        1 => '1$0$0',
                        2 => '1$1$0',
                        99 => '1$1$0',
                    ];
                    $qrUpdate->status = $statusMap[$this->record['status']][$arguments['value']] ?? 1;
                    $qrUpdate->catatan = $data['catatan'] ?? null;
                    $qrUpdate->status_bot = $statusBotMap[$this->record['status']] ?? '0$0$0';
                    if ($qrUpdate->save()) {
                        $this->dispatch('closeModalHistory');
                        notifyValidation('Berhasil mengubah status pengajuan.', '', 'success', '');
                    } else {
                        notifyValidation('Gagal mengubah status pengajuan.');
                    }
                } else {
                    notifyValidation('Gagal mengubah status pengajuan. 404.');
                }
            });
    }

    public function pdfAction(): Action
    {
        return Action::make('pdf')
            ->label('Unduh PDF')
            ->icon('heroicon-m-arrow-down-tray')
            ->color(Color::hex('#cbd5e1'))
            ->requiresConfirmation()
            ->modalHeading('Peringatan')
            ->modalDescription('Apakah anda yakin ingin mengunduh surat izin?')
            ->modalSubmitActionLabel('Ya, unduh')
            ->extraAttributes(['class' => 'btn-approve-fi py-5'])
            ->action(function () {
                try {
                    // Generate Encrypted Data
                    $qrCodeEncrypt = '';
                    if ($this->record['status'] == '4') {
                        $jsonData = array_filter(array_map(function ($key, $value) {
                            if (in_array($key, ['id', 'status', 'catatan', 'status_bot', 'created_at', 'updated_at'])) {
                                return null;
                            }
                            if (in_array($key, ['tanggal_keluar', 'tanggal_kembali'])) {
                                return formattedDate($value, IntlDateFormatter::LONG);
                            }
                            if ($key == 'user_id' || $key == 'atasan_1' || $key == 'atasan_2') {
                                return $this->allUser[$value]['nama_lengkap'];
                            }
                            return $value;
                        }, array_keys($this->record), $this->record));
                        $jsonData['jabatan'] = $this->allUser[$this->record['user_id']]['new_jabatan']['nama'];
                        $jsonData['unit'] = $this->allListDepartement[auth()->user()->id_departement]['nama'];
                        $jsonData = json_encode(array_values($jsonData), JSON_FORCE_OBJECT);
                        $encryptedData = aes_encrypt($jsonData, 'CBI@2024');
                        $qrCodeEncrypt = "data:image/png;base64, " . base64_encode(QrCode::size(250)->format('png')->merge(public_path('images/icons/logo_srs.png'), 0.3, true)->generate($encryptedData));
                    }

                    $name_file = 'Surat Izin - ' . formattedDate($this->record['tanggal_keluar'], IntlDateFormatter::LONG) . ' - ' . auth()->user()->nama_lengkap . '.pdf';
                    $pdf = Pdf::loadView('components.layouts.pdf', [
                        'data' => $this->record,
                        'allUser' => $this->allUser,
                        'departement' => $this->allListDepartement[auth()->user()->id_departement]['nama'],
                        'name_file' => $name_file,
                        'qrCodeEncrypt' => $qrCodeEncrypt
                    ])->setPaper([0, 0, 595.28, 900]);

                    $this->dispatch('closeModalHistory');
                    notifyValidation('PDF berhasil diunduh.', '', 'success');
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        $name_file
                    );
                } catch (\Throwable $th) {
                    notifyValidation('Gagal mengunduh surat izin.' . $th->getMessage());
                }
            });
    }

    public function getAllChildIdDepart($data, $currentId)
    {
        $children = [];

        foreach ($data as $entry) {
            if ($entry['id'] == $currentId) {
                $children[] = $entry['id'];
            }
            if ($entry['id_parent'] == $currentId) {
                $children = array_merge($children, $this->getAllChildIdDepart($data, $entry['id']));
            }
        }

        return $children;
    }

    public function listDataResultsNew($arg = '', $search = '')
    {
        $listDataNew = [];

        $copyArrSortAlluser = $this->allUser;
        usort($copyArrSortAlluser, function ($a, $b) {
            $a_level = $a['new_jabatan']['id_level'] ?? 0;
            $b_level = $b['new_jabatan']['id_level'] ?? 0;
            return $b_level <=> $a_level;
        });

        $userLevelJabatan = $this->allUser[auth()->user()->user_id]['new_jabatan']['id_level'] ?? 0;
        foreach ($copyArrSortAlluser as $value) {
            $valueLevel = $value['new_jabatan']['id_level'] ?? 0;

            if ($value['lokasi_kerja'] == auth()->user()->lokasi_kerja) {
                if ($arg == 'query') {
                    if (in_array($value['id_departement'], $this->listDataResultDepart('down')) && $valueLevel < $userLevelJabatan) {
                        $listDataNew[] = $value['user_id'];
                    }
                } else {
                    if (in_array($value['id_departement'], $this->listDataResultDepart()) && $valueLevel > $userLevelJabatan) {
                        if (!empty($search) && isset($valueLevel)) {
                            if (str_contains(strtolower($value['nama_lengkap']), strtolower($search)) || str_contains(strtolower($value['new_jabatan']['nama']), strtolower($search))) {
                                $listDataNew[$value['user_id']] = $value['nama_lengkap'];
                            }
                        } else {
                            $listDataNew[$value['user_id']] = $value['nama_lengkap'];
                        }
                    }
                }
            }
        }

        return $listDataNew;
    }

    public function listDataResultDepart($arg = 'up'): array
    {
        $listDataResultDepart = [];
        if ($arg == 'up') {
            $idDepartUser = $this->allListDepartement[auth()->user()->id_departement] ?? 0;
            if ($idDepartUser != 0) {
                array_push($listDataResultDepart, auth()->user()->id_departement);
                $currentDepartId = $idDepartUser['id_parent'];
                while ($currentDepartId != 0) {
                    array_push($listDataResultDepart, $currentDepartId);
                    if (isset($this->allListDepartement[$currentDepartId])) {
                        $currentDepartId = $this->allListDepartement[$currentDepartId]['id_parent'];
                    } else {
                        break;
                    }
                }
            }
        } else {
            $listDataResultDepart = $this->getAllChildIdDepart($this->allListDepartement, auth()->user()->id_departement);
        }

        return $listDataResultDepart;
    }

    public function logoutAction(): Action
    {
        return Action::make('logout')
            ->requiresConfirmation()
            ->icon('heroicon-m-arrow-left-end-on-rectangle')
            ->color('danger')
            ->size(ActionSize::Large)
            ->modalHeading('Peringatan')
            ->modalDescription('Apakah anda yakin ingin keluar?')
            ->modalSubmitActionLabel('Ya, keluar')
            ->action(function () {
                Auth::logout();
                session()->flush();
                return redirect()->route('login');
            });
    }

    public function render()
    {
        return view('livewire.dashboard')->extends('components.layouts.app')->section('content');
    }
}
