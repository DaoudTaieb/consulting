<div class="details-wrapper" style="padding: 2rem; background: #fcfcfc; border-bottom: 1px solid var(--border);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; margin: 0;">{{ $type }} #{{ $numero }} ({{ $bon->numerointerne ?: '---' }})
            </h2>
            <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                Date:
                <strong>{{ date('d/m/Y', strtotime($bon->datecreation ?? $bon->fbldate ?? $bon->fbrdate)) }}</strong>
            </div>
        </div>
        <div style="text-align: right;">
            <div
                style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.25rem;">
                Fournisseur</div>
            <div style="font-weight: 700;">{{ $bon->fournisseur_nom }}</div>
        </div>
    </div>

    <div
        style="border: 1px solid var(--border); border-radius: 8px; overflow-x: auto; background: white; margin-bottom: 1.5rem;">
        <table style="width: 100%; min-width: 600px; border-collapse: collapse; font-size: 0.85rem;">
            <thead>
                <tr style="background: #f8f8f7; border-bottom: 1px solid var(--border);">
                    <th style="padding: 0.75rem; text-align: left;">Référence</th>
                    <th style="padding: 0.75rem; text-align: left;">Désignation</th>
                    <th style="padding: 0.75rem; text-align: right;">Qte</th>
                    <th style="padding: 0.75rem; text-align: right;">P.U HT</th>
                    <th style="padding: 0.75rem; text-align: right;">Rem (%)</th>
                    <th style="padding: 0.75rem; text-align: right;">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $d)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 0.75rem; font-family: monospace;">{{ $d->produitcode }}</td>
                        <td style="padding: 0.75rem;">{{ $d->produitlibelle }}</td>
                        <td style="padding: 0.75rem; text-align: right;">{{ number_format($d->qte, 2, ',', ' ') }}</td>
                        <td style="padding: 0.75rem; text-align: right;">{{ number_format($d->ht, 3, ',', ' ') }}</td>
                        <td style="padding: 0.75rem; text-align: right;">{{ number_format($d->remise, 2, ',', ' ') }}%</td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 600;">
                            {{ number_format($d->totalttc, 3, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: flex-end; width: 100%;">
        <div
            style="{{ isset($is_mobile) ? 'width: 100%;' : 'width: 250px;' }} background: white; border: 1px solid var(--border); border-radius: 8px; padding: 1rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.85rem;">
                <span>Total HT</span>
                <span>{{ number_format($bon->totalhtnet ?? $bon->totalnetht ?? 0, 3, ',', ' ') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.85rem;">
                <span>Total TVA</span>
                <span>{{ number_format($bon->totaltva, 3, ',', ' ') }}</span>
            </div>
            @if(($bon->droittimbre ?? 0) > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.85rem;">
                    <span>Timbre</span>
                    <span>{{ number_format($bon->droittimbre ?? 0, 3, ',', ' ') }}</span>
                </div>
            @endif
            <div
                style="display: flex; justify-content: space-between; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border); font-weight: 800; color: var(--primary);">
                <span>NET A PAYER</span>
                <span>{{ number_format($bon->netapayer, 3, ',', ' ') }} TND</span>
            </div>
        </div>
    </div>
</div>