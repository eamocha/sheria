import Api from 'Api'

export default {
    name: 'EditInvoiceNb',
    props: ['showmodal', 'submitinvoicnb', 'closemodal', 'prefix', 'referencenumber', 'suffix', 'entity'],
    components: { 
        'p-dialog': primevue.dialog,
        'p-button': primevue.button
    },
    setup(props) {
        const { ref, watch } = Vue;
        const showModal = ref(props.showmodal)
        const prefix = ref(props.prefix)
        const referenceNumber = ref(props.referencenumber)
        const suffix = ref(props.suffix)
        const errorMsg = ref([])
        const showLoader = ref("")
        const entity = ref(props.entity)

        const closeModal = () => {
            errorMsg.value = []
            props.closemodal()
        }
        watch(props, () => {
            showLoader.value = ""
            errorMsg.value = []
            showModal.value = props.showmodal
            prefix.value = props.prefix;
            referenceNumber.value = props.referencenumber;
            suffix.value = props.suffix;
            entity.value = props.entity;
        })
        const validatePrefix = (val) => {
            var decimalPattern = /^.{1,8}$/;
            if (!decimalPattern.test(val)) {
                errorMsg.value.push(_lang.validation_field_required.sprintf([_lang.invoicePrefix]) + '. ' + _lang.allowedMaxLength.sprintf(['8']))
            }
        }
        const validateRefNb = (val) => {
            var decimalPattern = /^[0-9]{1,7}$/;
            if (!decimalPattern.test(val)) {
                errorMsg.value.push(_lang.validation_field_required.sprintf([_lang.invoiceReferenceNB]) + '. ' + _lang.allowedMaxNumberAndLength.sprintf(['7']))
            }
        }
        const validateSuffix = (val) => {
            var decimalPattern = /^.{0,15}$/;
            if (!decimalPattern.test(val)) {
                errorMsg.value.push(_lang.allowedMaxLength.sprintf(['15']))
            }
        }
        const submitModal = () => {
            errorMsg.value = []
            validatePrefix(prefix.value)
            validateRefNb(referenceNumber.value)
            validateSuffix(suffix.value)

            if (errorMsg.value.length == 0) {
                showLoader.value = "loading"
                let postData = {
                    "organization_id": entity.value.id,
                    "prefix": prefix.value,
                    "reference_number": referenceNumber.value,
                    "suffix": suffix.value,
                    "is_debit_note": isDebitNoteGlobal
                }
                let completeUrl = invoiceEditIdGlobal > 0 ? '?voucher_header_id=' + invoiceEditIdGlobal : '' 
                axios.post(Api.getApiBaseUrl("money") + '/invoices/checkinvoicenumber' + completeUrl, postData, Api.getInitialHeaders()).then(response => {
                    if (response.data.invoice_number) {
                        props.submitinvoicnb({
                            prefix: response.data.invoice_number.prefix,
                            referenceNumber: response.data.invoice_number.reference_number,
                            suffix: response.data.invoice_number.suffix ?? "",
                        })
                    } else {
                        showLoader.value = ""
                        errorMsg.value.push(response.data.message)
                    }
                }).catch((error) => {
                    showLoader.value = ""
                    errorMsg.value.push(error.response.data.message)
                });
            }
        }
        return {
            showModal,
            prefix,
            referenceNumber,
            suffix,
            errorMsg,
            showLoader,
            submitModal,
            closeModal
        }
    },

    template: `
<p-dialog header="${_lang.editInvoiceNb}" v-model:visible="showModal" :breakpoints="{'960px': '75vw'}" :style="{width: '50vw'}" position="top" :modal="true" @hide="closeModal">
    <div class="panel">
        <div class="p-fluid p-formgrid p-grid">
            <div class="col-md-12">
                <div class="form-group row">
                    <label for="invoice-prefix" class="col-sm-2 col-form-label required">${_lang.invoicePrefix }</label>
                    <div class="col-sm-5">
                        <input type="text" v-model="prefix" class="form-control" id="invoice-prefix">
                    </div>
                    <div class="col-sm-5">
                        <label>${_lang.allowedMaxLength.sprintf(['8'])}</label>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="invoice-prefix" class="col-sm-2 col-form-label required">${_lang.invoiceReferenceNB }</label>
                    <div class="col-sm-5">
                        <input type="text" v-model="referenceNumber" class="form-control" id="invoice-ref-nb">
                    </div>
                    <div class="col-sm-5">
                        <label>${ _lang.allowedMaxNumberAndLength.sprintf(['7']) }</label>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="invoice-suffix" class="col-sm-2 col-form-label">${_lang.invoiceSuffix }</label>
                    <div class="col-sm-5">
                        <input type="text" v-model="suffix" class="form-control" id="invoice-suffix">
                    </div>
                    <div class="col-sm-5">
                        <label>${ _lang.allowedMaxLength.sprintf(['15']) }</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="alert alert-danger" role="alert" v-for="(msg, i) in errorMsg" :key="i">
                    {{msg}}
                </div>
            </div>
        </div>
    </div>
    <template #footer>
        <p-button label="${_lang.cancel}" icon="pi pi-times" @click="closeModal" class="p-button-text"></p-button>
        <p-button type="button" label="${_lang.set}" icon="pi pi-check" :loading="showLoader==='loading'" @click="submitModal"></p-button>
    </template>
</p-dialog>

`,
};