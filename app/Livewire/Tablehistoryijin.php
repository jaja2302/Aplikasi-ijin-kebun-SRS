<?php

namespace App\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\Historyform;
use App\Models\Pengguna;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Unitlist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Tablehistoryijin extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            // ->query(Historyform::query())
            ->query(function () {
                if (in_array(auth()->user()->departemen, ['Programmer', 'SRS Calibration'])) {
                    return Historyform::query();
                } else {
                    return Historyform::query()->where('user_id', auth()->user()->user_id);
                }
            })
            ->columns([
                TextColumn::make('Requestor.nama_lengkap')
                    ->label('Nama User'),
                TextColumn::make('Unit.nama'),
                TextColumn::make('tanggal_keluar')
                    ->state(function (Model $record) {
                        $columns = Historyform::where('id', $record['id'])->pluck('tanggal_keluar')->first();
                        // dd($columns);
                        return Carbon::parse($columns)->format('d-M-Y');
                    }),
                TextColumn::make('tanggal_kembali')
                    ->state(function (Model $record) {
                        $columns = Historyform::where('id', $record['id'])->pluck('tanggal_kembali')->first();
                        return Carbon::parse($columns)->format('d-M-Y');
                    }),
                TextColumn::make('lokasi_tujuan'),
                TextColumn::make('keperluan'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render(): View
    {
        return view('livewire.tablehistoryijin');
    }
}
