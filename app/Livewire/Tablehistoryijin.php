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

class Tablehistoryijin extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Historyform::query())
            ->columns([
                TextColumn::make('user_id'),
                TextColumn::make('unit_id'),
                TextColumn::make('tanggal_keluar'),
                TextColumn::make('tanggal_kembali'),
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
