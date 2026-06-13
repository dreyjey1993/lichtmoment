{{-- resources/views/admin/partials/design-system.blade.php --}}
{{-- Unified Design System for Lichtmoment Admin --}}
<style>
    /* === BUTTONS === */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
        cursor: pointer;
        user-select: none;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.5rem;
    }
    .btn-md {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    .btn-lg {
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
    }
    .btn-xl {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }

    .btn-primary {
        background-color: #c9a84c;
        color: white;
        box-shadow: 0 1px 2px rgba(201, 168, 76, 0.2);
    }
    .btn-primary:hover { background-color: #b8943f; }
    .btn-primary:active { background-color: #a07d34; transform: scale(0.98); }

    .btn-secondary {
        background-color: white;
        border: 1px solid #e5e7eb;
        color: #4b5563;
    }
    .btn-secondary:hover { border-color: #c9a84c; color: #c9a84c; }
    .btn-secondary:active { background-color: #fef9eb; }

    .btn-danger {
        background-color: white;
        border: 1px solid #fecaca;
        color: #ef4444;
    }
    .btn-danger:hover { background-color: #fef2f2; border-color: #fca5a5; }
    .btn-danger:active { background-color: #fee2e2; }

    .btn-danger-solid {
        background-color: #ef4444;
        color: white;
        box-shadow: 0 1px 2px rgba(239, 68, 68, 0.2);
    }
    .btn-danger-solid:hover { background-color: #dc2626; }
    .btn-danger-solid:active { background-color: #b91c1c; transform: scale(0.98); }

    .btn-ghost {
        color: #6b7280;
        background: transparent;
        border-color: transparent;
    }
    .btn-ghost:hover { color: #374151; background-color: #f3f4f6; }
    .btn-ghost:active { background-color: #e5e7eb; }

    .btn-icon {
        width: 2.25rem;
        height: 2.25rem;
        padding: 0;
        border-radius: 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-icon-sm {
        width: 1.75rem;
        height: 1.75rem;
        padding: 0;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        cursor: pointer;
    }

    /* === INPUTS === */
    .input {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        outline: none;
        transition: all 0.2s;
        background: white;
    }
    .input:focus {
        border-color: #c9a84c;
        box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.1);
    }
    .input-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.5rem;
    }

    /* === CHECKBOXES === */
    .checkbox {
        width: 1.125rem;
        height: 1.125rem;
        border-radius: 0.375rem;
        border: 2px solid #d1d5db;
        background: white;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        position: relative;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .checkbox:checked {
        background-color: #c9a84c;
        border-color: #c9a84c;
    }
    .checkbox:checked::after {
        content: '';
        position: absolute;
        left: 4px;
        top: 1px;
        width: 5px;
        height: 9px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    .checkbox:hover { border-color: #c9a84c; }

    /* === TOGGLE SWITCH === */
    .toggle { position: relative; display: inline-flex; align-items: center; cursor: pointer; }
    .toggle input { position: absolute; opacity: 0; width: 0; height: 0; }
    .toggle-track {
        width: 2.75rem;
        height: 1.5rem;
        background-color: #e5e7eb;
        border-radius: 9999px;
        transition: background-color 0.2s;
    }
    .toggle input:checked + .toggle-track { background-color: #c9a84c; }
    .toggle-thumb {
        position: absolute;
        left: 2px;
        top: 2px;
        width: 1.25rem;
        height: 1.25rem;
        background: white;
        border-radius: 9999px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .toggle input:checked ~ .toggle-thumb { transform: translateX(1.25rem); }

    /* === CARDS === */
    .card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #f3f4f6;
        overflow: hidden;
    }
    .card-hover { transition: all 0.3s; }
    .card-hover:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    /* === BADGES === */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .badge-gold { background: #fef9eb; color: #b8943f; border: 1px solid #fde68a; }
    .badge-gray { background: #f3f4f6; color: #6b7280; }

    /* === TABS === */
    .tab {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.75rem;
        border: 1px solid;
        transition: all 0.2s;
        cursor: pointer;
    }
    .tab-active {
        background: #fef9eb;
        border-color: #c9a84c;
        color: #b8943f;
    }
    .tab-inactive {
        border-color: #e5e7eb;
        color: #6b7280;
        background: white;
    }
    .tab-inactive:hover { border-color: #c9a84c; color: #c9a84c; }

    /* === SHARE LINK CARD === */
    .share-card {
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.75rem;
        border: 1px solid #f3f4f6;
    }
</style>
