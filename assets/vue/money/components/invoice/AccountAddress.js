import Api from "Api";

export default {
    name: "AccountAddress",
    props: ["showmodal", "countrylist", "additionalidtypeslist", "accountrequiredfields", "organizationid", "clientaccountresource"],
    emits: ["submitaccountaddress", "closemodal"],

    components: {
        "p-dropdown": primevue.dropdown,
        "p-dialog": primevue.dialog,
        "p-inputtext": primevue.inputtext,
        "p-button": primevue.button,
        "p-textarea": primevue.textarea,
        "p-inputnumber": primevue.inputnumber
    },
    setup(props, context) {
        const { ref, watch } = Vue;

        const accountAddressForm = ref({
            tax_number: null,
            additional_id_type: null,
            additional_id_value: null,
            address: null,
            zip: null,
            city: null,
            country_id: null,
            state: null,
            street_name: null,
            additional_street_name: null,
            building_number: null,
            address_additional_number: null,
            district_neighborhood: null,
        });
        const showModal = ref(props.showmodal);
        const organizationId = ref(props.organizationid);
        const accountRequiredFields = ref(props.accountrequiredfields);
        const countryList = ref(props.countrylist);
        const additionalIdTypesList = ref([]);
        const clientAccountResource = ref(props.clientaccountresource);
        const loading = ref(false);
        const showValidation = ref(false);
        watch(props, (newValue, oldValue) => {
            showModal.value = props.showmodal;
            additionalIdTypesList.value = props.additionalidtypeslist;
            clientAccountResource.value = props.clientaccountresource;
        });
        const openModal = () => {
            showValidation.value = false;
            for (let index in accountAddressForm.value)
                accountAddressForm.value[index] = clientAccountResource.value[index];
        };
        const closeModal = (isSkipAccountFieldsChecking) => {
            for (let index in accountAddressForm.value)
                accountAddressForm.value[index] = null;
            context.emit("closemodal", isSkipAccountFieldsChecking);
        };
        const validateModal = () => {
            if (accountRequiredFields.value) {
                for (let i = 0; i < accountRequiredFields.value.length; i++) {
                    if (!accountAddressForm.value[accountRequiredFields.value[i]])
                        return false;
                }
            }
            return true;
        };
        const submitAccountAddress = () => {
            if (!validateModal()) {
                showValidation.value = true;
                pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.fillRequiredFields });
            } else {
                loading.value = true
                axios.post(Api.getApiBaseUrl("money") + '/accounts/updateadditionalfields/' + clientAccountResource.value.id + '?organization_id=' + organizationId.value, accountAddressForm.value, Api.getInitialHeaders()).then((response) => {
                    if (response.data.account) {
                        loading.value = false
                        pinesMessageV2({ ty: 'success', m: response.data.message });
                        context.emit("submitaccountaddress", { account: response.data.account });
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
        return { showModal, accountAddressForm, countryList, accountRequiredFields, clientAccountResource, additionalIdTypesList, loading, showValidation, openModal, closeModal, submitAccountAddress };
    },
    template: `
    <p-dialog :header="_lang.clientName + ': ' + clientAccountResource.name" v-model:visible="showModal" :breakpoints="{'960px': '75vw'}" :style="{width: '50vw'}" position="top" :modal="true" @hide="closeModal" @show="openModal">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning">${_lang.money.eInvoicingMissingFields}</div>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-tax-number" :class="{'required-star':accountRequiredFields.indexOf('tax_number') != -1}">${_lang.taxNumber}</label>
                        <p-inputtext id="acc-tax-number" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.tax_number && accountRequiredFields.indexOf('tax_number') != -1 && showValidation) }" v-model="accountAddressForm.tax_number" type="text"></p-inputtext>
                    </div>
                    <div class="form-group col-12 col-md-6">&nbsp;</div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-additional-id-type" :class="{'required-star':accountRequiredFields.indexOf('additional_id_type') != -1}">${_lang.additionalIdType}</label>
                        <p-dropdown id="acc-additional-id-type" class="w-100" show-clear="true" :class="{ 'p-invalid' : (!accountAddressForm.additional_id_type && accountRequiredFields.indexOf('additional_id_type') != -1 && showValidation) }" v-model="accountAddressForm.additional_id_type" :options="additionalIdTypesList" option-label="name" option-value="id"></p-dropdown>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-additional-id-value" :class="{'required-star':accountRequiredFields.indexOf('additional_id_value') != -1}">${_lang.additionalIdValue}</label>
                        <p-inputtext id="acc-additional-id-value" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.additional_id_value && accountRequiredFields.indexOf('additional_id_value') != -1 && showValidation) }" v-model="accountAddressForm.additional_id_value" type="text"></p-inputtext>
                    </div>
                    
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-country-id" :class="{'required-star':accountRequiredFields.indexOf('country_id') != -1}">${_lang.country}</label>
                        <p-dropdown id="acc-country-id" class="w-100" :class="{ 'p-invalid' : (!accountAddressForm.country_id && accountRequiredFields.indexOf('country_id') != -1 && showValidation) }" v-model="accountAddressForm.country_id" :options="countryList" option-label="name" option-value="id" show-clear="true" filter="true" ></p-dropdown>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-city" :class="{'required-star':accountRequiredFields.indexOf('city') != -1}">${_lang.city}</label>
                        <p-inputtext id="acc-city" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.city && accountRequiredFields.indexOf('city') != -1 && showValidation) }" v-model="accountAddressForm.city" type="text"></p-inputtext>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-state" :class="{'required-star':accountRequiredFields.indexOf('state') != -1}">${_lang.userFields.state}</label>
                        <p-inputtext id="acc-state" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.state && accountRequiredFields.indexOf('state') != -1 && showValidation) }" v-model="accountAddressForm.state" type="text"></p-inputtext>
                    </div>

                    <div class="form-group col-12 col-md-6">
                        <label for="acc-address" :class="{'required-star':accountRequiredFields.indexOf('address') != -1}">${_lang.address}</label>
                        <p-inputtext id="acc-address" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.address && accountRequiredFields.indexOf('address') != -1 && showValidation) }" v-model="accountAddressForm.address" type="text"></p-inputtext>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-street-name" :class="{'required-star':accountRequiredFields.indexOf('street_name') != -1}">${_lang.streetName}</label>
                        <p-inputtext id="acc-street-name" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.street_name && accountRequiredFields.indexOf('street_name') != -1 && showValidation) }" v-model="accountAddressForm.street_name" type="text"></p-inputtext>
                    </div>
                    
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-additional-street-name" :class="{'required-star':accountRequiredFields.indexOf('additional_street_name') != -1}">${_lang.additionalStreetName}</label>
                        <p-inputtext id="acc-additional-street-name" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.additional_street_name && accountRequiredFields.indexOf('additional_street_name') != -1 && showValidation) }" v-model="accountAddressForm.additional_street_name" type="text"></p-inputtext>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-building-number" :class="{'required-star':accountRequiredFields.indexOf('building_number') != -1}">${_lang.buildingNumber}</label>
                        <p-inputtext id="acc-building-number" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.building_number && accountRequiredFields.indexOf('building_number') != -1 && showValidation) }" v-model="accountAddressForm.building_number" type="text"></p-inputtext>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-address-additional-number" :class="{'required-star':accountRequiredFields.indexOf('address_additional_number') != -1}">${_lang.addressAdditionalNumber}</label>
                        <p-inputtext id="acc-address-additional-number" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.address_additional_number && accountRequiredFields.indexOf('address_additional_number') != -1 && showValidation) }" v-model="accountAddressForm.address_additional_number" type="text"></p-inputtext>
                    </div>

                    <div class="form-group col-12 col-md-6">
                        <label for="acc-zip" :class="{'required-star':accountRequiredFields.indexOf('zip') != -1}">${_lang.userFields.zip}</label>
                        <p-inputtext id="acc-zip" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.zip && accountRequiredFields.indexOf('zip') != -1 && showValidation) }" v-model="accountAddressForm.zip" type="text"></p-inputtext>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="acc-district-neighborhood" :class="{'required-star':accountRequiredFields.indexOf('district_neighborhood') != -1}">${_lang.districtNeighborhood}</label>
                        <p-inputtext id="acc-district-neighborhood" class="form-control" :class="{ 'p-invalid' : (!accountAddressForm.district_neighborhood && accountRequiredFields.indexOf('district_neighborhood') != -1 && showValidation) }" v-model="accountAddressForm.district_neighborhood" type="text"></p-inputtext>
                    </div>
                
                </div>
            </div>
        </div>

        <template #footer>
            <p-button label="${_lang.cancel}" icon="pi pi-times" @click="closeModal" class="p-button-text"></p-button>
            <p-button label="${_lang.money.skipForNow}" icon="pi pi-times" @click="closeModal(true)"></p-button>
            <p-button type="button" label="${_lang.save}" icon="pi pi-check" :loading="loading" @click="submitAccountAddress"></p-button>
        </template>
    </p-dialog>
    `,
};
