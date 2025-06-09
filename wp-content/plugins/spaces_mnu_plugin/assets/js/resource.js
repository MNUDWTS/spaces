// assets/js/resource.js

(function ($) {
  $(document).ready(function () {
    // Проверяем, был ли загружен объект spacesMnuPlugin
    if (typeof spacesMnuPlugin === "undefined") {
      // console.error("spacesMnuPlugin is not defined");
    } else {
      // console.log("spacesMnuPlugin loaded", spacesMnuPlugin);
      // Инициализируем скрипты
      if (typeof window.initResourceEditFormScripts === "function") {
        window.initResourceEditFormScripts();
      } else {
        console.error("initResourceEditFormScripts is not defined");
      }
    }
  });
})(jQuery);

jQuery(document).ready(function ($) {
  if (
    $("#resource-create-form-container").length > 0 ||
    $("#resource-edit-form-container").length > 0
  ) {
    window.initResourceEditFormScripts();

    if ($("#resource_type").val()) {
      updateDefaultCategories();
    }
  }

  $(document).on("submit", "#resource-form", function (e) {
    // console.log("Save button clicked");
    e.preventDefault(); // Предотвращение стандартного поведения
    // $("#preloader").removeClass("tw-hidden").addClass("tw-fixed");
    // $("input#save-resource").prop("disabled", true);

    const form = this;
    const submitButton = form.querySelector("button[type='submit']");
    const loader = submitButton.querySelector(".loader");

    submitButton.disabled = true;
    loader.classList.remove("tw-hidden");

    var postURL = spacesMnuPlugin.ajaxurl;
    var data = $("form#resource-form").serializeArray(); // Предполагаем, что форма с ID #resource-form используется для создания/редактирования ресурса
    data.push({ name: "action", value: "save_resource" });
    data.push({ name: "doing_ajax_save", value: true });
    data.push({ name: "post_type", value: "resource" }); // Добавляем post_type
    var resourceId = $("#editing_resource_id").val();
    if (resourceId) {
      data.push({ name: "resource_id", value: resourceId });
    }
    data.push({ name: "nonce", value: spacesMnuPlugin.nonce });
    // console.log("Data to be sent:", data); // Диагностический вывод перед запросом

    $.post(postURL, data, function (response) {
      // console.log("Server response:", response);
      // $("#preloader").removeClass("tw-fixed").addClass("tw-hidden");
      // $("input#save-resource").prop("disabled", false);

      if (response.success) {
        // alert("Successfully saved post!");
        window.location.href = "/profile-ru/?section=resources";
      } else {
        // alert("Something went wrong: " + response.data.message);
        Toastify({
          text: "Something went wrong: " + response.data.message,
          duration: 3000,
          close: true,
          gravity: "top",
          position: "right",
          style: {
            background: "#E2474CCC",
          },
        }).showToast();
        submitButton.disabled = false;
        loader.classList.add("tw-hidden");
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.error("AJAX request failed: ", textStatus, errorThrown);
      submitButton.disabled = false;
      loader.classList.add("tw-hidden");
    });
  });

  if (typeof spacesMnuPlugin !== "undefined" && spacesMnuPlugin.i18n) {
    // console.log(spacesMnuPlugin.i18n); // проверка доступности
  } else {
    console.error("i18n is undefined!");
  }

  $(".delete-resource-button").on("click", function (e) {
    e.preventDefault();

    var ajaxurl = spacesMnuPlugin.ajaxurl;
    var resourceId = $(this).data("resource-id");
    var nonce = $(this).data("nonce");

    // Шаг 1: Проверка наличия бронирований
    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "check_resource_bookings",
        resource_id: resourceId,
        nonce: nonce,
      },
      success: function (response) {
        if (response.success && response.data.hasBookings) {
          let bookingsList = response.data.bookings
            .map(
              (booking) =>
                `${spacesMnuPlugin.i18n.bookingId} ${booking.id}, ${spacesMnuPlugin.i18n.date} ${booking.date}, ${spacesMnuPlugin.i18n.user} ${booking.user}`
            )
            .join("\n");

          showModal(
            spacesMnuPlugin.i18n.deleteResource,
            spacesMnuPlugin.i18n.deleteWithBookings +
              " " +
              response.data.bookings.length +
              ". \n\n" +
              spacesMnuPlugin.i18n.confirmDeleteWithBookings,
            function () {
              deleteResourceAndCancelBookings(resourceId, nonce);
            }
          );
        } else {
          showModal(
            spacesMnuPlugin.i18n.deleteResource,
            spacesMnuPlugin.i18n.confirmDelete,
            function () {
              deleteResource(resourceId, nonce);
            }
          );
        }
      },
      error: function () {
        Toastify({
          text: spacesMnuPlugin.i18n.errorCheckingBookings,
          duration: 3000,
          close: true,
          gravity: "top",
          position: "right",
          style: {
            background: "#E2474CCC",
          },
        }).showToast();
      },
    });

    function deleteResource(resourceId, nonce) {
      $.ajax({
        url: spacesMnuPlugin.ajaxurl,
        type: "POST",
        data: {
          action: "delete_resource",
          resource_id: resourceId,
          nonce: nonce,
        },
        success: function (response) {
          handleDeleteResponse(response);
        },
        error: function () {
          Toastify({
            text: "Ошибка при отправке запроса на сервер.",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
              background: "#E2474CCC",
            },
          }).showToast();
        },
      });
    }

    function deleteResourceAndCancelBookings(resourceId, nonce) {
      $.ajax({
        url: spacesMnuPlugin.ajaxurl,
        type: "POST",
        data: {
          action: "delete_resource_and_cancel_bookings",
          resource_id: resourceId,
          nonce: nonce,
        },
        success: function (response) {
          handleDeleteResponse(response);
        },
        error: function () {
          Toastify({
            text: "Ошибка при отправке запроса на сервер.",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
              background: "#E2474CCC",
            },
          }).showToast();
        },
      });
    }

    function handleDeleteResponse(response) {
      if (response.success) {
        Toastify({
          text: response.data.message,
          duration: 3000,
          close: true,
          gravity: "top",
          position: "right",
          style: {
            background: "#00b09b",
          },
        }).showToast();
        location.reload();
      } else {
        Toastify({
          text: response.data.message,
          duration: 3000,
          close: true,
          gravity: "top",
          position: "right",
          style: {
            background: "#E2474CCC",
          },
        }).showToast();
      }
    }
  });
});

(function ($) {
  var ajaxurl = spacesMnuPlugin.ajaxurl;
  var nonce = spacesMnuPlugin.nonce;
  //   console.log("nonce here: ", nonce);
  //   console.log("nonce here: ", nonce);
  //   console.log("AJAX nonce for create form:", spacesMnuPlugin.nonce.createForm);
  //   console.log("AJAX nonce for edit form:", spacesMnuPlugin.nonce.editForm);

  var i18n = spacesMnuPlugin.i18n;
  window.initResourceEditFormScripts = function () {
    // console.log("Initializing scripts");
    // Initialize tabs
    if (typeof $.fn.tabs === "function") {
      $("#resource-localization-tabs").tabs();
    }

    // Initialize character counters for descriptions
    var textareas = document.querySelectorAll(
      'textarea[id^="resource_description_"]'
    );
    textareas.forEach(function (textarea) {
      var localeKey = textarea.id.replace("resource_description_", "");
      var charCount = document.getElementById("char-count-" + localeKey);

      function updateCharCount() {
        var length = textarea.value.length;
        charCount.textContent = length;
      }
      updateCharCount();
      textarea.addEventListener("input", updateCharCount);
    });

    // Handle adding metadata
    $(".add-metadata").on("click", function (e) {
      e.preventDefault();
      var locale = $(this).data("locale");
      var container = $(this).siblings(".metadata-container");
      container.append(
        '<div class="metadata-item tw-flex tw-gap-2 tw-mb-2">' +
          '<input type="text" name="resource_metadata_' +
          locale +
          '_key[]" placeholder="' +
          i18n.keyPlaceholder +
          '" class="tw-border tw-rounded tw-px-3 tw-py-2" />' +
          '<input type="text" name="resource_metadata_' +
          locale +
          '_value[]" placeholder="' +
          i18n.valuePlaceholder +
          '" class="tw-border tw-rounded tw-px-3 tw-py-2" />' +
          '<button type="button" class="remove-metadata tw-bg-red-500 tw-text-white tw-px-3 tw-py-2 tw-rounded">' +
          i18n.remove +
          "</button>" +
          "</div>"
      );
    });

    // Handle removing metadata
    $(document).on("click", ".remove-metadata", function (e) {
      e.preventDefault();
      $(this).closest(".metadata-item").remove();
    });

    // Duplicate data from Russian
    $(".duplicate-from-ru").on("click", function () {
      var locale = $(this).data("locale");

      // Duplicate Name
      var ruName = $("#resource_name_ru").val();
      $("#resource_name_" + locale).val(ruName);

      // Duplicate Description
      var ruDescription = $("#resource_description_ru").val();
      $("#resource_description_" + locale).val(ruDescription);

      // Duplicate Metadata
      var ruMetadataContainer = $("#tab-ru").find(".metadata-container");
      var targetContainer = $("#tab-" + locale).find(".metadata-container");
      targetContainer.empty();

      // Iterate over Russian metadata items and copy them
      ruMetadataContainer.find(".metadata-item").each(function () {
        var key = $(this)
          .find('input[name="resource_metadata_ru_key[]"]')
          .val();
        var value = $(this)
          .find('input[name="resource_metadata_ru_value[]"]')
          .val();

        var metadataItem = $(
          '<div class="metadata-item tw-flex tw-gap-2 tw-mb-2"></div>'
        );
        var keyInput = $(
          '<input type="text" name="resource_metadata_' +
            locale +
            '_key[]" placeholder="' +
            i18n.keyPlaceholder +
            '" class="tw-border tw-rounded tw-px-3 tw-py-2" />'
        ).val(key);
        var valueInput = $(
          '<input type="text" name="resource_metadata_' +
            locale +
            '_value[]" placeholder="' +
            i18n.valuePlaceholder +
            '" class="tw-border tw-rounded tw-px-3 tw-py-2" />'
        ).val(value);
        var removeButton = $(
          '<button type="button" class="remove-metadata tw-bg-red-500 tw-text-white tw-px-3 tw-py-2 tw-rounded">' +
            i18n.remove +
            "</button>"
        );

        metadataItem.append(keyInput).append(valueInput).append(removeButton);
        targetContainer.append(metadataItem);
      });
    });

    // Handle resource type change
    $("#resource_type").on("change", function () {
      updateDefaultCategories();
    });

    // Function to update default categories
    function updateDefaultCategories() {
      var selectedType = $("#resource_type").val();

      // Set base categories for the current resource type
      var defaultCategories = ["all"];
      if (selectedType === "space") {
        defaultCategories.push("all-space");
      } else if (selectedType === "equipment") {
        defaultCategories.push("all-equipment");
      }

      // Update categories
      var updatedCategories = defaultCategories;

      // Update the hidden input
      $("#resource_categories").val(updatedCategories.join(","));

      // Clear and repopulate the categories container
      $("#resource_categories_container").empty();
      updatedCategories.forEach(function (categorySlug) {
        var categoryName = "";

        if (categorySlug === "all") {
          categoryName = i18n.all;
        } else if (categorySlug === "all-space") {
          categoryName = i18n.allSpaces;
        } else if (categorySlug === "all-equipment") {
          categoryName = i18n.allEquipment;
        } else {
          categoryName = categorySlug;
        }

        // Add the category without a remove button, since it's a base category
        var categoryHtml =
          '<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">' +
          categoryName +
          "</div>";
        $("#resource_categories_container").append(categoryHtml);
      });
    }

    // Initialize default categories on page load if needed
    if (!$("#resource_type").val()) {
      updateDefaultCategories();
    }

    // Initialize autocomplete for resource categories
    $("#resource_category_search")
      .autocomplete({
        minLength: 0,
        source: function (request, response) {
          var selectedType = $("#resource_type").val();
          $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {
              action: "search_categories",
              term: request.term,
              resource_type: selectedType,
              selected_categories: $("#resource_categories").val(),
            },
            success: function (data) {
              response(data);
            },
          });
        },
        select: function (event, ui) {
          var categorySlug = ui.item.slug;
          var categoryName = ui.item.label;
          var selectedCategories = $("#resource_categories")
            .val()
            .split(",")
            .filter(Boolean);
          if (!selectedCategories.includes(categorySlug)) {
            selectedCategories.push(categorySlug);
            $("#resource_categories").val(selectedCategories.join(","));
            $("#resource_categories_container").append(
              '<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">' +
                categoryName +
                '<span class="tw-ml-2 tw-cursor-pointer remove-resource-category" data-category-slug="' +
                categorySlug +
                '">×</span></div>'
            );
          }
          $(this).val("");
          // Trigger search to update suggestions
          $(this).autocomplete("search", "");
          return false;
        },
      })
      .on("focus click", function () {
        // Trigger search on focus and click
        $(this).autocomplete("search", $(this).val());
      });

    // Remove resource category
    $(document).on("click", ".remove-resource-category", function () {
      var categorySlug = $(this).data("category-slug");
      var selectedCategories = $("#resource_categories").val().split(",");
      selectedCategories = selectedCategories.filter(function (slug) {
        return slug != categorySlug;
      });
      $("#resource_categories").val(selectedCategories.join(","));
      $(this).parent().remove();
    });

    // Autocomplete for responsible users
    $("#responsible_user_search").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: ajaxurl,
          type: "POST",
          dataType: "json",
          data: {
            action: "search_users",
            term: request.term,
          },
          success: function (data) {
            response(data);
          },
        });
      },
      select: function (event, ui) {
        var userId = ui.item.id;
        var userName = ui.item.label;
        var responsibleUsers = $("#resource_responsible")
          .val()
          .split(",")
          .filter(Boolean);
        if (!responsibleUsers.includes(userId.toString())) {
          responsibleUsers.push(userId);
          $("#resource_responsible").val(responsibleUsers.join(","));
          $("#responsible_users_container").append(
            '<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">' +
              userName +
              '<span class="tw-ml-2 tw-cursor-pointer remove-responsible-user" data-user-id="' +
              userId +
              '">×</span></div>'
          );
        }
        $(this).val("");
        return false;
      },
    });

    // Remove responsible user
    $(document).on("click", ".remove-responsible-user", function () {
      var userId = $(this).data("user-id");
      var responsibleUsers = $("#resource_responsible")
        .val()
        .split(",")
        .filter(Boolean);
      responsibleUsers = responsibleUsers.filter(function (id) {
        return id != userId;
      });
      $("#resource_responsible").val(responsibleUsers.join(","));
      $(this).parent().remove();
    });

    // Autocomplete for access roles
    $("#access_role_search")
      .autocomplete({
        minLength: 0,
        source: function (request, response) {
          $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {
              action: "search_roles",
              term: request.term,
              selected_roles: $("#resource_access").val(),
            },
            success: function (data) {
              response(data);
            },
          });
        },
        select: function (event, ui) {
          var roleId = ui.item.id;
          var roleName = ui.item.label;
          var accessRoles = $("#resource_access")
            .val()
            .split(",")
            .filter(Boolean);
          if (!accessRoles.includes(roleId.toString())) {
            accessRoles.push(roleId);
            $("#resource_access").val(accessRoles.join(","));
            $("#access_roles_container").append(
              '<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">' +
                roleName +
                '<span class="tw-ml-2 tw-cursor-pointer remove-access-role" data-role-id="' +
                roleId +
                '">×</span></div>'
            );
          }
          $(this).val("");
          return false;
        },
      })
      .on("focus click", function () {
        $(this).autocomplete("search", $(this).val());
      });

    // Remove access role
    $(document).on("click", ".remove-access-role", function () {
      var roleId = $(this).data("role-id");
      var accessRoles = $("#resource_access").val().split(",").filter(Boolean);
      accessRoles = accessRoles.filter(function (id) {
        return id != roleId;
      });
      $("#resource_access").val(accessRoles.join(","));
      $(this).parent().remove();
    });

    // Image uploader
    $("#upload_images_button").on("click", function (e) {
      e.preventDefault();
      var imageFrame;
      var existingImageIds = $("#resource_images")
        .val()
        .split(",")
        .filter(Boolean);

      if (imageFrame) {
        imageFrame.open();
        return;
      }

      // Create media frame
      imageFrame = wp.media({
        title: i18n.selectImages,
        button: {
          text: i18n.addImages,
        },
        multiple: "add",
      });

      // Pass in existing images
      imageFrame.on("open", function () {
        var selection = imageFrame.state().get("selection");
        existingImageIds.forEach(function (id) {
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add(attachment ? [attachment] : []);
        });
      });

      // When images are selected
      imageFrame.on("select", function () {
        var selection = imageFrame.state().get("selection");
        var imageIds = [];
        var imageHtml = "";

        selection.map(function (attachment) {
          attachment = attachment.toJSON();
          imageIds.push(attachment.id);
          imageHtml +=
            '<div class="tw-relative tw-mr-2 tw-mb-2">' +
            '<img src="' +
            attachment.url +
            '" class="tw-w-20 tw-h-20 tw-object-cover tw-rounded" />' +
            '<span class="tw-absolute tw-top-0 tw-right-0 tw-bg-red-500 tw-text-white tw-rounded-full tw-w-5 tw-h-5 tw-flex tw-items-center tw-justify-center tw-cursor-pointer remove-image" data-image-id="' +
            attachment.id +
            '">×</span>' +
            "</div>";
        });

        // Update the hidden input
        $("#resource_images").val(imageIds.join(","));

        // Update the images preview
        $("#images_preview").html(imageHtml);
      });

      imageFrame.open();
    });

    // Remove image
    $(document).on("click", ".remove-image", function () {
      var imageId = $(this).data("image-id");
      var imageIds = $("#resource_images").val().split(",");
      imageIds = imageIds.filter(function (id) {
        return id != imageId;
      });
      $("#resource_images").val(imageIds.join(","));
      $(this).parent().remove();
    });
  };
})(jQuery);
