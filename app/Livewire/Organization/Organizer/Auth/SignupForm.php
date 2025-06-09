<?php

namespace App\Livewire\Organization\Organizer\Auth;

use App\Models\Organizer;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class SignupForm extends Component
{
    use WithFileUploads;

    public int $step = 1;

    #[Validate('required|string|max:255')]
    public string $nom = '';
    #[Validate('required|string|max:255')]
    public string $prenom = '';
    #[Validate('required|string|max:20')]
    public string $telephone = '';
    #[Validate('nullable|string|max:255')]
    public ?string $pays = null;
    #[Validate('nullable|string|max:255')]
    public ?string $ville = null;

    public  $photoProfil = null;
    public  $pieceIdentiteRecto = null;
    public  $pieceIdentiteVerso = null;

    public ?string $photoProfilPreview = null;
    public ?string $pieceIdentiteRectoPreview = null;
    public ?string $pieceIdentiteVersoPreview = null;

    public ?string $photoProfilCamera = null;
    public ?string $pieceIdentiteRectoCamera = null;
    public ?string $pieceIdentiteVersoCamera = null;

    #[Validate('required|email|unique:organizers,email')]
    public string $email = '';
    public string $otp = '';
    public string $otpCacheKey = '';
    public string $password = '';
    public string $passwordConfirmation = '';

    protected OtpService $otpService;

    protected function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'pays' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',

            'photoProfil' => 'nullable|mimes:jpeg,jpg,png|max:1024',
            'pieceIdentiteRecto' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'pieceIdentiteVerso' => 'nullable|mimes:jpeg,jpg,png|max:2048',

            'photoProfilCamera' => 'nullable|string',
            'pieceIdentiteRectoCamera' => 'nullable|string',
            'pieceIdentiteVersoCamera' => 'nullable|string',

            'email' => 'required|email|unique:organizers,email',
            'otp' => 'required|string|size:6',

            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()],
            'passwordConfirmation' => 'required|same:password',
        ];
    }

    public function boot(OtpService $otpService): void
    {
        $this->otpService = $otpService;
    }

    public function updated($name, $value): void
    {
        if (in_array($name, ['photoProfil', 'pieceIdentiteRecto', 'pieceIdentiteVerso'])) {
            if ($value instanceof TemporaryUploadedFile) {
                $previewProperty = $name . 'Preview';
                $this->{$previewProperty} = $value->temporaryUrl();
                $cameraProperty = $name . 'Camera';
                $this->{$cameraProperty} = null;
            } elseif (is_null($value)) {
                $previewProperty = $name . 'Preview';
                $this->{$previewProperty} = null;
                $cameraProperty = $name . 'Camera';
                $this->{$cameraProperty} = null;
            }
        }

        if (in_array($name, ['photoProfilCamera', 'pieceIdentiteRectoCamera', 'pieceIdentiteVersoCamera'])) {
            if ($value) {
                $previewProperty = str_replace('Camera', 'Preview', $name);
                $this->{$previewProperty} = 'data:image/jpeg;base64,' . $value;
                $fileProperty = str_replace('Camera', '', $name);
                $this->{$fileProperty} = null;
            } elseif (is_null($value)) {
                $previewProperty = str_replace('Camera', 'Preview', $name);
                $this->{$previewProperty} = null;
                $fileProperty = str_replace('Camera', '', $name);
                $this->{$fileProperty} = null;
            }
        }

        if ($name === 'email') {
            $this->resetOtpProcess();
        }

        $this->validateOnly($name);
    }

    public function processCameraImage(string $propertyName, string $imageData): void
    {
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        $this->{$propertyName} = $imageData;

        $previewProperty = str_replace('Camera', 'Preview', $propertyName);
        $this->{$previewProperty} = 'data:image/jpeg;base64,' . $imageData;

        $fileProperty = str_replace('Camera', '', $propertyName);
        $this->{$fileProperty} = null;

        $this->validateOnly($propertyName);
    }


    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'nom' => $this->rules()['nom'],
                'prenom' => $this->rules()['prenom'],
                'telephone' => $this->rules()['telephone'],
                'pays' => $this->rules()['pays'],
                'ville' => $this->rules()['ville'],
            ]);
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->validate([
                'photoProfil' => $this->rules()['photoProfil'],
                'photoProfilCamera' => $this->rules()['photoProfilCamera'],
                'pieceIdentiteRecto' => $this->rules()['pieceIdentiteRecto'],
                'pieceIdentiteRectoCamera' => $this->rules()['pieceIdentiteRectoCamera'],
                'pieceIdentiteVerso' => $this->rules()['pieceIdentiteVerso'],
                'pieceIdentiteVersoCamera' => $this->rules()['pieceIdentiteVersoCamera'],
            ]);
            $this->step = 3;
        } elseif ($this->step === 3) {
            $this->validate(['email' => $this->rules()['email']]);
            $this->sendOtp();
            $this->step = 4;
        }
    }


    public function previousStep(): void
    {
        if ($this->step === 4) {
            $this->resetOtpProcess();
            $this->step = 3;
        } else {
            $this->step = max(1, $this->step - 1);
        }
        session()->forget(['otp_sent_success', 'otp_error', 'otp_verified_success']);
    }

    /**
     * Envoie un code OTP à l'adresse email fournie.
     */
    public function sendOtp(): void
    {
        $this->validate(['email' => $this->rules()['email']]);
        $this->otpCacheKey = 'organizer_signup_otp_' . md5($this->email);

        try {
            $this->otpService->sendOtp(
                $this->email,
                $this->otpCacheKey,
                ['email' => $this->email]
            );
            session()->flash('otp_sent_success', 'Un code OTP a été envoyé à votre adresse email. Il est valide 5 minutes.');

        } catch (\Exception $e) {
            session()->flash('otp_error', 'Erreur lors de l\'envoi de l\'OTP: ' . $e->getMessage());
            Log::error('Erreur OTP: ' . $e->getMessage(), ['exception' => $e]);
        }
    }


    public function submitForm()
    {
        // This method will now be called from step 4, validating OTP and password.
        $this->validate([
            'otp' => $this->rules()['otp'],
            'password' => $this->rules()['password'],
            'passwordConfirmation' => $this->rules()['passwordConfirmation'],
        ]);

        try {
            // Verify OTP first
            $this->otpService->verifyOtp($this->otpCacheKey, $this->otp);
            session()->flash('otp_verified_success', 'Email vérifié avec succès!');

            $photoProfilPath = null;
            if ($this->photoProfil instanceof TemporaryUploadedFile) {
                $photoProfilPath = $this->photoProfil->store('organizer_photos', 'public');
            } elseif ($this->photoProfilCamera) {
                $decodedImage = base64_decode($this->photoProfilCamera);
                $fileName = 'organizer_photos/' . md5($this->nom . microtime()) . '.jpeg';
                Storage::disk('public')->put($fileName, $decodedImage);
                $photoProfilPath = $fileName;
            }

            $pieceIdentiteRectoPath = null;
            if ($this->pieceIdentiteRecto instanceof TemporaryUploadedFile) {
                $pieceIdentiteRectoPath = $this->pieceIdentiteRecto->store('organizer_identity_docs', 'public');
            } elseif ($this->pieceIdentiteRectoCamera) {
                $decodedImage = base64_decode($this->pieceIdentiteRectoCamera);
                $fileName = 'organizer_identity_docs/' . md5($this->nom . microtime() . 'recto') . '.jpeg';
                Storage::disk('public')->put($fileName, $decodedImage);
                $pieceIdentiteRectoPath = $fileName;
            }

            $pieceIdentiteVersoPath = null;
            if ($this->pieceIdentiteVerso instanceof TemporaryUploadedFile) {
                $pieceIdentiteVersoPath = $this->pieceIdentiteVerso->store('organizer_identity_docs', 'public');
            } elseif ($this->pieceIdentiteVersoCamera) {
                $decodedImage = base64_decode($this->pieceIdentiteVersoCamera);
                $fileName = 'organizer_identity_docs/' . md5($this->nom . microtime() . 'verso') . '.jpeg';
                Storage::disk('public')->put($fileName, $decodedImage);
                $pieceIdentiteVersoPath = $fileName;
            }

            // Création de l'Organisateur
            $organizer = Organizer::create([
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'telephone' => $this->telephone,
                'pays' => $this->pays,
                'ville' => $this->ville,
                'photoProfil' => $photoProfilPath,
                'pieceIdentiteRecto' => $pieceIdentiteRectoPath,
                'pieceIdentiteVerso' => $pieceIdentiteVersoPath,
                'profile_verification_status' => 'en attente',
                'password_changed_at' => now(),
            ]);

            session()->flash('success_message', 'Inscription réussie ! Votre compte organisateur a été créé. Un administrateur vérifiera votre profil.');
            $this->reset();
            $this->step = 1;

            return $this->redirect(route('organization.organizer.auth.disconnected.login'), navigate: true);

        } catch (\Exception $e) {
            // Handle OTP verification errors separately
            if ($e->getMessage() === 'Code OTP invalide ou expiré.') {
                session()->flash('otp_error', 'Code OTP invalide ou expiré.');
                Log::warning('Tentative OTP échouée pour ' . $this->email . ': ' . $e->getMessage());
            } else {
                session()->flash('error_message', 'Erreur lors de l\'inscription: ' . $e->getMessage());
                Log::error('Erreur lors de l\'inscription d\'un organisateur: ' . $e->getMessage(), ['exception' => $e]);
            }
        }
    }

    public function resetOtpProcess(): void
    {
        $this->otp = '';
        $this->otpCacheKey = '';
        session()->forget(['otp_sent_success', 'otp_error', 'otp_verified_success']);
    }


    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('livewire.organization.organizer.auth.signup-form');
    }
}
