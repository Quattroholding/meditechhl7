@if($referralCode)
<div class="card referral-code-card">
    <div class="card-body">
        <div class="referral-header">
            <i class="fas fa-gift referral-icon"></i>
            <h4 class="card-title mb-0">Tu Código de Referido</h4>
        </div>

        <p class="text-muted mb-3">Comparte con tus colegas tu código y gana $5 de crédito en tu próxima factura por cada nuevo cliente registrado con tu código.</p>

        <div class="referral-code-section">
            <div class="code-display">
                <div class="code-box" id="referral-code-box">
                    <span class="code-label">Código:</span>
                    <span class="code-value" id="referral-code-value">{{ $referralCode->code }}</span>
                </div>
                <button type="button" class="btn btn-primary btn-copy" onclick="copyReferralCode()" id="copy-btn">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>

            <div class="share-url-section mt-3">
                <label class="form-label">Enlace de registro:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="share-url-input" value="{{ $shareUrl }}" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyShareUrl(this)" id="copy-url-btn">
                        <i class="fas fa-link"></i> Copiar enlace
                    </button>
                </div>
            </div>
        </div>

        <div class="referral-stats mt-4">
            <div class="stat-item">
                <i class="fas fa-users text-primary"></i>
                <span>Referidos totales:</span>
                <strong>{{ $referralCode->times_used ?? 0 }}</strong>
            </div>
        </div>

        <div class="download-section mt-4">
            <a href="{{ route('referral.code.pdf', $client) }}" class="btn btn-success btn-download" target="_blank">
                <i class="fas fa-file-pdf"></i> Descargar PDF con QR
            </a>
            <p class="text-muted mt-2 mb-0" style="font-size: 13px;">
                <i class="fas fa-info-circle"></i> Descarga un PDF imprimible con código QR para compartir fácilmente
            </p>
        </div>
    </div>
</div>

<style>
.referral-code-card {
    border-left: 4px solid #2D1B69;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.referral-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.referral-icon {
    font-size: 24px;
    color: #2D1B69;
}

.referral-code-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 10px;
}

.code-display {
    display: flex;
    gap: 10px;
    align-items: center;
}

.code-box {
    flex: 1;
    background: white;
    padding: 15px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    gap: 10px;
}

.code-label {
    font-weight: 600;
    color: #6c757d;
}

.code-value {
    font-size: 20px;
    font-weight: 700;
    color: #2D1B69;
    letter-spacing: 1px;
}

.btn-copy {
    background: linear-gradient(135deg, #2D1B69 0%, #1E3A8A 100%);
    border: none;
    white-space: nowrap;
}

.btn-copy:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(45, 27, 105, 0.3);
}

.btn-copy.copied {
    background: #28a745;
}

.share-url-section label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
}

.referral-stats {
    display: flex;
    gap: 20px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-item i {
    font-size: 18px;
}

.stat-item span {
    color: #6c757d;
}

.stat-item strong {
    color: #2D1B69;
    font-size: 18px;
}

.download-section {
    text-align: center;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
}

.btn-download {
    padding: 12px 30px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

@media (max-width: 768px) {
    .code-display {
        flex-direction: column;
    }

    .btn-copy {
        width: 100%;
    }

    .btn-download {
        width: 100%;
    }
}
</style>

<script>
function copyReferralCode() {
    const codeValue = document.getElementById('referral-code-value').innerText;
    const copyBtn = document.getElementById('copy-btn');

    navigator.clipboard.writeText(codeValue).then(() => {
        const originalHTML = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        copyBtn.classList.add('copied');

        setTimeout(() => {
            copyBtn.innerHTML = originalHTML;
            copyBtn.classList.remove('copied');
        }, 2000);
    }).catch(err => {
        alert('Error al copiar: ' + err);
    });
}

function copyShareUrl(btn) {
    const urlInput = document.getElementById('share-url-input');
    urlInput.select();

    navigator.clipboard.writeText(urlInput.value).then(() => {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');

        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(err => {
        alert('Error al copiar: ' + err);
    });
}
</script>
@endif
