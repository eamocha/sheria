<div class="row col-md-12 ">j
    <div class="col-md-12 no-margin">
        <br>
        <?php echo form_open("", ["name" => "legalOpinionsSearchFilters", "id" => "legalOpinionsSearchFilters", "method" => "post", "class" => "form-horizontal"]);

        echo form_input(["name" => "take", "value" => "10", "type" => "hidden"]);
        echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
        echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
        echo form_input(["name" => "pageSize", "value" => "10", "type" => "hidden"]);
        echo form_input(["name" => "filter.logic", "id" => "defaultFilterLogic", "value" => "and", "type" => "hidden"]);
        echo form_input(["name" => "quickSearch.logic", "value" => "and", "type" => "hidden"]);
        echo form_input(["name" => "filter.filters[0].filters[0].field", "value" => "opinions.contract_id", "type" => "hidden"]);
        echo form_input(["name" => "filter.filters[0].filters[0].operator", "value" => "eq", "class" => "form-control", "type" => "hidden"]);
        echo form_input(["name" => "filter.filters[0].filters[0].value", "value" => $contract["id"], "class" => "form-control", "type" => "hidden"]);
        ?>
        <div id="filtersFormWrapper" class="row no-margin d-none">
            <div class="row no-margin ui-widget ui-widget-content">
                <h3><?php echo $this->lang->line("opinion_search_filters"); ?></h3>
                <div class="form-group row d-none">
                    <div class="controls">
                        <?php
                        echo form_input(["name" => "quickSearch.filters[0].filters[0].field", "id" => "quickSearchFilterContractField", "value" => "opinions.contract_id", "type" => "hidden"]);
                        echo form_input(["name" => "quickSearch.filters[0].filters[0].operator", "id" => "quickSearchFilterContractOperator", "value" => "eq", "type" => "hidden"]);
                        echo form_input(["name" => "quickSearch.filters[0].filters[0].value", "id" => "quickSearchFilterContractValue", "value" => $contract["id"], "type" => "hidden"]);
                        ?>
                    </div>
                    <div class="controls">
                        <?php
                        echo form_input(["name" => "quickSearch.filters[1].filters[0].field", "id" => "quickSearchFilterSubjectField", "value" => "opinionFullDescription", "type" => "hidden"]);
                        echo form_input(["name" => "quickSearch.filters[1].filters[0].operator", "id" => "quickSearchFilterSubjectOperator", "value" => "contains", "type" => "hidden"]);
                        echo form_input(["name" => "quickSearch.filters[1].filters[0].value", "id" => "quickSearchFilterSubjectValue", "type" => "hidden"]);
                        ?>
                    </div>
                    <div class="controls">
                        <?php
                        echo form_input(["name" => "quickSearch.filters[2].filters[0].field", "id" => "quickSearchFilterTitleField", "value" => "title", "type" => "hidden"]);
                        echo form_input(["name" => "quickSearch.filters[2].filters[0].operator", "id" => "quickSearchFilterTitleOperator", "value" => "contains", "type" => "hidden"]);
                        echo form_input(["name" => "quickSearch.filters[2].filters[0].value", "id" => "quickSearchFilterTitleValue", "type" => "hidden"]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>

        <?php echo form_open("", ["name" => "legalOpinionGridFormContent", "id" => "legalOpinionGridFormContent", "method" => "post", "class" => "no-margin"]); ?>
        <?php echo form_input(["name" => "contract_id", "id" => "contractIdInPage", "value" => $contract["id"], "type" => "hidden"]); ?>
        <div <?php echo $this->session->userdata("AUTH_language") == "arabic" ? 'class="k-rtl"' : ''; ?>>
            <div id="legalOpinionsGrid"></div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
    var contract_id=<?php echo $contract["id"]?>;
    function confirmDelete(url) {
        if (confirm("<?php echo $this->lang->line('confirm_delete'); ?>")) {
            window.location.href = url;
        }
    }

    jQuery(document).ready(function () {
        jQuery("#legalOpinionsGrid").kendoGrid({
            dataSource: {
                transport: {
                    read: {
                        url: getBaseURL("contract")+ 'contracts/related_legalOpinions',
                        dataType: "json",
                        type: "POST",
                        data: function () {
                            const formArray = jQuery("#legalOpinionsSearchFilters").serializeArray();
                            const formData = {};
                            formArray.forEach(item => {
                                formData[item.name] = item.value;
                            });
                            return formData;
                        }

                        // data: function () {
                        //     return jQuery("#legalOpinionsSearchFilters").serialize();
                        // }
                    }
                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
                pageSize: 10,
                schema: {
                    data: "data",
                    total: "total"
                }
            },
            height: 550,
            sortable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            columns: [
                { field: "opinionId", title: "<?php echo $this->lang->line('id'); ?>" },
                { field: "title", title: "<?php echo $this->lang->line('opinion_title'); ?>" },
                { field: "background_info", title: "<?php echo $this->lang->line('background_info'); ?>" },
                { field: "detailed_info", title: "<?php echo $this->lang->line('detailed_info'); ?>" },
                { field: "assigned_to", title: "<?php echo $this->lang->line('assigned_to'); ?>" },
                { field: "createdOn", title: "<?php echo $this->lang->line('createdOn'); ?>" },
                {
                    title: "<?php echo $this->lang->line('actions'); ?>",
                    template: function (dataItem) {
                        var actions = '<a class="btn btn-info btn-xs" href="'+getBaseURL()+'legal_opinions/view/' + dataItem.id + '"><?php echo $this->lang->line('view'); ?></a>';
                        <?php if (isset($is_admin) && $is_admin): ?>
                        actions += ' <a class="btn btn-warning btn-xs" href="'+getBaseURL()+'legal_opinions/edit/' + dataItem.id + '"><?php echo $this->lang->line('edit'); ?></a>';
                        actions += ' <button class="btn btn-danger btn-xs" onclick="confirmDelete('+getBaseURL() + dataItem.id + ')"><?php echo $this->lang->line('delete'); ?></button>';
                        <?php endif; ?>
                        return actions;
                    },
                    sortable: false
                }
            ],
            dataBound: function () {
                this.element.find(".k-grid-content").height(jQuery(window).height() - 350);
            }
        });
    });
</script>
