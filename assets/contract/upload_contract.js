var container,
  id,
  module,
  myDropzone;
// thisDropzone;
jQuery(document).ready(function () {
  container = jQuery(".upload-contract-container");
  jQuery("#show-more-fields", container).click(function () {
    //show more fields
    showMoreFields(container, jQuery("#description", container));
  });
  jQuery("#type", container).change(function () {
    jQuery("#type", container).val(jQuery(this).val()).selectpicker("refresh");
    if (jQuery(this).val()) {
      contractTypeEvent(jQuery(this).val(), jQuery("#sub-type", container));
    }
    addContractTypeCustomFields(jQuery(this).val());
  });
  contractFormEvents(container, selectParties);
  multiStepFormEvents();
  Dropzone.autoDiscover = false;
  myDropzone = new Dropzone(".dropzone", {
    autoProcessQueue: false,
    parallelUploads: 10, // Number of files process at a time (default 2)
    addRemoveLinks: true,
    autoQueue: true,
    uploadMultiple: true,
    maxFiles: 10,
    uploadProgress: function(progress) {
      document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
  }
  });
  myDropzone.on("removedfile" , file => {
    var fileCount = myDropzone.files.length;
    if (fileCount == 0) {
      jQuery('#upload-icon').removeClass('d-none');
    }
  })
  myDropzone.on("addedfile", file => {
  jQuery('#upload-icon').addClass('d-none');

  });
});

function multiStepFormEvents() {
  var currentFs, nextFs, previousFs, currentFsNb, nextFsNb, previousFsNb;
  jQuery(".next-btn").click(function () {
    currentFsNb = parseInt(jQuery("#current-fs", container).val());
    nextFsNb = parseInt(currentFsNb) + 1;
    currentFs = jQuery("#fieldset" + currentFsNb);
    nextFs = jQuery("#fieldset" + nextFsNb);
    jQuery("#step" + nextFsNb).addClass("active");
    nextFs.show();
    currentFs.hide();
    jQuery("#current-fs", container).val(nextFsNb);
    if (currentFsNb == 1) {
      jQuery(".previous-btn").removeClass("d-none");
      jQuery(".next-btn").addClass("d-none");
      jQuery(".submit-btn").removeClass("d-none");
    }
  });

  jQuery(".previous-btn").click(function () {
    currentFsNb = parseInt(jQuery("#current-fs", container).val());
    previousFsNb = parseInt(currentFsNb) - 1;
    currentFs = jQuery("#fieldset" + currentFsNb);
    previousFs = jQuery("#fieldset" + previousFsNb);
    if (currentFsNb > 1) {
      jQuery("#step" + currentFsNb).removeClass("active");
      jQuery("#step" + previousFsNb).addClass("active");
      previousFs.show();
      currentFs.hide();
      jQuery("#current-fs", container).val(previousFsNb);
      if (currentFsNb == 2) {
        jQuery(".previous-btn").addClass("d-none");
        jQuery(".next-btn").removeClass("d-none");
        jQuery(".submit-btn").addClass("d-none");
      }
    } else {
      return false;
    }
  });

  jQuery(".submit-btn").click(function () {
    return false;
  });
}

function contractUpload() {
  var formData = new FormData(
    document.getElementById(
      jQuery("form", ".upload-contract-container").attr("id")
    )
  );
  var fileCount = myDropzone.files.length;
  for (var i = 0; i < fileCount; i++) {
    formData.append(
      "file_" + i,
      jQuery(".dropzone")[0].dropzone.getAcceptedFiles()[i]
    ); // attach dropzone files element
  }
  formData.append("_method", "PUT"); // required to spoof a PUT request for a FormData object (not needed for POST request)
  formData.append("option" , "upload");
  jQuery.ajax({
    url: getBaseURL("contract") + "contracts/add",
    dataType: "JSON",
    type: "POST",
    data: formData,
    processData: false, // required for FormData with jQuery
    contentType: false, // required for FormData with jQuery
    cache: false,
    beforeSend: function () {
      jQuery("#loader-global").show();
    },
    success: function (response) {
      jQuery(".inline-error").addClass("d-none");
      if (response.result) {
        window.location.href =
          getBaseURL("contract") + "contracts/view/" + response.id;
      } else {
        displayValidationErrors(response.validationErrors);
      }
    },
    complete: function () {
      jQuery("#loader-global").hide();
    },
    error: defaultAjaxJSONErrorsHandler,
  });
}

function selectParties(ev, data, container) {
  console.log(data.firstName);
  jQuery("#name").val(data.firstName + " " + data.lastName);
  if (typeof data.firstName != "undefined") {
  } else {
    jQuery("#name", container).val(data.firstName + " " + data.lastName);
  }
}