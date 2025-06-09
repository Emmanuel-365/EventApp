@extends('organization.organizer.connected-base')

@section('title', 'MES ORGANISATIONS')

@section('content')

    {{-- Conteneur principal de la page --}}
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        {{-- Bouton pour ouvrir la modale de création d'organisation --}}
        <div class="mb-6 flex justify-end">
            <button type="button"
                    @click="$dispatch('openCreateOrganizationModal')" {{-- Déclenche l'événement pour ouvrir la modale CreateOrganization --}}
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-600 transition-colors duration-200">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                Créer Organisation
            </button>
        </div>

        {{-- Le composant Livewire des organisations de l'organisateur  --}}
        @livewire('organization.organizer.manage-organizations.organizations-list')
    </div>

    {{-- Inclusion du composant Livewire CreateOrganization qui est une modale autonome --}}
    @livewire('organization.organizer.manage-organizations.create-organization')

    {{-- Inclusion du composant Livewire OrganizerOrganizationDetailsModal  --}}
    @livewire('organization.organizer.manage-organizations.organization-details-modal')

@endsection
