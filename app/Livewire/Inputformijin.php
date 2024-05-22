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
                    ->columnSpanFull(),
                DatePicker::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->required()
                    ->format('d/m/Y'),
                DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required()
                    ->format('d/m/Y'),
                Select::make('kendaraan')
                    ->required()
                    ->options([
                        'Motor' => 'Motor',
                        'Mobil' => 'Mobil',
                    ]),
                TextInput::make('plat_nomor')
                    ->required(),
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
                        '1' => 'Jojok',
                        '2' => 'Dendi',
                        '3' => 'Dimas',
                    ]),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render(): View
    {
        return view('livewire.inputformijin');
    }
}
