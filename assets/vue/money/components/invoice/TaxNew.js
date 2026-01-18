import Api from "Api";

export default {
  name: "TaxNew",
  props: ["showmodal", "rowdetails", "entity", "taxescodeslist"],
  emits: ["submitnewtax", "closemodal"],

  components: {
    "p-dropdown": primevue.dropdown,
    "p-dialog": primevue.dialog,
    "p-inputtext": primevue.inputtext,
    "p-button": primevue.button,
    "p-textarea": primevue.textarea,
    "p-inputnumber":primevue.inputnumber
  },
  setup(props, context) {
    const { ref, watch } = Vue;

    const taxForm = ref({
        account_id: null,
        code: '',
        name: '',
        description: '',
        percentage: null,
    });
    const showModal = ref(props.showmodal);
    const rowDetails = ref(props.rowdetails);
    const entity = ref(props.entity);
    const taxesCodesList = ref(props.taxescodeslist);
    const loading = ref(false);
    const taxAccounts = ref([]);
    const showValidation = ref(false);
    
    watch(props, (newValue, oldValue) => {
      showModal.value = props.showmodal;
      rowDetails.value = props.rowdetails
      entity.value = props.entity
      taxesCodesList.value = props.taxescodeslist;
    });
    const openModal = () => {
        taxAccounts.value = []
        axios.get(Api.getApiBaseUrl("money") + '/accounts?account_type_id=7&organization_id=' + entity.value.id, Api.getInitialHeaders())
        .then(response => {
            if (response.data.accounts && response.data['accounts'].length > 0) {
                response.data.accounts.map((item) => {
                    taxAccounts.value.push({
                        id: item.id,
                        name: item.name,
                    });
                })
            } else {
                pinesMessageV2({ ty: 'error', m: response.data.message });
            }
        }).catch((error) => {
            pinesMessageV2({ ty: 'error', m: error?.response?.data.message ?? _lang.feedback_messages.error });
        });
    };
    const closeModal = () => {
      taxForm.value.account_id = null;
      taxForm.value.code = '';
      taxForm.value.name = '';
      taxForm.value.description = '';
      taxForm.value.percentage = '';
      context.emit("closemodal", false);
    };
    const validateModal = () => {
        return (taxForm.value.account_id && taxForm.value.code && taxForm.value.name && taxForm.value.description && taxForm.value.percentage) ? true : false;
    };
    const submitNewTax = () => {
        if(!validateModal()){
            showValidation.value = true;
            pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.fillRequiredFields });
        }else{
            loading.value = true
            axios.post(Api.getApiBaseUrl("money") + '/taxes', taxForm.value, Api.getInitialHeaders()).then((response) => {
                if (response.data.tax) {
                    loading.value = false
                    pinesMessageV2({ ty: 'success', m: response.data.message });                
                    context.emit("submitnewtax", { index: rowDetails.value.index, category: rowDetails.value.category, data: response.data.tax });
                    context.emit("closemodal", false);
                } else {
                    loading.value = false
                    pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
                }
            }).catch((error) => {
                loading.value = false
                pinesMessageV2({ ty: 'error', m: error.response.data.message });
            });
        }

    };
    return { showModal, taxForm, taxAccounts, taxesCodesList, loading, showValidation, openModal, closeModal, submitNewTax };
  },
  template: `
    <p-dialog header="${_lang.money.addTax}" v-model:visible="showModal" :breakpoints="{'960px': '25vw'}" :style="{width: '35vw'}" position="top" :modal="true" @hide="closeModal" @show="openModal">
        <div class="panel">
            <div class="p-fluid p-formgrid p-grid">
                <div class="p-field p-col-12 p-md-6">
                    <label for="tax-name">${_lang.money.taxName} * </label>
                    <p-inputtext class="p-inputtext-sm" :class="{ 'p-invalid' : (!taxForm.name && showValidation) }" id="tax-name"  v-model="taxForm.name" type="text"></p-inputtext>
                </div>
                <div class="p-field p-col-12 p-md-6">
                    <label for="tax-code">${_lang.code} * </label>
                    <p-dropdown id="tax-code" :class="{ 'p-invalid' : (!taxForm.code && showValidation) }" v-model="taxForm.code" :options="taxesCodesList" option-label="title" option-value="id"></p-dropdown>
                </div>
                <div class="p-field p-col-12 p-md-6">
                    <label for="tax-percent">${_lang.money.percentage} (%) * </label>
                    <p-inputnumber class="p-inputtext-sm" :class="{ 'p-invalid' : (!taxForm.percentage && showValidation) }" id="tax-percent" v-model="taxForm.percentage" mode="decimal" :min-fraction-digits="0" :max-fraction-digits="2" :min="0" :max="100"></p-inputnumber>
                </div>
                <div class="p-field p-col-12 p-md-6">
                    <label for="tax-account">${_lang.money.relatedAccount} * </label>
                    <p-dropdown id="tax-account" :class="{ 'p-invalid' : (!taxForm.account_id && showValidation) }" v-model="taxForm.account_id" :options="taxAccounts" option-label="name" option-value="id"></p-dropdown>
                </div>

                <div class="p-field p-col-12 p-md-6">
                    <label for="tax-description">${_lang.description} * </label>
                    <p-textarea id="tax-description" :class="{ 'p-invalid' : (!taxForm.description && showValidation) }" v-model="taxForm.description" rows="3"></p-textarea>
                </div>
            </div>
        </div>

        <template #footer>
            <p-button label="${_lang.cancel}" icon="pi pi-times" @click="closeModal" class="p-button-text"></p-button>
            <p-button type="button" label="${_lang.save}" icon="pi pi-check" :loading="loading" @click="submitNewTax"></p-button>
        </template>
    </p-dialog>
    `,
};
