function contactIndex(){
    function startIntro(){
        var intro = introJs();
        intro.setOptions({
            steps: [
                {
                    element: "#contacts-menu-demo",
                    intro: introTemplate( _lang.walkthrough.contactsMenuDemo, 'contacts.png'),
                },
                {
                    element: "#gridFiltersList",
                    intro: introTemplate( _lang.walkthrough.gridFiltersListContact, 'custimization_filter_data.svg'),
                    position: 'top'
                },
                {
                    element: "#filtersForm",
                    intro: introTemplate( _lang.walkthrough.filtersFormContact, 'quick_filters.svg'),
                    position: 'top'
                },
                {
                    element: "#searchResults",
                    intro: introTemplate( _lang.walkthrough.searchResultsContact, 'contacts.png'),
                    position: 'top'
                },
            ],
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        intro.onbeforechange(function(targetElement) {
            if (this._currentStep === 0) {
                jQuery("#contacts-menu-demo-open").addClass('show');
            }
            if (this._currentStep === 1) {
                jQuery("#contacts-menu-demo-open").removeClass('show');
            }
        });
        intro.start();
    }
    if(workthrough.length == 0  && isloggedIn == "logged" || (jQuery.inArray('contacts', workthrough)  === -1 && isloggedIn == "logged")){
        setTimeout(startIntro,1500);
        if(chatbotAnable){
            window.Tawk_API.onLoad = function(){
                window.Tawk_API.hideWidget();
            };
        }
        userGuideObject.submitGuide('contacts', true);
    } else{
        supportDemo();
    }
}
function companiesDemo(){
    function startIntro(){
        var intro = introJs();
        intro.setOptions({
            steps: [
                {
                    element: "#companies-menu-demo",
                    intro: introTemplate( _lang.walkthrough.companiesMenuDemo, 'companies.svg'),
                },
                {
                    element: "#gridFiltersList",
                    intro: introTemplate( _lang.walkthrough.gridFiltersListCompanies, 'custimization_filter_data.svg'),
                    position: 'top'
                },
                {
                    element: "#filtersForm",
                    intro: introTemplate( _lang.walkthrough.filtersFormCompanies, 'quick_filters.svg'),
                    position: 'top'
                },
                {
                    element: "#companyGrid",
                    intro: introTemplate( _lang.walkthrough.companyGrid, 'companies.svg'),
                    position: 'top'
                },
            ],
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });

        intro.onbeforechange(function(targetElement) {
            if (this._currentStep === 0) {
                jQuery("#contacts-menu-demo-open").addClass('show');
            }
            if (this._currentStep === 1) {
                jQuery("#contacts-menu-demo-open").removeClass('show');
            }
        });
        intro.start();
        userGuideObject.submitGuide('companies', true);
    }
    if(workthrough.length == 0  && isloggedIn == "logged" || (jQuery.inArray('companies', workthrough)  === -1 && isloggedIn == "logged")){
        if(chatbotAnable){
            window.Tawk_API.onLoad = function(){
                window.Tawk_API.hideWidget();
            };
        }
        setTimeout(startIntro,1500);
    } else{
        supportDemo();
    }
}
function addContactDemo(){
    var intro = introJs();
    intro.setOptions({
        steps: [
            {
                element: "#required-fields-contact-demo",
                intro: introTemplate( _lang.walkthrough.requiredFieldsContactDemo, 'contacts.png'),
                position: 'top'
            },
            {
                element: "#modal-body-add-contact",
                intro: introTemplate( _lang.walkthrough.modalBodyAddContact, 'contacts.png'),
                position: 'left'
            }
        ],
        nextLabel: _lang.next,
        prevLabel: _lang.back,
        doneLabel: _lang.done,
        tooltipClass: 'style-intro',
        exitOnOverlayClick: false,
        showProgress: true
    });
    intro.onexit(function() {
        if(chatbotAnable) window.Tawk_API.showWidget();
    });
    intro.onafterchange(function(element) {
        if (this._currentStep === 2) {
            setTimeout(function() {
                jQuery(".modal-body").scrollTop(95);
            }, 100);
        }
    });
    if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('add_contact', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
        if(jQuery.inArray('add_contact', workthrough)  === -1) workthrough.push('add_contact');
        userGuideObject.submitGuide('add_contact', true);
        if(chatbotAnable){
            window.Tawk_API.onLoad = function(){
                window.Tawk_API.hideWidget();
            };
        }
        intro.start();
    }
}

function addCompanyDemo(){
    var intro = introJs();
    intro.setOptions({
        steps: [
            {
                element: "#required-fields-company-demo",
                intro: introTemplate( _lang.walkthrough.requiredFieldsCompanyDemo, 'companies.svg'),
                position: 'top'
            },
            {
                element: "#modal-body-add-company",
                intro: introTemplate(_lang.walkthrough.modalBodyAddCompany, 'companies.svg'),
                position: 'left'
            }
        ],
        nextLabel: _lang.next,
        prevLabel: _lang.back,
        doneLabel: _lang.done,
        tooltipClass: 'style-intro',
        exitOnOverlayClick: false,
        showProgress: true
    });
    intro.onafterchange(function(element) {
    });
    intro.onexit(function() {
        if(chatbotAnable) window.Tawk_API.showWidget();
    });
    if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('add_company', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
        if(jQuery.inArray('add_company', workthrough)  === -1) workthrough.push('add_company');
        if(chatbotAnable){
            window.Tawk_API.onLoad = function(){
                window.Tawk_API.hideWidget();
            };
        }
        userGuideObject.submitGuide('add_company', true);
        intro.start();
    }
}
function companyDetails()
{
    let stepsDemo = [
        {
            element: "#company-details-wrapper",
            intro: introTemplate( _lang.walkthrough.companyDetailsWrapper, 'company_inforamtion.svg', 'width: 330px;'),
            position: 'top'
        },
        {
            element: ".related-documents-tab-company",
            intro: introTemplate( _lang.walkthrough.relatedDocumentsTabCompany, 'upload_ducuments.svg'),
            position: 'left'
        },
        {
            element: ".related-reminders-tab-company",
            intro: introTemplate( _lang.walkthrough.relatedRemindersTabCompany, 'reminders.svg'),
            position: 'left'
        },
        {
            element: ".related-contacts-tab-company",
            intro: introTemplate( _lang.walkthrough.relatedContactsTabCompany, 'contacts.png'),
            position: 'left'
        },
        {
            element: ".related-matters-tab-company",
            intro: introTemplate( _lang.walkthrough.relatedMattersTabCompany, 'add_matter.svg'),
            position: 'left'
        }
    ];
    if(showExtraTabs == 1){
        if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('company_details', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
            stepsDemo = [
                {
                    element: "#company-details-wrapper",
                    intro: introTemplate( _lang.walkthrough.companyDetailsWrapper, 'company_inforamtion.svg', 'width: 330px;'),
                    position: 'top'
                },
                {
                    element: ".shareholders-tab-company",
                    intro: introTemplate( _lang.walkthrough.shareholdersTabCompany, 'manage_company_share.svg', 'width: 220px;'),
                    position: 'left'
                },
                {
                    element: ".board-members-tab-company",
                    intro: introTemplate( _lang.walkthrough.boardMembersTabCompany, 'assigne_task_specific_user.svg'),
                    position: 'left'
                },
                {
                    element: ".related-documents-tab-company",
                    intro: introTemplate( _lang.walkthrough.relatedDocumentsTabCompany, 'upload_ducuments.svg'),
                    position: 'left'
                },
                {
                    element: ".assets-tab-company",
                    intro: introTemplate( _lang.walkthrough.assetsTabCompany, 'define_rate_persentage.png'),
                    position: 'left'
                },
                {
                    element: ".related-reminders-tab-company",
                    intro: introTemplate( _lang.walkthrough.relatedRemindersTabCompany, 'reminders.svg'),
                    position: 'left'
                },
                {
                    element: ".tree-view-tab-company",
                    intro: introTemplate( _lang.walkthrough.treeViewTabCompany, 'companies.svg'),
                    position: 'left'
                },
                {
                    element: ".bank-accounts-tab-company",
                    intro: introTemplate( _lang.walkthrough.bankAccountsTabCompany, 'bank.svg'),
                    position: 'left'
                },
                {
                    element: ".related-contacts-tab-company",
                    intro: introTemplate( _lang.walkthrough.relatedContactsTabCompany, 'contacts.png'),
                    position: 'left'
                },
                {
                    element: ".related-matters-tab-company",
                    intro: introTemplate( _lang.walkthrough.relatedMattersTabCompany, 'add_matter.svg'),
                    position: 'left'
                },
                {
                    element: ".signature-authority-tab-company",
                    intro: introTemplate( _lang.walkthrough.signatureAuthorityTabCompany, 'signature.svg'),
                    position: 'left'
                },
            ];
        } else{
            stepsDemo = [
                {
                    element: ".shareholders-tab-company",
                    intro: introTemplate( _lang.walkthrough.shareholdersTabCompany, 'manage_company_share.svg', 'width: 220px;'),
                    position: 'left'
                },
                {
                    element: ".board-members-tab-company",
                    intro: introTemplate( _lang.walkthrough.boardMembersTabCompany, 'assigne_task_specific_user.svg'),
                    position: 'left'
                },
                {
                    element: ".assets-tab-company",
                    intro: introTemplate( _lang.walkthrough.assetsTabCompany, 'define_rate_persentage.png'),
                    position: 'left'
                },
                {
                    element: ".tree-view-tab-company",
                    intro: introTemplate( _lang.walkthrough.treeViewTabCompany, 'companies.svg'),
                    position: 'left'
                },
                {
                    element: ".bank-accounts-tab-company",
                    intro: introTemplate( _lang.walkthrough.bankAccountsTabCompany, 'bank.svg'),
                    position: 'left'
                },
                {
                    element: ".signature-authority-tab-company",
                    intro: introTemplate( _lang.walkthrough.signatureAuthorityTabCompany, 'signature.svg'),
                    position: 'left'
                },
            ];
        }
        if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('company_details', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
            if(licensePackage === 'core_contract' || licensePackage === 'contract'){
                stepsDemo.splice(10, 0, {
                    element: ".related-contract-tab-company",
                    intro: introTemplate( _lang.walkthrough.relatedContractTabCompany, 'contact_matter_contract.svg'),
                    position: 'left'
                });
            }
        } else{
            if(licensePackage === 'core_contract' || licensePackage === 'contract'){
                stepsDemo.splice(5, 0, {
                    element: ".related-contract-tab-company",
                    intro: introTemplate( _lang.walkthrough.relatedContractTabCompany, 'contact_matter_contract.svg'),
                    position: 'left'
                });
            }
        }
    } else{
        if(licensePackage === 'core_contract' || licensePackage === 'contract'){
            stepsDemo.push({
                element: ".related-contract-tab-company",
                intro: introTemplate( _lang.walkthrough.relatedContractTabCompany, 'contact_matter_contract.svg'),
                position: 'left'
            });
        }
    }
    setTimeout(insideCompanyDemo,1000);
    function insideCompanyDemo(){
        var intro = introJs();
        intro.setOptions({
            steps: stepsDemo,
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.onbeforechange(function(element) {
            if (this._currentStep === 0) {
                jQuery(window).scrollTop(0);
            }
        });
        intro.onafterchange(function(element) {
            if (this._currentStep === 0) {
                setTimeout(function() {
                    jQuery(window).scrollTop(0);
                }, 400);
            }
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('company_details', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
            if(showExtraTabs == 1){
                userGuideObject.submitGuide('company_details', true);
                userGuideObject.submitGuide('company_details_extra_tabs', true);
                if(chatbotAnable){
                    window.Tawk_API.onLoad = function(){
                        window.Tawk_API.hideWidget();
                    };
                }
                setTimeout(function(){intro.start()},1500);
            } else{
                userGuideObject.submitGuide('company_details', true);
                if(chatbotAnable){
                    window.Tawk_API.onLoad = function(){
                        window.Tawk_API.hideWidget();
                    };
                }
                setTimeout(function(){intro.start()},1500);
            }
        } else{
            if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('company_details_extra_tabs', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
                userGuideObject.submitGuide('company_details_extra_tabs', true);
                if(chatbotAnable){
                    window.Tawk_API.onLoad = function(){
                        window.Tawk_API.hideWidget();
                    };
                }
                setTimeout(function(){intro.start()},1500);
            } else{
                supportDemo();
            }
        }
    }
}
function contactDetails(){
    let stepsDemo = [
        {
            element: "#personal_info_div",
            intro: _lang.walkthrough.personalInfoDivContact,
            intro: introTemplate( _lang.walkthrough.personalInfoDivContact, 'contacts.png'),
            position: 'right'
        },
        {
            element: ".related-documents-tab-contact",
            intro: introTemplate( _lang.walkthrough.relatedDocumentsTabContact, 'upload_ducuments.svg'),
            position: 'left'
        },
        {
            element: ".related-contacts-tab-contact",
            intro: introTemplate( _lang.walkthrough.relatedContactsTabContact, 'contacts.png'),
            position: 'left'
        },
        {
            element: ".related-matters-tab-contact",
            intro: introTemplate( _lang.walkthrough.relatedMattersTabContact, 'add_matter.svg'),
            position: 'left'
        },
        {
            element: ".related-reminders-tab-contact",
            intro: introTemplate( _lang.walkthrough.relatedRemindersTabContact, 'reminders.svg'),
            position: 'left'
        }
    ];
    if(licensePackage === 'core_contract' || licensePackage === 'contract'){
        stepsDemo.splice(4, 0, {
            element: ".related-contracts-tab-contact",
            intro: introTemplate( _lang.walkthrough.relatedContractsTabContact, 'contact_matter_contract.svg'),
            position: 'left'
        });
    }
    setTimeout(insideContactDemo,1000);
    function insideContactDemo(){
        var intro = introJs();
        intro.setOptions({
            steps: stepsDemo,
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('contact_details', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
            userGuideObject.submitGuide('contact_details', true);
            if(chatbotAnable){
                window.Tawk_API.onLoad = function(){
                    window.Tawk_API.hideWidget();
                };
            }
            setTimeout(function(){intro.start()},1500);
        } else{
            supportDemo();
        }
    }
}
function addMatterDemo() {
    function addCaseDemo(){
        var intro = introJs();
        intro.setOptions({
            steps: [
                {
                    element: "#required-fields-matter-demo",
                    intro: introTemplate( _lang.walkthrough.requiredFieldsMatterDemo, 'add_matter.svg'),
                },
                {
                    element: "#modal-body-add-case",
                    intro: introTemplate( _lang.walkthrough.modalBodyAddMatter, 'add_matter.svg'),
                    position: 'top'
                }
            ],
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('add_matter', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
            if(jQuery.inArray('add_matter', workthrough)  === -1) workthrough.push('add_matter');
            userGuideObject.submitGuide('add_matter', true);
            if(chatbotAnable){
                window.Tawk_API.onLoad = function(){
                    window.Tawk_API.hideWidget();
                };
            }
            setTimeout(function(){intro.start()},1500);
        }
        intro.onafterchange(function(element) {
            if (this._currentStep === 2) {
                setTimeout(function() {
                    jQuery(".modal-body").scrollTop(200);
                }, 100);
            }
        });
    }
    setTimeout(addCaseDemo,1500);
}
function addLitigationDemo(){
    function _addLitigationDemo(){
        var intro = introJs();
        intro.setOptions({
            steps: [
                {
                    element: "#required-fields-litigation-demo",
                    intro: introTemplate( _lang.walkthrough.requiredFieldsLitigationDemo, 'add_litigation.svg'),
                },
                {
                    element: "#modal-body-add-litigation",
                    intro: introTemplate( _lang.walkthrough.modalBodyAddLitigation, 'add_litigation.svg'),
                    position: 'top'
                },
            ],
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        if(workthrough.length == 0  && isloggedIn == "logged" && userGuide !== "" || (jQuery.inArray('add_litigation', workthrough)  === -1 && isloggedIn == "logged"  && userGuide !== "")){
            if(jQuery.inArray('add_litigation', workthrough)  === -1) workthrough.push('add_litigation');
            userGuideObject.submitGuide('add_litigation', true);
            if(chatbotAnable){
                window.Tawk_API.onLoad = function(){
                    window.Tawk_API.hideWidget();
                };
            }
            setTimeout(function(){intro.start()},1500);
        }

        intro.onafterchange(function(element) {
            if (this._currentStep === 2) {
                setTimeout(function() {
                    jQuery(".modal-body").scrollTop(450);
                }, 100);
            }
        });
    }
    setTimeout(_addLitigationDemo,1500);
}
function editCaseDemo(category){
    var caselangName = _lang.matter.toLowerCase();
    var caselangNameThe = _lang.thematter.toLowerCase();
    if(category == 1) caselangName =  _lang.litigation.toLowerCase();
    if(category == 1) caselangNameThe =  _lang.thelitigation.toLowerCase();
    let stepsDemo = [
        {
            element: "#personal_info_div",
            intro: introTemplate( _lang.walkthrough.personalInfoDivCase.sprintf([caselangNameThe]), 'add_litigation.svg'),
            position: 'left'
        },
        {
            element: "#case_notes_tabs_btn_container",
            intro: introTemplate(_lang.walkthrough.caseNotesTabsBtnContainer.sprintf([caselangName]), 'add_matter.svg'),
            position: 'top'
        },
        {
            element: "#status-top-nav-container",
            intro: introTemplate( _lang.walkthrough.statusTopNavContainer.sprintf([caselangName]), 'change_workflow.svg'),
        },
        {
            element: ".related-documents-tab-case",
            intro: introTemplate( _lang.walkthrough.relatedDocumentsTabCase.sprintf([caselangName]), 'upload_ducuments.svg'),
            position: 'left'
        },
        {
            element: ".related-expenses-tab-case",
            intro: introTemplate( _lang.walkthrough.relatedExpensesTabCase.sprintf([caselangName]), 'legal_expenses.svg'),
            position: 'left'
        },
        {
            element: ".related-time-logs-tab-case",
            intro: introTemplate( _lang.walkthrough.relatedTimeLogsTabCase.sprintf([caselangName]), 'log_important_update.svg'),
            position: 'left'
        },
        {
            element: ".related-settings-tab-case",
            intro: introTemplate( _lang.walkthrough.relatedSettingsTabCase, 'add_litigation.svg'),
            position: 'left'
        }
    ];
    if(category){
        stepsDemo.splice(3, 0,{
            element: "#cases-actions-button",
            intro: introTemplate(_lang.walkthrough.litigationActionsButton, 'assigne_task_specific_user.svg'),
            position: 'top'
        });
        stepsDemo.splice(4, 0, {
            element: ".related-stages-tab-case",
            intro: introTemplate(_lang.walkthrough.relatedStagesTabCase, 'litigation_stages.png'),
            position: 'left'
        });
    } else{
        stepsDemo.splice(3, 0,{
            element: "#cases-actions-button",
            intro: introTemplate( _lang.walkthrough.casesActionsButton, 'assigne_task_specific_user.svg'),
            position: 'top'
        });
        stepsDemo.splice(4, 0, {
            element: ".related-tasks-tab-case",
            intro: introTemplate(_lang.walkthrough.relatedTasksTabCase.sprintf([caselangName]), 'assign_task.svg'),
            position: 'left'
        });
    }
    function startIntro(){
        var intro = introJs();
        intro.setOptions({
            steps: stepsDemo,
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.onbeforechange(function(element) {
            if (this._currentStep === 0 || this._currentStep === 3) {
                jQuery(window).scrollTop(0);
            }
        });
        intro.onafterchange(function(element) {
            if (this._currentStep === 0 || this._currentStep === 3) {
                setTimeout(function() {
                    jQuery(window).scrollTop(0);
                }, 400);
            }
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        intro.start();
    }
    if(category){
        if(workthrough.length == 0  && isloggedIn == "logged" || (jQuery.inArray('litigation_details', workthrough)  === -1 && isloggedIn == "logged")){
            setTimeout(startIntro,1500);
            if(chatbotAnable){
                window.Tawk_API.onLoad = function(){
                    window.Tawk_API.hideWidget();
                };
            }
            userGuideObject.submitGuide('litigation_details', true);
        } else{
            supportDemo();
        }
    } else{
        if(workthrough.length == 0  && isloggedIn == "logged" || (jQuery.inArray('matter_details', workthrough)  === -1 && isloggedIn == "logged")){
            setTimeout(startIntro,1500);
            if(chatbotAnable){
                window.Tawk_API.onLoad = function(){
                    window.Tawk_API.hideWidget();
                };
            }
            userGuideObject.submitGuide('matter_details', true);
        } else{
            supportDemo();
        }
    }
}
function addTaskDemo(){
    function startIntro(){
        var intro = introJs();
        intro.setOptions({
            steps: [
                {
                    element: "#required-fields-task-demo",
                    intro: introTemplate(_lang.walkthrough.requiredFieldsTaskDemo, 'add_task.svg'),
                    position: 'left'
                },
                {
                    element: "#task-modal-wrapper",
                    intro: introTemplate(_lang.walkthrough.modalBodyAddTask, 'assign_task.svg'),
                    position: 'top'
                },
            ],
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.onafterchange(function(element) {
            if (this._currentStep === 2) {
                setTimeout(function() {
                    jQuery(".modal-body").scrollTop(170);
                }, 100);
            }
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        intro.start();
    }
    if(workthrough.length == 0  && isloggedIn == "logged" || (jQuery.inArray('add_task', workthrough)  === -1 && isloggedIn == "logged")){
        if(jQuery.inArray('add_task', workthrough)  === -1) workthrough.push('add_task');
        setTimeout(startIntro,1500);
        if(chatbotAnable){
            window.Tawk_API.onLoad = function(){
                window.Tawk_API.hideWidget();
            };
        }
        userGuideObject.submitGuide('add_task', true);
    }
}
function editTaskDemo(){
    function startIntro(){
        var intro = introJs();
        intro.setOptions({
            steps: [
                {
                    element: "#reported-by-me",
                    intro: introTemplate(_lang.walkthrough.reportedByMeTask, 'assigne_task_specific_user.svg'),
                    position: 'top'
                },
                {
                    element: "#export-task-demo",
                    intro: introTemplate(_lang.walkthrough.exportTaskDemo, 'assign_task.svg'),
                    position: 'top'
                },
                {
                    element: "#taskLookUp",
                    intro: introTemplate(_lang.walkthrough.taskLookUpDemo, 'custimization_filter_data.svg'),
                    position: 'left'
                },
                {
                    element: ".list-grid-action-demo",
                    intro: introTemplate(_lang.walkthrough.listGridActionDemoTask, 'manage_data_person.svg'),
                    position: 'bottom'
                },
            ],
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });

        intro.onbeforechange(function(targetElement) {
            if (this._currentStep === 1) {
                jQuery("#export-task-demo").show();
            }
            if (this._currentStep === 2) {
                jQuery("#export-task-demo").hide();
            }
            if (this._currentStep === 3) {
                jQuery(".k-grid-content tbody td .dropdown").first().addClass('d-block');
                jQuery(".list-grid-action-demo").first().addClass('show');
            }
            intro.onafterchange(function(element) {
                if (this._currentStep === 3) {
                    setTimeout(function() {
                        // jQuery(".k-grid-content tbody td .dropdown").first().addClass('d-block');
                        jQuery(".list-grid-action-demo").first().addClass('show');
                    }, 100);
                }
            });

            intro._options.steps.forEach(function(value, key) {
                // Look for the element in the DOM
                let element = typeof value.element == 'string' ? document.querySelector(value.element) : value.element;
                // If the element now exists, update the items
                if (element) {
                    intro._introItems[key].element = element;
                }
            });
        });
        intro.oncomplete(function(element) {
            jQuery(".k-grid-content tbody td .dropdown").first().removeClass('d-block');
            jQuery(".list-grid-action-demo").first().removeClass('show');
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        intro.start();
    }
    if(workthrough.length == 0  && isloggedIn == "logged" || (jQuery.inArray('tasks', workthrough)  === -1 && isloggedIn == "logged")){
        setTimeout(startIntro,1500);
        if(chatbotAnable){
            window.Tawk_API.onLoad = function(){
                window.Tawk_API.hideWidget();
            };
        }
        userGuideObject.submitGuide('tasks', true);
    } else{
        supportDemo();
    }
}
function editOpinionDemo(){
    function startIntro(){
        var intro = introJs();
        intro.setOptions({
            steps: [
                {
                    element: "#reported-by-me",
                    intro: introTemplate(_lang.walkthrough.reportedByMeOpinion, 'assigne_opinion_specific_user.svg'),
                    position: 'top'
                },
                {
                    element: "#export-opinion-demo",
                    intro: introTemplate(_lang.walkthrough.exportOpinionDemo, 'assign_opinion.svg'),
                    position: 'top'
                },
                {
                    element: "#opinionLookUp",
                    intro: introTemplate(_lang.walkthrough.opinionLookUpDemo, 'custimization_filter_data.svg'),
                    position: 'left'
                },
                {
                    element: ".list-grid-action-demo",
                    intro: introTemplate(_lang.walkthrough.listGridActionDemoOpinion, 'manage_data_person.svg'),
                    position: 'bottom'
                },
            ],
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });

        intro.onbeforechange(function(targetElement) {
            if (this._currentStep === 1) {
                jQuery("#export-opinion-demo").show();
            }
            if (this._currentStep === 2) {
                jQuery("#export-opinion-demo").hide();
            }
            if (this._currentStep === 3) {
                jQuery(".k-grid-content tbody td .dropdown").first().addClass('d-block');
                jQuery(".list-grid-action-demo").first().addClass('show');
            }
            intro.onafterchange(function(element) {
                if (this._currentStep === 3) {
                    setTimeout(function() {
                        // jQuery(".k-grid-content tbody td .dropdown").first().addClass('d-block');
                        jQuery(".list-grid-action-demo").first().addClass('show');
                    }, 100);
                }
            });

            intro._options.steps.forEach(function(value, key) {
                // Look for the element in the DOM
                let element = typeof value.element == 'string' ? document.querySelector(value.element) : value.element;
                // If the element now exists, update the items
                if (element) {
                    intro._introItems[key].element = element;
                }
            });
        });
        intro.oncomplete(function(element) {
            jQuery(".k-grid-content tbody td .dropdown").first().removeClass('d-block');
            jQuery(".list-grid-action-demo").first().removeClass('show');
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        intro.start();
    }
    if(workthrough.length == 0  && isloggedIn == "logged" || (jQuery.inArray('opinions', workthrough)  === -1 && isloggedIn == "logged")){
        setTimeout(startIntro,1500);
        if(chatbotAnable){
            window.Tawk_API.onLoad = function(){
                window.Tawk_API.hideWidget();
            };
        }
        userGuideObject.submitGuide('opinions', true);
    } else{
        supportDemo();
    }
}
function supportDemo(){
    function startIntro(){
        var intro = introJs();
        intro.setOptions({
            steps: [
                {
                    element: "#support-button-action-wrapper",
                    intro: introTemplate(_lang.walkthrough.supportMenu, 'reach_out_support.png'),
                    position: 'left'
                },
                {
                    element: ".dropdown-menu-customer-support",
                    intro: introTemplate(_lang.walkthrough.dropdownMenuCustomerSupport, 'report_bug.svg'),
                    position: 'left'
                },
                {
                    element: ".dropdown-menu-customer-support",
                    intro: introTemplate(_lang.walkthrough.customerSuccess, 'reach_out_customer_success.png', 'width: 200px;'),
                    position: 'top'
                },
            ],
            nextLabel: _lang.next,
            prevLabel: _lang.back,
            doneLabel: _lang.done,
            tooltipClass: 'style-intro',
            exitOnOverlayClick: false,
            showProgress: true
        });
        intro.oncomplete(function(element) {
            jQuery('.dropdown-menu-customer-support').removeClass('show');
        });
        intro.onexit(function() {
            if(chatbotAnable) window.Tawk_API.showWidget();
        });
        jQuery('.dropdown-menu-customer-support').addClass('show');
        customerSupport();
        intro.start();
    }
    if(APPENVIRONMENT === 'production' && workthrough.length == 0  && isloggedIn == "logged" || (APPENVIRONMENT === 'production' && jQuery.inArray('support', workthrough)  === -1 && isloggedIn == "logged")){
        if(jQuery.inArray('support', workthrough)  === -1) workthrough.push('support');
        setTimeout(startIntro,1500);
        if(chatbotAnable){
            window.Tawk_API.onLoad = function(){
                window.Tawk_API.hideWidget();
            };
        }
        userGuideObject.submitGuide('support', true);
    }
}

function introTemplate(text, image, _style){
    _style = _style || "";
    return '<div class="w-100 white-color-bg padding-15" style="min-height: 155px;"><img style="' + _style + '" class="intro-header-cover-img" src="assets/images/demo/'+ image + '"  /></div><div class="intro-body-style white-color">' + text + '</div>' + '<div class="d-flex">';
}