/**
 * app-errors.js — shared API error surfacing.
 *
 *  - handleApiError(err, context) — logs the error and shows a toastr popup
 *    with the most specific message available. Robust to both shapes used in
 *    this app:
 *      • apiFetch() throws  Error + { status, body }   (body.message)
 *      • jQuery $.ajax gives jqXHR                      (responseJSON.message)
 *
 *  Exposed as window.handleApiError so every page sharing layouts/app.blade.php
 *  can reuse it. Guest captive-portal pages are standalone and use their own
 *  alert UI instead.
 */
window.handleApiError = function (err, context = '') {
    console.error('API Error' + (context ? ` [${context}]` : ''), err);

    const common = (window.APP_I18N && window.APP_I18N.common) || {};
    const msg = err?.body?.message
        || err?.responseJSON?.message
        || err?.message
        || common.generic_error
        || 'An unexpected error occurred.';

    if (typeof toastr !== 'undefined') toastr.error(msg, common.error_title || 'Error');

    if (err?.status === 401) window.location.href = '/login';
};
