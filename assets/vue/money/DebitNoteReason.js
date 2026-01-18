import Loader from 'Loader'
import ToolTip from 'ToolTip'
import Api from 'Api'
import CommonInit from 'CommonInit';

export default {
    name: 'DebitNoteReason',
    components: {
        'loader': Loader,
        'tool-tip': ToolTip,
        'p-inputtext': primevue.inputtext,
        'p-button': primevue.button,
    },
    setup() {
        const { onMounted, ref, watch } = Vue;
        const recordType = ref("");
        const showLoader = ref(true);
        const validatePage = ref(false);
        const isError = ref(false);
        const disableFields = ref(true);
        const loader = (action) => showLoader.value = action;

        const reasonObj = ref({
            name: "",
            fl1name: "",
            fl2name: "",
        });

        const initializeRecordObject = () => {
            if (recordEditIdGlobal > 0) {
                recordType.value = "edit"
                axios.get(Api.getApiBaseUrl("money") + '/debit_note_reasons/' + recordEditIdGlobal)
                    .then(response => {
                        if (response.data.debit_note_reason && response.data['debit_note_reason'].id) {
                            let recordResponse = response.data.debit_note_reason;
                            reasonObj.value.id = recordResponse.id;
                            reasonObj.value.name = recordResponse.name ?? '';
                            reasonObj.value.fl1name = recordResponse.fl1name ?? '';
                            reasonObj.value.fl2name = recordResponse.fl2name ?? '';
                            disableFields.value = false;
                            loader(false);
                        } else {
                            pinesMessageV2({ ty: 'error', m: response.data.message });
                            setTimeout(() => window.location = getBaseURL('money') + 'debit_note_reasons/', 700);
                        }
                    }).catch((error) => {
                        pinesMessageV2({ ty: 'error', m: error?.response?.data.message ?? _lang.feedback_messages.error });
                        setTimeout(() => window.location = getBaseURL('money') + 'debit_note_reasons/', 700);
                    });
            } else {
                recordType.value = "add";
                disableFields.value = false;
                loader(false);
            }
        };

        onMounted(() => {
            CommonInit.initialize(initializeRecordObject, getBaseURL('money') + 'debit_note_reasons/');
        });

        watch(reasonObj.value, () => {
            resetValidation();
        });

        const resetValidation = () => {
            isError.value = false;
            validatePage.value = false;
        };

        const isValidForm = () => {
            resetValidation();
            let obj = reasonObj.value;
            if (!obj.name) isError.value = true;
            //
            validatePage.value = true;
            if (isError.value)
                pinesMessageV2({ ty: 'warning', m: _lang.feedback_messages.fillRequiredFields });
            else
                return true;
            return false;
        };

        const saveRecord = () => {
            if (!isValidForm())
                return;
            //
            loader(true);
            disableFields.value = true;
            if (recordEditIdGlobal > 0) {
                axios.put(Api.getApiBaseUrl("money") + '/debit_note_reasons/' + reasonObj.value.id, reasonObj.value).then((response) => {
                    disableFields.value = false;
                    loader(false);
                    pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                    validatePage.value = false;
                }).catch((error) => {
                    disableFields.value = false;
                    loader(false);
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                });
            } else {
                axios.post(Api.getApiBaseUrl("money") + '/debit_note_reasons', Api.toFormData(reasonObj.value), Api.getInitialHeaders("multipart/form-data")).then((response) => {
                    if (response.data.debit_note_reason && response.data.debit_note_reason.id) {
                        pinesMessageV2({ ty: 'success', m: _lang.recordAddedSuccessfully });
                        setTimeout(() => window.location = getBaseURL('money') + 'debit_note_reasons/edit/' + response.data.debit_note_reason.id, 700);
                    } else {
                        disableFields.value = false;
                        pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
                    }
                }).catch((error) => {
                    disableFields.value = false;
                    loader(false);
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                });
            }
        };

        const deleteRecord = () => {
            if (!confirm(_lang.money.confirmationDeleteRecord))
                return;
            loader(true);
            axios.delete(Api.getApiBaseUrl("money") + '/debit_note_reasons/' + reasonObj.value.id)
                .then((response) => {
                    pinesMessageV2({ ty: 'success', m: response.data.message });
                    setTimeout(() => window.location = getBaseURL('money') + 'debit_note_reasons/', 700);
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                    loader(false)
                });
        }

        return {
            showLoader,
            validatePage,
            disableFields,
            recordType,
            reasonObj,
            saveRecord,
            deleteRecord,
        };
    },
};
