import Api from "Api";

export default {
  name: "ItemNew",
  props: ["showmodal", "rowdetails", "taxeslist", "itemslist", "entity"],
  emits: ["submitnewitem", "closemodal"],

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

    const itemForm = ref({
        item_id: null,
        account_id: null,
        tax_id: null,
        name: '',
        description: '',
        unit_price: '',
    });
    const showModal = ref(props.showmodal);
    const rowDetails = ref(props.rowdetails);
    const taxesList = ref([]);
    const itemsList = ref([]);
    const loading = ref(false);
    const itemAccounts = ref([]);
    const entity = ref(props.entity);
    const showValidation = ref(false);

    watch(props, (newValue, oldValue) => {
      showModal.value = props.showmodal;
      rowDetails.value = props.rowdetails;
      taxesList.value = props.taxeslist;
      entity.value = props.entity

      itemsList.value = props.itemslist.reduce(function(filtered, option) {
        if (option.isParent) 
           filtered.push(option);
        
        return filtered;
      }, []);
    });
    const openModal = () => {
        itemAccounts.value = []
        axios.get(Api.getApiBaseUrl("money") + '/accounts?account_type_id=9&organization_id=' + entity.value.id, Api.getInitialHeaders())
        .then(response => {
            if (response.data.accounts && response.data['accounts'].length > 0) {
                response.data.accounts.map((item) => {
                    itemAccounts.value.push({
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
      itemForm.value.item_id = null;
      itemForm.value.account_id = null;
      itemForm.value.tax_id = null;
      itemForm.value.name = '';
      itemForm.value.description = '';
      itemForm.value.unit_price = '';
      context.emit("closemodal", false);
    };
    const validateModal = () => {
        return (itemForm.value.account_id && itemForm.value.name) ? true : false;
    };
    const submitNewItem = () => {
        if(!validateModal()){
            showValidation.value = true;
            pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.fillRequiredFields });
        }else{
            loading.value = true
            axios.post(Api.getApiBaseUrl("money") + '/items', itemForm.value, Api.getInitialHeaders()).then((response) => {
                if (response.data.item) {
                    loading.value = false
                    pinesMessageV2({ ty: 'success', m: response.data.message });                
                    context.emit("submitnewitem", { index: rowDetails.value.index, category: rowDetails.value.category, data: response.data.item });
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
    return { showModal, itemForm, itemAccounts,taxesList, itemsList, entity, loading,showValidation, openModal, closeModal, submitNewItem };
  },
  template: `
    <p-dialog header="${_lang.money.addService}" v-model:visible="showModal" :breakpoints="{'960px': '25vw'}" :style="{width: '35vw'}" position="top" :modal="true" @hide="closeModal" @show="openModal">
        <div class="panel">
            <div class="p-fluid p-formgrid p-grid">
                <div class="p-field p-col-12 p-md-6">
                    <label for="item-name">${_lang.money.serviceName} * </label>
                    <p-inputtext id="item-name" :class="{ 'p-invalid' : (!itemForm.name && showValidation) }" v-model="itemForm.name" type="text"></p-inputtext>
                </div>
                <div class="p-field p-col-12 p-md-6">
                    <label for="item-account">${_lang.money.relatedAccount} * </label>
                    <p-dropdown id="item-account" :class="{ 'p-invalid' : (!itemForm.account_id && showValidation) }" v-model="itemForm.account_id" :options="itemAccounts" option-label="name" option-value="id"></p-dropdown>
                </div>
                <div class="p-field p-col-12 p-md-6">
                    <label for="item-price">${_lang.money.unitPrice} ({{entity.currency_code}})</label>
                    <p-inputnumber id="item-price" v-model="itemForm.unit_price" mode="decimal" :min-fraction-digits="0" :max-fraction-digits="2" :min="0"></p-inputnumber>
                </div>
                <div class="p-field p-col-12 p-md-6">
                    <label for="item-tax"> ${_lang.tax} </label>
                    <p-dropdown id="item-tax" show-clear="true" v-model="itemForm.tax_id" :options="taxesList" option-label="label" option-value="id"></p-dropdown>
                </div>
                <div class="p-field p-col-12 p-md-6">
                    <label for="item-nest"> Nest Service Under </label>
                    <p-dropdown id="item-nest" show-clear="true" v-model="itemForm.item_id" :options="itemsList" option-label="name" option-value="id"></p-dropdown>
                </div>
                <div class="p-field p-col-12 p-md-6">
                    <label for="item-description">${_lang.description} </label>
                    <p-textarea id="item-description"  v-model="itemForm.description" rows="3"></p-textarea>
                </div>
            </div>
        </div>

        <template #footer>
            <p-button label="${_lang.cancel}" icon="pi pi-times" @click="closeModal" class="p-button-text"></p-button>
            <p-button type="button" label="${_lang.save}" icon="pi pi-check" :loading="loading" @click="submitNewItem"></p-button>
        </template>
    </p-dialog>
    `,
};
