import Api from 'Api';
import CommonGrid from 'CommonGrid';

export default {
    name: 'CreditNoteReasonGrid',
    components: {
        'common-grid': CommonGrid
    },
    setup(props, context) {
        const { ref, watch } = Vue;
        const { FilterMatchMode, FilterOperator } = primevue.api;

        const commonGridRef = ref();
        let selectedRecordId = null;
        const title = langCreditNoteReasons;
        const initErrorUrl = getBaseURL('money') + 'money_dashboards/';
        const searchListUrl = `${Api.getApiBaseUrl("money")}/credit_note_reasons`;
        const addRecordUrl = `${getBaseURL("money")}credit_note_reasons/add`;
        const responseResourceName = 'credit_note_reason';

        const columns = ref([
            { field: '', header: '', type: 'actions' },
            { field: 'name', header: langCreditNoteReason + ' (' + langSystemLanguage + ')', frozen: true, type: 'link', link: { path: 'modules/money/credit_note_reasons/edit/', key: 'id' } },
            { field: 'fl1name', header: langCreditNoteReason + ' (' + langSystemForeignLanguage_1 + ')' },
            { field: 'fl2name', header: langCreditNoteReason + ' (' + langSystemForeignLanguage_2 + ')' },
        ]);
        const filterableColumns = ref(['name', 'fl1name', 'fl2name']);
        const globalFilterFields = ref(['name', 'fl1name', 'fl2name']);
        const filters = {
            name: { constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            fl1name: { constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            fl2name: { constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        };
        const optionsMenuItems = ref([
            {
                label: _lang.edit,
                icon: 'pi pi-pencil',
                command: () => { window.location = `${getBaseURL('money')}credit_note_reasons/edit/${selectedRecordId}`; }
            }, {
                label: _lang.delete,
                icon: 'pi pi-trash',
                command: () => { deleteCreditNoteReason(selectedRecordId); }
            }
        ]);

        function updateSelectedRecordId(id) { selectedRecordId = id; }

        function deleteCreditNoteReason(id) {
            commonGridRef.value.deleteRecord(`${Api.getApiBaseUrl("money")}/credit_note_reasons/${id}`);
        }

        return {
            commonGridRef, globalFilterFields, columns, searchListUrl, addRecordUrl, filterableColumns, responseResourceName,
            title, filters, initErrorUrl, optionsMenuItems, updateSelectedRecordId
        }
    },
    template: `
        <common-grid ref="commonGridRef" :globalFilterFields="globalFilterFields" :columns="columns" :searchListUrl="searchListUrl"
            :filterableColumns="filterableColumns" :responseResourceName="responseResourceName" :title="title" :filters="filters" 
            :addRecordUrl="addRecordUrl" :initErrorUrl="initErrorUrl" @updateSelectedRecordId="updateSelectedRecordId" >
        </common-grid>
    `,
};