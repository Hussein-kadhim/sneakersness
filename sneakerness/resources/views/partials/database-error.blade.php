{{-- Herbruikbare waarschuwingsmelding voor database- en verbindingsfouten --}}
@php
    $type = $itemType ?? 'gegevens';
@endphp

<div class="sn-db-alert">
    {{-- Waarschuwingsicoon --}}
    <div class="sn-db-alert-icon">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    {{-- Tekstuele toelichting voor de gebruiker --}}
    <div class="sn-db-alert-content">
        <h3 class="sn-db-alert-title">Verbindingsfout</h3>
        <p class="sn-db-alert-message">
            Database is momenteel niet beschikbaar, de {{ $type }} konden niet worden geladen. Probeer het later opnieuw.
        </p>
    </div>
</div>

{{-- Inline styling voor de foutmelding component --}}
<style>
    .sn-db-alert {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        width: 100%;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 0.75rem;
        padding: 0.875rem 1.125rem;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
        box-sizing: border-box;
    }

    .sn-db-alert-icon {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        max-width: 38px !important;
        max-height: 38px !important;
        border-radius: 0.5rem;
        background: #fee2e2;
        color: #dc2626;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .sn-db-alert-content {
        flex: 1;
        min-width: 0;
    }

    .sn-db-alert-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #991b1b;
        margin: 0 0 0.125rem 0;
        line-height: 1.25;
    }

    .sn-db-alert-message {
        font-size: 0.8125rem;
        color: #b91c1c;
        margin: 0;
        line-height: 1.4;
    }

    @media (max-width: 640px) {
        .sn-db-alert {
            align-items: flex-start;
            padding: 0.75rem 0.875rem;
            gap: 0.75rem;
        }

        .sn-db-alert-icon {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            min-height: 32px !important;
            font-size: 0.875rem;
        }

        .sn-db-alert-title {
            font-size: 0.8125rem;
        }

        .sn-db-alert-message {
            font-size: 0.75rem;
        }
    }
</style>
