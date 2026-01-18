<div class="container my-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Multi-Language Support</h5>
            <div>
                <select class="form-select form-select-sm" onchange="switchLanguage(this.value)">
                    <option value="en">English</option>
                    <option value="sw">Kiswahili</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <p id="description-en">
                Our system supports full functionality in both English and Kiswahili, enhancing accessibility and user
                experience. Users can switch languages seamlessly, with all system labels, menus, and key functions
                updating instantly.
            </p>
            <p id="description-sw" class="d-none">
                Mfumo wetu unasaidia kikamilifu Kiingereza na Kiswahili, ili kuongeza upatikanaji na urahisi wa matumizi.
                Watumiaji wanaweza kubadilisha lugha kwa urahisi, na maandiko yote, menyu, na kazi muhimu
                kusasishwa papo hapo.
            </p>
            <hr>
            <button class="btn btn-primary btn-sm">Save Settings</button>
            <button class="btn btn-outline-secondary btn-sm">Cancel</button>
        </div>
    </div>
</div>

<script>
    function switchLanguage(lang) {
        document.getElementById('description-en').classList.toggle('d-none', lang !== 'en');
        document.getElementById('description-sw').classList.toggle('d-none', lang !== 'sw');
    }
</script>

<style>
    .card {
        border-radius: 10px;
    }
</style>
