<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\Unitlist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\Historyform;
use Carbon\Carbon;

class Inputformijin extends Component implements HasForms
{

    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->required()
                    ->default(auth()->user()->nama_lengkap)
                    ->readOnly()
                    ->columnSpanFull(),
                DatePicker::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->required()
                    ->format('Y-m-d'),
                DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required()
                    ->format('Y-m-d'),
                Select::make('kendaraan')
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name_doc')
                            ->label('New')
                            ->required(),
                    ])
                    ->options([
                        'Motor' => 'Motor',
                        'Mobil' => 'Mobil',
                    ]),
                Select::make('unit_kerja')
                    ->label('Unit Kerja')
                    // ->required()
                    ->options(Unitlist::query()->pluck('nama', 'id'))
                    ->createOptionForm([
                        TextInput::make('unit_create')
                            ->label('Unit Kerja')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $check = Unitlist::where('nama', $data['unit_create'])->first();

                        if ($check) {
                            Notification::make()
                                ->warning()
                                ->title('Warning')
                                ->body('Unit Kerja dengan nama ini sudah ada didalam database, Silahkan ganti dengan nama yang lainnnya.')
                                ->send();


                            return 0;
                        } else {
                            DB::beginTransaction();
                            try {
                                Notification::make()
                                    ->success()
                                    ->title('Success')
                                    ->body('Unit kerja berhasil ditambah')
                                    ->send();

                                $locs = Unitlist::create(['nama' => $data['unit_create']]);

                                DB::commit();
                                return $locs->id;
                            } catch (\Throwable $th) {
                                DB::rollBack();

                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body($th)
                                    ->send();

                                return 0;
                            }
                        }
                    }),
                TextInput::make('plat_nomor')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('tujuan')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('keperluan')
                    ->required()
                    ->columnSpanFull(),
                Select::make('atasan_1')
                    ->required()
                    ->label('Pilih atasan I')
                    ->options([
                        '1' => 'Jojok',
                        '2' => 'Dendi',
                        '3' => 'Dimas',
                    ]),
                Select::make('atasan_2')
                    ->required()
                    ->label('Pilih atasan II')
                    ->options([
                        '1' => 'Jojoka',
                        '2' => 'Dendi',
                        '3' => 'Dimas',
                    ]),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function create(): void
    {
        // dd($this->form->getState());
        $form = $this->form->getState();


        try {
            // Create a new Administrator instance
            $administrator = new Historyform();
            $administrator->user_id = auth()->user()->user_id;
            $administrator->unit_id = $form['unit_kerja'];
            $administrator->tanggal_keluar = Carbon::parse($form['tanggal_keluar']);
            $administrator->tanggal_kembali = Carbon::parse($form['tanggal_kembali']);
            $administrator->lokasi_tujuan = $form['tujuan'];
            $administrator->keperluan = $form['keperluan'];
            $administrator->atasan_1 = $form['atasan_1'];
            $administrator->atasan_2 = $form['atasan_2'];

            $administrator->save();

            Notification::make()
                ->success()
                ->title('Success')
                ->body('Permintaan Ijin keluar berhasil di tambahkan')
                ->send();
            $this->form->fill();
            $this->dispatch('refresh');
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('danger')
                ->body($e)
                ->send();
            // $this->form->phone_number->fill();
        }
    }

    public function render(): View
    {
        return view('livewire.inputformijin');
    }
}
