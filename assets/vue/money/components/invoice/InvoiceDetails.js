import ItemRecord from 'ItemRecord'

export default {
    name: 'InvoiceDetails',
    components: { 'item-record': ItemRecord },
    emits: ['handlechange', 'handlepartners', 'handlenewtax', 'handlenewitem'],
    props: ['timelogs', 'expenses', 'items', 'withtax', 'withdiscount', 'withdate', 'withquantity', 'withcommission', 'currency', 'exchangerate', 'validate', 'disabled', 'itemslist', 'taxeslist', 'invoicediscountpercent'],
    setup(props, context) {
        const { ref, watch } = Vue;
        const timelogs = ref(props.timelogs);
        const expenses = ref(props.expenses);
        const items = ref(props.items);
        const itemsList = ref(props.itemslist);
        const taxesList = ref(props.taxeslist);
        const currency = ref(props.currency);
        const exchangeRate = ref(props.exchangerate);
        const validate = ref(props.validate);
        const disabled = ref(props.disabled);
        const invoiceDiscountPercent = ref(props.invoicediscountpercent);

        const withTax = ref(props.withtax == "1" ? true : false);
        const withDiscount = ref(props.withdiscount);
        const withDate = ref(props.withdate == "1" ? true : false);
        const withQuantity = ref(props.withquantity == "1" ? true : false);

        watch(props, (newValue, oldValue) => {
            exchangeRate.value = props.exchangerate
            currency.value = props.currency
            timelogs.value = props.timelogs
            expenses.value = props.expenses
            items.value = props.items

            itemsList.value = props.itemslist
            taxesList.value = props.taxeslist
            validate.value = props.validate
            disabled.value = props.disabled
            invoiceDiscountPercent.value = props.invoicediscountpercent

            withTax.value = props.withtax == "1" ? true : false
            withDiscount.value = props.withdiscount
            withDate.value = props.withdate == "1" ? true : false
            withQuantity.value = props.withquantity == "1" ? true : false
        })
        const handleObjectChange = (obj) => {
            context.emit('handlechange', { name: obj.name, value: obj.value })
        }

        const handlePartners = (obj) => {
            context.emit('handlepartners', obj)
        }
        const handleNewTax = (obj) => {
            context.emit('handlenewtax', obj)
        }
        const handleNewItem = (obj) => {
            context.emit('handlenewitem', obj)
        }

        return {
            timelogs,
            expenses,
            items,
            itemsList,
            taxesList,
            currency,
            exchangeRate,
            withTax,
            withDiscount,
            withDate,
            withQuantity,
            withcommission: props.withcommission,
            validate,
            disabled,
            invoiceDiscountPercent,
            handleObjectChange,
            handlePartners,
            handleNewTax,
            handleNewItem,
        }
    },
    template: `
    <div>
        <table class="table">
            <thead class="invoice-rows-type">
                <tr>
                    <th v-if="withDate">${labelsGlobal['date']} </th>
                    <th>${labelsGlobal['Items_Item']}</th>
                    <th>${labelsGlobal['Items_Description']}</th>
                    <th>${labelsGlobal['Items_Quantity']}</th>
                    <th>${labelsGlobal['Items_Unit_Price']} ({{currency}}) </th>
                    <th v-if="withDiscount == 'item_level' || withDiscount == 'both_item_after_level' || withDiscount == 'both_item_before_level'">${labelsGlobal['Items_Discount']} </th>
                    <th v-if="withTax">${labelsGlobal['Items_Tax']} (%)</th>
                    <th class="text-center">${labelsGlobal['Items_Amount']} ({{currency}})</th>
                    <th></th>
                </tr>
            </thead>
            <item-record @handlechange = "handleObjectChange" @handlepartners= "handlePartners" @handlenewtax= "handleNewTax" @handlenewitem= "handleNewItem" :itemslist=itemsList :taxeslist=taxesList :withtax=withTax :withdiscount=withDiscount :withdate=withDate :withquantity=withQuantity category="invoice_expenses" :withcommission=withcommission type="${labelsGlobal['expenses']}" :expenses="expenses" :currency=currency :exchangerate=exchangeRate :validate=validate :invoicediscountpercent=invoiceDiscountPercent :disabled=disabled></item-record>
            <item-record @handlechange = "handleObjectChange" @handlepartners= "handlePartners" @handlenewtax= "handleNewTax" @handlenewitem= "handleNewItem" :itemslist=itemsList :taxeslist=taxesList :withtax=withTax :withdiscount=withDiscount :withdate=withDate :withquantity=withQuantity category="invoice_time_logs" :withcommission=withcommission type="${labelsGlobal['time_logs']}"  :timelogs="timelogs" :currency=currency :exchangerate=exchangeRate :validate=validate :invoicediscountpercent=invoiceDiscountPercent :disabled=disabled></item-record>
            <item-record @handlechange = "handleObjectChange" @handlepartners= "handlePartners" @handlenewtax= "handleNewTax" @handlenewitem= "handleNewItem" :itemslist=itemsList :taxeslist=taxesList :withtax=withTax :withdiscount=withDiscount :withdate=withDate :withquantity=withQuantity category="invoice_details" :withcommission=withcommission type="${labelsGlobal['items']}" nbrecords=1 :items="items" :timelogs="timelogs" :expenses="expenses" :currency=currency :exchangerate=exchangeRate :validate=validate :invoicediscountpercent=invoiceDiscountPercent :disabled=disabled></item-record>
        </table>
    </div>
    `,
};