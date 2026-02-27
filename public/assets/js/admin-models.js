// Admin Models Management
const PAGE_LOCALE = document.documentElement.lang || 'en';
let currentPage = 1;
let currentModel = null;

document.addEventListener('DOMContentLoaded', function() {
    if (!UserManager.isAdminOrAbove()) {
        toastr.error(PAGE_LOCALE === 'fr' ? 'Accès refusé' : 'Access denied');
        window.location.href = `/${PAGE_LOCALE}/dashboard`;
        return;
    }
    
    loadModels();
    
    document.getElementById('image-upload')?.addEventListener('change', handleImageUpload);
});

async function loadModels(page = 1) {
    const token = UserManager.getToken();
    currentPage = page;
    
    const search = document.getElementById('search').value;
    const typeFilter = document.getElementById('type-filter').value;
    const activeFilter = document.getElementById('active-filter').value;
    
    let url = `${APP_CONFIG.API.BASE_URL}/v1/admin/models?page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (typeFilter) url += `&device_type=${typeFilter}`;
    if (activeFilter !== '') url += `&is_active=${activeFilter}`;
    
    try {
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            displayModels(data.models);
        } else {
            toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Erreur de chargement des modèles' : 'Error loading models'));
        }
    } catch (error) {
        console.error('Error loading models:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur de chargement des modèles' : 'Error loading models');
    } finally {
        document.getElementById('models-loading').style.display = 'none';
    }
}

function displayModels(modelsData) {
    const modelsList = document.getElementById('models-list');
    const models = modelsData.data || [];
    
    if (models.length === 0) {
        modelsList.innerHTML = `
            <div class="card">
                <div class="card-body text-center py-5">
                    <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
                    <p class="mt-2">${PAGE_LOCALE === 'fr' ? 'Aucun modèle trouvé' : 'No models found'}</p>
                </div>
            </div>
        `;
        feather.replace();
        return;
    }
    
    let html = '<div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover">';
    html += '<thead><tr>';
    html += `<th>${PAGE_LOCALE === 'fr' ? 'Image' : 'Image'}</th>`;
    html += `<th>${PAGE_LOCALE === 'fr' ? 'Nom' : 'Name'}</th>`;
    html += `<th>${PAGE_LOCALE === 'fr' ? 'Type' : 'Type'}</th>`;
    html += `<th>${PAGE_LOCALE === 'fr' ? 'Prix' : 'Price'}</th>`;
    html += `<th>${PAGE_LOCALE === 'fr' ? 'Stock' : 'Stock'}</th>`;
    html += `<th>${PAGE_LOCALE === 'fr' ? 'Statut' : 'Status'}</th>`;
    html += `<th>${PAGE_LOCALE === 'fr' ? 'Actions' : 'Actions'}</th>`;
    html += '</tr></thead><tbody>';
    
    models.forEach(model => {
        const imageUrl = model.primary_image || '/app-assets/images/placeholder.png';
        const stockQty = model.available_quantity || 0;
        const stockBadge = stockQty > 0 
            ? `<span class="badge badge-success">${stockQty}</span>`
            : `<span class="badge badge-danger">${PAGE_LOCALE === 'fr' ? 'Épuisé' : 'Out of Stock'}</span>`;
        
        html += '<tr>';
        html += `<td><img src="${imageUrl}" alt="${model.name}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"></td>`;
        html += `<td><strong>${model.name}</strong><br><small class="text-muted">${model.slug}</small></td>`;
        html += `<td><span class="badge badge-info">${model.device_type || 'N/A'}</span></td>`;
        html += `<td><strong>$${parseFloat(model.price).toFixed(2)}</strong></td>`;
        html += `<td>${stockBadge}</td>`;
        html += `<td>`;
        html += `<div class="custom-control custom-switch">`;
        html += `<input type="checkbox" class="custom-control-input" id="active-${model.id}" ${model.is_active ? 'checked' : ''} onchange="toggleActive(${model.id})">`;
        html += `<label class="custom-control-label" for="active-${model.id}"></label>`;
        html += `</div></td>`;
        html += `<td>`;
        html += `<button class="btn btn-sm btn-primary" onclick="showModelModal(${model.id})" title="${PAGE_LOCALE === 'fr' ? 'Modifier' : 'Edit'}">`;
        html += `<i data-feather="edit-2" style="width: 14px; height: 14px;"></i></button> `;
        html += `<button class="btn btn-sm btn-danger" onclick="deleteModel(${model.id})" title="${PAGE_LOCALE === 'fr' ? 'Supprimer' : 'Delete'}">`;
        html += `<i data-feather="trash-2" style="width: 14px; height: 14px;"></i></button>`;
        html += `</td>`;
        html += '</tr>';
    });
    
    html += '</tbody></table></div></div></div>';
    
    if (modelsData.last_page > 1) {
        html += '<div class="card"><div class="card-body"><nav><ul class="pagination justify-content-center">';
        for (let i = 1; i <= modelsData.last_page; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">`;
            html += `<a class="page-link" href="javascript:void(0)" onclick="loadModels(${i})">${i}</a></li>`;
        }
        html += '</ul></nav></div></div>';
    }
    
    modelsList.innerHTML = html;
    feather.replace();
}

async function showModelModal(modelId = null) {
    currentModel = modelId;
    const isEdit = modelId !== null;
    
    document.getElementById('modal-title').textContent = isEdit 
        ? (PAGE_LOCALE === 'fr' ? 'Modifier le Modèle' : 'Edit Model')
        : (PAGE_LOCALE === 'fr' ? 'Ajouter un Modèle' : 'Add New Model');
    
    document.getElementById('model-form').reset();
    document.getElementById('model-id').value = modelId || '';
    document.getElementById('model-active').checked = true;
    document.getElementById('sort-order').value = 0;
    document.getElementById('edit-images-section').style.display = 'none';
    
    if (isEdit) {
        const token = UserManager.getToken();
        try {
            const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.model) {
                const model = data.model;
                document.getElementById('model-name').value = model.name;
                document.getElementById('model-type').value = model.device_type || '';
                document.getElementById('model-price').value = model.price;
                document.getElementById('description-en').value = model.description_en;
                document.getElementById('description-fr').value = model.description_fr;
                document.getElementById('model-active').checked = model.is_active;
                document.getElementById('sort-order').value = model.sort_order || 0;
                
                document.getElementById('edit-images-section').style.display = 'block';
                displayModelImages(model.images || []);
            }
        } catch (error) {
            console.error('Error loading model:', error);
            toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur de chargement du modèle' : 'Error loading model');
        }
    }
    
    $('#model-modal').modal('show');
}

function displayModelImages(images) {
    const imagesList = document.getElementById('images-list');
    
    if (!images || images.length === 0) {
        imagesList.innerHTML = `<div class="col-12"><p class="text-muted">${PAGE_LOCALE === 'fr' ? 'Aucune image' : 'No images'}</p></div>`;
        return;
    }
    
    let html = '';
    images.forEach(image => {
        html += `<div class="col-md-3 mb-3">`;
        html += `<div class="card">`;
        html += `<img src="${image.image_url}" class="card-img-top" alt="Product Image" style="height: 150px; object-fit: cover;">`;
        html += `<div class="card-body p-2">`;
        if (image.is_primary) {
            html += `<span class="badge badge-primary badge-sm">${PAGE_LOCALE === 'fr' ? 'Principal' : 'Primary'}</span>`;
        } else {
            html += `<button class="btn btn-sm btn-outline-primary btn-block" onclick="setPrimaryImage(${currentModel}, ${image.id})">`;
            html += `${PAGE_LOCALE === 'fr' ? 'Définir comme principal' : 'Set as Primary'}</button>`;
        }
        html += `<button class="btn btn-sm btn-danger btn-block mt-1" onclick="deleteImage(${currentModel}, ${image.id})">`;
        html += `<i data-feather="trash-2" style="width: 12px; height: 12px;"></i> ${PAGE_LOCALE === 'fr' ? 'Supprimer' : 'Delete'}</button>`;
        html += `</div></div></div>`;
    });
    
    imagesList.innerHTML = html;
    feather.replace();
}

async function saveModel() {
    const modelId = document.getElementById('model-id').value;
    const isEdit = modelId !== '';
    
    const formData = {
        name: document.getElementById('model-name').value,
        device_type: document.getElementById('model-type').value,
        price: document.getElementById('model-price').value,
        description_en: document.getElementById('description-en').value,
        description_fr: document.getElementById('description-fr').value,
        is_active: document.getElementById('model-active').checked,
        sort_order: parseInt(document.getElementById('sort-order').value) || 0,
    };
    
    if (!formData.name || !formData.device_type || !formData.price || !formData.description_en || !formData.description_fr) {
        toastr.error(PAGE_LOCALE === 'fr' ? 'Veuillez remplir tous les champs obligatoires' : 'Please fill all required fields');
        return;
    }
    
    const token = UserManager.getToken();
    const url = isEdit 
        ? `${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}`
        : `${APP_CONFIG.API.BASE_URL}/v1/admin/models`;
    
    try {
        const response = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(data.message || (PAGE_LOCALE === 'fr' ? 'Modèle enregistré avec succès' : 'Model saved successfully'));
            $('#model-modal').modal('hide');
            loadModels(currentPage);
        } else {
            if (data.errors) {
                Object.values(data.errors).forEach(err => {
                    toastr.error(Array.isArray(err) ? err[0] : err);
                });
            } else {
                toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Erreur d\'enregistrement' : 'Error saving model'));
            }
        }
    } catch (error) {
        console.error('Error saving model:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur d\'enregistrement' : 'Error saving model');
    }
}

async function deleteModel(modelId) {
    const confirmMsg = PAGE_LOCALE === 'fr' 
        ? 'Êtes-vous sûr de vouloir supprimer ce modèle? Cette action ne peut pas être annulée.'
        : 'Are you sure you want to delete this model? This action cannot be undone.';
    
    if (!confirm(confirmMsg)) return;
    
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(data.message || (PAGE_LOCALE === 'fr' ? 'Modèle supprimé' : 'Model deleted'));
            loadModels(currentPage);
        } else {
            toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Erreur de suppression' : 'Error deleting model'));
        }
    } catch (error) {
        console.error('Error deleting model:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur de suppression' : 'Error deleting model');
    }
}

async function toggleActive(modelId) {
    const checkbox = document.getElementById(`active-${modelId}`);
    const isActive = checkbox.checked;
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: isActive })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(PAGE_LOCALE === 'fr' ? 'Statut mis à jour' : 'Status updated');
        } else {
            checkbox.checked = !isActive;
            toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Erreur de mise à jour' : 'Error updating status'));
        }
    } catch (error) {
        console.error('Error toggling active:', error);
        checkbox.checked = !isActive;
        toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur de mise à jour' : 'Error updating status');
    }
}

async function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (file.size > 2048 * 1024) {
        toastr.error(PAGE_LOCALE === 'fr' ? 'La taille de l\'image doit être inférieure à 2MB' : 'Image size must be less than 2MB');
        event.target.value = '';
        return;
    }
    
    const modelId = document.getElementById('model-id').value;
    if (!modelId) {
        toastr.error(PAGE_LOCALE === 'fr' ? 'Veuillez d\'abord enregistrer le modèle' : 'Please save the model first');
        event.target.value = '';
        return;
    }
    
    const token = UserManager.getToken();
    const formData = new FormData();
    formData.append('image', file);
    formData.append('is_primary', false);
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}/images`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(data.message || (PAGE_LOCALE === 'fr' ? 'Image téléchargée' : 'Image uploaded'));
            event.target.value = '';
            
            const modelResponse = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const modelData = await modelResponse.json();
            if (modelResponse.ok && modelData.model) {
                displayModelImages(modelData.model.images || []);
            }
        } else {
            if (data.errors) {
                Object.values(data.errors).forEach(err => {
                    toastr.error(Array.isArray(err) ? err[0] : err);
                });
            } else {
                toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Erreur de téléchargement' : 'Error uploading image'));
            }
        }
    } catch (error) {
        console.error('Error uploading image:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur de téléchargement' : 'Error uploading image');
    }
}

async function deleteImage(modelId, imageId) {
    const confirmMsg = PAGE_LOCALE === 'fr' 
        ? 'Supprimer cette image?'
        : 'Delete this image?';
    
    if (!confirm(confirmMsg)) return;
    
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(data.message || (PAGE_LOCALE === 'fr' ? 'Image supprimée' : 'Image deleted'));
            
            const modelResponse = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const modelData = await modelResponse.json();
            if (modelResponse.ok && modelData.model) {
                displayModelImages(modelData.model.images || []);
            }
        } else {
            toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Erreur de suppression' : 'Error deleting image'));
        }
    } catch (error) {
        console.error('Error deleting image:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur de suppression' : 'Error deleting image');
    }
}

async function setPrimaryImage(modelId, imageId) {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}/images/${imageId}/primary`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            toastr.success(data.message || (PAGE_LOCALE === 'fr' ? 'Image principale définie' : 'Primary image set'));
            
            const modelResponse = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/admin/models/${modelId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const modelData = await modelResponse.json();
            if (modelResponse.ok && modelData.model) {
                displayModelImages(modelData.model.images || []);
            }
        } else {
            toastr.error(data.message || (PAGE_LOCALE === 'fr' ? 'Erreur de mise à jour' : 'Error updating image'));
        }
    } catch (error) {
        console.error('Error setting primary image:', error);
        toastr.error(PAGE_LOCALE === 'fr' ? 'Erreur de mise à jour' : 'Error updating image');
    }
}
