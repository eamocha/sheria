import Loader from 'Loader'
import ToolTip from 'ToolTip'
import ClientLookup from 'ClientLookup'
import PartnerShares from 'PartnerShares'
import InvoiceDetails from 'InvoiceDetails'
import RelatedMatter from 'RelatedMatter'
import EditInvoiceNb from 'EditInvoiceNb'
import EditExchangeRate from 'EditExchangeRate'
import TaxNew from 'TaxNew'
import ItemNew from 'ItemNew'
import AccountAddress from 'AccountAddress'
import EditInvoiceTemplate from 'EditInvoiceTemplate'
import Api from 'Api'

export default {
    name: 'Invoice',
    components: {
        'loader': Loader,
        'client-lookup': ClientLookup,
        'invoice-details': InvoiceDetails,
        'related-matter': RelatedMatter,
        'partner-shares': PartnerShares,
        'invoice-nb': EditInvoiceNb,
        'tax-new': TaxNew,
        'item-new': ItemNew,
        'exchange-rate': EditExchangeRate,
        'tool-tip': ToolTip,
        'p-inputtext': primevue.inputtext,
        'p-button': primevue.button,
        'p-dropdown': primevue.dropdown,
        "p-fileupload": primevue.fileupload,
        "p-panel": primevue.panel,
        "ckeditor": CKEditor.component,
        'account-address': AccountAddress,
        'p-confirmdialog': primevue.confirmdialog,
        'invoice-template': EditInvoiceTemplate
    },
    setup() {
        const { onMounted, ref, watch } = Vue;
        const { useConfirm } = primevue.useconfirm;
        const confirm = useConfirm();
        const defaultTemplate = ref({
            id: "",
            name: _lang.money.defaultInvoiceTemplate
        });
        const invoiceType = ref("")
        const invoice = ref({
            is_debit_note: isDebitNoteGlobal,
            original_invoice_id: "",
            original_invoice_voucher_id: "",
            original_invoice_number: "",
            user_id: "",
            organization_id: "",
            account_id: 0,
            client_id: 0,
            client_name: "",
            invoice_type_id: "",
            related_quote_id: "",
            currency_id: organizationCurrencyIDGlobal,
            currency: "?",
            exchange_rate: 1,
            prefix: "",
            reference_number: "",
            suffix: "",
            purchase_order: "",
            invoice_reference: null,
            bill_to: "",
            invoice_date: null,
            due_on: null,
            transaction_type_id: '',
            payment_method_id: '',
            debit_note_reason_id: '',
            invoice_template_id: '',
            invoice_template_name: defaultTemplate.value.name,
            term_id: "",
            term_days_nb: 0,
            notes: "",
            description: "",
            paid_status: "",
            display_item_date: 1,
            display_item_quantity: 1,
            display_tax: 1,
            display_discount: 'no',
            discount_amount: 0,
            discount_id: '',
            discount_percentage: 0,
            discount_value_type: 'percentage',
            lines_total_discount: 0,
            lines_total_subtotal: 0,
            lines_total_tax: 0,
            lines_totals: 0,
            total: 0,
            invoice_reminder: {
                reminder_time: null,
                reminder_time_type: "day",
                reminder_type: null
            },
            invoice_details: [],
            invoice_expenses: [],
            invoice_time_logs: [],
            related_matters: [],
            is_skip_account_fields_checking: false
        });
        const invoiceReminder = ref({
            reminder_time: notifyMeDefaultGlobal,
            reminder_time_type: "day",
            reminder_type: "popup"
        })
        const invoiceSummary = ref({
            subTotal: 0,
            discount: 0,
            subTotalWithDiscount: 0,
            tax: 0,
            total: 0,
            totalValue: 0,
            subTotalEntity: 0,
            discountEntity: 0,
            subTotalWithDiscountEntity: 0,
            taxEntity: 0,
            totalEntity: 0,
            related_credit_notes: [],
            related_debit_notes: [],
        })
        const entity = ref({
            id: organizationIDGlobal,
            currency_code: organizationCurrencyGlobal,
            currency_id: organizationCurrencyIDGlobal,
        })
        const invDoc = ref("")

        const showLoader = ref(true)
        const showRelatedMatterModal = ref(false)
        const showPartnerSharesModal = ref(false)
        const showEditInvoiceNbModal = ref(false)
        const showEditInvoiceTemplateModal = ref(false)
        const showNewTaxModal = ref(false)
        const showNewItemModal = ref(false)
        const showEditExRateModal = ref(false)
        const showAccountAddressModal = ref(false)

        const relatedMatters = ref([])
        const notesListArr = ref(notesListGlobal)
        const transactionTypesArr = ref(transactionTypesGlobal)
        const paymentMethodsArr = ref(paymentMethodsGlobal)
        const termsArr = ref(termsGlobal)
        const PartnerData = ref(null)
        const showNotify = ref(false)
        const validatePage = ref(false)

        const errorMsg = ref([])
        const disableFields = ref(false)
        const isAddingFromQuote = ref(false);
        const rowDetails = ref()
        const eInvoicingKey = ref('');
        const isUserChangeInvoiceType = ref(false);

        const taxesCodesList = ref([]);
        const taxesList = ref([]);
        const itemsList = ref([]);
        const invoiceTypesList = ref({});
        const debitNoteReasonsList = ref({});
        const clientAccountResource = ref({});
        const invoiceRequiredFields = ref([]);
        const accountRequiredFields = ref([]);
        const invoiceAdditionalFields = ref([]);
        const personAdditionalIdTypesList = ref({});
        const companyAdditionalIdTypesList = ref({});
        const templateIdsList = ref({});
        const countryList = ref([]);

        const initialize = () => {
            initApiAccessToken(function () {
                initializeInvoiceObject(invoiceEditIdGlobal);
            }, getBaseURL('money') + 'vouchers/invoices_list/');
        };
        const initializeInvoiceObject = (invoiceId) => {
            prepareInvoiceData();
            if (invoiceId > 0 || createInvoiceFromQuoteId) {
                invoiceType.value = (isCreateDebitNoteGlobal || createInvoiceFromQuoteId ? "add" : "edit");
                let dataUrl = Api.getApiBaseUrl("money") + '/invoices?voucher_header_id=' + invoiceId + '&organization_id=' + entity.value.id;
                if (createInvoiceFromQuoteId)
                    dataUrl = Api.getApiBaseUrl("money") + '/quotes/' + createInvoiceFromQuoteId + '?organization_id=' + entity.value.id;
                axios.get(dataUrl, Api.getInitialHeaders())
                    .then(response => {
                        if ((!createInvoiceFromQuoteId && (!response.data.invoices || response.data['invoices'].length <= 0))
                            || (createInvoiceFromQuoteId && !response.data.quote)) {
                            pinesMessageV2({ ty: 'error', m: response.data.message });
                            setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoices_list/', 700);
                            return;
                        }
                        let recordResponse;
                        if (createInvoiceFromQuoteId) {
                            isAddingFromQuote.value = true;
                            let quoteResponse = response.data.quote;
                            recordResponse = quoteResponse;

                            initEmptyInvoiceObj();
                            invoice.value.user_id = userIDGlobal;
                            invoice.value.organization_id = entity.value.id;
                            invoice.value.related_quote_id = quoteResponse.voucher_header_id;
                            invoice.value.purchase_order = quoteResponse.purchase_order ?? '';
                            invoice.value.invoice_reference = quoteResponse.quote_number;
                            invoice.value.bill_to = quoteResponse.bill_to;
                            invoice.value.invoice_date = moment(quoteResponse.quote_date).locale('en').format('YYYY-MM-DD');
                            if(quoteResponse.term_id){
                                invoice.value.term_id = quoteResponse.term_id;
                                onTermChange(); //to set term days nb, update invoice due date,..
                            }
                            invoice.value.notes = quoteResponse.notes ?? "";
                            invoice.value.description = quoteResponse.description;
                            invoice.value.total = quoteResponse.total;
                            //if we set it as draft then the time log status must be set to 'to-invoice' while they already set
                            //to 'invoiced' when the quote was approved. so we set it as open and disable change it to draft.
                            invoice.value.paid_status = 'open';

                            invoice.value.invoice_details = quoteResponse.quote_details;
                            invoice.value.invoice_expenses = quoteResponse.quote_expenses;
                            invoice.value.invoice_time_logs = quoteResponse.quote_time_logs;

                            ['invoice_details', 'invoice_expenses', 'invoice_time_logs'].map((itemType) => {
                                invoice.value[itemType].map((itemRow) => {
                                    itemRow.id = null;
                                    itemRow.item_title = itemRow.item;
                                    itemRow.discount_type = (accounting.toNumber(itemRow.discount_amount) > 0 ? 'amount' : 'percentage');
                                    if (itemRow.sub_item_id)
                                        itemRow.item_id = itemRow.sub_item_id;
                                    //if discount type is percent then make round to remove the .0 zero decimals
                                    if (itemRow.discount_type != 'amount')
                                        itemRow.discount_percentage = accounting.roundNumber(accounting.toNumber(itemRow.discount_percentage));
                                    itemRow.partner_shares = [];
                                    if (itemType == 'invoice_time_logs') {
                                        itemRow.timelog_data.map((timeLog) => {
                                            timeLog.id = null;
                                            timeLog.description = timeLog.description ?? '';
                                        });
                                    }
                                });
                            });
                            invoice.value.related_matters = quoteResponse.related_matters;
                            invoice.value.related_matters.map((matterRow) => {
                                matterRow.id = null;
                            });
                            recordResponse.display_discount = (quoteResponse.display_discount == '1' ? 'item_level' : 'no');
                            disableFields.value = false;
                        }
                        else {
                            let invoiceResponse = response.data.invoices[0];
                            recordResponse = invoiceResponse;
                            if (isCreateDebitNoteGlobal) {
                                invoiceEditIdGlobal = 0;    //free it since we are in add not edit
                                disableFields.value = false;
                                initEmptyInvoiceObj();
                                invoice.value.original_invoice_id = invoiceResponse.id;
                                invoice.value.original_invoice_voucher_id = invoiceResponse.voucher_header_id;
                                invoice.value.original_invoice_number = invoiceResponse.invoice_number;
                                invoice.value.invoice_type_id = invoiceResponse.invoice_type_id ?? "";
                                invoice.value.transaction_type_id = invoiceResponse.transaction_type_id ?? "";
                                //if client not related to term it will kept as first term selected from inside initEmptyInvoiceObj 
                                if(invoiceResponse.account.client.term_id){
                                    invoice.value.term_id = invoiceResponse.account.client.term_id;
                                    onTermChange(); //to set term days nb, update invoice due date,..
                                }
                                if (invoiceResponse.account.client.discount_percentage > 0) {
                                    invoice.value.discount_percentage = invoiceResponse.account.client.discount_percentage;
                                    invoice.value.discount_value_type = 'percentage';
                                }
                                recordResponse.display_discount = activateDiscountGlobal;
                            }
                            else {   //for invoice/debit note edit only
                                invoice.value.id = invoiceResponse.id
                                invoice.value.original_invoice_id = invoiceResponse.original_invoice_id ?? ""
                                invoice.value.original_invoice_voucher_id = invoiceResponse.original_invoice ? invoiceResponse.original_invoice.voucher_header_id : '';
                                invoice.value.original_invoice_number = invoiceResponse.original_invoice ? invoiceResponse.original_invoice.invoice_number : '';
                                invoice.value.user_id = userIDGlobal
                                invoice.value.organization_id = entity.value.id
                                invoice.value.invoice_type_id = invoiceResponse.invoice_type_id ?? "";
                                invoice.value.related_quote_id = invoiceResponse.related_quote_id
                                invoice.value.saved_exchange_rate = invoiceResponse.paid_status === "draft" ? null : invoiceResponse.exchange_rate == null || invoiceResponse.exchange_rate == '0' ? null : accounting.toNumber(invoiceResponse.exchange_rate).toString()
                                invoice.value.prefix = invoiceResponse.prefix
                                invoice.value.reference_number = invoiceResponse.reference_number
                                invoice.value.suffix = invoiceResponse.suffix ?? ''
                                invoice.value.purchase_order = invoiceResponse.purchase_order
                                invoice.value.invoice_reference = invoiceResponse.invoice_reference
                                invoice.value.bill_to = invoiceResponse.bill_to
                                invoice.value.invoice_date = moment(invoiceResponse.invoice_date).locale('en').format('YYYY-MM-DD')
                                invoice.value.due_on = moment(invoiceResponse.due_on).locale('en').format('YYYY-MM-DD')
                                invoice.value.transaction_type_id = invoiceResponse.transaction_type_id ?? ""
                                invoice.value.payment_method_id = invoiceResponse.payment_method_id ?? ""
                                invoice.value.debit_note_reason_id = invoiceResponse.debit_note_reason_id ?? ""
                                invoice.value.invoice_template_id = invoiceResponse.invoice_template_id ?? "";
                                invoice.value.invoice_template_name = invoiceResponse.invoice_template_name ?? defaultTemplate.value.name;
                                invoice.value.term_id = invoiceResponse.term_id;
                                invoice.value.term = invoiceResponse.terms;
                                invoice.value.term_days_nb = invoiceResponse.term_days_nb;
                                invoice.value.notes = invoiceResponse.notes ?? ""
                                invoice.value.description = invoiceResponse.description
                                invoice.value.paid_status = invoiceResponse.paid_status
                                invoice.value.discount_id = invoiceResponse.discount_id
                                //if discount type is percent then make round to remove the .00000 zero decimals
                                invoice.value.discount_percentage = (invoiceResponse.discount_value_type == 'amount' ? invoiceResponse.discount_percentage : accounting.roundNumber(invoiceResponse.discount_percentage));
                                invoice.value.discount_amount = (invoiceResponse.discount_value_type == 'amount' ? accounting.roundNumber(invoiceResponse.discount_amount) : invoiceResponse.discount_amount);
                                invoice.value.discount_value_type = invoiceResponse.discount_value_type;
                                invoice.value.lines_total_discount = invoiceResponse.lines_total_discount;
                                invoice.value.lines_total_subtotal = invoiceResponse.lines_total_subtotal;
                                invoice.value.lines_total_tax = invoiceResponse.lines_total_tax;
                                invoice.value.lines_totals = invoiceResponse.lines_totals;
                                invoice.value.total = invoiceResponse.total;
                                if (invoiceResponse.invoice_reminder) {
                                    invoice.value.invoice_reminder.id = invoiceResponse?.invoice_reminder?.id
                                    invoice.value.invoice_reminder.reminder_time = invoiceResponse?.invoice_reminder?.reminder_time
                                    invoice.value.invoice_reminder.reminder_time_type = invoiceResponse?.invoice_reminder?.reminder_time_type
                                    invoice.value.invoice_reminder.reminder_type = invoiceResponse?.invoice_reminder?.reminder_type
                                }
                                invoice.value.invoice_details = invoiceResponse.invoice_details;
                                invoice.value.invoice_expenses = invoiceResponse.invoice_expenses;
                                invoice.value.invoice_time_logs = invoiceResponse.invoice_time_logs;

                                ['invoice_details', 'invoice_expenses', 'invoice_time_logs'].map((itemType) => {
                                    invoice.value[itemType].map((item) => {
                                        //if discount type is percent then make round to remove the .0 zero decimals
                                        if (item.discount_type != 'amount')
                                            item.discount_percentage = accounting.roundNumber(item.discount_percentage);
                                    });
                                });

                                invoice.value.related_matters = invoiceResponse.related_matters;
                                invoiceSummary.value.related_credit_notes = invoiceResponse.credit_notes;
                                invoiceSummary.value.related_debit_notes = invoiceResponse.debit_notes;

                                invoiceReminder.value.reminder_time = invoice.value.invoice_reminder.reminder_time;
                                invoiceReminder.value.reminder_time_type = invoice.value.invoice_reminder.reminder_time_type;
                                invoiceReminder.value.reminder_type = invoice.value.invoice_reminder.reminder_type;
                                disableFields.value = (invoice.value.paid_status == "draft" ? false : true);
                            }
                        }
                        //common for invoice/debit note edit and for add debit note also, and quote
                        clientAccountResource.value = recordResponse.account;
                        invoice.value.account_id = recordResponse.account.id;
                        invoice.value.client_id = recordResponse.account.model_id;
                        invoice.value.client_name = recordResponse.account.name + ' - ' + recordResponse.account.currency;
                        invoice.value.currency_id = recordResponse.account.currency_id;
                        invoice.value.currency = recordResponse.account.currency;
                        invoice.value.exchange_rate = (ratesListGlobal[recordResponse.account.currency_id] * 1);
                        invoice.value.display_item_date = recordResponse.display_item_date;
                        invoice.value.display_item_quantity = recordResponse.display_item_quantity ?? 1;
                        invoice.value.display_discount = recordResponse.display_discount;
                        invoice.value.display_tax = recordResponse.display_tax;

                        //we use it specially for create invoice from quote to calculate item records
                        //calc. fields like total, tax amount,.. which will also cal. header totals
                        isInvoiceDataLoaded = true;
                        loader(false);
                    }).catch((error) => {
                        pinesMessageV2({ ty: 'error', m: error?.response?.data.message ?? _lang.feedback_messages.error });
                        if (error?.response?.status == 401) 
                            localStorage.removeItem('api-access-token');
                        setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoices_list/', 700);
                    });
            } else {
                disableFields.value = false;
                initEmptyInvoiceObj();
                clientAccountResource.value = {};
                isInvoiceDataLoaded = true;
                loader(false);
            }
        };
        const initEmptyInvoiceObj = () => {
            invoiceType.value = "add"

            invoice.value.is_debit_note = isDebitNoteGlobal;
            invoice.value.original_invoice_id = '';
            invoice.value.original_invoice_voucher_id = '';
            invoice.value.original_invoice_number = '';

            invoice.value.user_id = userIDGlobal
            invoice.value.organization_id = entity.value.id
            invoice.value.account_id = 0
            invoice.value.client_id = 0
            invoice.value.invoice_type_id = ''
            invoice.value.related_quote_id = ''
            invoice.value.client_name = ''
            invoice.value.currency_id = entity.value.currency_id
            invoice.value.currency = '?'
            invoice.value.exchange_rate = 1
            invoice.value.prefix = invoiceNumberPrefixGlobal
            invoice.value.reference_number = autoGenerateInvoiceNumberGlobal
            invoice.value.suffix = ''
            invoice.value.purchase_order = ''
            invoice.value.invoice_reference = ''
            invoice.value.bill_to = ''
            invoice.value.invoice_date = moment().locale('en').format('YYYY-MM-DD')
            invoice.value.due_on = moment().locale('en').format('YYYY-MM-DD')
            invoice.value.transaction_type_id = ''
            invoice.value.payment_method_id = ''
            invoice.value.debit_note_reason_id = ''
            invoice.value.invoice_template_id = '';
            invoice.value.invoice_template_name = defaultTemplate.value.name;
            invoice.value.term_id = termsArr.value.length ? termsArr.value[0].id : '';
            onTermChange(); //to set term days nb, update invoice due date,..
            invoice.value.notes = ''
            invoice.value.description = ''
            invoice.value.paid_status = eInvoicingGlobal ? 'draft' : 'open'
            invoice.value.display_item_date = displayItemDateGlobal
            invoice.value.display_item_quantity = 1
            invoice.value.display_discount = activateDiscountGlobal
            invoice.value.discount_amount = 0
            invoice.value.discount_id = ''
            invoice.value.discount_percentage = 0
            invoice.value.discount_value_type = 'percentage'
            invoice.value.lines_total_discount = 0;
            invoice.value.lines_total_subtotal = 0;
            invoice.value.lines_total_tax = 0;
            invoice.value.lines_totals = 0;
            invoice.value.total = 0;
            invoice.value.display_tax = activateTaxGlobal
            invoice.value.invoice_reminder.reminder_time = null
            invoice.value.invoice_reminder.reminder_time_type = 'day'
            invoice.value.invoice_reminder.reminder_type = null
            invoice.value.invoice_details = []
            invoice.value.invoice_expenses = []
            invoice.value.invoice_time_logs = []
            invoice.value.related_matters = []
            invoice.value.is_skip_account_fields_checking = false;
        };
        const prepareInvoiceData = () => {
            //
            for (let index in countriesListGlobal)
                countryList.value.push({ id: index, name: countriesListGlobal[index] });
            //
            axios.get(Api.getApiBaseUrl("money") + '/invoices/preparedata?organization_id=' + entity.value.id + '&is_debit_note=' + isDebitNoteGlobal, Api.getInitialHeaders())
                .then(response => {
                    eInvoicingKey.value = (response.data.e_invoicing_key ?? '');
                    if (response.data.taxes_codes)
                        taxesCodesList.value = response.data.taxes_codes;
                    if (response.data.taxes) {
                        response.data.taxes.map((tax, index) => {
                            taxesList.value[index] = tax
                            taxesList.value[index]['label'] = tax['name'] + ' (' + tax['percentage'] + "%)"
                        });
                    }
                    if (response.data.items)
                        buildItemsList(response.data.items);
                    if (response.data.invoice_types) {
                        response.data.invoice_types.map((type, index) => {
                            invoiceTypesList.value[type['id']] = type['display_name'];
                        });
                    }
                    if (response.data.debit_note_reasons) {
                        response.data.debit_note_reasons.map((reason, index) => {
                            debitNoteReasonsList.value[reason['id']] = reason['display_name'];
                        });
                    }
                    if (response.data.person_additional_id_types)
                        personAdditionalIdTypesList.value = response.data.person_additional_id_types;
                    if (response.data.company_additional_id_types)
                        companyAdditionalIdTypesList.value = response.data.company_additional_id_types;
                    if (response.data.additiona_fields) {
                        for (const field in response.data.additiona_fields.invoice) {
                            invoiceAdditionalFields.value.push(field);
                            if (response.data.additiona_fields.invoice[field].indexOf('required') != -1)
                                invoiceRequiredFields.value.push(field);
                        }
                        for (const field in response.data.additiona_fields.account) {
                            if (response.data.additiona_fields.account[field].indexOf('required') != -1)
                                accountRequiredFields.value.push(field);
                        }
                    }
                    if (response.data.invoice_templates)
                        templateIdsList.value = response.data.invoice_templates;
                    if (response.data.default_template){
                        defaultTemplate.value = response.data.default_template;
                        //maybe invoice obj. already filled then we get the data here, so we update the name
                        if(invoice.value.invoice_template_id == '')
                            invoice.value.invoice_template_name = defaultTemplate.value.name;
                    }
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                    if (error?.response?.status == 401) 
                        localStorage.removeItem('api-access-token');
                    setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoices_list/', 700);
                });
        };
        const buildItemsList = (items) => {
            itemsList.value = items.reduce((flat, constructor) => {
                return flat.concat({
                    id: constructor.id,
                    account_id: constructor.account_id,
                    currency: constructor.currency,
                    currency_id: constructor.currency_id,
                    description: constructor.description,
                    exchange_rate: constructor.exchange_rate,
                    name: langGlobal === "fl1" ? constructor.fl1_name : langGlobal === "fl2" ? constructor.fl2_name : constructor.name,
                    price: constructor.price,
                    tax_id: constructor.tax_id,
                    isParent: true,
                }).concat(constructor.invoice_sub_items.map(sub_item => ({
                    id: sub_item.id,
                    account_id: sub_item.account_id,
                    currency: sub_item.currency,
                    currency_id: sub_item.currency_id,
                    description: sub_item.description,
                    exchange_rate: sub_item.exchange_rate,
                    name: sub_item.name,
                    name: langGlobal === "fl1" ? sub_item.fl1_name : langGlobal === "fl2" ? sub_item.fl2_name : sub_item.name,
                    price: sub_item.price,
                    tax_id: sub_item.tax_id,
                    parent_id: sub_item.parent_id,
                })))
            }, []);
        };
        onMounted(() => { initialize() })
        const invoiceStatusChange = (e) => {
            invoice.value.paid_status = (e.target.checked) ? 'open' : 'draft'
        }

        watch(invoice.value, () => {
            resetValidation()
            let subTotalVariable = 0;
            let lineDiscountVariable = 0;
            let taxVariable = 0;
            let subTotalWithDiscount = 0;
            let linesTotal = 0;

            ['invoice_details', 'invoice_expenses', 'invoice_time_logs'].map((itemType) => {
                invoice.value[itemType].map((item) => {
                    subTotalVariable = accounting.addNumbers(subTotalVariable, item.line_sub_total);
                    lineDiscountVariable = accounting.addNumbers(lineDiscountVariable, item.discount_amount);
                    taxVariable = accounting.addNumbers(taxVariable, item.tax_amount);
                    linesTotal = accounting.addNumbers(linesTotal, item.total);
                });
            })

            let tempAmountForDiscount = null;
            if (invoice.value.display_discount == 'invoice_level_before_tax' || invoice.value.display_discount == 'both_item_before_level') {
                tempAmountForDiscount = accounting.subtractNumbers(subTotalVariable, lineDiscountVariable);
            } else if (invoice.value.display_discount == 'invoice_level_after_tax' || invoice.value.display_discount == 'both_item_after_level') {
                tempAmountForDiscount = accounting.subtractNumbers(subTotalVariable, lineDiscountVariable);
                tempAmountForDiscount = accounting.addNumbers(tempAmountForDiscount, taxVariable);
            } else {
                invoice.value.discount_value_type = 'percentage';
                invoice.value.discount_amount = 0;
                invoice.value.discount_percentage = 0;
            }
            if (tempAmountForDiscount !== null) {
                if (invoice.value.discount_value_type == 'amount' && invoice.value.discount_amount > 0) {
                    invoice.value.discount_percentage = accounting.multiplyNumbers((accounting.toNumber(invoice.value.discount_amount) / tempAmountForDiscount), 100);
                } else if (invoice.value.discount_value_type == 'percentage' && invoice.value.discount_percentage > 0) {
                    invoice.value.discount_amount = accounting.roundNumber(accounting.multiplyNumbers(invoice.value.discount_percentage, tempAmountForDiscount) / 100);
                }
                if (invoice.value.discount_percentage <= 0 || invoice.value.discount_percentage > 100 || invoice.value.discount_amount < 0) {
                    invoice.value.discount_amount = 0;
                    invoice.value.discount_percentage = 0;
                }
            }

            let discountVariable = accounting.addNumbers(lineDiscountVariable, invoice.value.discount_amount);
            subTotalWithDiscount = accounting.subtractNumbers(subTotalVariable, discountVariable);
            let finalAmount = accounting.addNumbers(subTotalWithDiscount, taxVariable);

            invoice.value.lines_total_discount = lineDiscountVariable;    //sum lines discount
            invoice.value.lines_total_subtotal = subTotalVariable;    //just sum lines (qty * unit price), before any discount and before tax
            invoice.value.lines_total_tax = taxVariable; //sum lines taxes 
            invoice.value.lines_totals = linesTotal;    //sum lines 'total' column
            invoice.value.total = finalAmount;

            invoiceSummary.value.subTotal = accounting.formatMoney(accounting.roundNumber(subTotalVariable), "")
            invoiceSummary.value.discount = accounting.formatMoney(accounting.roundNumber(discountVariable), "")
            invoiceSummary.value.subTotalWithDiscount = accounting.formatMoney(accounting.roundNumber(subTotalWithDiscount), "")
            invoiceSummary.value.tax = accounting.formatMoney(accounting.roundNumber(taxVariable), "")
            invoiceSummary.value.total = accounting.formatMoney(accounting.roundNumber(finalAmount), "")
            invoiceSummary.value.totalValue = accounting.roundNumber(finalAmount)
            invoiceSummary.value.subTotalEntity = accounting.formatMoney(accounting.roundNumber(subTotalVariable * invoice.value.exchange_rate), "")
            invoiceSummary.value.discountEntity = accounting.formatMoney(accounting.roundNumber(discountVariable * invoice.value.exchange_rate), "")
            invoiceSummary.value.subTotalWithDiscountEntity = accounting.formatMoney(accounting.roundNumber(subTotalWithDiscount * invoice.value.exchange_rate), "")
            invoiceSummary.value.taxEntity = accounting.formatMoney(accounting.roundNumber(taxVariable * invoice.value.exchange_rate), "")
            invoiceSummary.value.totalEntity = accounting.formatMoney(accounting.roundNumber((finalAmount) * invoice.value.exchange_rate), "")

        });

        const checkUpdateEInvoicingImplementation = () => {
            //if invoice type not changed manual by user and invoice is less than 1000 SAR (entity currency) set invoie type 
            //as simplified[2] else standard[1]. for debit note it take automatic the same original invoice type
            //we call it in save not on watch since in watch will change the type even if not change totals yet 
            if (!isUserChangeInvoiceType.value && eInvoicingKey.value == 'saudi' && invoice.value.is_debit_note != '1') {
                let totalEntityCurrency = accounting.roundNumber((invoice.value.total) * invoice.value.exchange_rate);
                if (totalEntityCurrency < 1000)
                    invoice.value.invoice_type_id = 2;
                else
                    invoice.value.invoice_type_id = 1;
            }
        }
        const isAccountFieldsChecking = () => {
            return (!invoice.value.is_skip_account_fields_checking && accountRequiredFields.value
                && eInvoicingKey.value == 'saudi' && invoice.value.invoice_type_id == 1);
        }
        const resetValidation = () => {
            errorMsg.value = []
            validatePage.value = false
        }
        const validateForm = () => {
            resetValidation()
            let obj = invoice.value
            if (invoiceSummary.value.totalValue < 0) {
                pinesMessageV2({ ty: 'warning', m: "Total can't be negative." });
                errorMsg.value.push("Total can't be negative.");
                return false;
            }
            if (!obj.account_id) errorMsg.value.push("Client Name is required")
            if (!obj.prefix) errorMsg.value.push("Prefix is required")
            if (!obj.reference_number || !obj.prefix) errorMsg.value.push("Reference Number is required")
            if (!obj.invoice_date) errorMsg.value.push("Date is required")
            if (!obj.due_on) errorMsg.value.push("Due Date is required")
            if (!obj.term_id) errorMsg.value.push("Terms is required")
            if (obj.invoice_details.length == 0 && obj.invoice_expenses.length == 0 && obj.invoice_time_logs.length == 0)
                errorMsg.value.push("Fill item Record")
            obj.invoice_details.map((item) => {
                if (!item.account_id || !item.item_id) errorMsg.value.push("Fill items")
                if (!item.item_date && invoice.value.display_item_date == '1') errorMsg.value.push("Fill date")
                if (item.unit_price < 0 || item.unit_price == null) errorMsg.value.push("Fill Unit Price For Items")
                if (item.quantity < 0 || item.quantity == null) errorMsg.value.push("Fill Quantity For Items")
            });
            obj.invoice_expenses.map((expense) => {
                if (expense.unit_price < 0 || expense.unit_price == null) errorMsg.value.push("Fill Unit Price For Expenses")
                if (expense.quantity < 0 || expense.quantity == null) errorMsg.value.push("Fill Quantity For Expenses")
            });
            obj.invoice_time_logs.map((timelog) => {
                if (timelog.unit_price < 0 || timelog.unit_price == null) errorMsg.value.push("Fill Unit Price For Time Logs")
                if (timelog.quantity < 0 || timelog.quantity == null) errorMsg.value.push("Fill Quantity For Time Logs")
            });
            if (invoiceRequiredFields.value) {
                invoiceRequiredFields.value.forEach(function (field) {
                    if (!obj[field])
                        errorMsg.value.push(field + " is required");
                });
            }
            if (isAccountFieldsChecking() && Object.keys(clientAccountResource.value).length > 0) {
                let isOpenAccountAddress = false;
                accountRequiredFields.value.forEach(function (field) {
                    if (!clientAccountResource.value[field]) {
                        errorMsg.value.push(field + " is required");
                        isOpenAccountAddress = true;
                    }
                });
                if (isOpenAccountAddress)
                    openAccountAddressModal();
            }
            //
            if (errorMsg.value.length > 0) {
                pinesMessageV2({ ty: 'warning', m: _lang.feedback_messages.fillRequiredFields });
            }
            validatePage.value = true;
        }
        const handleSaveInvoice = () => {
            checkUpdateEInvoicingImplementation();
            validateForm()
            if (errorMsg.value.length == 0) {
                if (eInvoicingGlobal && invoice.value.paid_status === 'open') {
                    confirm.require({
                        message: _lang.money.confirmationSaveInvoice,
                        header: _lang.confirmation,
                        icon: 'pi pi-exclamation-triangle',
                        acceptClass: 'p-button-warning',
                        acceptLabel: _lang.yes,
                        acceptIcon: 'pi pi-check',
                        rejectClass: 'p-button-secondary',
                        rejectLabel: _lang.no,
                        rejectIcon: 'pi pi-times',
                        accept: () => {
                            saveInvoice();
                        }
                    });
                } else {
                    saveInvoice();
                }
            }
        }
        const saveInvoice = () => {
            invoice.value.invoice_attachment = (invDoc.value !== "" ? invDoc.value : null);
            loader(true)
            if (invoiceEditIdGlobal > 0) {
                axios.put(Api.getApiBaseUrl("money") + '/' + (invoice.value.is_debit_note == '1' ? 'debit_notes' : 'invoices') + '/' + invoice.value.id, invoice.value, Api.getInitialHeaders()).then((response) => {
                    pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                    loader(false)
                    validatePage.value = false
                }).catch((error) => {
                    loader(false)
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                });
            } else {
                disableFields.value = true
                axios.post(Api.getApiBaseUrl("money") + '/' + (invoice.value.is_debit_note == '1' ? 'debit_notes' : 'invoices'), Api.toFormData(invoice.value), Api.getInitialHeaders("multipart/form-data")).then((response) => {
                    if (response.data.invoice && response.data.invoice.voucher_header_id) {
                        pinesMessageV2({ ty: 'success', m: _lang.record_added_successfull.sprintf([_lang.invoice]) });
                        setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoice_edit/' + response.data.invoice.voucher_header_id, 700);
                    } else {
                        disableFields.value = false
                        pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
                    }
                }).catch((error) => {
                    disableFields.value = false
                    loader(false)
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                });
            }
        }
        const openRelatedMatterModal = () => {
            if (invoice.value.account_id == "")
                pinesMessageV2({ ty: 'warning', m: _lang.feedback_messages.clientNotChosen });
            else {
                if (!showLoader.value) {
                    loader(true)
                    axios.get(Api.getApiBaseUrl("money") + '/invoices/relatedmatters/' + invoice.value.client_id + '?client_account_id=' + invoice.value.account_id + '&organization_id=' + invoice.value.organization_id, Api.getInitialHeaders())
                        .then(response => {
                            if (response.status == "200") {
                                if (response.data.related_matters) {
                                    showRelatedMatterModal.value = true
                                    relatedMatters.value = response.data.related_matters
                                } else pinesMessageV2({ ty: 'warning', m: _lang.noRelatedMatters });
                                loader(false)
                            }
                        }).catch((error) => {
                            pinesMessageV2({ ty: 'warning', m: _lang.noRelatedMatters });
                            loader(false)
                        });
                }
            }
        }
        const handleRelatedMatterModalSubmit = (relatedMatters, timeLogs, expenses) => {
            let timeLogArr = []
            let expenseArr = []
            timeLogs.map((timelog) => {
                timeLogArr.push({
                    "account_id": timelog['account_id'],
                    "tax_id": timelog['tax_id'] ? timelog['tax_id'] : null,
                    "discount_id": timelog['discount_id'] ?? null,
                    "time_log_id": timelog['id'],
                    "item_title": timelog['user_data'],
                    "user_id": timelog['user_id'],
                    "unit_price": accounting.roundNumber(accounting.toNumber(timelog['rate']) / accounting.toNumber(invoice.value.exchange_rate)),
                    "quantity": accounting.toNumber(timelog['effective_effort']),
                    "discount_percentage": accounting.toNumber(timelog['discount_percentage']),
                    "discount_amount": 0,
                    "tax_percentage": accounting.toNumber(timelog['tax_percentage']),
                    "item_description": timelog['description'],
                    "item_date": timelog['log_date'],
                    "partner_shares": timelog['partner_shares'] ?? [],
                    "timelog_data": timelog['timelog_data'] ?? [],
                });
            })
            expenses.map((expense) => {
                expenseArr.push({
                    "account_id": expense['account_id'],
                    "tax_id": null,
                    "discount_id": null,
                    "expense_id": expense['expense_id'],
                    "item_title": langGlobal == "" ? expense['category'] : expense[langGlobal + '_category'],
                    "unit_price": accounting.roundNumber(accounting.toNumber(expense['amount']) * accounting.toNumber(ratesListGlobal[expense['currency_id']]) / invoice.value.exchange_rate),
                    "quantity": 1,
                    "discount_percentage": 0,
                    "discount_amount": 0,
                    "tax_percentage": 0,
                    "item_description": expense['paid_on'] + (expense['comments'] != "" && expense['comments'] != null ? " - " + expense['comments'] : ""),
                    "item_date": expense['paid_on'],
                    "partner_shares": expense['partner_shares'] ?? [],
                });
            })
            invoice.value.related_matters = relatedMatters;
            invoice.value.invoice_time_logs = timeLogArr
            invoice.value.invoice_expenses = expenseArr
            showRelatedMatterModal.value = false
        }
        const closeRelatedMatterModal = () => {
            showRelatedMatterModal.value = false
        }

        const openPartnerSharesModal = (obj) => {
            showPartnerSharesModal.value = true
            PartnerData.value = obj
        }
        const handlePartnerSharesSubmit = (obj, category1, index1) => {
            showPartnerSharesModal.value = false
            invoice.value[category1].map((item, i) => {
                if (i == index1) item.partner_shares = obj
            })
        }
        const closePartnerSharesModal = () => {
            showPartnerSharesModal.value = false
        }

        const openEditInvoiceNbModal = () => {
            showEditInvoiceNbModal.value = true
        }
        const handleEditInvoiceNbSubmit = (obj) => {
            showEditInvoiceNbModal.value = false
            invoice.value.prefix = obj.prefix
            invoice.value.reference_number = obj.referenceNumber
            invoice.value.suffix = obj.suffix
        }
        const closeEditInvoiceNbModal = () => {
            showEditInvoiceNbModal.value = false
        }

        const openEditInvoiceTemplateModal = () => {
            showEditInvoiceTemplateModal.value = true
        }
        const handleEditInvoiceTemplateSubmit = (obj) => {
            showEditInvoiceTemplateModal.value = false;
            invoice.value.invoice_template_id = obj.template_id;
            invoice.value.invoice_template_name = (obj.template_name ? obj.template_name : defaultTemplate.value.name);
            //pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
        }
        const closeEditInvoiceTemplateModal = () => {
            showEditInvoiceTemplateModal.value = false
        }

        const handleClientChange = (obj) => {
            clientAccountResource.value = obj;
            invoice.value['account_id'] = obj.id
            invoice.value['client_id'] = obj.model_id
            invoice.value['client_name'] = obj.name + ' - ' + obj.currency
            invoice.value['bill_to'] = obj.account_data
            invoice.value['currency_id'] = obj.currency_id
            invoice.value['exchange_rate'] = (ratesListGlobal[obj.currency_id] * 1)
            invoice.value['currency'] = obj.currency
            invoice.value.invoice_expenses = []
            invoice.value.invoice_time_logs = []
            invoice.value.related_matters = []
            if (obj.client.term_id) {
                invoice.value.term_id = obj.client.term_id;
                onTermChange();
            }
            if (obj.client.discount_percentage > 0) {
                invoice.value.discount_percentage = obj.client.discount_percentage;
                invoice.value.discount_value_type = 'percentage';
            }
        }

        const handleDetailObjectChange = (obj) => {
            invoice.value[obj.name] = obj.value
        }

        const openNewTaxModal = (obj) => {
            rowDetails.value = obj
            showNewTaxModal.value = true
        }
        const handleNewTaxSubmit = (obj) => {
            obj.data['label'] = obj.data['name'] + ' (' + obj.data['percentage'] + "%)"
            taxesList.value.push(obj.data)
            invoice.value[obj.category].map((item, i) => {
                if (i == obj.index) {
                    item.tax_percentage = obj.data.percentage;
                    item.tax_id = obj.data.id;
                    //call the cal. method to calculate the line tax amount, total,.. since we set tax_id above 
                    //not call the onchange of tax dropdown.
                    item._updateItemDataCalc();
                }
            })
        }
        const closeNewTaxModal = () => {
            showNewTaxModal.value = false
        }

        const openNewItemModal = (obj) => {
            rowDetails.value = obj
            showNewItemModal.value = true
        }
        const handleNewItemSubmit = (obj) => {
            if (obj.data.parent_id > 0) {
                let index = itemsList.value.findIndex(x => x.id == obj.data.parent_id);
                itemsList.value.splice(index + 1, 0, obj.data);
            } else {
                obj.data['isParent'] = true;
                itemsList.value.push(obj.data);
            }
            invoice.value[obj.category].map((item, i) => {
                if (i == obj.index) {
                    item.item_title = obj.data.name
                    item.item_description = obj.data.description
                    item.item_id = obj.data.id
                    item.unit_price = accounting.roundNumber(obj.data.price / invoice.value.exchange_rate)
                    item.account_id = obj.data.account_id
                    item.tax_id = obj.data.tax_id
                    taxesList.value.map((tx) => {
                        if (item.tax_id == tx['id']) {
                            item.tax_percentage = accounting.toNumber(tx['percentage']);
                        }
                    });
                    item._updateItemDataCalc();
                }
            })
        }
        const closeNewItemModal = () => showNewItemModal.value = false;

        const openAccountAddressModal = () => {
            if (Object.keys(clientAccountResource.value).length == 0) {  //must select client first
                pinesMessageV2({ ty: 'warning', m: _lang.feedback_messages.clientNotChosen });
                return;
            }
            showAccountAddressModal.value = true
        }
        const handleAccountAddressSubmit = (obj) => {
            clientAccountResource.value = obj.account;
        }
        const closeAccountAddressModal = (isSkipAccountFieldsChecking) => {
            //we add this condition since in cancel, skip, save it is called twice on of the model hide and one on the btn, action
            if (typeof isSkipAccountFieldsChecking != 'undefined')
                invoice.value.is_skip_account_fields_checking = (isSkipAccountFieldsChecking == true ? true : false);
            showAccountAddressModal.value = false;
        }

        const openEditExRateModal = () => showEditExRateModal.value = true;

        const handleNewExRateSubmit = (obj) => {
            ratesListGlobal[invoice.value.currency_id] = obj.rate
            invoice.value.exchange_rate = ratesListGlobal[invoice.value.currency_id].toString()
        }
        const closeEditExRateModal = () => showEditExRateModal.value = false

        const noteListChange = (event) => {
            for (const [key, value] of Object.entries(notesListArr.value)) {
                if (event.target.value == key) {
                    invoice.value.notes = value.description
                }
            }
        }
        const updateInvoiceDueDate = () => {
            invoice.value.due_on = moment(invoice.value.invoice_date).add(invoice.value.term_days_nb, 'days').locale('en').format('YYYY-MM-DD');
        }
        const onTermChange = () => {
            let isTermExist = false;
            for (let i in termsArr.value) {
                if (invoice.value.term_id == termsArr.value[i].id) {
                    invoice.value.term = termsArr.value[i].name;
                    invoice.value.term_days_nb = termsArr.value[i].number_of_days;
                    isTermExist = true;
                    break;
                }
            }
            if(!isTermExist){
                invoice.value.term = '';
                invoice.value.term_days_nb = 0;
            }
            updateInvoiceDueDate();
        }
        const setReminder = (flag) => {
            if (flag) {
                invoice.value.invoice_reminder.reminder_time = invoiceReminder.value.reminder_time
                invoice.value.invoice_reminder.reminder_time_type = invoiceReminder.value.reminder_time_type
                invoice.value.invoice_reminder.reminder_type = invoiceReminder.value.reminder_type
            } else {
                invoice.value.invoice_reminder.reminder_time = null
                invoice.value.invoice_reminder.reminder_time_type = null
                invoice.value.invoice_reminder.reminder_type = null
            }
            showNotify.value = false
        }
        const hideUnHideColumn = (flag, type) => {
            invoice.value[type] = (flag == "1" ? '0' : '1');
            //if we hide the tax, then empty it and re-calculate it
            if(type == 'display_tax' && invoice.value.display_tax != '1'){
                ['invoice_details', 'invoice_expenses', 'invoice_time_logs'].map((itemType) => {
                    invoice.value[itemType].map((item) => {
                        item.tax_percentage = 0;
                        item.tax_id = null;
                        //call the cal. method to calculate the line tax amount, total,.. since we change tax above 
                        item._updateItemDataCalc();
                    });
                });
            }
        }
        const convertTo = (status) => {
            switch (status) {
                case "open":
                    if (eInvoicingGlobal) {
                        confirm.require({
                            message: _lang.money.confirmationSaveInvoice,
                            header: _lang.confirmation,
                            icon: 'pi pi-exclamation-triangle',
                            acceptClass: 'p-button-warning',
                            acceptLabel: _lang.yes,
                            acceptIcon: 'pi pi-check',
                            rejectClass: 'p-button-secondary',
                            rejectLabel: _lang.no,
                            rejectIcon: 'pi pi-times',
                            accept: () => {
                                convertToOpen();
                            }
                        });
                    } else {
                        convertToOpen()
                    }
                    break;
                case "draft":
                    convertToDraft()
                    break;
                case "cancelled":
                    convertToCancelled()
                    break;
                case "delete":
                    confirm.require({
                        message: _lang.confirmationDeleteInvoice,
                        header: _lang.confirmation,
                        icon: 'pi pi-exclamation-triangle',
                        acceptClass: 'p-button-danger',
                        acceptLabel: _lang.yes,
                        acceptIcon: 'pi pi-check',
                        rejectClass: 'p-button-secondary',
                        rejectLabel: _lang.no,
                        rejectIcon: 'pi pi-times',
                        accept: () => {
                            deleteInvoice();
                        }
                    });
                    break;
                default:
                    break;
            }
        }
        const convertToCancelled = () => {
            loader(true)
            axios.patch(Api.getApiBaseUrl("money") + '/invoices/' + invoice.value.id + '/status/cancelled?organization_id=' + entity.value.id, {}, Api.getInitialHeaders())
                .then(() => {
                    pinesMessageV2({ ty: 'success', m: _lang.invoiceHasBeenCancelled });
                    setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoice_edit/' + invoiceEditIdGlobal, 700);
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                    loader(false)
                });
        }
        const convertToOpen = () => {
            loader(true)
            axios.patch(Api.getApiBaseUrl("money") + '/invoices/' + invoice.value.id + '/status/open?organization_id=' + entity.value.id, {}, Api.getInitialHeaders())
                .then(() => {
                    pinesMessageV2({ ty: 'success', m: _lang.invoiceHasBeenConvertedToOpen });
                    setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoice_edit/' + invoiceEditIdGlobal, 700);
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                    loader(false)
                });
        }
        const convertToDraft = () => {
            loader(true)
            axios.patch(Api.getApiBaseUrl("money") + '/invoices/' + invoice.value.id + '/status/draft?organization_id=' + entity.value.id, {}, Api.getInitialHeaders())
                .then(() => {
                    pinesMessageV2({ ty: 'success', m: _lang.invoiceHasBeenSetAsDraft });
                    setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoice_edit/' + invoiceEditIdGlobal, 700);
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                    loader(false)
                });
        }
        const deleteInvoice = () => {
            loader(true)
            axios.delete(Api.getApiBaseUrl("money") + '/invoices/' + invoice.value.id + '?organization_id=' + entity.value.id, Api.getInitialHeaders())
                .then((response) => {
                    pinesMessageV2({ ty: 'success', m: response.data.message });
                    setTimeout(() => window.location = getBaseURL('money') + 'vouchers/invoices_list/', 700);
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error.response.data.message });
                    loader(false)
                });
        }
        const uploadInvDocument = (event) => invDoc.value = event.target.files[0]
        const loader = (action) => showLoader.value = action

        const validateDiscountNumber = (event) => {
            if (accounting.toNumber(invoiceSummary.value.totalValue) < 0) invoice.value.discount_amount = 0;
            let discount = invoice.value.discount_amount + "";   //convert to string, limit numbers after decimal
            if (discount.indexOf(".") >= 0)
                invoice.value.discount_amount = discount.slice(0, discount.indexOf(".") + accounting.toNumber(allowedDecimalFormatGlobal) + 1);
        }
        const validateDiscountPercentage = (event) => {
            let numberPattern = new RegExp("^(\\d+\\.?\\d{0," + (accounting.toNumber(allowedDecimalFormatGlobal) + 2) + "}|\\.\\d{1," + (accounting.toNumber(allowedDecimalFormatGlobal) + 2) + "})$");   //  /^(\d+\.?\d{0,4}|\.\d{1,4})$/
            if (!numberPattern.test(event.target.value) || Math.abs(event.target.value) > 100) invoice.value.discount_percentage = 0;
            let discount = invoice.value.discount_percentage + "";
            if (discount.indexOf(".") >= 0)
                invoice.value.discount_percentage = discount.slice(0, discount.indexOf(".") + accounting.toNumber(allowedDecimalFormatGlobal) + 1);
        }
        const onDiscountType = (event) => {
            let selected = event.target.value
            invoice.value.discount_id = null
            invoice.value.discount_percentage = 0
            invoice.value.discount_amount = 0
            invoice.value.discount_value_type = selected === "amount" ? 'amount' : "percentage"
            if (selected != "amount" && selected != "percentage") {
                invoice.value.discount_id = selected
                discountsGlobal.map((disc) => {
                    if (selected == disc['id']) {
                        invoice.value.discount_percentage = disc['percentage']
                        invoice.value.discount_id = disc['id']
                    }
                })
            }
        }
        const createCreditNote = () => {
            window.location = getBaseURL('money') + 'vouchers/save_credit_note/' + invoiceEditIdGlobal;
        }
        const createDebitNote = () => {
            window.location = getBaseURL('money') + 'vouchers/debit_note_add/' + invoiceEditIdGlobal;
        }
        const isRevertStatusAllowed = () => {
            return (invoice.value.invoice_expenses.length > 0 || invoice.value.invoice_time_logs.length > 0) && eInvoicingGlobal && invoiceType.value == 'edit' && getTotalCreditNoteAmount() == invoiceSummary.value.totalValue;
        }

        const getTotalCreditNoteAmount = () => {
            let total = 0;
            invoiceSummary.value.related_credit_notes.map((cr, index) => {
                if (cr.paid_status != "draft" && cr.paid_status != "cancelled") {
                    total += Number(cr.credit_note_total);
                }
            });
            return total;
        }
        const revertInvoiceTimeLogsExpensesStatus = () => {
            confirm.require({
                message: _lang.money.confirmRevertStatus,
                header: _lang.confirmation,
                icon: 'pi pi-exclamation-triangle',
                acceptClass: 'p-button-warning',
                acceptLabel: _lang.yes,
                acceptIcon: 'pi pi-check',
                rejectClass: 'p-button-secondary',
                rejectLabel: _lang.no,
                rejectIcon: 'pi pi-times',
                accept: () => {
                    loader(true);
                    axios.patch(Api.getApiBaseUrl("money") + '/invoices/timelogs-expenses/status/' + invoice.value.id, {}, Api.getInitialHeaders())
                        .then((response) => {
                            pinesMessageV2({ ty: 'success', m: response.data.message });
                            loader(false);
                        }).catch((error) => {
                            pinesMessageV2({ ty: 'error', m: error.response.data.message });
                            loader(false);
                        });
                }
            });
        }

        const isOverdue = () => {
            return ((invoice.value.paid_status === 'open' || invoice.value.paid_status === 'partially paid') && new Date(invoice.value.due_on) < new Date(new Date().getTime() - (1000 * 60 * 60 * 24)));
        }
        return {
            invoiceType,
            invoice,
            defaultTemplate,
            notesListArr,
            transactionTypesArr,
            paymentMethodsArr,
            termsArr,
            showRelatedMatterModal,
            showPartnerSharesModal,
            showEditInvoiceNbModal,
            showEditInvoiceTemplateModal,
            showNewTaxModal,
            showNewItemModal,
            showAccountAddressModal,
            showEditExRateModal,
            relatedMatters,
            showLoader,
            PartnerData,
            entity,

            partnersCommissions: partnersCommissionsGlobal,
            notifyMeTimeTypesArr: notifyMeTimeTypesGlobal,
            notifyMeTypesArr: notifyMeTypesGlobal,
            showNotify,
            invoiceSummary,
            validatePage,
            invoiceReminder,
            disableFields,
            discountsList: discountsGlobal,
            rowDetails,
            taxesCodesList,
            taxesList,
            itemsList,
            eInvoicing: eInvoicingGlobal,
            isAddingFromQuote,
            isUserChangeInvoiceType,

            invoiceStatusChange,
            handleSaveInvoice,

            handleClientChange,
            handleDetailObjectChange,

            openRelatedMatterModal,
            handleRelatedMatterModalSubmit,
            closeRelatedMatterModal,

            openPartnerSharesModal,
            handlePartnerSharesSubmit,
            closePartnerSharesModal,

            openEditInvoiceNbModal,
            handleEditInvoiceNbSubmit,
            closeEditInvoiceNbModal,
            
            openEditInvoiceTemplateModal,
            handleEditInvoiceTemplateSubmit,
            closeEditInvoiceTemplateModal,

            openNewTaxModal,
            handleNewTaxSubmit,
            closeNewTaxModal,

            openNewItemModal,
            handleNewItemSubmit,
            closeNewItemModal,

            openAccountAddressModal,
            handleAccountAddressSubmit,
            closeAccountAddressModal,

            openEditExRateModal,
            handleNewExRateSubmit,
            closeEditExRateModal,

            setReminder,
            noteListChange,
            onTermChange,
            updateInvoiceDueDate,

            hideUnHideColumn,
            convertTo,
            uploadInvDocument,

            validateDiscountNumber,
            validateDiscountPercentage,
            onDiscountType,
            editor: ClassicEditor,
            createCreditNote,
            createDebitNote,
            isRevertStatusAllowed,
            revertInvoiceTimeLogsExpensesStatus,
            isOverdue,

            debitNoteReasonsList,
            invoiceTypesList,
            invoiceAdditionalFields,
            invoiceRequiredFields,
            accountRequiredFields,
            clientAccountResource,
            personAdditionalIdTypesList,
            companyAdditionalIdTypesList,
            templateIdsList,
            countryList,
        }
    },
};