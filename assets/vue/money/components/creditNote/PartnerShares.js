import PartnerLookup from 'PartnerLookup';

export default {
    name: 'PartnerShares',
    components: {
        'partner-lookup': PartnerLookup,
        'p-dialog': primevue.dialog,
        'p-inputtext': primevue.inputtext,
        'p-button': primevue.button
    },
    props: ['showmodal', 'closemodal', 'submitshares', 'partnerdata'],
    setup(props, context) {
        const { ref, watch } = Vue;
        const showModal = ref(props.showmodal)
        const selectedPartnerData = ref(null);
        const errorMsg = ref([])
        watch(props, (newValue, oldValue) => {
            errorMsg.value = []
            showModal.value = props.showmodal
            selectedPartnerData.value = JSON.parse(JSON.stringify(props.partnerdata.data));
            if (props.partnerdata.action == "add")
                selectedPartnerData.value.push({
                    partner_account_id: '',
                    partner_name: '',
                    partner_commission: '',
                });
        })

        const closeModal = () => {
            errorMsg.value = []
            selectedPartnerData.value = []
            props.closemodal()
        }

        const submitModal = () => {
            let totalSum = 0
            let emptyField = false
            errorMsg.value = []
            var valueArr = selectedPartnerData.value.map(function(partData) {
                totalSum += Number(partData.partner_commission)
                if (partData.partner_commission == 0 || partData.partner_account_id == '')
                    emptyField = true
                return partData.partner_account_id
            });
            var isDuplicate = valueArr.some(function(partData, idx) {
                return valueArr.indexOf(partData) != idx
            });
            if (isDuplicate) errorMsg.value.push(_lang.duplicatedPartner)
            if (totalSum > 100) errorMsg.value.push(_lang.totalOfCommissionsMustEqual100)
            if (emptyField) errorMsg.value.push(_lang.feedback_messages.fillRequiredFields)
            if (errorMsg.value.length == 0)
                props.submitshares(selectedPartnerData.value, props.partnerdata.category, props.partnerdata.index)
        }

        const removeRecord = (index) => {
            selectedPartnerData.value.splice(index, 1);
        }
        const addRecord = () => {
            selectedPartnerData.value.push({
                partner_account_id: '',
                partner_name: '',
                partner_commission: '',
            });
        }
        const handlePartnerChange = (index, obj) => {
            selectedPartnerData.value[index]['partner_account_id'] = obj.id
            selectedPartnerData.value[index]['partner_name'] = obj.name + " - " + obj.currency
        }

        return {
            showModal,
            selectedPartnerData,
            errorMsg,
            handlePartnerChange,
            closeModal,
            submitModal,
            removeRecord,
            addRecord,
        };
    },
    template: `
    <p-dialog header="${_lang.partnersShares}" v-model:visible="showModal" :breakpoints="{'960px': '75vw'}" :style="{width: '50vw'}" position="top" :modal="true" @hide="closeModal">
        <div class="panel">
            <div class="p-fluid p-formgrid p-grid">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>` + _lang.partners + `</th>
                                <th>` + _lang.percentage + ` (%)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr  v-for="(partner1, index) in selectedPartnerData" :key="index">
                                <td>
                                    <partner-lookup :partnerchange="handlePartnerChange" :partnername="partner1.partner_name" :index="index"></partner-lookup>
                                </td>
                                <td>
                                    <input type="text" v-model="partner1.partner_commission" class="form-control" />
                                </td>
                                <td>
                                    <div class="row">
                                        <p class="cursor-pointer-click font-18" style="color: #828282;" @click="removeRecord(index)"><i class="icon-alignment fa fa-trash" aria-hidden="true"></i></p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan=3 class="text-center" >
                                    <p class="border-box-to-add" @click="addRecord()"><i class="fa-solid fa-circle-plus"></i> ` + _lang.addNewLine + `</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <ul class="list-group">
                    <li v-for="(msg, i) in errorMsg" :key="i" href="javascript:;" class="list-group-item text-danger">* {{msg}}</li>
                </ul>
            </div>
        <template #footer>
            <p-button label="${_lang.cancel}" icon="pi pi-times" @click="closeModal" class="p-button-text"></p-button>
            <p-button type="button" label="${_lang.save}" icon="pi pi-check" @click="submitModal"></p-button>
        </template>
    </p-dialog>
`,
};