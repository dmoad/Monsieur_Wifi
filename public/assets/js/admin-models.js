// Admin Models Management
const t = window.APP_I18N.admin_models;
let currentPage = 1;
let currentModel = null;

document.addEventListener('DOMContentLoaded', function() {
    if (!UserManager.isAdminOrAbove()) {
        toastr.error(t.access_denied);
        window.location.href = t.dashboard_url;
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
            toastr.error(data.message || t.error_load_models);
        }
    } catch (error) {
        console.error('Error loading models:', error);
        toastr.error(t.error_load_models);
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
                    <p class="mt-2">${t.no_models}</p>
                </div>
            </div>
        `;
        feather.replace();
        return;
    }

    let html = '<div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover">';
    html += '<thead><tr>';
    html += `<th>${t.col_image}</th>`;
    html += `<th>${t.col_name}</th>`;
    html += `<th>${t.col_type}</th>`;
    html += `<th>${t.col_price}</th>`;
    html += `<th>${t.col_stock}</th>`;
    html += `<th>${t.col_status}</th>`;
    html += `<th>${t.col_actions}</th>`;
    html += '</tr></thead><tbody>';

    models.forEach(model => {
        const imageUrl = model.primary_image || '/app-assets/images/placeholder.png';
        const stockQty = model.available_quantity || 0;
        const stockBadge = stockQty > 0
            ? `<span class="badge badge-success">${stockQty}</span>`
            : `<span class="badge badge-danger">${t.badge_out_of_stock}</span>`;

        html += '<tr>';
        html += `<td><img src="${imageUrl}" alt="${model.name}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"></td>`;
        html += `<td><strong>${model.name}</strong><br><small class="text-muted">${model.slug}</small></td>`;
        html += `<td><span class="badge badge-info">${model.device_type || 'N/A'}</span></td>`;
        html += `<td><strong>€${parseFloat(model.price).toFixed(2)}</strong></td>`;
        html += `<td>${stockBadge}</td>`;
        html += `<td>`;
        html += `<div class="custom-control custom-switch">`;
        html += `<input type="checkbox" class="custom-control-input" id="active-${model.id}" ${model.is_active ? 'checked' : ''} onchange="toggleActive(${model.id})">`;
        html += `<label class="custom-control-label" for="active-${model.id}"></label>`;
        html += `</div></td>`;
        html += `<td>`;
        html += `<button class="btn btn-sm btn-primary" onclick="showModelModal(${model.id})" title="${t.btn_edit}">`;
        html += `<i data-feather="edit-2" style="width: 14px; height: 14px;"></i></button> `;
        html += `<button class="btn btn-sm btn-danger" onclick="deleteModel(${model.id})" title="${t.btn_delete}">`;
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

    document.getElementById('modal-title').textContent = isEdit ? t.modal_edit_title : t.modal_add_title;

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
            toastr.error(t.error_load_model);
        }
    }

    $('#model-modal').modal('show');
}

function displayModelImages(images) {
    const imagesList = document.getElementById('images-list');

    if (!images || images.length === 0) {
        imagesList.innerHTML = `<div class="col-12"><p class="text-muted">${t.no_images}</p></div>`;
        return;
    }

    let html = '';
    images.forEach(image => {
        html += `<div class="col-md-3 mb-3">`;
        html += `<div class="card">`;
        html += `<img src="${image.image_url}" class="card-img-top" alt="Product Image" style="height: 150px; object-fit: cover;">`;
        html += `<div class="card-body p-2">`;
        if (image.is_primary) {
            html += `<span class="badge badge-primary badge-sm">${t.badge_primary}</span>`;
        } else {
            html += `<button class="btn btn-sm btn-outline-primary btn-block" onclick="setPrimaryImage(${currentModel}, ${image.id})">`;
            html += `${t.btn_set_primary}</button>`;
        }
        html += `<button class="btn btn-sm btn-danger btn-block mt-1" onclick="deleteImage(${currentModel}, ${image.id})">`;
        html += `<i data-feather="trash-2" style="width: 12px; height: 12px;"></i> ${t.btn_delete}</button>`;
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
        toastr.error(t.fill_required);
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
            toastr.success(data.message || t.saved);
            $('#model-modal').modal('hide');
            loadModels(currentPage);
        } else {
            if (data.errors) {
                Object.values(data.errors).forEach(err => {
                    toastr.error(Array.isArray(err) ? err[0] : err);
                });
            } else {
                toastr.error(data.message || t.error_save);
            }
        }
    } catch (error) {
        console.error('Error saving model:', error);
        toastr.error(t.error_save);
    }
}

async function deleteModel(modelId) {
    if (!confirm(t.confirm_delete_model)) return;

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
            toastr.success(data.message || t.deleted);
            loadModels(currentPage);
        } else {
            toastr.error(data.message || t.error_delete_model);
        }
    } catch (error) {
        console.error('Error deleting model:', error);
        toastr.error(t.error_delete_model);
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
            toastr.success(t.status_updated);
        } else {
            checkbox.checked = !isActive;
            toastr.error(data.message || t.error_update_status);
        }
    } catch (error) {
        console.error('Error toggling active:', error);
        checkbox.checked = !isActive;
        toastr.error(t.error_update_status);
    }
}

async function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 2048 * 1024) {
        toastr.error(t.image_too_large);
        event.target.value = '';
        return;
    }

    const modelId = document.getElementById('model-id').value;
    if (!modelId) {
        toastr.error(t.save_model_first);
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
            toastr.success(data.message || t.image_uploaded);
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
                toastr.error(data.message || t.error_upload_image);
            }
        }
    } catch (error) {
        console.error('Error uploading image:', error);
        toastr.error(t.error_upload_image);
    }
}

async function deleteImage(modelId, imageId) {
    if (!confirm(t.confirm_delete_image)) return;

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
            toastr.success(data.message || t.image_deleted);

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
            toastr.error(data.message || t.error_delete_image);
        }
    } catch (error) {
        console.error('Error deleting image:', error);
        toastr.error(t.error_delete_image);
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
            toastr.success(data.message || t.primary_set);

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
            toastr.error(data.message || t.error_update_image);
        }
    } catch (error) {
        console.error('Error setting primary image:', error);
        toastr.error(t.error_update_image);
    }
}
