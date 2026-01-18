import Loader from "Loader";
import RelatedMatter from "RelatedMatter";
import PartnerShares from "PartnerShares";
import CreditNoteDetails from "CreditNoteDetails";
import AccountAddress from 'AccountAddress';
import Api from "Api";
import { isTrue } from "Utils";

export default {
	name: "CreditNote",
	components: {
		"loader": Loader,
		"credit-note-details": CreditNoteDetails,
		"related-matter": RelatedMatter,
		"partner-shares": PartnerShares,
		"p-breadcrumb": primevue.breadcrumb,
		"p-menu": primevue.menu,
		"p-button": primevue.button,
		"p-autocomplete": primevue.autocomplete,
		"p-dropdown": primevue.dropdown,
		"p-inputtext": primevue.inputtext,
		"p-inputnumber": primevue.inputnumber,
		"p-calendar": primevue.calendar,
		"p-fieldset": primevue.fieldset,
		"p-divider": primevue.divider,
		"p-fileupload": primevue.fileupload,
		"p-textarea": primevue.textarea,
		"p-card": primevue.card,
		"p-datatable": primevue.datatable,
		"p-column": primevue.column,
		"p-selectbutton": primevue.selectbutton,
		"p-badge": primevue.badge,
		"p-blockui": primevue.blockui,
		"p-dialog": primevue.dialog,
		"p-toast": primevue.toast,
		"p-confirmdialog": primevue.confirmdialog,
		"account-address": AccountAddress,
	},

	setup() {
		const { onMounted, ref, watch, computed } = Vue;
		const { useToast } = primevue.usetoast;
		const { useConfirm } = primevue.useconfirm;
		const toast = useToast();
		const confirm = useConfirm();

		onMounted(() => {
			initialize();
		});

		const timeoutDuration = 700;
		const data = ref(frontEndData);
		const creditNote = ref({
			organization_id: data.value.organization_id,
			user_id: data.value.user_id,
			account_id: null,
			description: null,
			credit_note_reference: null,
			bill_to: null,
			credit_note_date: moment().locale("en").format("YYYY-MM-DD"),
			credit_note_type_id: null,
			transaction_type_id: null,
			credit_note_reason_id: null,
			term_id: null,
			notes: null,
			paid_status: null,
			client_id: null,
			client_name: null,
			tax_number: null,
			currency_id: null,
			currency: null,
			exchange_rate: null,
			credit_note_number: null,
			display_item_date: null,
			display_tax: null,
			display_discount: null,
			discount_amount: 0,
			discount_id: null,
			discount_percentage: 0,
			discount_value_type: 'percentage',
			lines_total_discount: 0,
			lines_total_subtotal: 0,
			lines_total_tax: 0,
			lines_totals: 0,
			total: 0,
			credit_note_details: [],
			credit_note_expenses: [],
			credit_note_time_logs: [],
			related_matters: [],
			related_invoices: null,
			related_invoice_id: {},
			is_skip_account_fields_checking: false
		});
		const creditNoteStatusOptions = ref([
			{ name: _lang.money.draft, value: 'draft' },
			{ name: _lang.money.open, value: 'open' },
		]);
		const exportTypesOptions = ref([
			{ label: _lang.exportToWord, value: 'word' },
			{ label: _lang.exportToPDF, value: 'pdf' }
		]);
		const selectedNoteId = ref(null);
		const selectedTemplateId = ref(null);
		const displayExportModal = ref(false);
		const exportFileType = ref(null);
		const displayExchangeRateModal = ref(false);
		const isClientCurrency = ref(true);
		const isEditExchangeRate = ref(false);
		const isEditCreditNote = ref(false);
		const linkedMatters = ref([]);
		const showLoader = ref(true);
		const allNotes = ref(data.value.notes_descriptions);
		const transactionTypesList = ref(data.value.transaction_types);
		const creditNoteReasonsList = ref(data.value.credit_note_reasons);
		const termsList = ref(data.value.terms);
		const validatePage = ref(false);
		const errorMsg = ref([]);
		const eInvoicingKey = ref('');
		const optionsMenu = ref();
		const statusesMenu = ref();
		const showLinkedMattersModal = ref(false);
		const showPartnerSharesModal = ref(false);
		const taxesList = ref([]);
		const itemsList = ref([]);
		const invoiceTypesList = ref({});
		const partnerData = ref(null);

		const showAccountAddressModal = ref(false)
		const clientAccountResource = ref({});
		const creditNoteRequiredFields = ref([]);
		const accountRequiredFields = ref([]);
		const creditNoteAdditionalFields = ref([]);
		const personAdditionalIdTypesList = ref({});
		const companyAdditionalIdTypesList = ref({});
		const countryList = ref([]);

		const currency = ref({
			id: creditNote.value.currency_id,
			code: creditNote.value.currency,
			rate: creditNote.value.exchange_rate,
		});
		const creditNoteSummary = ref({
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
		});
		const summaryCurrencies = computed(() => [
			{ name: creditNote.value.currency, value: true },
			{ name: data.value.organization_currency, value: false }
		]);
		const breadcrumbCreditNote = ref([{
			label: _lang.money.creditNotes,
			url: data.value.site_url + '/vouchers/credit_notes'
		}, {
			label: isEditCreditNote ? _lang.money.editCreditNote : _lang.money.newCreditNote,
			url: data.value.site_url + '/vouchers/credit_notes'
		}]);
		const breadcrumbHome = ref({
			icon: 'pi pi-home',
			url: data.value.site_url
		});
		const statusesMenuItems = computed(() => [
			{
				visible: creditNote.value.paid_status !== 'draft',
				label: _lang.money.draft,
				icon: 'pi pi-file',
				command: () => handleChangeCreditNoteStatus('draft')
			},
			{
				visible: creditNote.value.paid_status !== 'open',
				label: _lang.money.open,
				icon: 'pi pi-check',
				command: () => handleChangeCreditNoteStatus('open')
			},
			{
				visible: creditNote.value.paid_status !== 'cancelled',
				label: _lang.cancelled,
				icon: 'pi pi-times',
				command: () => handleChangeCreditNoteStatus('cancelled')
			}
		]);
		const optionsMenuItems = computed(() => [{
			label: _lang.options,
			items: [
				{
					label: _lang.money.exportCreditNote,
					icon: 'pi pi-file',
					disabled: !isEditCreditNote.value,
					command: () => {
						toggleExportModal();
					}
				},
				{
					label: _lang.money.deleteCreditNote,
					icon: 'pi pi-trash',
					visible: isEditCreditNote.value,
					command: () => {
						deleteCreditNote();
					}
				}
			]
		}, {
			label: _lang.money.goTo,
			items: [{
				label: _lang.money.services,
				icon: 'pi pi-list',
				url: data.value.site_url + '/items'
			}, {
				label: _lang.money.discounts,
				icon: 'pi pi-sort-amount-down',
				command: () => {
					configureInvoiceDiscount(true)();
				}
			}, {
				label: _lang.money.taxes,
				icon: 'pi pi-percentage',
				url: data.value.site_url + '/taxes'
			}]
		}]);

		const toggleOptionsMenu = (event) => {
			optionsMenu.value.toggle(event);
		};

		const toggleStatusesMenu = (event) => {
			statusesMenu.value.toggle(event);
		};

		const toggleExportModal = () => {
			displayExportModal.value = !displayExportModal.value;
		};

		const toggleExchangeRateModal = () => {
			displayExchangeRateModal.value = !displayExchangeRateModal.value;
		};

		const loader = (action) => {
			showLoader.value = action;
		}

		const initialize = () => {
			loader(true);
			initApiAccessToken(function () {
                initializeCreditNote();
            }, getBaseURL('money') + 'vouchers/credit_notes/');
		};

		const initializeCreditNote = () => {
			prepareCreditNoteData();
			const creditNoteId = data.value.credit_note_id;
			const invoiceId = data.value.invoice_id;
			if (creditNoteId) {
				isEditCreditNote.value = true;
				axios.get(`${Api.getApiBaseUrl("money")}/credit_notes/${creditNoteId}`, Api.getInitialHeaders()).then((response) => {
					if (response.data.credit_note && response.data.credit_note.account?.organization_id === data.value.organization_id) {
						initializeCreditNoteObject(response.data.credit_note);
						isInvoiceDataLoaded = true;
						loader(false);
					} else {
						toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: _lang.access_denied, life: 4000 });
						setTimeout(() => {
							window.location = getBaseURL("money") + "vouchers/credit_notes/";
						}, timeoutDuration);
					}
				}).catch((error) => {
					toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: error?.response?.data.message ?? _lang.feedback_messages.error, life: 4000 });
					if (error?.response?.status == 401) {
						localStorage.removeItem("api-access-token");
					}
					setTimeout(() => {
						window.location = getBaseURL("money") + "vouchers/credit_notes/";
					}, timeoutDuration);
				});
			} else {
				isEditCreditNote.value = false;
				if (invoiceId) {
					axios.get(`${Api.getApiBaseUrl("money")}/invoices?voucher_header_id=${invoiceId}&organization_id=${data.value.organization_id}`, Api.getInitialHeaders()).then((response) => {
						if (response.data.invoices) {
							initializeCreditNoteObject(response.data.invoices[0], true);
							isInvoiceDataLoaded = true;
							loader(false);
						} else {
							toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: response.data.message, life: 4000 });
							setTimeout(() => {
								window.location = getBaseURL("money") + "vouchers/credit_notes/";
							}, timeoutDuration);
						}
					}).catch((error) => {
						toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: error?.response?.data.message ?? _lang.feedback_messages.error, life: 4000 });
						if (error?.response?.status == 401) {
							localStorage.removeItem("api-access-token");
						}
						setTimeout(() => {
							window.location = getBaseURL("money") + "vouchers/credit_notes/";
						}, timeoutDuration);
					});
				}
				else {
					//isInvoiceDataLoaded = true;
					toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: _lang.access_denied, life: 4000 });
					setTimeout(() => {
						window.location = getBaseURL("money") + "vouchers/credit_notes/";
					}, timeoutDuration);
				}
			}
		};

		const initializeCreditNoteObject = (creditNoteResponse, isInvoice = false) => {
			creditNote.value.organization_id = data.value.organization_id;
			creditNote.value.user_id = data.value.user_id;
			clientAccountResource.value = creditNoteResponse.account;
			creditNote.value.account_id = creditNoteResponse.account.id;
			creditNote.value.description = creditNoteResponse.description;
			creditNote.value.bill_to = creditNoteResponse.bill_to;
			creditNote.value.credit_note_date = moment(creditNoteResponse.credit_note_date).locale("en").format("YYYY-MM-DD");
			creditNote.value.transaction_type_id = creditNoteResponse.transaction_type_id;
			creditNote.value.term_id = creditNoteResponse.term_id;
			creditNote.value.client_id = creditNoteResponse.account.model_id;
			creditNote.value.client_name = creditNoteResponse.account.name;
			creditNote.value.tax_number = creditNoteResponse.account.tax_number;
			creditNote.value.currency_id = creditNoteResponse.account.currency_id;
			creditNote.value.currency = creditNoteResponse.account.currency;
			creditNote.value.display_item_date = creditNoteResponse.display_item_date;
			creditNote.value.display_tax = creditNoteResponse.display_tax;
			creditNote.value.display_discount = creditNoteResponse.display_discount;
			creditNote.value.discount_id = creditNoteResponse.discount_id;
			//if discount type is percent then make round to remove the .00000 zero decimals
			creditNote.value.discount_percentage = (creditNoteResponse.discount_value_type == 'amount' ? creditNoteResponse.discount_percentage : accounting.roundNumber(creditNoteResponse.discount_percentage));
			creditNote.value.discount_amount = (creditNoteResponse.discount_value_type == 'amount' ? accounting.roundNumber(creditNoteResponse.discount_amount) : creditNoteResponse.discount_amount);
			creditNote.value.discount_value_type = creditNoteResponse.discount_value_type;
			creditNote.value.lines_total_discount = creditNoteResponse.lines_total_discount;
			creditNote.value.lines_total_subtotal = creditNoteResponse.lines_total_subtotal;
			creditNote.value.lines_total_tax = creditNoteResponse.lines_total_tax;
			creditNote.value.lines_totals = creditNoteResponse.lines_totals;
			creditNote.value.total = creditNoteResponse.total;
			creditNote.value.related_matters = creditNoteResponse.related_matters;
			//
			if (isInvoice) {
				creditNote.value.credit_note_reference = null;
				creditNote.value.credit_note_type_id = creditNoteResponse.invoice_type_id;
				creditNote.value.credit_note_reason_id = null;
				creditNote.value.notes = null;
				creditNote.value.paid_status = "draft";
				creditNote.value.exchange_rate = accounting.toNumber(data.value.rates[creditNoteResponse.account.currency_id])
				creditNote.value.credit_note_number = null;
				creditNote.value.credit_note_details = creditNoteResponse.invoice_details;
				creditNote.value.credit_note_expenses = creditNoteResponse.invoice_expenses;
				creditNote.value.credit_note_time_logs = creditNoteResponse.invoice_time_logs;
				creditNote.value.related_invoices = creditNoteResponse.id;
				creditNote.value.related_invoice_id = {
					value: creditNoteResponse.voucher_header_id,
					viewValue: creditNoteResponse.invoice_number
				}
			} else {
				creditNote.value.id = creditNoteResponse.id;
				creditNote.value.voucher_header_id = creditNoteResponse.voucher_header_id;
				creditNote.value.credit_note_reference = creditNoteResponse.credit_note_reference;
				creditNote.value.credit_note_type_id = creditNoteResponse.credit_note_type_id;
				creditNote.value.credit_note_reason_id = creditNoteResponse.credit_note_reason_id;
				creditNote.value.notes = creditNoteResponse.notes;
				creditNote.value.paid_status = creditNoteResponse.paid_status;
				creditNote.value.exchange_rate = accounting.toNumber(creditNoteResponse.exchange_rate);
				creditNote.value.credit_note_number = creditNoteResponse.credit_note_number;
				creditNote.value.credit_note_details = creditNoteResponse.credit_note_details;
				creditNote.value.credit_note_expenses = creditNoteResponse.credit_note_expenses;
				creditNote.value.credit_note_time_logs = creditNoteResponse.credit_note_time_logs;
				creditNote.value.related_invoices = creditNoteResponse.related_invoices[0] ? creditNoteResponse.related_invoices[0].invoice_header_id : null;
				creditNote.value.related_invoice_id = {
					value: creditNoteResponse.related_invoices[0] ? creditNoteResponse.related_invoices[0].voucher_header_id : null,
					viewValue: creditNoteResponse.related_invoices[0] ? creditNoteResponse.related_invoices[0].invoice_number : null
				}
			}
			['credit_note_details', 'credit_note_expenses', 'credit_note_time_logs'].map((itemType) => {
				creditNote.value[itemType].map((item) => {
					//if discount type is percent then make round to remove the .00000 zero decimals
					if (item.discount_type != 'amount')
						item.discount_percentage = accounting.roundNumber(item.discount_percentage);
				});
			});
		}

		const prepareCreditNoteData = () => {
			//
			for (let index in data.value.countries)
				countryList.value.push({ id: index, name: data.value.countries[index] });
			//
			axios.get(Api.getApiBaseUrl("money") + '/credit_notes/preparedata?organization_id=' + data.value.organization_id, Api.getInitialHeaders())
				.then(response => {
					eInvoicingKey.value = (response.data.e_invoicing_key ?? '');
					if (response.data.taxes) {
						response.data.taxes.map((tax, index) => {
							taxesList.value[index] = tax
							taxesList.value[index]['label'] = tax['name'] + ' (' + tax['percentage'] + "%)"
						});
					}
					if (response.data.items)
						buildItemsList(response.data.items);
					if (response.data.invoice_types)
						invoiceTypesList.value = response.data.invoice_types;
					if (response.data.person_additional_id_types)
						personAdditionalIdTypesList.value = response.data.person_additional_id_types;
					if (response.data.company_additional_id_types)
						companyAdditionalIdTypesList.value = response.data.company_additional_id_types;
					if (response.data.additiona_fields) {
						for (const field in response.data.additiona_fields.credit_note) {
							creditNoteAdditionalFields.value.push(field);
							if (response.data.additiona_fields.credit_note[field].indexOf('required') != -1)
								creditNoteRequiredFields.value.push(field);
						}
						for (const field in response.data.additiona_fields.account) {
							if (response.data.additiona_fields.account[field].indexOf('required') != -1)
								accountRequiredFields.value.push(field);
						}
					}
				}).catch((error) => {
					pinesMessageV2({ ty: 'error', m: error.response.data.message });
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
					name: data.value.money_language === "fl1" ? constructor.fl1_name : data.value.money_language === "fl2" ? constructor.fl2_name : constructor.name,
					price: constructor.price,
					isParent: true,
				}).concat(constructor.invoice_sub_items.map(sub_item => ({
					id: sub_item.id,
					account_id: sub_item.account_id,
					currency: sub_item.currency,
					currency_id: sub_item.currency_id,
					description: sub_item.description,
					exchange_rate: sub_item.exchange_rate,
					name: sub_item.name,
					name: data.value.money_language === "fl1" ? sub_item.fl1_name : data.value.money_language === "fl2" ? sub_item.fl2_name : sub_item.name,
					price: sub_item.price,
					parent_id: sub_item.parent_id,
				})))
			}, []);
		};

		const isAccountFieldsChecking = () => {
			return (!creditNote.value.is_skip_account_fields_checking && accountRequiredFields.value
				&& eInvoicingKey.value == 'saudi' && creditNote.value.credit_note_type_id == 1);
		}

		const resetValidation = () => {
			errorMsg.value = [];
			validatePage.value = false;
		}

		const openPartnerSharesModal = (obj) => {
			showPartnerSharesModal.value = true;
			partnerData.value = obj;
		}

		const handlePartnerSharesSubmit = (obj, category, index) => {
			category = category.replace("invoice", "credit_note");
			showPartnerSharesModal.value = false;
			creditNote.value[category].map((item, i) => {
				if (i == index) {
					item.partner_shares = obj;
				}
			})
		}

		const closePartnerSharesModal = () => {
			showPartnerSharesModal.value = false;
		}

		const openRelatedMatterModal = () => {
			axios.get(Api.getApiBaseUrl('money') + "/invoices/relatedmatters/" + creditNote.value.client_id + "?client_account_id=" + creditNote.value.account_id + "&organization_id=" + creditNote.value.organization_id, Api.getInitialHeaders()).then((response) => {
				if (response.status == "200") {
					if (response.data.related_matters) {
						toggleLinkedMattersModal();
						linkedMatters.value = response.data.related_matters;
					} else {
						toast.add({ severity: 'info', summary: _lang.noMatchesFound, detail: _lang.noRelatedMatters, life: 4000 });
					}
					loader(false);
				}
			}).catch((error) => {
				toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: error?.response?.data.message ?? _lang.feedback_messages.error, life: 4000 });
				loader(false);
			});
		};

		const handleLinkedMattersSubmit = (linkedMatters, timeLogs, expenses) => {
			let timeLogArr = [];
			let expenseArr = [];
			timeLogs.map((timelog) => {
				timeLogArr.push({
					account_id: timelog["account_id"],
					tax_id: timelog["tax_id"] ?? null,
					discount_id: timelog["discount_id"] ?? null,
					time_log_id: timelog["id"],
					item_title: timelog["user_data"],
					user_id: timelog["user_id"],
					unit_price: accounting.roundNumber(accounting.toNumber(timelog["rate"]) / accounting.toNumber(creditNote.value.exchange_rate)),
					quantity: accounting.toNumber(timelog["effective_effort"]),
					discount_percentage: accounting.toNumber(timelog["discount_percentage"]),
					discount_amount: 0,
					tax_percentage: accounting.toNumber(timelog["tax_percentage"]),
					item_description: timelog["description"],
					item_date: timelog["log_date"],
					partner_shares: timelog['partner_shares'] ?? [],
					timelog_data: timelog["timelog_data"] ?? [],
				});
			});
			expenses.map((expense) => {
				expenseArr.push({
					account_id: expense["account_id"],
					tax_id: null,
					discount_id: null,
					expense_id: expense["expense_id"],
					item_title: data.value.money_language == "" ? expense["category"] : expense[data.value.money_language + "_category"],
					unit_price: accounting.roundNumber(accounting.multiplyNumbers(expense["amount"], data.value.rates[expense["currency_id"]]) / creditNote.value.exchange_rate),
					quantity: 1,
					discount_percentage: 0,
					discount_amount: 0,
					tax_percentage: 0,
					item_description: expense["paid_on"],
					item_date: expense["paid_on"],
					partner_shares: expense['partner_shares'] ?? [],
				});
			});
			creditNote.value.related_matters = linkedMatters.slice();
			creditNote.value.credit_note_time_logs = timeLogArr;
			creditNote.value.credit_note_expenses = expenseArr;
			showLinkedMattersModal.value = false;
		};

		const handleDetailObjectChange = (obj) => {
			obj.name = obj.name.replace("invoice", "credit_note");
			creditNote.value[obj.name] = obj.value;
		};

		const toggleLinkedMattersModal = (value = null) => {
			showLinkedMattersModal.value = value ?? !showLinkedMattersModal.value;
		};

		const handleRemoveMatter = (matterId) => {
			let linkedMatters = creditNote.value.related_matters.slice();
			const index = linkedMatters.findIndex(item => item.id === matterId);
			if (index !== -1) {
				linkedMatters.splice(index, 1);
			}
			creditNote.value.related_matters = linkedMatters;
		}

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
			if (typeof isSkipAccountFieldsChecking != 'undefined')
				creditNote.value.is_skip_account_fields_checking = (isSkipAccountFieldsChecking == true ? true : false);
			showAccountAddressModal.value = false
		}

		const validateForm = () => {
			resetValidation();
			let obj = creditNote.value;
			if (creditNoteSummary.value.totalValue < 0) errorMsg.value.push("Total can't be negative.");
			if (!obj.account_id) errorMsg.value.push("Client Name is required")
			if (!obj.credit_note_date) errorMsg.value.push("Date is required")
			if (!obj.credit_note_reason_id) errorMsg.value.push("Reason is required")
			if (!obj.term_id) errorMsg.value.push("Terms is required")
			if (obj.credit_note_details.length == 0 && obj.credit_note_expenses.length == 0 && obj.credit_note_time_logs.length == 0)
				errorMsg.value.push("Fill item Record")
			obj.credit_note_details.map((item) => {
				if (!item.account_id || !item.item_id) errorMsg.value.push("Fill items")
				if (!item.item_date && creditNote.value.display_item_date == '1') errorMsg.value.push("Fill date")
				if (item.unit_price < 0 || item.unit_price == '') errorMsg.value.push("Fill Unit Price For Items")
				if (item.quantity < 0 || item.quantity == '') errorMsg.value.push("Fill Quantity For Items")
			})
			obj.credit_note_expenses.map((expense) => {
				if (expense.unit_price < 0 || expense.unit_price == '') errorMsg.value.push("Fill Unit Price For Expenses")
				if (expense.quantity < 0 || expense.quantity == '') errorMsg.value.push("Fill Quantity For Expenses")
			})
			obj.credit_note_time_logs.map((timelog) => {
				if (timelog.unit_price < 0 || timelog.unit_price == '') errorMsg.value.push("Fill Unit Price For Time Logs")
				if (timelog.quantity < 0 || timelog.quantity == '') errorMsg.value.push("Fill Quantity For Time Logs")
			});
			if (creditNoteRequiredFields.value) {
				creditNoteRequiredFields.value.forEach(function (field) {
					if (!obj[field]) errorMsg.value.push(field + " is required");
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
				toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: _lang.feedback_messages.requiredFormFields, life: 4000 });
			}
			validatePage.value = true;
		}

		const handleSaveCreditNote = () => {
			validateForm()
			if (errorMsg.value.length == 0) {
				if (data.value.e_invoicing && creditNote.value.paid_status === 'open') {
					confirm.require({
						message: _lang.money.confirmationSaveCreditNote,
						header: _lang.confirmation,
						icon: 'pi pi-exclamation-triangle',
						acceptClass: 'p-button-warning',
						acceptLabel: _lang.yes,
						acceptIcon: 'pi pi-check',
						rejectClass: 'p-button-secondary',
						rejectLabel: _lang.no,
						rejectIcon: 'pi pi-times',
						accept: () => {
							saveCreditNote();
						}
					});
				} else {
					saveCreditNote();
				}
			}
		}

		const saveCreditNote = () => {
			loader(true);
			if (isEditCreditNote.value) {
				axios.put(Api.getApiBaseUrl("money") + '/credit_notes/' + creditNote.value.id, creditNote.value, Api.getInitialHeaders()).then(() => {
					loader(false);
					validatePage.value = false;
					toast.add({ severity: 'success', summary: _lang.feedback_messages.success, detail: _lang.money.creditNoteSaved, life: 4000 });
				}).catch((error) => {
					catchSaveError(error);
				});
			} else {
				axios.post(Api.getApiBaseUrl("money") + '/credit_notes', Api.toFormData(creditNote.value), Api.getInitialHeaders("multipart/form-data")).then((response) => {
					if (response.data.credit_note) {
						toast.add({ severity: 'success', summary: _lang.feedback_messages.success, detail: _lang.money.creditNoteSaved, life: 4000 });
						setTimeout(() => {
							window.location = getBaseURL('money') + 'vouchers/edit_credit_note/' + response.data.credit_note.id
						}, timeoutDuration);
					} else {
						toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: _lang.money.somethingWrong, life: 4000 });
					}
				}).catch((error) => {
					catchSaveError(error);
				});
			}
		}

		const catchSaveError = (error) => {
			loader(false);
			toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: error.response.data.message, life: 4000 });
		}
		const handleChangeCreditNoteStatus = (status) => {
			if (data.value.e_invoicing && status === 'open') {
				confirm.require({
					message: _lang.money.confirmationSaveCreditNote,
					header: _lang.confirmation,
					icon: 'pi pi-exclamation-triangle',
					acceptClass: 'p-button-warning',
					acceptLabel: _lang.yes,
					acceptIcon: 'pi pi-check',
					rejectClass: 'p-button-secondary',
					rejectLabel: _lang.no,
					rejectIcon: 'pi pi-times',
					accept: () => {
						changeCreditNoteStatus(status);
					}
				});
			} else {
				changeCreditNoteStatus(status);
			}
		}
		const changeCreditNoteStatus = (status) => {
			loader(true);
			axios.patch(`${Api.getApiBaseUrl("money")}/credit_notes/${creditNote.value.id}/status/${status}?organization_id=${data.value.organization_id}`, null, Api.getInitialHeaders()).then((response) => {
				if (response.data.credit_note) {
					initializeCreditNoteObject(response.data.credit_note);
				} else {
					creditNote.value.paid_status = status;
				}
				loader(false);
				toast.add({ severity: 'success', summary: _lang.feedback_messages.success, detail: _lang.money.creditNoteStatusSaved, life: 4000 });
			}).catch((error) => {
				loader(false);
				toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: error.response.data.message, life: 4000 });
			});
		}

		const dateSelected = (date) => {
			creditNote.value.credit_note_date = moment(date).locale("en").format("YYYY-MM-DD");
		}

		const validateDiscountNumber = () => {
			if (accounting.toNumber(creditNoteSummary.value.totalValue) < 0) {
				creditNote.value.discount_amount = 0;
			}
		}

		const validateDiscountPercentage = (event) => {
			let numberPattern = /^(\d+\.?\d{0,4}|\.\d{1,4})$/
			if (!numberPattern.test(event.value) || Math.abs(event.value) > 100)
				creditNote.value.discount_percentage = 0;
		}

		const onDiscountType = (event) => {
			let selected = event.target.value;
			creditNote.value.discount_id = null;
			creditNote.value.discount_percentage = 0;
			creditNote.value.discount_amount = 0;
			creditNote.value.discount_value_type = (selected === "amount" ? 'amount' : "percentage");
			if (selected != "amount" && selected != "percentage") {
				creditNote.value.discount_id = selected;
				data.value.discounts.map((discount) => {
					if (selected == discount.id) {
						creditNote.value.discount_percentage = discount.percentage;
						creditNote.value.discount_id = discount.id;
					}
				})
			}
		}

		const creditNoteExport = () => {
			toggleExportModal();
			window.location = `${getBaseURL('money')}vouchers/credit_note_export_to_word/${creditNote.value.id}/${selectedTemplateId.value}/${exportFileType.value}`;
		};

		const onFileSelect = (event) => {
			creditNote.value.credit_note_attachment = event.files[0];
		}

		const onNoteChange = (event) => {
			const noteId = event.value;
			selectedNoteId.value = noteId;
			const noteObject = allNotes.value.find(item => item.id === noteId);
			creditNote.value.notes = noteObject ? noteObject.description : null;
		}

		const deleteCreditNote = () => {
			confirm.require({
				message: _lang.money.confirmationDeleteCreditNote,
				header: _lang.confirmationDelete,
				icon: 'pi pi-exclamation-triangle',
				acceptClass: 'p-button-danger',
				acceptLabel: _lang.yes,
				acceptIcon: 'pi pi-check',
				rejectClass: 'p-button-secondary',
				rejectLabel: _lang.no,
				rejectIcon: 'pi pi-times',
				accept: () => {
					axios.delete(`${Api.getApiBaseUrl("money")}/credit_notes/${creditNote.value.id}?organization_id=${data.value.organization_id}`, Api.getInitialHeaders())
						.then(() => {
							toast.add({ severity: 'success', summary: _lang.feedback_messages.success, detail: _lang.money.creditNoteDeletedSuccessfully, life: 3000 });
							setTimeout(() => {
								window.location = getBaseURL("money") + "vouchers/credit_notes/";
							}, timeoutDuration);
						}).catch((error) => {
							toast.add({ severity: 'error', summary: _lang.error, detail: error.response.data.message, life: 3000 });
						});
				}
			});
		};

		const EditExchangeRate = () => {
			loader(true);
			axios.put(`${Api.getApiBaseUrl("money")}/exchangerates/${data.value.organization_id}/${currency.value.id}`, currency.value, Api.getInitialHeaders()).then((response) => {
				if (response.data.exchange_rate) {
					loader(false);
					toggleExchangeRateModal();
					data.value.rates[creditNote.value.currency_id] = response.data.exchange_rate.rate;
					creditNote.value.exchange_rate = data.value.rates[creditNote.value.currency_id].toString();
					toast.add({ severity: 'success', summary: _lang.feedback_messages.success, detail: _lang.feedback_messages.updatesSavedSuccessfully, life: 4000 });
				} else {
					loader(false);
					toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: _lang.feedback_messages.updatesFailed, life: 4000 });

				}
			}).catch((error) => {
				loader(false);
				toast.add({ severity: 'error', summary: _lang.feedback_messages.error, detail: error.response.data.message, life: 4000 });
			});
		};

		watch(creditNote.value, () => {
			resetValidation();
			let subTotalVariable = 0;
			let lineDiscountVariable = 0;
			let taxVariable = 0;
			let subTotalWithDiscount = 0;
			let linesTotal = 0;
			['credit_note_details', 'credit_note_expenses', 'credit_note_time_logs'].map((itemType) => {
				creditNote.value[itemType].map((item) => {
					let subTot = accounting.roundNumber(accounting.multiplyNumbers(item.quantity, item.unit_price));
					subTotalVariable = accounting.addNumbers(subTotalVariable, subTot);
					lineDiscountVariable = accounting.addNumbers(lineDiscountVariable, item.discount_amount);
					taxVariable = accounting.addNumbers(taxVariable, item.tax_amount);
					linesTotal = accounting.addNumbers(linesTotal, item.total);
				});
			});

			let tempAmountForDiscount = null;
			if (creditNote.value.display_discount == 'invoice_level_before_tax' || creditNote.value.display_discount == 'both_item_before_level') {
				tempAmountForDiscount = accounting.subtractNumbers(subTotalVariable, lineDiscountVariable);
			} else if (creditNote.value.display_discount == 'invoice_level_after_tax' || creditNote.value.display_discount == 'both_item_after_level') {
				tempAmountForDiscount = accounting.subtractNumbers(subTotalVariable, lineDiscountVariable);
				tempAmountForDiscount = accounting.addNumbers(tempAmountForDiscount, taxVariable);
			} else {
				creditNote.value.discount_value_type = 'percentage';
				creditNote.value.discount_amount = 0;
				creditNote.value.discount_percentage = 0;
			}
			if (tempAmountForDiscount !== null) {
				if (creditNote.value.discount_value_type == 'amount' && creditNote.value.discount_amount > 0) {
					creditNote.value.discount_percentage = accounting.multiplyNumbers((accounting.toNumber(creditNote.value.discount_amount) / tempAmountForDiscount), 100);
				} else if (creditNote.value.discount_value_type == 'percentage' && creditNote.value.discount_percentage > 0) {
					creditNote.value.discount_amount = accounting.roundNumber(accounting.multiplyNumbers(creditNote.value.discount_percentage, tempAmountForDiscount) / 100);
				}
				if (creditNote.value.discount_percentage <= 0 || creditNote.value.discount_percentage > 100 || creditNote.value.discount_amount <= 0) {
					creditNote.value.discount_amount = 0;
					creditNote.value.discount_percentage = 0;
				}
			}

			let discountVariable = accounting.addNumbers(lineDiscountVariable, creditNote.value.discount_amount);
			subTotalWithDiscount = accounting.subtractNumbers(subTotalVariable, discountVariable);
			let finalAmount = accounting.addNumbers(subTotalWithDiscount, taxVariable);

			creditNote.value.lines_total_discount = lineDiscountVariable;    //sum lines discount
			creditNote.value.lines_total_subtotal = subTotalVariable;    //just sum lines (qty * unit price), before any discount and before tax
			creditNote.value.lines_total_tax = taxVariable; //sum lines taxes 
			creditNote.value.lines_totals = linesTotal;    //sum lines 'total' column
			creditNote.value.total = finalAmount;

			currency.value.id = creditNote.value.currency_id;
			currency.value.code = creditNote.value.currency;
			currency.value.rate = creditNote.value.exchange_rate;

			creditNoteSummary.value.subTotal = accounting.formatMoney(accounting.roundNumber(subTotalVariable), "")
			creditNoteSummary.value.discount = accounting.formatMoney(accounting.roundNumber(discountVariable), "")
			creditNoteSummary.value.subTotalWithDiscount = accounting.formatMoney(accounting.roundNumber(subTotalWithDiscount), "")
			creditNoteSummary.value.tax = accounting.formatMoney(accounting.roundNumber(taxVariable), "")
			creditNoteSummary.value.total = accounting.formatMoney(accounting.roundNumber((finalAmount)), "")
			creditNoteSummary.value.totalValue = accounting.roundNumber(finalAmount)
			creditNoteSummary.value.subTotalEntity = accounting.formatMoney(accounting.roundNumber(subTotalVariable * creditNote.value.exchange_rate), "")
			creditNoteSummary.value.discountEntity = accounting.formatMoney(accounting.roundNumber(discountVariable * creditNote.value.exchange_rate), "")
			creditNoteSummary.value.subTotalWithDiscountEntity = accounting.formatMoney(accounting.roundNumber(subTotalWithDiscount * creditNote.value.exchange_rate), "")
			creditNoteSummary.value.taxEntity = accounting.formatMoney(accounting.roundNumber(taxVariable * creditNote.value.exchange_rate), "")
			creditNoteSummary.value.totalEntity = accounting.formatMoney(accounting.roundNumber((finalAmount) * creditNote.value.exchange_rate), "")
		});

		return {
			data,
			isEditCreditNote,
			breadcrumbCreditNote,
			breadcrumbHome,
			optionsMenuItems,
			optionsMenu,
			statusesMenu,
			showLoader,
			selectedNoteId,
			displayExchangeRateModal,
			creditNote,
			linkedMatters,
			showLinkedMattersModal,
			allNotes,
			validatePage,
			selectedTemplateId,
			editor: ClassicEditor,
			isEditExchangeRate,
			transactionTypesList,
			creditNoteReasonsList,
			termsList,
			taxesList,
			itemsList,
			invoiceTypesList,
			creditNoteSummary,
			creditNoteStatusOptions,
			showPartnerSharesModal,
			displayExportModal,
			exportFileType,
			exportTypesOptions,
			statusesMenuItems,
			summaryCurrencies,
			isClientCurrency,
			partnerData,
			currency,
			partnersCommissions: (isTrue(data.value.partners_commissions) && !isTrue(data.value.is_settlements_per_invoice_enabled)),
			openPartnerSharesModal,
			handlePartnerSharesSubmit,
			closePartnerSharesModal,
			creditNoteExport,
			dateSelected,
			toggleStatusesMenu,
			onDiscountType,
			validateDiscountNumber,
			validateDiscountPercentage,
			toggleExportModal,
			handleSaveCreditNote,
			onNoteChange,
			onFileSelect,
			toggleLinkedMattersModal,
			handleLinkedMattersSubmit,
			handleDetailObjectChange,
			openRelatedMatterModal,
			deleteCreditNote,
			toggleExchangeRateModal,
			handleRemoveMatter,
			toggleOptionsMenu,
			EditExchangeRate,

			showAccountAddressModal,
			openAccountAddressModal,
			handleAccountAddressSubmit,
			closeAccountAddressModal,

			creditNoteAdditionalFields,
			creditNoteRequiredFields,
			accountRequiredFields,
			clientAccountResource,
			personAdditionalIdTypesList,
			companyAdditionalIdTypesList,
			countryList,
		};
	},
	template:
		`
<p-toast baseZIndex="9999"></p-toast>
<loader :show="showLoader"></loader>
<p-confirmdialog></p-confirmdialog>
<div class="container-fluid" v-cloak>
    <div id="breadcrumb-container">
        <p-breadcrumb :home="breadcrumbHome" :model="breadcrumbCreditNote" class="bg-gray-50"></p-breadcrumb>
        <div class="menu-container flex align-items-center">
            <p-menu ref="optionsMenu" :model="optionsMenuItems" :popup="true"></p-menu>
            <p-menu ref="statusesMenu" :model="statusesMenuItems" :popup="true"></p-menu>
            <p-selectbutton v-if="!isEditCreditNote" v-model="creditNote.paid_status"
                :options="creditNoteStatusOptions" option-label="name" option-value="value" class="mr-2 ml-2">
            </p-selectbutton>
            <p-button v-if="isEditCreditNote && creditNote.paid_status === 'draft'"
                label="${_lang.saveChanges}" icon="pi pi-save" @click="handleSaveCreditNote" class="mr-2 ml-2">
            </p-button>
            <p-button v-if="!isEditCreditNote" label="${_lang.money.saveCreditNote}" icon="pi pi-save"
                @click="handleSaveCreditNote" class="mr-2 ml-2"></p-button>
            <p-button v-if="isEditCreditNote" label="${_lang.money.changeStatus}" icon="pi pi-unlock"
                @click="toggleStatusesMenu" class="mr-2 ml-2"></p-button>
            <p-button icon="pi pi-cog" class="p-button-primary mr-2 ml-2" @click="toggleOptionsMenu"
                aria-haspopup="true" aria-controls="overlay_menu"></p-button>
        </div>
    </div>
    <div class="grid mt-2 mb-2">
        <div class="col-12 md:col-9">
            <div class="grid">
                <div class="col-6">
                    <h5 class="text-800 font-semibold text-base">${_lang.clientAccount}
						<span v-if="((isEditCreditNote && creditNote.paid_status == 'draft') || !isEditCreditNote) && creditNote.account_id">
							<a href="javascript:;" @click="openAccountAddressModal()"> <b><i class="fa fa-fw fa-pencil"></i></b></a>
						</span>
					</h5>
                    <p-autocomplete disabled autofocus="!isEditCreditNote" @item-select="selectClient($event)"
                        placeholder="${_lang.startTyping}" class="w-full" inputClass="w-full" minLength="3"
                        autoHighlight forceSelection scrollHeight="400px" v-model="creditNote.client_name"
                        field="label" option-group-label="label" option-group-children="items"> <template
                            #optiongroup="slotProps">
                            <div class="p-d-flex p-ai-center client-item">
                                <div class="p-ml-2">{{slotProps.item.label}}</div>
                            </div>
                        </template> </p-autocomplete>
                </div>
                <div class="col-3">
                    <h5 class="text-800 font-semibold text-base">${_lang.currency}</h5>
                    <p-inputtext class="w-full" inputClass="w-full" v-model="creditNote.currency" disabled>
                    </p-inputtext>
                </div>
                <div class="col-3">
                    <h5 class="text-800 font-semibold text-base">${_lang.exchangeRate}</h5>
                    <div class="p-inputgroup" v-if="!isEditExchangeRate">
                        <p-inputtext class="exchange-rate-input"
                            :placeholder="creditNote.currency ? '1 ' + creditNote.currency + ' = ' + creditNote.exchange_rate + ' ' + data.organization_currency : null"
                            disabled></p-inputtext>
                        <p-button icon="pi pi-pencil" class="p-button-primary"
                            @click="toggleExchangeRateModal"
                            :disabled="(creditNote.paid_status !== 'draft' && isEditCreditNote) || (data.organization_currency_id == creditNote.currency_id)">
                        </p-button>
                    </div>
                </div>
            </div>
            <div class="grid">
                <div class="col-3">
					<h5 class="text-800 font-semibold text-base">${_lang.date}</h5>
				<p-calendar :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote" id="date"
					v-model="creditNote.credit_note_date" @date-select="dateSelected" :showIcon="true"
					class="w-full" inputClass="w-full" />
                </div>
                <div class="col-3">
					<h5 class="text-800 font-semibold text-base">${_lang.terms}</h5>
                    <p-dropdown :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
                        class="w-full" inputClass="w-full" v-model="creditNote.term_id" :options="termsList"
                        option-label="viewValue" option-value="value" placeholder="${_lang.money.selectTerm}">
                    </p-dropdown>
                </div>
				<div class="col-3">
					<h5 class="text-800 font-semibold text-base">${_lang.reason}</h5>
					<p-dropdown :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
						:class="{ 'required-field' : (!creditNote.credit_note_reason_id && validatePage)}" 
						class="w-full" inputClass="w-full" v-model="creditNote.credit_note_reason_id" :options="creditNoteReasonsList"
						option-label="viewValue" option-value="value" placeholder="${_lang.money.selectReason}">
					</p-dropdown>
				</div>
                <div class="col-3">
					<h5 class="text-800 font-semibold text-base">${_lang.money.creditNoteRef}</h5>
					<p-inputtext :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
						class="w-full" inputClass="w-full" type="text"
						v-model="creditNote.credit_note_reference"></p-inputtext>
                </div>

				<div class="col-3" v-if="creditNoteAdditionalFields.indexOf('credit_note_type_id') != -1">
					<h5 class="text-800 font-semibold text-base">${_lang.invoiceType}</h5>
					<p-dropdown v-model="creditNote.credit_note_type_id" :disabled="true" show-clear="true"
						:class="{ 'required-field' : (!creditNote.credit_note_type_id && creditNoteRequiredFields.indexOf('credit_note_type_id') != -1 && validatePage)}" 
						class="w-full" inputClass="w-full" :options="invoiceTypesList"
						option-label="display_name" option-value="id" placeholder="${_lang.selectOption}">
					</p-dropdown>
				</div>
				<div class="col-3" v-if="creditNoteAdditionalFields.indexOf('transaction_type_id') != -1">
					<h5 class="text-800 font-semibold text-base">${_lang.transactionType}</h5>
					<p-dropdown v-model="creditNote.transaction_type_id" :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote" show-clear="true"
						:class="{ 'required-field' : (!creditNote.transaction_type_id && creditNoteRequiredFields.indexOf('transaction_type_id') != -1 && validatePage)}" 
						class="w-full" inputClass="w-full" :options="transactionTypesList"
						option-label="viewValue" option-value="value" placeholder="${_lang.selectOption}">
					</p-dropdown>
				</div>
            </div>
            <div class="grid">
                <div class="col-6">
                    <h5 class="text-800 font-semibold text-base">${_lang.money.billTo}</h5>
                    <p-textarea :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
                        class="w-full" inputClass="w-full" v-model="creditNote.bill_to" rows="3" cols="30">
                    </p-textarea>
                </div>
                <div class="col-6">
                    <h5 class="text-800 font-semibold text-base">${_lang.money.creditNoteDescription}</h5>
                    <p-textarea :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
                        class="w-full" inputClass="w-full" v-model="creditNote.description" rows="3"
                        cols="30"></p-textarea>
                </div>
            </div>
            <div class="w-full mt-3">
                <p-card class="border-double matter-container surface-50"> <template #title>
                        <div v-if="creditNote.related_matters.length === 0" class="w-full text-center">
                            <div>
                                <h5 class="opacity-70">${_lang.money.noMattersLinked}</h5>
                                <p-button v-if="creditNote.paid_status === 'draft' || !isEditCreditNote"
                                    @click="openRelatedMatterModal" label="${_lang.money.clickToAdd}"
                                    class="p-button-outlined mt-2 p-button-secondary font-bold text-blue-600">
                                </p-button>
                            </div>
                        </div>
                        <div class="flex align-items-center" v-if="creditNote.related_matters.length > 0">
                            <span class="text-base mr-3">{{creditNote.related_matters.length}}
                                ${_lang.money.mattersLinked}</span>
                            <p-button type="button"
                                v-if="creditNote.paid_status === 'draft' || !isEditCreditNote"
                                label="${_lang.money.editLinkedMatters}" icon="pi pi-link"
                                class="p-button-sm p-button-info p-button-text mt-1"
                                @click="openRelatedMatterModal"></p-button>
                        </div>
                    </template> <template #content v-if="creditNote.related_matters.length > 0">
                        <div class="related-matters-container">
                            <p-datatable rowEditor :rowHover="true" :value="creditNote.related_matters"
                                class="p-datatable-sm related-matters-table" :scrollable="true"
                                scroll-height="91px">
                                <p-column field="matter_code" header="${_lang.case}" headerClass="bg-white">
                                </p-column>
                                <p-column field="subject" header="${_lang.caseSubject}"
                                    headerClass="bg-white"></p-column>
                                <p-column field="assigned_user" header="${_lang.assignee}"
                                    headerClass="bg-white"></p-column>
                                <p-column field="case_type" header="${_lang.caseType}" headerClass="bg-white">
                                </p-column>
                                <p-column :exportable="false" headerClass="bg-white"
                                    body-style="text-align: center; overflow: visible; padding:0px;">
                                    <template #body="slotProps">
                                        <p-button
                                            v-if="creditNote.paid_status === 'draft' || !isEditCreditNote"
                                            icon="pi pi-trash" class="p-button-rounded p-button-text"
                                            @click="handleRemoveMatter(slotProps.data.id)"></p-button>
                                    </template> </p-column>
                            </p-datatable>
                        </div>
                    </template> </p-card>
            </div>
        </div>
        <div class="col-12 md:col-3">
            <p-card class="w-full credit-note-summary-box"> <template #content>
                    <div class="flex justify-content-between align-items-center"> <span
                            class="font-bold text-xl">${_lang.money.creditNoteSummary}</span>
                        <p-selectbutton v-if="data.organization_currency_id != creditNote.currency_id"
                            v-model="isClientCurrency" :options="summaryCurrencies" option-label="name"
                            option-value="value" class="mr-2 ml-2 select-button-currency"></p-selectbutton>
                    </div>
                    <div class="align-items-center"> <span class="text-xl">${_lang.status}</span>
                        <p-badge v-if="creditNote.paid_status === 'draft'" value="${_lang.money.draft}"
                            severity="warning" size="large"></p-badge>
                        <p-badge v-else-if="creditNote.paid_status === 'open'" value="${_lang.money.open}"
                            severity="info" size="large"></p-badge>
                        <p-badge v-else-if="creditNote.paid_status === 'paid'" value="${_lang.paid}"
                            severity="success" size="large"></p-badge>
                        <p-badge v-else-if="creditNote.paid_status === 'cancelled'" value="${_lang.cancelled}"
                            severity="danger" size="large"></p-badge>
                    </div>
                    <div
                        v-if="(creditNote.paid_status === 'open' || creditNote.paid_status === 'paid') && isEditCreditNote">
                        <span>${_lang.money.creditNoteNumber}</span>
                        <span>{{creditNote.credit_note_number}}</span> </div>
                    <div> <span>${_lang.money.invoiceCredited}</span> <span> <a
                                :href="data.site_url + 'vouchers/invoice_edit/' + creditNote.related_invoice_id.value"
                                target="_blank">{{creditNote.related_invoice_id.viewValue}}</a> </span> </div>
                    <div> <span>${_lang.clientName}</span> <span>{{creditNote.client_name}}</span> </div>
                    <div> <span>${_lang.date}</span> <span>{{creditNote.credit_note_date}}</span> </div>
                    <div> <span>${_lang.money.creditNoteRef}</span>
                        <span>{{creditNote.credit_note_reference}}</span> </div>
                    <div> <span>${_lang.terms}</span>
                        <span>{{termsList.find(item => item.value === creditNote.term_id)?.viewValue}}</span>
                    </div>
                    <p-divider class="m-0"></p-divider>
                    <div> <span> ${_lang.subTotal} <span v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.subTotal}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.subTotalEntity}}</span> </div>
                    <div v-if="creditNote.display_discount"> <span> ${_lang.totalDiscount} <span
                                v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.discount}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.discountEntity}}</span> </div>
                    <div v-if="creditNote.display_discount == 'invoice_level_before_tax' || creditNote.display_discount == 'both_item_before_level'"> <span>
                            ${_lang.subTotalAfterDiscount} <span
                                v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.subTotalWithDiscount}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.subTotalWithDiscountEntity}}</span>
                    </div>
                    <div> <span> ${_lang.money.totalTax} <span v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.tax}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.taxEntity}}</span> </div>
                    <p-divider class="m-0"></p-divider>
                    <div class="text-2xl text-blue-600"> <span>${_lang.total}</span> <span> <span
                                v-if="isClientCurrency">{{creditNoteSummary.total}}</span> <span
                                v-else-if="!isClientCurrency">{{creditNoteSummary.totalEntity}} </span> <span
                                v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{creditNote.currency}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{data.organization_currency}}</span> </span> </div>
                </template> </p-card>
        </div>
    </div>
    <p-fieldset legend="${_lang.money.creditNoteDetails}" :toggleable="true"
        class="credit-note-details-container">
        <div>
            <credit-note-details :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
                :withTax="creditNote.display_tax" :withDiscount="creditNote.display_discount"
                :withDate="creditNote.display_item_date" @handle-change="handleDetailObjectChange"
                @handle-new-tax="openNewTaxModal" @handle-new-item="openNewItemModal"
                :timeLogs="creditNote.credit_note_time_logs" :expenses="creditNote.credit_note_expenses"
                :items="creditNote.credit_note_details" :itemsList="itemsList" :taxesList="taxesList"
                :currency="creditNote.currency" :exchangeRate="creditNote.exchange_rate"
                :withCommission="partnersCommissions" @handle-partners="openPartnerSharesModal"
                :validate="validatePage" :creditNoteDiscountPercent="creditNote.discount_percentage"></credit-note-details>
        </div>
    </p-fieldset>
    <div class="grid mt-5">
        <div class="col-12 md:col-9">
            <p-fieldset legend="${_lang.money.additionalInfo}" :toggleable="true">
                <div class="mt-4">
                    <h5 class="text-800 font-semibold text-base notes-title">${_lang.money.notes}</h5>
                    <p-dropdown :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
                        v-model="selectedNoteId" @change="onNoteChange" class="w-4 mb-2" inputClass="w-full"
                        :options="allNotes" option-label="name" option-value="id"
                        placeholder="${_lang.money.selectNote}"></p-dropdown>
                    <p-blockui :blocked="creditNote.paid_status !== 'draft' && isEditCreditNote">
                        <div
                            :class="{ 'field-disabled' : (creditNote.paid_status !== 'draft' && isEditCreditNote)}">
                            <ckeditor v-model="creditNote.notes" :editor="editor" :config="data.is_rtl ? {language: {
            ui: 'ar', content: 'ar'}} : null"></ckeditor>
                        </div>
                    </p-blockui>
                </div>
                <div class="mt-4 hidden">
                    <h5 class="text-800 font-semibold text-base">${_lang.uploadDocument}</h5>
                    <p-fileupload chooseLabel="${_lang.add}" :showCancelButton="false"
                        :showUploadButton="false" :customUpload="true" :auto="true"
                        :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
                        @uploader="onFileSelect" :multiple="false" accept="image/*" :max-file-size="1000000">
                        <template #empty>
                            <p>${_lang.dragAndDrop.dropFilesHereToUpload}</p>
                        </template> </p-fileupload>
                </div>
            </p-fieldset>
        </div>
        <div class="col-12 md:col-3">
            <p-card class="w-full credit-note-summary-box credit-note-totals-box"> <template #content>
                    <div> <span> ${_lang.subTotal} <span v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.subTotal}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.subTotalEntity}}</span> </div>
                    <div v-if="creditNote.display_discount == 'item_level'"> <span> ${_lang.totalDiscount}
                            <span v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.discount}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.discountEntity}}</span> </div>
                    <div
                        v-if="['invoice_level_after_tax', 'invoice_level_before_tax', 'both_item_after_level', 'both_item_before_level'].includes(creditNote.display_discount)">
                        <span> ${_lang.totalDiscount} <span v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span>
                        <div class="flex justify-content-end">
                            <p-inputnumber :min-fraction-digits="0" maxFractionDigits="2" inputClass="w-6rem"
                                @input="validateDiscountNumber($event)"
                                v-if="creditNote.discount_value_type === 'amount'"
                                :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
                                v-model="creditNote.discount_amount"
                                :class="{ 'p-invalid' : (creditNoteSummary.totalValue < 0 && validatePage)}">
                            </p-inputnumber>
                            <p-inputnumber :min-fraction-digits="0" maxFractionDigits="2" suffix="%"
                                inputClass="w-6rem" @input="validateDiscountPercentage($event)" v-else
                                :disabled="(creditNote.paid_status !== 'draft' && isEditCreditNote)  || creditNote.discount_id > 0"
                                v-model="creditNote.discount_percentage"
                                :class="{ 'p-invalid' : (creditNoteSummary.totalValue < 0 && validatePage)}">
                            </p-inputnumber> <select
                                :disabled="creditNote.paid_status !== 'draft' && isEditCreditNote"
                                @change="onDiscountType($event)" class="w-8rem border-300">
                                <option value="percentage">${_lang.percentage}</option>
                                <option v-for="(discount, i) in data.discounts" :key="i"
                                    :selected="creditNote.discount_id == discount.id" :value="discount.id">
                                    {{ discount.name }} ({{ discount.percentage }} %) </option>
                                <option value="amount"
                                    :selected="creditNote.discount_value_type === 'amount'">
                                    {{creditNote.currency}} </option>
                            </select>
                        </div>
                    </div>
                    <div v-if="['invoice_level_after_tax', 'invoice_level_before_tax', 'both_item_after_level', 'both_item_before_level'].includes(creditNote.display_discount) && creditNoteSummary.totalValue < 0 && validatePage"
                        class="justify-content-end -mt-2"> <small
                            class="input-invalid">${_lang.feedback_messages.noNegativeTotal}</small> </div>
                    <div v-if="creditNote.display_discount"> <span> ${_lang.money.discountAmount} <span
                                v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.discount}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.discountEntity}}</span> </div>
                    <div v-if="creditNote.display_discount == 'invoice_level_before_tax' || creditNote.display_discount == 'both_item_before_level'"> <span>
                            ${_lang.subTotalAfterDiscount} <span
                                v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.subTotalWithDiscount}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.subTotalWithDiscountEntity}}</span>
                    </div>
                    <div v-if="creditNote.display_tax"> <span> ${_lang.money.totalTax} <span
                                v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{'(' + creditNote.currency + ')'}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{'(' + data.organization_currency + ')'}}</span> </span> <span
                            v-if="isClientCurrency">{{creditNoteSummary.tax}}</span> <span
                            v-else-if="!isClientCurrency">{{creditNoteSummary.taxEntity}}</span> </div>
                    <p-divider class="m-0"></p-divider>
                    <div class="text-2xl text-blue-600"> <span>${_lang.total}</span> <span> <span
                                v-if="isClientCurrency">{{creditNoteSummary.total}}</span> <span
                                v-else-if="!isClientCurrency">{{creditNoteSummary.totalEntity}} </span> <span
                                v-if="creditNote.client_id && isClientCurrency"
                                class="ml-1">{{creditNote.currency}}</span> <span
                                v-else-if="creditNote.client_id && !isClientCurrency"
                                class="ml-1">{{data.organization_currency}}</span> </span> </div>
                </template> </p-card>
				<div class="text-start mt-3 ml-3 mr-3">
					<p-button v-if="isEditCreditNote && creditNote.paid_status === 'draft'"
						label="${_lang.saveChanges}" icon="pi pi-save" @click="handleSaveCreditNote">
					</p-button>
					<p-button v-if="!isEditCreditNote" label="${_lang.money.saveCreditNote}" icon="pi pi-save"
						@click="handleSaveCreditNote"></p-button>
				</div>
        </div>
    </div>
    <related-matter @on-submit-matters="handleLinkedMattersSubmit" @onChange="toggleLinkedMattersModal(false)"
        :showModal="showLinkedMattersModal" :matters="linkedMatters"
        :invoicedMatters="creditNote.related_matters" :showDiscount="creditNote.display_discount"
        :showTax="creditNote.display_tax" :taxesList="taxesList"></related-matter>
    <partner-shares :submitshares="handlePartnerSharesSubmit" :closemodal="closePartnerSharesModal"
        :showmodal="showPartnerSharesModal" :partnerdata="partnerData"></partner-shares>
	<account-address @submitaccountaddress="handleAccountAddressSubmit" @closemodal="closeAccountAddressModal" 
		:showmodal="showAccountAddressModal" :countrylist="countryList" :accountrequiredfields="accountRequiredFields"  
		:additionalidtypeslist="clientAccountResource.model_name == 'Company' ? companyAdditionalIdTypesList : personAdditionalIdTypesList" 
		:clientaccountresource="clientAccountResource" :organizationid="data.organization_id" ></account-address>
	<p-dialog header="${_lang.money.exportCreditNote}" v-model:visible="displayExportModal"
        :style="{width: '30vw'}" :modal="true">
		<h5 class="text-800 font-semibold text-base notes-title">${_lang.type}</h5>
		<p-dropdown style="width:100%" class="templates-dropdown" v-model="exportFileType"
            :options="exportTypesOptions" option-label="label" option-value="value"
            placeholder="${_lang.chooseField}">
		</p-dropdown>
        <h5 class="mt-5 text-800 font-semibold text-base notes-title">${_lang.money.invoiceTemplates}</h5>
        <p-dropdown style="width:100%" class="templates-dropdown" v-model="selectedTemplateId"
            :options="data.templates" option-label="name" option-value="id"
            placeholder="${_lang.money.chooseTemplate}">
		</p-dropdown>
		<template #footer>
            <div style="direction:ltr;">
                <p-button :style="data.is_rtl ? 'direction:rtl;' : null" label="${_lang.cancel}"
                    icon="pi pi-times" @click="toggleExportModal" class="p-button-text"></p-button>
                <p-button :style="data.is_rtl ? 'direction:rtl;' : null" :disabled="!selectedTemplateId"
                    label="${_lang.money.exportCreditNote}" icon="pi pi-external-link" @click="creditNoteExport"
                    autofocus></p-button>
            </div>
        </template>
    </p-dialog>
    <p-dialog header="${_lang.money.editExchangeRate}" v-model:visible="displayExchangeRateModal"
        :style="{width: '450px'}" :modal="true">
        <div class="panel">
            <div class="p-formgroup-inline" style="display:flex; justify-content: space-around;">
                <div class="p-field" style="direction: ltr;"> <span for="rate">1 {{currency.code}} = </span>
                    <p-inputnumber id="rate" v-model="currency.rate" :min-fraction-digits="0"
                        :max-fraction-digits="10" :min="0"></p-inputnumber> <span> &nbsp;
                        {{data.organization_currency}}</span>
                </div>
                <p-button type="button" label="${_lang.save}" icon="pi pi-check" @click="EditExchangeRate">
                </p-button>
            </div>
        </div>
    </p-dialog>
</div>
`
};
