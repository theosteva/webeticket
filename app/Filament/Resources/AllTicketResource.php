<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AllTicketResource\Pages;
use App\Filament\Resources\AllTicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AllTicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationLabel = 'All Ticket';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'E-Ticket';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Nama Pelapor')
                ->relationship('user', 'name')
                ->searchable()
                ->required()
                ->disabled(fn ($livewire) => method_exists($livewire, 'getEditEnabledState') ? !$livewire->getEditEnabledState() : false),
            Forms\Components\TextInput::make('nomor_tiket')
                ->label('Nomor Tiket')
                ->disabled(),
            Forms\Components\TextInput::make('tipe')
                ->label('Tipe')
                ->required()
                ->disabled(fn ($livewire) => method_exists($livewire, 'getEditEnabledState') ? !$livewire->getEditEnabledState() : false),
            Forms\Components\TextInput::make('kategori')
                ->label('Kategori')
                ->required()
                ->disabled(fn ($livewire) => method_exists($livewire, 'getEditEnabledState') ? !$livewire->getEditEnabledState() : false),
            Forms\Components\Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->required()
                ->disabled(fn ($livewire) => method_exists($livewire, 'getEditEnabledState') ? !$livewire->getEditEnabledState() : false),
            Forms\Components\FileUpload::make('lampiran')
                ->label('Lampiran')
                ->directory('lampiran-tiket')
                ->preserveFilenames()
                ->disabled(fn ($livewire) => method_exists($livewire, 'getEditEnabledState') ? !$livewire->getEditEnabledState() : false),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'Ticket Dibuat' => 'Ticket Dibuat',
                    'Ticket Diterima' => 'Ticket Diterima',
                    'Dalam Proses' => 'Dalam Proses',
                    'Selesai' => 'Selesai',
                    'Ditutup' => 'Ditutup',
                ])
                ->required()
                ->disabled(fn ($livewire) => method_exists($livewire, 'getEditEnabledState') ? !$livewire->getEditEnabledState() : false),
            Forms\Components\Select::make('divisions')
                ->label('Divisi')
                ->multiple()
                ->relationship('divisions', 'name')
                ->required(false)
                ->disabled(fn ($livewire) => method_exists($livewire, 'getEditEnabledState') ? !$livewire->getEditEnabledState() : false),
            Forms\Components\TextInput::make('id')
                ->label('ID Ticket')
                ->disabled()
                ->dehydrated(false),
            Forms\Components\Select::make('application_id')
                ->label('Aplikasi (Opsional)')
                ->options(\App\Models\Application::pluck('name', 'id')->toArray())
                ->searchable()
                ->nullable()
                ->disabled(fn ($livewire) => method_exists($livewire, 'getEditEnabledState') ? !$livewire->getEditEnabledState() : false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if (!$user) return $query->whereRaw('0=1');

                // Cek apakah user melakukan sort manual
                $sort = request('tableSortColumn');
                if ($user->can('assign ticket')) {
                    if (!$sort) {
                        // Tidak ada sort manual, urutkan status Selesai di bawah
                        return $query->orderByRaw("CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END, created_at DESC");
                    }
                    // Jika ada sort manual, biarkan Filament handle
                    return $query;
                }
                // Hanya lihat ticket yang divisinya di-assign ke user
                $q = $query->whereHas('divisions', function ($q) use ($user) {
                    $q->whereIn('divisions.id', $user->divisions->pluck('id'));
                });
                if (!$sort) {
                    return $q->orderByRaw("CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END, created_at DESC");
                }
                return $q;
            })
            ->columns([
                Tables\Columns\TextColumn::make('nomor_tiket')->label('Nomor Tiket')->sortable(),
                Tables\Columns\TextColumn::make('kategori')->label('Kategori')->sortable(),
                Tables\Columns\TextColumn::make('tipe')->label('Tipe')->sortable(),
                Tables\Columns\BadgeColumn::make('kategori_urgensi')
                    ->label('Urgensi')
                    ->getStateUsing(fn($record) => optional(\App\Models\TicketCategory::where('name', $record->kategori)->first())->urgensi)
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ]),
                Tables\Columns\BadgeColumn::make('sla_remaining')
                    ->label('Sisa Waktu SLA')
                    ->getStateUsing(fn($record) => $record->sla_remaining)
                    ->colors([
                        'danger' => fn($record) => \App\Models\TicketCategory::where('name', $record->kategori)->first()?->sla_hours && (now()->gt($record->created_at->copy()->addHours(\App\Models\TicketCategory::where('name', $record->kategori)->first()->sla_hours)) || now()->diffInMinutes($record->created_at->copy()->addHours(\App\Models\TicketCategory::where('name', $record->kategori)->first()->sla_hours), false) < 60),
                        'warning' => fn($record) => \App\Models\TicketCategory::where('name', $record->kategori)->first()?->sla_hours && now()->diffInMinutes($record->created_at->copy()->addHours(\App\Models\TicketCategory::where('name', $record->kategori)->first()->sla_hours), false) < (\App\Models\TicketCategory::where('name', $record->kategori)->first()->sla_hours * 60 * 0.25) && now()->diffInMinutes($record->created_at->copy()->addHours(\App\Models\TicketCategory::where('name', $record->kategori)->first()->sla_hours), false) >= 60,
                        'success' => fn($record) => \App\Models\TicketCategory::where('name', $record->kategori)->first()?->sla_hours && now()->diffInMinutes($record->created_at->copy()->addHours(\App\Models\TicketCategory::where('name', $record->kategori)->first()->sla_hours), false) >= (\App\Models\TicketCategory::where('name', $record->kategori)->first()->sla_hours * 60 * 0.25),
                    ])
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'success' => 'Ticket Diterima',
                        'primary' => 'Ticket Dibuat',
                        'info' => 'Dalam Review',
                        'warning' => 'Dalam Proses',
                        'secondary' => 'Pending',
                        'danger' => 'Ditutup',
                        'blue' => 'Dalam Proses',
                        'green' => 'Selesai',
                        'red' => 'Ditutup',
                        'yellow' => 'Pending',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        $map = [
                            'in progress' => 'Dalam Proses',
                            'dalam proses' => 'Dalam Proses',
                            'selesai' => 'Selesai',
                            'ditutup' => 'Ditutup',
                            'ticket dibuat' => 'Ticket Dibuat',
                            'ticket diterima' => 'Ticket Diterima',
                        ];
                        $stateKey = strtolower(trim($state));
                        $label = $map[$stateKey] ?? $state;
                        if ($stateKey === 'ditutup') {
                            return $label . ' <span class="text-xs text-red-500">(dihapus otomatis setelah 30 hari)</span>';
                        }
                        return $label;
                    })
                    ->html(),
                Tables\Columns\BadgeColumn::make('assigned_divisions')
                    ->label('Assigned To')
                    ->getStateUsing(fn($record) => $record->divisions->isNotEmpty() ? $record->divisions->pluck('name')->join(', ') : 'None')
                    ->colors([
                        'gray' => fn($state) => $state === 'None',
                        'primary' => fn($state) => $state !== 'None',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Submit')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('ubah_status')
                    ->label('Ubah Status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Ticket Dibuat' => 'Ticket Dibuat',
                                'Ticket Diterima' => 'Ticket Diterima',
                                'Dalam Proses' => 'Dalam Proses',
                                'Selesai' => 'Selesai',
                                'Ditutup' => 'Ditutup',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, $data) {
                        $record->status = $data['status'];
                        $record->save();
                    })
                    ->color('success')
                    ->button()
                    ->hidden(function ($record) {
                        $user = auth()->user();
                        if ($record->divisions->isEmpty()) return false;
                        if (!$user) return true;
                        return !$user->divisions()->whereIn('divisions.id', $record->divisions->pluck('id'))->exists();
                    }),
                Tables\Actions\EditAction::make()
                    ->label('See Detail')
                    ->color('info')
                    ->extraAttributes([
                        'class' => 'rounded px-4 py-2 font-bold shadow bg-blue-100 hover:bg-blue-200 text-blue-700 transition-colors',
                    ]),
                Tables\Actions\Action::make('assign_to')
                    ->label('Assign To')
                    ->form([
                        Forms\Components\Select::make('divisions')
                            ->label('Divisi')
                            ->multiple()
                            ->options(fn() => \App\Models\Division::pluck('name', 'id')->toArray())
                            ->required(),
                    ])
                    ->action(function ($record, $data) {
                        $record->divisions()->sync($data['divisions']);
                        // Logging ke TicketLog
                        $divisions = \App\Models\Division::whereIn('id', $data['divisions'])->pluck('name')->toArray();
                        \App\Models\TicketLog::create([
                            'ticket_id' => $record->id,
                            'user_id' => auth()->id(),
                            'action' => 'assigned',
                            'description' => 'Ticket di-assign ke divisi: ' . implode(', ', $divisions),
                        ]);
                    })
                    ->color('primary')
                    ->extraAttributes([
                        'class' => 'rounded px-4 py-2 font-bold shadow bg-green-100 hover:bg-green-200 text-green-700 transition-colors',
                    ])
                    ->visible(fn() => auth()->user()->can('assign ticket')),
            ])
            ->bulkActions([])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('range')
                    ->label('Rentang Waktu')
                    ->options([
                        '30' => '30 Hari Terakhir',
                        '15' => '15 Hari Terakhir',
                        '60' => '60 Hari Terakhir',
                        '90' => '90 Hari Terakhir',
                    ])
                    ->default('30')
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (in_array($value, ['15', '30', '60', '90'])) {
                            $query->where('created_at', '>=', now()->subDays((int)$value));
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\TicketLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAllTickets::route('/'),
            'edit' => Pages\EditAllTicket::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        $permissions = $user->getAllPermissions();
        foreach ($permissions as $permission) {
            $resources = $permission->resource ?? [];
            if (is_string($resources)) $resources = [$resources];
            if (in_array('AllTicket', $resources)) {
                return true;
            }
        }
        return false;
    }
}
