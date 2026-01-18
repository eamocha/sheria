export default {
    name: 'ItemRecord',
    components: {
        'p-inputtext': primevue.inputtext,
        'p-inputnumber': primevue.inputnumber,
        'p-dropdown': primevue.dropdown,
        'p-textarea': primevue.textarea
    },
    props: ['withtax', 'withdiscount', 'withdate', 'withquantity', 'withcommission', 'currency', 'exchangerate', 'type', 'category', 'nbrecords', 'expenses', 'timelogs', 'items', 'validate', 'disabled', 'itemslist', 'taxeslist', 'invoicediscountpercent'],
    emits: ['handlechange', 'handlepartners', 'handlenewtax', 'handlenewitem'],
    setup(props, context) {
        const { ref, watch } = Vue;
        const itemRecords = ref([]);
        const currency = ref(props.currency);
        const exchangeRate = ref(props.exchangerate);
        const showValidation = ref(props.validate)
        const disabled = ref(props.disabled)
        const itemsList = ref(props.itemslist)
        const timeLogSummary = ref([])

        const itemsShowDiscount = ref(props.withdiscount)
        const itemsShowTax = ref(props.withtax)
        const itemsShowDate = ref(props.withdate)
        const itemsShowQuantity = ref(props.withquantity)
        const taxesList = ref(props.taxeslist)
        const invoiceDiscountPercent = ref(props.invoicediscountpercent)
        let isFirstLoad = true;

        watch(props, (newValue, oldValue) => {
            showValidation.value = props.validate
            disabled.value = props.disabled
            currency.value = props.currency
            exchangeRate.value = props.exchangerate

            itemsList.value = props.itemslist
            taxesList.value = props.taxeslist
            let isInvDiscChange = (invoiceDiscountPercent.value !== props.invoicediscountpercent);
            invoiceDiscountPercent.value = props.invoicediscountpercent

            itemsShowDiscount.value = props.withdiscount
            itemsShowTax.value = props.withtax
            itemsShowDate.value = props.withdate
            itemsShowQuantity.value = props.withquantity
            if (!itemsShowQuantity.value) resetQuantity()
            if (props.category == "invoice_details") {
                if (props.items.length == 0) {
                    itemRecords.value.length = 0
                    if (props.expenses.length == 0 && props.timelogs.length == 0) {
                        addItemRecord();
                    }
                } else {
                    itemRecords.value = props.items;
                }
            }
            if (props.category == "invoice_expenses") itemRecords.value = props.expenses
            if (props.category == "invoice_time_logs") {
                itemRecords.value = props.timelogs
                fillTimeLogSummary()
            }
            //here since the change maybe in the invoice discount percent so we must recalculate to get new tax amount this 
            //case when discount before tax, so we need the invoice discount percent
            if (isInvDiscChange && (itemsShowDiscount.value == 'invoice_level_before_tax' || itemsShowDiscount.value == 'both_item_before_level'))
                updateAllItemsDataCalc();
            //if we add new row(s) or at edit fill the rows at load, then add an properties to the item row if not yet set. 
            //we use this method and not inside addItemRecord since we need it on edit, on link time log,..
            //also we can't depend on checking item records length now and before change as maybe relatd matter already set
            //and just he edit and submit same related matter expense/timelog or at load when there is empty row and on edit
            //we get one row also.. in this case no change in the item record length but the row data changed.
            //so we need to calculate the row data we do that for example by checking if the total property is defined or not.
            itemRecords.value.map((item, i) => {
                if (item._updateItemDataCalc == undefined) {
                    item._updateItemDataCalc = function () {
                        updateItemDataCalc(item);
                    }
                }
                //
                if (item.total == undefined || item.sub_total_after_line_disc == undefined)
                    updateItemDataCalc(item);
            });
            //at load first time only if from quote then recalculate all items data to fill the totals
            //we use isInvoiceDataLoaded to make sure that data loaded means get record data and itemRecords filled
            if (isInvoiceDataLoaded && isFirstLoad) {
                if (createInvoiceFromQuoteId)
                    updateAllItemsDataCalc();
                isFirstLoad = false;
            }
        });
        // just for invoice items details
        watch(itemRecords.value, (newValue, oldValue) => {
            context.emit('handlechange', { name: props.category, value: newValue });
        });

        const updateAllItemsDataCalc = () => {
            itemRecords.value.map((item, i) => {
                updateItemDataCalc(item);
            });
        }
        const updateItemDataCalc = (item) => {
            item.line_sub_total = accounting.roundNumber(accounting.multiplyNumbers(item.quantity, item.unit_price));
            let subTotalBeforeTax = item.line_sub_total;
            if (showLineDiscount()) {
                if (item.discount_type == "amount")
                    item.discount_percentage = (accounting.multiplyNumbers(item.discount_amount, 100) / item.line_sub_total);
                else
                    item.discount_amount = accounting.roundNumber(accounting.multiplyNumbers(item.line_sub_total, item.discount_percentage) / 100);
                //this case may occur if discount type is amount and price 124 and put discount 60 then change price to 12 only
                if (item.discount_percentage < 0 || item.discount_percentage > 100) {
                    item.discount_percentage = 0;
                    item.discount_amount = 0;
                }
                subTotalBeforeTax = accounting.subtractNumbers(subTotalBeforeTax, item.discount_amount);
            }
            item.sub_total_after_line_disc = subTotalBeforeTax;  //sub-total price after line discount only
            let diff;
            if (itemsShowDiscount.value == 'item_level' || itemsShowDiscount.value == 'both_item_after_level') {
                diff = accounting.subtractNumbers(item.line_sub_total, item.discount_amount);
            } else if (itemsShowDiscount.value == 'invoice_level_before_tax' || itemsShowDiscount.value == 'both_item_before_level') {
                let tempSubTotal = accounting.subtractNumbers(item.line_sub_total, item.discount_amount);
                diff = accounting.subtractNumbers(tempSubTotal, (accounting.multiplyNumbers(tempSubTotal, (invoiceDiscountPercent.value / 100))))
            } else {
                diff = item.line_sub_total;
            }
            item.tax_amount = (itemsShowTax.value ? accounting.roundNumber(accounting.multiplyNumbers(diff, (accounting.toNumber(item.tax_percentage) / 100))) : 0);
            //for the total subtotal price after line discount and after tax, but after tax only in case if discount not before tax
            //if discount before tax then we don't know the tax till subtract also the invoice discount at the line.
            item.total = item.sub_total_after_line_disc;
            if (itemsShowDiscount.value != 'invoice_level_before_tax' && itemsShowDiscount.value != 'both_item_before_level')
                item.total = accounting.addNumbers(item.total, item.tax_amount);
        }

        const resetQuantity = () => {
            if (props.category != "invoice_time_logs")
                itemRecords.value.map((item1) => { item1['quantity'] = 1 })
        }
        const addItemRecord = () => {
            itemRecords.value.push({
                "account_id": null,
                "item_id": null,
                "tax_id": null,
                "discount_id": null,
                "item_title": 0,
                "unit_price": 0,
                "quantity": 1,
                "discount_amount": 0,
                "discount_percentage": 0,
                "tax_percentage": 0,
                "item_description": "",
                "item_date": "",
                "partner_shares": [],
                "discount_type": "percentage",
                "line_sub_total": 0,
                "tax_amount": 0,
                "sub_total_after_line_disc": 0,
                "total": 0
            });
        }

        const removeItemRecord = (index) => {
            itemRecords.value.splice(index, 1);
            updateAllItemsDataCalc();
        }
        const onItemChange = (event, data) => {
            let selectedItem = event.value ?? 0;
            itemsList.value.map((item) => {
                if (selectedItem == item['id']) {
                    data.item_id = item['id'];
                    data.unit_price = accounting.roundNumber(accounting.toNumber(item['price']) / accounting.toNumber(exchangeRate.value));
                    data.account_id = item['account_id'];
                    data.item_description = item['description'];
                    data.item_title = item['name'];
                    data.tax_percentage = 0;
                    data.tax_id = null;
                    taxesList.value.map((tx) => {
                        if (item['tax_id'] == tx['id']) {
                            data.tax_percentage = accounting.toNumber(tx['percentage']);
                            data.tax_id = tx['id'];
                        }
                    })
                    return;
                }
            })
            updateItemDataCalc(data);
        }

        const onDiscountType = (event, data) => {
            let selected = event.target.value
            data.discount_id = null
            data.discount_percentage = 0
            data.discount_amount = 0
            data.discount_type = (selected === "amount" ? "amount" : "percentage")
            if (selected != "amount" && selected != "percentage") {
                data.discount_id = selected
                discountsGlobal.map((disc) => {
                    if (selected == disc['id']) {
                        data.discount_percentage = disc['percentage']
                        data.discount_id = disc['id']
                    }
                })
            }
            updateItemDataCalc(data);
        }

        const onTaxChange = (event, data) => {
            data.tax_percentage = 0
            data.tax_amount = 0
            data.tax_id = null
            let selectedTax = event.value ?? 0
            taxesList.value.map((tx) => {
                if (selectedTax == tx['id']) {
                    data.tax_percentage = accounting.toNumber(tx['percentage'])
                    data.tax_id = tx['id']
                }
            })
            updateItemDataCalc(data);
        }

        const validateDiscountNumber = (event, data) => {
            if (event.target.value > data.line_sub_total)
                data.discount_amount = 0;
            let discount = data.discount_amount + "";
            if (discount.indexOf(".") >= 0)
                data.discount_amount = discount.slice(0, discount.indexOf(".") + accounting.toNumber(allowedDecimalFormatGlobal) + 1);
            updateItemDataCalc(data);
        }
        const validateDiscountPercentage = (event, data) => {
            let numberPattern = new RegExp("^(\\d+\\.?\\d{0," + (accounting.toNumber(allowedDecimalFormatGlobal) + 2) + "}|\\.\\d{1," + (accounting.toNumber(allowedDecimalFormatGlobal) + 2) + "})$");   //  /^(\d+\.?\d{0,4}|\.\d{1,4})$/
            if (!numberPattern.test(event.target.value) || event.target.value < 0 || event.target.value > 100)
                data.discount_percentage = 0
            let discount = data.discount_percentage + "";
            if (discount.indexOf(".") >= 0)
                data.discount_percentage = discount.slice(0, discount.indexOf(".") + accounting.toNumber(allowedDecimalFormatGlobal) + 1);
            updateItemDataCalc(data);
        }

        const onItemQtyChange = (event, data) => {
            data.quantity = accounting.toNumber(event.value);
            updateItemDataCalc(data);
        }

        const onItemPriceChange = (event, data) => {
            //we update it here, since if we log here data.unit_price will still get old price not updated yet 
            //i think this occur when we use "<p-inputnumber .." not the native "<input .."
            data.unit_price = accounting.toNumber(event.value);
            updateItemDataCalc(data);
        }

        const showPartnerSharesModal = (data, index, action = "") => {
            context.emit('handlepartners', { data: data.partner_shares ? data.partner_shares : [], index: index, action: action, category: props.category })
        }
        const mergeGroupBy = (array) => {
            let result = []
            for (const val in array) {
                let obj = {}
                let quantity = 0

                array[val].map((time) => {
                    quantity = Number(quantity) + Number(time['quantity'])
                    obj.userCode = time['timelog_data'][0]['user_code']
                    obj.userName = time['timelog_data'][0]['user_full_name']
                    obj.unitPrice = time['unit_price']
                    obj.quantity = quantity

                })
                result.push(obj)
            }
            return result
        }

        const fillTimeLogSummary = () => {
            timeLogSummary.value = []
            let result = {}
            let key = ""
            itemRecords.value.map((itemSingle) => {
                key = itemSingle['timelog_data'][0]['user_id'] + "_" + itemSingle['unit_price']
                if (!result[key]) result[key] = []
                result[key].push(itemSingle)
            })
            timeLogSummary.value = mergeGroupBy(result)
        }

        const formatTotalAmount = (val) => {
            return accounting.formatMoney(accounting.roundNumber(val), "")
        }

        const calcPartnerCommissionAmount = function (vals, commission_percent) {
            let commission_amount = accounting.toNumber(vals.sub_total_after_line_disc);
            return (commission_amount * commission_percent / 100);
        }

        const showNewTaxModal = (index) => {
            context.emit('handlenewtax', { index: index, category: props.category })
        }
        const showNewItemModal = (index) => {
            context.emit('handlenewitem', { index: index, category: props.category })
        }
        const showLineDiscount = () => {
            return (itemsShowDiscount.value == 'item_level' || itemsShowDiscount.value == 'both_item_after_level' || itemsShowDiscount.value == 'both_item_before_level');
        }

        return {
            itemsList,
            taxesList,
            discounts: discountsGlobal,
            itemRecords,
            itemsShowDiscount,
            itemsShowTax,
            itemsShowDate,
            itemsShowQuantity,
            itemsShowPartners: props.withcommission,
            category: props.category,
            recordsType: props.type,
            currency,
            exchangeRate,
            showValidation,
            disabled,
            timeLogSummary,
            accounting,

            addItemRecord,
            removeItemRecord,
            onItemChange,
            onItemQtyChange,
            onItemPriceChange,
            onDiscountType,
            onTaxChange,
            validateDiscountNumber,
            validateDiscountPercentage,
            formatTotalAmount,
            calcPartnerCommissionAmount,
            showPartnerSharesModal,
            showNewItemModal,
            showNewTaxModal,
            showLineDiscount,
            updateItemDataCalc,
        }
    },
    template: `
        <thead class="invoice-rows-type">
            <tr v-if="itemRecords.length > 0">
                <th colspan=10 class="rows-title" :class="category"> {{recordsType}} </th>
            </tr>
        </thead>
        <tbody class="row-item" :class="category" v-for="(data1, index) in itemRecords" :key="index">
            <tr>
                <td v-if="itemsShowDate">
                    <input :disabled="category != 'invoice_details' || disabled" :class="{ 'required-field' : (!data1.item_id && showValidation) }" type="date" v-model="data1.item_date" class="form-control" />
                    <span :class="(!data1.item_date && showValidation) ? '' : 'd-none'" class='text-danger'> ${_lang.requiredField}</span>      
                 </td>
                <td style="width:15%">
                    <p-dropdown v-if="category == 'invoice_details'" :options="itemsList" option-label="name" option-value="id" v-model="data1.item_id"
                        placeholder="${_lang.money.selectService}" @change="onItemChange($event, data1)" :disabled=disabled :filter="true" :show-clear="true" :class="{ 'required-field' : (!data1.item_id && showValidation) }">
                        <template #option="slotProps">
                            <div class="item-li">
                                <div v-if="slotProps.option.isParent"><strong>{{slotProps.option.name}} </strong></div>
                                <div v-else class="pl-10">{{slotProps.option.name}} </div>
                            </div>
                        </template>
                        <template #footer="slotProps">
                            <div style="padding:5px; border-top: 1px solid #dee2e6;">
                                <a href="javascript:;" @click="showNewItemModal(index)"><b> + ${_lang.money.addService} </b></a>
                            </div>
                        </template>
                    </p-dropdown>
                    <span  v-else-if="category == 'invoice_time_logs'">
                        <input disabled type="text" class="form-control" v-model=data1.item_title />
                        <input type="hidden" class="form-control" v-model=data1.time_log_id />
                    </span>
                    <span v-else-if="category == 'invoice_expenses'">
                        <input disabled type="text" class="form-control" v-model=data1.item_title />
                        <input type="hidden" class="form-control" v-model=data1.expense_id />
                    </span>
                    <span :class="(category == 'invoice_details' && !data1.item_id && showValidation) ? '' : 'd-none'" class='text-danger'> ${_lang.requiredField}</span>      
                </td>

                <td style="width:30%">
                    <p-textarea :disabled=disabled rows=1 v-model="data1.item_description"></p-textarea>
                </td>
                <td style="width:15%">
                    <span  v-if="itemsShowQuantity || category == 'invoice_time_logs'">
                        <p-inputnumber :min-fraction-digits="0" maxFractionDigits="2" :disabled=disabled :min="0" v-model="data1.quantity" @input="onItemQtyChange($event, data1)" :class="{ 'required-field' : ( (data1.quantity<0 || data1.quantity==null) && showValidation) }"></p-inputnumber>
                        <span :class="((data1.quantity<0 || data1.quantity==null) && showValidation ) ? '' : 'd-none'" class='text-danger'> ${_lang.requiredField}</span>
                    </span>
                    <span v-else>
                        <input disabled class="form-control" style='border:0' />
                    </span>
                </td>
                <td style="width:15%">
                    <div class="p-inputgroup">
                        <p-inputnumber :disabled=disabled v-model="data1.unit_price" @input="onItemPriceChange($event, data1)" :class="{ 'required-field' : ( (accounting.toNumber(data1.unit_price)<0 || data1.unit_price==null) && showValidation ) }" :min-fraction-digits="0" :max-fraction-digits="2" :min="0"></p-inputnumber>
                        <span :class="{'gray-bg': disabled}" class="p-inputgroup-addon">{{currency}}</span>
                    </div>
                    <span :class="( (accounting.toNumber(data1.unit_price)<0 || data1.unit_price==null)  && showValidation ) ? '' : 'd-none'" class='text-danger'> ${_lang.requiredField}</span>
                </td>
                <td style="width:10%" v-if="showLineDiscount()">
                    <div class="input-group">
                        <input :disabled=disabled v-if="data1.discount_type === 'amount'" type="text" class="form-control" @input="validateDiscountNumber($event, data1)" v-model="data1.discount_amount" />
                        <input :disabled="disabled || data1.discount_id" v-else type="text" class="form-control" v-model="data1.discount_percentage" @input="validateDiscountPercentage($event, data1)" />
                        <span class="input-group-addon no-padding">
                            <select :disabled=disabled @change="onDiscountType($event, data1)" class="form-control discount-list">
                                <option value="percentage"> % </option>
                                <option v-for="(discount, i) in discounts" :key="i" :selected="data1.discount_id == discount.id" :value="discount.id">
                                    {{ discount.name }} ({{ discount.percentage }} %)
                                </option>
                                <option value="amount" :selected="data1.discount_type == 'amount'" > {{currency}} </option>
                            </select>
                        </span>
                    </div>
                </td>
                <td v-if="itemsShowTax">
                    <p-dropdown :options="taxesList" option-label="label" option-value="id" v-model="data1.tax_id"
                        placeholder="${_lang.money.selectTax}" @change="onTaxChange($event, data1)" :disabled=disabled :filter="true" :show-clear="true">
                        <template #footer="slotProps">
                            <div style="padding:5px; border-top: 1px solid #dee2e6;">
                                <a href="javascript:;" @click="showNewTaxModal(index)"><b> + ${_lang.money.addTax} </b></a>
                            </div>
                        </template>
                    </p-dropdown>
                </td>
                <td class="text-center font-size-14" style="color:#43425d;width:10%">
                    <strong>{{formatTotalAmount(data1.total)}}</strong>
                </td>
                <td style="width:2%">
                    <div v-if="!disabled" class="row">
                        <p class="cursor-pointer-click font-size-14" style="color: #828282;" @click="removeItemRecord(index)"><i class="icon-alignment fa fa-trash" aria-hidden="true"></i></p>
                    </div>
                </td>
            </tr>
            <tr v-if="itemsShowPartners">
                <td colspan=10>
                    <div class="">
                        <ul class="list-inline">
                            <li class="list-inline-item" v-if="!disabled">
                                <a href="javascript:;" @click="showPartnerSharesModal(data1, index,'add')"> <strong> <i class="fa fa-fw fa-plus"></i>${_lang.addPartnersShares}</strong></a>
                            </li>
                            <li class="list-inline-item" v-for="(partner_share, pIndex) in data1.partner_shares" :key="pIndex">
                                <strong>
                                (
                                    {{ partner_share.partner_name }}
                                    - {{ partner_share.partner_commission }}% 
                                    -  {{ formatTotalAmount(calcPartnerCommissionAmount(data1, partner_share.partner_commission)) }} {{currency}})
                                </strong>
                            </li>
                            <li class="list-inline-item" v-if="data1.partner_shares.length > 0 && !disabled">
                                <a class="blue-color" href="javascript:;" @click="showPartnerSharesModal(data1, index)"><strong>${_lang.edit}</strong></a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody class="row-item" :class="category" v-if="category == 'invoice_time_logs' && itemRecords.length > 0 && disabled">
            <tr>
                <td colspan=10 class="no-padding-top">
                    <p>
                        <strong>${_lang.timeLogsSummary} </strong> 
                        <a href="javascript:;" class="pull-right1" data-toggle="collapse" data-target="#collapse-invoice-time-log-summary"><i class="fa fa-fw fa-plus-circle"></i></a>
                    </p>
                    <div id="collapse-invoice-time-log-summary" class="collapse">
                        <table width=50%>
                            <tr v-for="(timeSummary, ind) in timeLogSummary" :key="ind">
                                <td>{{timeSummary.userCode}}</td>
                                <td>{{timeSummary.userName}}</td>
                                <td>{{timeSummary.quantity}} ${labelsGlobal['Time_Logs_Quantity']}</td>
                                <td>{{timeSummary.unitPrice}} {{currency}}/${labelsGlobal['time_logs_quantity_unit']}</td>
                                <td>{{formatTotalAmount(accounting.multiplyNumbers(timeSummary.unitPrice, timeSummary.quantity))}} {{currency}} </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody class="row-item" :class="category" v-if="category == 'invoice_details' && !disabled">
            <tr>
                <td colspan=10 class="text-center" >
                    <p class="border-box-to-add" @click="addItemRecord()"><i class="fa-solid fa-circle-plus"></i> ${_lang.addNewLine}</p>
                </td>
            </tr>
        </tbody>
    `,
};