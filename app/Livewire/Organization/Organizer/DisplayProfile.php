<?php

namespace App\Livewire\Organization\Organizer;

use App\Models\Organizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DisplayProfile extends Component
{
    use WithFileUploads;

    public Organizer $organizer;

    public string $nom;
    public string $prenom;
    public string $email;
    public string $telephone;
    public string $pays;
    public string $ville;

    public ?string $photoProfilUrl = null;
    public ?string $pieceIdentiteRectoUrl = null;
    public ?string $pieceIdentiteVersoUrl = null;


    public $photoProfil;
    public $pieceIdentiteRecto;
    public $pieceIdentiteVerso;

    public ?string $photoProfilPreview = null;
    public ?string $pieceIdentiteRectoPreview = null;
    public ?string $pieceIdentiteVersoPreview = null;

    public bool $showPieceIdentiteRecto = true;

    public string $matricule;
    public ?string $passwordChangedAt = null;
    public ?bool $hasPasscode = null;
    public ?string $passcodeResetDate = null;

    public ?string $message = null;
    public bool $isSuccess = false;


    public function mount(Organizer $organizer): void
    {
        $this->organizer = $organizer;
        $this->fillFromOrganizer();
    }

    private function fillFromOrganizer(): void
    {
        $this->nom = $this->organizer->nom;
        $this->prenom = $this->organizer->prenom;
        $this->email = $this->organizer->email;
        $this->telephone = $this->organizer->telephone;
        $this->pays = $this->organizer->pays;
        $this->ville = $this->organizer->ville;

        $this->matricule = $this->organizer->matricule;
        $this->passwordChangedAt = $this->organizer->password_changed_at ? $this->organizer->password_changed_at->format('d/m/Y H:i') : 'Jamais';
        $this->hasPasscode = !empty($this->organizer->passcode);
        $this->passcodeResetDate = $this->organizer->passcode_reset_date ? $this->organizer->passcode_reset_date->format('d/m/Y H:i') : 'Jamais';


        $this->photoProfilUrl = $this->organizer->photoProfil ? asset('storage/' . $this->organizer->photoProfil) : null;
        $this->pieceIdentiteRectoUrl = $this->organizer->pieceIdentiteRecto ? asset('storage/' . $this->organizer->pieceIdentiteRecto) : null;
        $this->pieceIdentiteVersoUrl = $this->organizer->pieceIdentiteVerso ? asset('storage/' . $this->organizer->pieceIdentiteVerso) : null;


        $this->photoProfil = null;
        $this->pieceIdentiteRecto = null;
        $this->pieceIdentiteVerso = null;
        $this->photoProfilPreview = null;
        $this->pieceIdentiteRectoPreview = null;
        $this->pieceIdentiteVersoPreview = null;
    }


    protected function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:organizers,email,' . $this->organizer->id],
            'telephone' => ['required', 'string', 'max:20'],
            'pays' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
            'photoProfil' => [
                'nullable',
                \Illuminate\Validation\Rule::when($this->photoProfil instanceof TemporaryUploadedFile, ['image', 'max:1024'])
            ],
            'pieceIdentiteRecto' => [
                'nullable',
                \Illuminate\Validation\Rule::when($this->pieceIdentiteRecto instanceof TemporaryUploadedFile, ['image', 'max:2048'])
            ],
            'pieceIdentiteVerso' => [
                'nullable',
                \Illuminate\Validation\Rule::when($this->pieceIdentiteVerso instanceof TemporaryUploadedFile, ['image', 'max:2048'])
            ],
        ];
    }


    public function updatedPhotoProfil(): void
    {
        if ($this->photoProfil instanceof TemporaryUploadedFile) {
            $this->photoProfilPreview = $this->photoProfil->temporaryUrl();
        } else {

            $this->photoProfilPreview = null;
        }
    }


    public function updatedPieceIdentiteRecto(): void
    {
        if ($this->pieceIdentiteRecto instanceof TemporaryUploadedFile) {
            $this->pieceIdentiteRectoPreview = $this->pieceIdentiteRecto->temporaryUrl();
        } else {
            $this->pieceIdentiteRectoPreview = null;
        }
    }


    public function updatedPieceIdentiteVerso(): void
    {
        if ($this->pieceIdentiteVerso instanceof TemporaryUploadedFile) {
            $this->pieceIdentiteVersoPreview = $this->pieceIdentiteVerso->temporaryUrl();
        } else {
            $this->pieceIdentiteVersoPreview = null;
        }
    }


    public function togglePieceIdentite(): void
    {
        $this->showPieceIdentiteRecto = !$this->showPieceIdentiteRecto;
    }

    /**
     * Processes a base64 encoded image string captured from a camera.
     * Stores it directly to public disk and sets the property to the path.
     * @param string $propertyName The Livewire property to assign the file path to (e.g., 'photoProfil').
     * @param string $imageData The base64 encoded image data (e.g., "data:image/jpeg;base64,...").
     */
    public function processCameraImage(string $propertyName, string $imageData): void
    {
        try {
            if (!str_starts_with($imageData, 'data:image/')) {
                throw new \Exception("Format d'image invalide.");
            }

            list($mimeType, $data) = explode(';', $imageData);
            list(, $data) = explode(',', $data);
            $mimeType = explode(':', $mimeType)[1];

            $decodedData = base64_decode($data);

            if ($decodedData === false) {
                throw new \Exception("Impossible de décoder les données de l'image.");
            }

            $extension = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                default => 'bin',
            };

            $directory = match ($propertyName) {
                'photoProfil' => 'organizer_profiles',
                'pieceIdentiteRecto', 'pieceIdentiteVerso' => 'organizer_id_cards',
                default => 'uploads',
            };

            $fileName = uniqid() . '.' . $extension;
            $path = $directory . '/' . $fileName;

            $currentOldPath = $this->organizer->{$propertyName};
            if ($currentOldPath && Storage::disk('public')->exists($currentOldPath)) {
                Storage::disk('public')->delete($currentOldPath);
            }

            Storage::disk('public')->put($path, $decodedData);

            $this->{$propertyName} = $path;

            $this->{$propertyName . 'Preview'} = asset('storage/' . $path);

            $this->message = "Image capturée avec succès.";
            $this->isSuccess = true;

        } catch (\Exception $e) {
            $this->message = "Erreur lors de la capture de l'image : " . $e->getMessage();
            $this->isSuccess = false;
            Log::error("Error processing camera image: " . $e->getMessage());
        }
    }


    public function saveProfile(): void
    {
        if ($this->organizer->profile_verification_status === 'accepted') {
            $this->message = "Votre profil est vérifié. Vous ne pouvez pas modifier ces informations.";
            $this->isSuccess = false;
            return;
        }


        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }


        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                $this->organizer->update([
                    'nom' => $this->nom,
                    'prenom' => $this->prenom,
                    'email' => $this->email,
                    'telephone' => $this->telephone,
                    'pays' => $this->pays,
                    'ville' => $this->ville,
                ]);

                $propertiesToHandle = ['photoProfil', 'pieceIdentiteRecto', 'pieceIdentiteVerso'];
                $organizerUpdated = false;

                foreach ($propertiesToHandle as $prop) {
                    if ($this->{$prop} instanceof TemporaryUploadedFile) {
                        if ($this->organizer->{$prop} && Storage::disk('public')->exists($this->organizer->{$prop})) {
                            Storage::disk('public')->delete($this->organizer->{$prop});
                        }
                        $directory = match ($prop) {
                            'photoProfil' => 'organizer_profiles',
                            'pieceIdentiteRecto', 'pieceIdentiteVerso' => 'organizer_id_cards',
                            default => 'uploads',
                        };
                        $this->organizer->{$prop} = $this->{$prop}->store($directory, 'public');
                        $organizerUpdated = true;
                    }
                    elseif (is_string($this->{$prop})) {

                        $this->organizer->{$prop} = $this->{$prop};
                        $organizerUpdated = true;
                    }
                }

                if ($this->organizer->isDirty() || $organizerUpdated) {
                    $this->organizer->save();
                }
            });

            $this->fillFromOrganizer();

            $this->message = "Profil mis à jour avec succès.";
            $this->isSuccess = true;

        } catch (\Exception $e) {
            $this->message = "Une erreur est survenue lors de la mise à jour du profil: " . $e->getMessage();
            $this->isSuccess = false;
            Log::error("Error saving profile: " . $e->getMessage());
        }
    }


    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('livewire.organization.organizer.display-profile', [
            'organizer' => $this->organizer,
            'matricule' => $this->matricule,
            'passwordChangedAt' => $this->passwordChangedAt,
            'hasPasscode' => $this->hasPasscode,
            'passcodeResetDate' => $this->passcodeResetDate,
        ]);
    }
}
