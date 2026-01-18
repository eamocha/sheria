import Api from 'Api'

export default {
    name: 'EditInvoiceTemplate',
    props: ['showmodal', 'submitinvoicetemplate', 'closemodal', 'templateid', 'templateidslist', 'invoiceid', 'entity', 'tooltipdefaultinvoicetemplate'],
    components: {
        'p-dialog': primevue.dialog,
        'p-button': primevue.button,
        'p-dropdown': primevue.dropdown
    },
    setup(props) {
        const { ref, watch } = Vue;
        const showModal = ref(props.showmodal)
        const templateIdsList = ref([]);
        const invoiceId = ref(props.invoiceid);
        const templateId = ref(props.templateid);
        const errorMsg = ref([])
        const showLoader = ref("")
        const entity = ref(props.entity)
        const tooltipDefaultInvoiceTemplate = ref(props.tooltipdefaultinvoicetemplate)

        const closeModal = () => {
            errorMsg.value = []
            props.closemodal()
        }
        watch(props, () => {
            showLoader.value = ""
            errorMsg.value = []
            showModal.value = props.showmodal
            templateIdsList.value = props.templateidslist;
            invoiceId.value = props.invoiceid;
            templateId.value = props.templateid;
            entity.value = props.entity;
        });
        const submitModal = () => {
            errorMsg.value = []
            // if(!templateId.value)
            //     errorMsg.value.push(_lang.money.chooseTemplate);
            if (errorMsg.value.length == 0) {
                showLoader.value = "loading"
                let postData = {
                    "organization_id": entity.value.id,
                    "template_id": templateId.value
                }
                axios.patch(Api.getApiBaseUrl("money") + '/invoices/' + invoiceId.value + '/invoicetemplate', postData, Api.getInitialHeaders()).then(response => {
                    props.submitinvoicetemplate({
                        template_id: response.data.template_id,
                        template_name: response.data.template_name,
                    })
                }).catch((error) => {
                    showLoader.value = ""
                    errorMsg.value.push(error.response.data.message)
                });
            }
        }
        return {
            showModal,
            templateIdsList,
            templateId,
            tooltipDefaultInvoiceTemplate,
            errorMsg,
            showLoader,
            submitModal,
            closeModal
        }
    },

    template: `
<p-dialog header="${_lang.money.invoiceTemplate}" v-model:visible="showModal" :breakpoints="{'800px': '60vw'}" :style="{width: '450px'}" position="top" :modal="true" @hide="closeModal">
    <div class="panel">
        <div class="p-fluid p-formgrid p-grid">
            <div class="col-md-12">
                <div class="form-group row">
                    <div class="col-sm-11">
                        <p-dropdown id="invoice-template-id" class="w-100" show-clear="true" placeholder="${_lang.money.defaultInvoiceTemplate}" v-model="templateId" :options="templateIdsList" option-label="name" option-value="id"></p-dropdown>
                    </div>
                </div>
            </div>
            <div class="col-md-11">
                <a href="${getBaseURL('money')}organization_invoice_templates">${_lang.money.invoiceTemplates}</a>
                <p class="text-muted"><small>{{tooltipDefaultInvoiceTemplate}}</small></p>
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