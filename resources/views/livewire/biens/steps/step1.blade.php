<div class="sidebar-body">
    <form class="row g-3 common-form">
        <div class="col-12">
            <h5 class="mb-3">Informations de Base</h5>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="type_bien">Type de Bien <span class="txt-danger">*</span></label>
            <select wire:model="type_bien" 
                    class="form-select @error('type_bien') is-invalid @enderror" 
                    id="type_bien">
                <option value="">Sélectionner un type</option>
                <option value="appartement">Appartement</option>
                <option value="maison">Maison</option>
                <option value="bureau">Bureau</option>
                <option value="magasin">Magasin</option>
                <option value="terrain">Terrain</option>
            </select>
            @error('type_bien')
                <div class="invalid-feedback d-block">
                    <i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <!-- AFFICHER LA RÉFÉRENCE EN MODE ÉDITION -->
        @if($isEdit && $bienId)
            <div class="col-md-6">
                <label class="form-label">Référence du Bien</label>
                <input type="text" 
                    value="{{ \App\Models\BienImmobilier::find($bienId)->reference }}" 
                    class="form-control-plaintext fw-bold text-primary" 
                    readonly>
                <small class="text-muted">Référence unique générée automatiquement</small>
            </div>
        @endif

        <div class="col-md-6">
            <label class="form-label" for="ville">Ville <span class="txt-danger">*</span></label>
            <input wire:model="ville" 
                   class="form-control @error('ville') is-invalid @enderror" 
                   id="ville" 
                   type="text" 
                   placeholder="Cotonou">
            @error('ville')
                <div class="invalid-feedback d-block">
                    <i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="quartier">Quartier <span class="txt-danger">*</span></label>
            <input wire:model="quartier" 
                   class="form-control @error('quartier') is-invalid @enderror" 
                   id="quartier" 
                   type="text" 
                   placeholder="Ex: Akpakpa, Fidjrossè...">
            @error('quartier')
                <div class="invalid-feedback d-block">
                    <i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="adresse">Adresse complète <span class="txt-danger">*</span></label>
            <input wire:model="adresse" 
                   class="form-control @error('adresse') is-invalid @enderror" 
                   id="adresse" 
                   type="text" 
                   placeholder="Ex: CALAVI/IITA">
            @error('adresse')
                <div class="invalid-feedback d-block">
                    <i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-12">
            <label class="form-label">Description du Bien</label>
            <textarea wire:model="description" 
                      class="form-control" 
                      rows="4" 
                      placeholder="Décrivez votre bien immobilier..."></textarea>
            <small class="text-muted">Décrivez les atouts principaux de votre bien</small>
        </div>

        <div class="col-12">
            <hr class="my-3">
            <h5 class="mb-3">Caractéristiques</h5>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="annee_construction">Année de Construction <span class="txt-danger">*</span></label>
            <input wire:model="annee_construction" 
                   class="form-control @error('annee_construction') is-invalid @enderror" 
                   id="annee_construction" 
                   type="date"
                   max="{{ date('Y-m-d') }}">
            @error('annee_construction')
                <div class="invalid-feedback d-block">
                    <i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label d-block">Équipements Communs</label>
            <div class="row g-2">
                @foreach($equipements_disponibles as $equipement)
                    <div class="col-md-4 col-6">
                        <div class="form-check">
                            <input wire:model="equipements_communs" 
                                   class="form-check-input" 
                                   type="checkbox" 
                                   value="{{ $equipement }}" 
                                   id="equip_{{ $loop->index }}">
                            <label class="form-check-label" for="equip_{{ $loop->index }}">
                                {{ $equipement }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <div></div>
            <button type="button" 
                    wire:click="nextStep" 
                    class="btn btn-success" 
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Suivant
                    <i class="bi bi-arrow-right-circle-fill ms-1"></i>
                </span>
                <span wire:loading>
                    <i class="fa-solid fa-spinner fa-spin me-2"></i>Chargement...
                </span>
            </button>
        </div>
    </form>
</div>