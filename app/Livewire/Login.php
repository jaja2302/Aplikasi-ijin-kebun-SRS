<?php

namespace App\Livewire;

use App\Models\User;
use IntlDateFormatter;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Http\Request;
use App\Models\FormSuratIzin;
use App\Models\ListDepartement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\RateLimiter;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Filament\Forms\Concerns\InteractsWithForms;

class Login extends Component implements HasForms
{
    use InteractsWithForms;

    public $email, $password, $remember;
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email Address')
                    ->placeholder('Email')
                    ->extraInputAttributes(['class' => 'py-2.5'])
                    ->required(),
                TextInput::make('password')
                    ->placeholder('Password')
                    ->extraInputAttributes(['class' => 'py-2.5'])
                    ->password()
                    ->revealable()
                    ->required()
            ])
            ->statePath('data');
    }

    public function authUser()
    {
        $data = $this->form->getState();
        $this->email = strtolower($data['email']);
        $this->password = $data['password'];

        $throttleKey = strtolower($this->email) . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            notifyValidation(__('auth.throttle', [
                'seconds' => RateLimiter::availableIn($throttleKey)
            ]), '', 'danger', '');
            return null;
        }

        $user = User::where('email', $this->email)
            ->where('password', $this->password)
            ->first();
        if (!$user) {
            RateLimiter::hit($throttleKey);
            notifyValidation(__('auth.failed'), '', 'danger', '');
            return null;
        }

        Auth::login($user, $this->remember);
        return redirect()->route('dashboard');
    }

    public function authUserValidation(Request $request)
    {
        $email = $request->query('email');
        $password = $request->query('password');

        if (isset($email) && isset($password)) {
            $user = User::where('email', $email)
                ->where('password', $password)
                ->first();

            if (!$user) {
                abort(403, 'UNAUTHORIZED ACCESS');
            }

            Auth::login($user, false);
            return redirect()->route('dashboard');
        }

        Auth::logout();
        session()->flush();
        return redirect()->route('login');
    }


    public function generatePdfIzinKebun(Request $request)
    {
        $email = $request->query('user');
        $password = $request->query('pw');
        $idForm = $request->query('id');

        if (isset($email) && isset($password) && isset($idForm)) {
            $user = User::where('email', $email)
                ->where('password', $password)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'UNAUTHORIZED ACCESS',
                    'code' => 0
                ], 200);
            } else {
                $dataQueryForm = FormSuratIzin::find($idForm)->toArray();
                $allUser = User::with('new_jabatan')->get()->keyBy('user_id')->toArray();
                $allListDepartement = ListDepartement::get()->keyBy('id')->toArray();

                $qrCodeEncrypt = '';
                if ($dataQueryForm['status'] == '4') {
                    $jsonData = array_filter(array_map(function ($key, $value) use ($allUser) {
                        if (in_array($key, ['id', 'status', 'catatan', 'status_bot', 'created_at', 'updated_at'])) {
                            return null;
                        }
                        if (in_array($key, ['tanggal_keluar', 'tanggal_kembali'])) {
                            return formattedDate($value, IntlDateFormatter::LONG);
                        }
                        if ($key == 'user_id' || $key == 'atasan_1' || $key == 'atasan_2') {
                            return $allUser[$value]['nama_lengkap'];
                        }
                        return $value;
                    }, array_keys($dataQueryForm), $dataQueryForm));
                    $jsonData['jabatan'] = $allUser[$dataQueryForm['user_id']]['new_jabatan']['nama'] ?? 'Data tidak ditemukan';
                    $jsonData['unit'] = $allListDepartement[$allUser[$dataQueryForm['user_id']]['id_departement']]['nama'] ?? 'Data tidak ditemukan';
                    $jsonData = json_encode(array_values($jsonData), JSON_FORCE_OBJECT);
                    $encryptedData = aes_encrypt($jsonData, 'CBI@2024');
                    $qrCodeEncrypt = "data:image/png;base64, " . base64_encode(QrCode::size(250)->format('png')->merge(public_path('images/icons/logo_srs.png'), 0.3, true)->generate($encryptedData));

                    $name_file = $dataQueryForm['user_id'] . date('YmdHis') . '.pdf';
                    $pdf = Pdf::loadView('components.layouts.pdf', [
                        'data' => $dataQueryForm,
                        'allUser' => $allUser,
                        'departement' => $allListDepartement[$allUser[$dataQueryForm['user_id']]['id_departement']]['nama'],
                        'name_file' => $name_file,
                        'qrCodeEncrypt' => $qrCodeEncrypt
                    ])->setPaper([0, 0, 595.28, 900]);

                    $stored = Storage::disk('public')->put('files/' . $name_file, $pdf->output());
                    if ($stored) {
                        return response()->json([
                            'status' => '200',
                            'filename' => $name_file,
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => 'Gagal generated PDF',
                            'code' => 0
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status' => 'Status surat belum disetujui',
                        'code' => 0
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'status' => 'kosong',
                'code' => 0
            ], 200);
        }
    }

    public function deletePdfIzinKebun(Request $request)
    {
        $email = $request->query('user');
        $password = $request->query('pw');
        $fileName = $request->query('filename');

        if (isset($email) && isset($password) && isset($fileName)) {
            $user = User::where('email', $email)
                ->where('password', $password)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'UNAUTHORIZED ACCESS',
                    'code' => 0
                ], 200);
            } else if (Storage::disk('public')->exists('files/' . $fileName)) {
                $deleted = Storage::disk('public')->delete('files/' . $fileName);
                if ($deleted) {
                    return response()->json([
                        'status' => 'Berhasil hapus file PDF',
                        'code' => 1
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'Gagal hapus file PDF',
                        'code' => 0
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 'File PDF tidak tersedia',
                    'code' => 0
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'kosong',
                'code' => 0
            ], 200);
        }
    }

    public function render()
    {
        return view('livewire.login')->extends('components.layouts.app')->section('content');
    }
}
