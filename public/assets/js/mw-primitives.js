/**
 * mw-primitives.js
 *
 * Shared UI primitives:
 *  - MwConfirm — promise-based confirm/alert dialog (replaces window.confirm)
 *  - MwDrawer  — right-anchored overlay, opened via [data-mw-drawer-open="id"]
 *               and closed via [data-mw-drawer-close] or Escape
 *
 * Must load before any page script that uses these globals.
 * CSS lives in public/assets/css/mrwifi.css (.mw-drawer*, .mw-confirm*).
 */

'use strict';

const MwConfirm = (function () {
    let backdrop = null;
    let dialog = null;
    let pending = null;
    let previousFocus = null;

    function ensureEls() {
        if (backdrop && dialog) return;
        backdrop = document.createElement('div');
        backdrop.className = 'mw-confirm-backdrop';
        document.body.appendChild(backdrop);

        dialog = document.createElement('div');
        dialog.className = 'mw-confirm';
        dialog.setAttribute('role', 'alertdialog');
        dialog.setAttribute('aria-modal', 'true');
        dialog.innerHTML = `
            <div class="mw-confirm-header" data-mw-confirm-title></div>
            <div class="mw-confirm-body" data-mw-confirm-message></div>
            <div class="mw-confirm-footer">
                <button type="button" class="btn btn-outline-secondary" data-mw-confirm-cancel></button>
                <button type="button" class="btn btn-primary" data-mw-confirm-ok></button>
            </div>`;
        document.body.appendChild(dialog);

        backdrop.addEventListener('click', () => resolve(false));
        dialog.querySelector('[data-mw-confirm-cancel]').addEventListener('click', () => resolve(false));
        dialog.querySelector('[data-mw-confirm-ok]').addEventListener('click', () => resolve(true));
        document.addEventListener('keydown', (e) => {
            if (!pending) return;
            if (e.key === 'Escape') { e.preventDefault(); resolve(false); }
            if (e.key === 'Enter')  { e.preventDefault(); resolve(true); }
        });
    }

    function resolve(result) {
        if (!pending) return;
        backdrop.classList.remove('is-open');
        dialog.classList.remove('is-open');
        document.body.classList.remove('mw-drawer-locked');
        const p = pending;
        pending = null;
        if (previousFocus && typeof previousFocus.focus === 'function') {
            previousFocus.focus();
            previousFocus = null;
        }
        p.resolve(result);
    }

    function open(opts = {}) {
        ensureEls();
        if (pending) pending.resolve(false); // close any outstanding prompt
        const {
            title = 'Confirm',
            message = '',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            destructive = false,
        } = opts;
        dialog.querySelector('[data-mw-confirm-title]').textContent = title;
        dialog.querySelector('[data-mw-confirm-message]').textContent = message;
        const cancelBtn = dialog.querySelector('[data-mw-confirm-cancel]');
        const okBtn     = dialog.querySelector('[data-mw-confirm-ok]');
        cancelBtn.textContent = cancelText;
        okBtn.textContent = confirmText;
        okBtn.classList.toggle('btn-danger', !!destructive);
        okBtn.classList.toggle('btn-primary', !destructive);
        previousFocus = document.activeElement;
        backdrop.classList.add('is-open');
        dialog.classList.add('is-open');
        document.body.classList.add('mw-drawer-locked');
        okBtn.focus();
        return new Promise((resolveFn) => { pending = { resolve: resolveFn }; });
    }

    return { open };
})();

const MwDrawer = (function () {
    const backdropId = '__mw_drawer_backdrop';
    let previousFocus = null;

    function ensureBackdrop() {
        let el = document.getElementById(backdropId);
        if (!el) {
            el = document.createElement('div');
            el.id = backdropId;
            el.className = 'mw-drawer-backdrop';
            el.setAttribute('data-mw-drawer-close', '');
            document.body.appendChild(el);
        }
        return el;
    }

    function open(id) {
        const drawer = document.getElementById(id);
        if (!drawer || !drawer.classList.contains('mw-drawer')) return;
        if (drawer.classList.contains('is-open')) return;
        previousFocus = document.activeElement;
        ensureBackdrop().classList.add('is-open');
        drawer.classList.add('is-open');
        document.body.classList.add('mw-drawer-locked');
        const closeBtn = drawer.querySelector('.mw-drawer-close');
        if (closeBtn) closeBtn.focus();
    }

    function close(id) {
        const drawer = id
            ? document.getElementById(id)
            : document.querySelector('.mw-drawer.is-open');
        if (!drawer) return;
        drawer.classList.remove('is-open');
        if (!document.querySelector('.mw-drawer.is-open')) {
            const bd = document.getElementById(backdropId);
            if (bd) bd.classList.remove('is-open');
            document.body.classList.remove('mw-drawer-locked');
        }
        if (previousFocus && typeof previousFocus.focus === 'function') {
            previousFocus.focus();
            previousFocus = null;
        }
    }

    document.addEventListener('click', function (e) {
        const opener = e.target.closest('[data-mw-drawer-open]');
        if (opener) {
            e.preventDefault();
            open(opener.getAttribute('data-mw-drawer-open'));
            return;
        }
        const closer = e.target.closest('[data-mw-drawer-close]');
        if (closer) {
            e.preventDefault();
            close();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.querySelector('.mw-drawer.is-open')) {
            close();
        }
    });

    return { open, close };
})();
