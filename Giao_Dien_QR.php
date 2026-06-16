<div style="display:flex;gap:40px">
    <div>
        <h4>MoMo</h4>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?= urlencode($qrMoMo) ?>">
    </div>

    <div>
        <h4>VietQR</h4>
        <img src="<?= $vietQR ?>" width="220">
    </div>
</div>
