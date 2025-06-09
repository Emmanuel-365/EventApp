<?php

namespace App\Livewire\Organization\Organizer\ManageOrganizations;

use App\Enums\TypeEntreprise;
use App\Models\Organization;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Stancl\Tenancy\Database\Models\Domain;

class CreateOrganization extends Component
{
    public ?Authenticatable $organizer;
    public string $nom = '';
    public string $NIU = '';
    public string $type = '';
    public string $date_creation;
    public string $subdomain = '';

    public array $typesEntreprise = [];
    public bool $show = false;

    protected $listeners = [
        'openCreateOrganizationModal' => 'open',
    ];

    public function mount(): void
    {
        $this->organizer = Auth::guard('organizer')->user();

        $this->typesEntreprise = class_exists(TypeEntreprise::class)
            ? TypeEntreprise::cases()
            : [];

        $this->date_creation = now()->toDateString();
    }

    public function open(): void
    {
        $this->reset(['nom', 'NIU', 'type', 'subdomain']);
        $this->date_creation = now()->toDateString();
        $this->resetValidation();
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255', 'unique:organizations,nom'],
            'NIU' => ['required', 'string', 'max:20', 'unique:organizations,NIU'],
            'type' => ['required', class_exists(TypeEntreprise::class) ? new Enum(TypeEntreprise::class) : 'string'],
            'date_creation' => ['required', 'date'],
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:60',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
        ];
    }

    /**
     * Validation personnalisée après les règles de base.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $subdomain = $this->subdomain;

            $exists = DB::table('domains')
                ->join('organizations', 'domains.tenant_id', '=', 'organizations.id')
                ->where('domains.subdomain', $subdomain)
                ->where('organizations.validation_status', '!=', 'rejected')
                ->exists();

            if ($exists) {
                $validator->errors()->add('subdomain', 'Ce sous-domaine est déjà utilisé par une organisation non rejetée.');
            }
        });
    }


    public function updatedSubdomain($value): void
    {
        $this->subdomain = Str::slug($value);

        $this->resetErrorBag('subdomain');
        $this->validateOnly('subdomain');

        $exists = DB::table('domains')
            ->join('organizations', 'domains.tenant_id', '=', 'organizations.id')
            ->where('domains.subdomain', $this->subdomain)
            ->where('organizations.validation_status', '!=', 'rejected')
            ->exists();

        if ($exists) {
            $this->addError('subdomain', 'Ce sous-domaine est déjà utilisé par une organisation non rejetée.');
        }
    }

    public function updatedNom($value): void
    {
        $this->validateOnly('nom');
    }

    public function updatedNIU($value): void
    {
        $this->validateOnly('NIU');
    }

    public function createOrganization(): void
    {
        $this->validate();

        $organizer = Auth::guard('organizer')->user();

        if (!$organizer) {
            $this->addError('general', 'Vous devez être connecté en tant qu\'organisateur pour créer une organisation.');
            return;
        }

        try {
            $organization = Organization::create([
                'nom' => $this->nom,
                'NIU' => $this->NIU,
                'organizer_id' => $organizer->id,
                'type' => $this->type,
                'date_creation' => $this->date_creation,
            ]);

            $baseUrl = config('app.url');
            $parsedUrl = parse_url($baseUrl);
            $baseDomain = $parsedUrl['host'] ?? 'localhost';

            $fullDomain = $this->subdomain . '.' . $baseDomain;

            $domain = new Domain([
                'domain' => $fullDomain,
                'subdomain' => $this->subdomain,
            ]);

            $organization->domains()->save($domain);

            $this->dispatch('organizationCreatedSuccess', message: 'Organisation "' . $this->nom . '" créée avec succès. Accédez-y via: ' . $fullDomain);
            $this->close();
        } catch (\Exception $e) {
            $this->addError('general', 'Erreur lors de la création: ' . $e->getMessage());
            Log::error('Erreur création organisation: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.organization.organizer.manage-organizations.create-organization');
    }
}
