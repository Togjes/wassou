<!-- À ajouter dans le profil de l'utilisateur ou dashboard -->
@php
    $user = Auth::user();
    $roleName = '';
    $roleColor = 'primary';
    
    if ($user->hasRole('admin')) {
        $roleName = 'Administrateur';
        $roleColor = 'danger';
    } elseif ($user->hasRole('proprietaire')) {
        $roleName = 'Propriétaire';
        $roleColor = 'success';
    } elseif ($user->hasRole('locataire')) {
        $roleName = 'Locataire';
        $roleColor = 'info';
    } elseif ($user->hasRole('gestionnaire')) {
        $roleName = 'Gestionnaire';
        $roleColor = 'warning';
    } else {
        $roleName = 'Utilisateur';
        $roleColor = 'secondary';
    }
@endphp

<div class="card border-{{ $roleColor }}">
    <div class="card-header bg-light-{{ $roleColor }}">
        <h6 class="mb-0">
            <i class="fa-solid fa-id-card me-2"></i>
            Mon Code Unique
        </h6>
    </div>
    <div class="card-body text-center">
        <div class="mb-2">
            <span class="badge bg-{{ $roleColor }} mb-2">{{ $roleName }}</span>
        </div>
        <p class="text-muted mb-2 small">Partagez ce code pour vous identifier facilement</p>
        <div class="alert alert-light border-{{ $roleColor }} mb-3">
            <h4 class="mb-0 text-{{ $roleColor }} fw-bold font-monospace">
                {{ $user->code_unique ?? 'Non généré' }}
            </h4>
        </div>
        @if($user->code_unique)
            <button type="button" 
                    class="btn btn-sm btn-outline-{{ $roleColor }}"
                    onclick="copyCodeToClipboard('{{ $user->code_unique }}')">
                <i class="fa-solid fa-copy me-2"></i>Copier le code
            </button>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function copyCodeToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Notification toast ou alert
            showNotification('Code copié !', 'Le code ' + text + ' a été copié dans le presse-papier.', 'success');
        }).catch(err => {
            console.error('Erreur lors de la copie:', err);
            alert('Impossible de copier le code. Veuillez le copier manuellement.');
        });
    }

    function showNotification(title, message, type) {
        // Si vous utilisez un système de notification (comme Toastr, SweetAlert, etc.)
        // Sinon, simple alert
        if (typeof toastr !== 'undefined') {
            toastr[type](message, title);
        } else {
            alert(title + '\n' + message);
        }
    }
</script>
@endpush