// assets/js/event.js

(function ($) {
  $(document).ready(function () {
    // Проверяем, был ли загружен объект spacesMnuPluginEvent
    if (typeof spacesMnuPluginEvent === "undefined") {
      // console.error("spacesMnuPluginEvent is not defined");
    } else {
      // console.log("spacesMnuPluginEvent loaded", spacesMnuPluginEvent);
      // Инициализируем скрипты
      if (typeof window.initEventEditFormScripts === "function") {
        window.initEventEditFormScripts();
      } else {
        console.error("initEventEditFormScripts is not defined");
      }
    }
  });
})(jQuery);

jQuery(document).ready(function ($) {
  if (
    $("#event-create-form-container").length > 0 ||
    $("#event-edit-form-container").length > 0
  ) {
    window.initEventEditFormScripts();

    // if ($("#event_type").val()) {
    //   updateDefaultCategories();
    // }
  }
  // перед сохранением нужно проверить нет ли бронирований на ресурс/локацию мероприятия, на дату мероприятия и в промежутке времени мероприятия
  $(document).on("submit", "#event-form", function (e) {
    e.preventDefault();

    const submitButton = document.getElementById("submit-button");
    const loader = submitButton.querySelector(".loader");

    submitButton.disabled = true;
    loader.classList.remove("tw-hidden");

    var postURL = spacesMnuPluginEvent.ajaxurl;
    var data = $("form#event-form").serializeArray();
    data.push({ name: "action", value: "check_resource_availability" });
    data.push({ name: "nonce", value: spacesMnuPluginEvent.nonce });

    $.post(postURL, data, function (response) {
      if (response.success) {
        if (response.data.resource_conflict) {
          // Если ресурс занят, показать модальное окно
          showConfirmationModal(
            response.data.conflict_message,
            function (confirmed) {
              if (confirmed) {
                data.push({ name: "action", value: "save_event" });
                data.push({ name: "doing_ajax_save", value: true });
                data.push({ name: "post_type", value: "event" });
                data.push({ name: "cancel_books", value: true });
                saveEvent(postURL, data);
              } else {
                submitButton.disabled = false;
                loader.classList.add("tw-hidden");
              }
            }
          );
        } else {
          // Если нет конфликта, сохраняем событие
          data.push({ name: "action", value: "save_event" });
          data.push({ name: "doing_ajax_save", value: true });
          data.push({ name: "post_type", value: "event" });
          data.push({ name: "cancel_books", value: false });
          saveEvent(postURL, data);
        }
      } else {
        // Toastify({
        //   text: "Ошибка проверки ресурса: " + response.data.message,
        //   duration: 3000,
        //   close: true,
        //   gravity: "top",
        //   position: "right",
        //   style: { background: "#E2474CCC" },
        // }).showToast();
        data.push({ name: "action", value: "save_event" });
        data.push({ name: "doing_ajax_save", value: true });
        data.push({ name: "post_type", value: "event" });
        data.push({ name: "cancel_books", value: false });
        saveEvent(postURL, data);
        // $("input#save-event").prop("disabled", false);
        submitButton.disabled = false;
        loader.classList.add("tw-hidden");
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      Toastify({
        text: textStatus,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        style: { background: "#E2474CCC" },
      }).showToast();
      console.error("AJAX request failed: ", textStatus, errorThrown);
      submitButton.disabled = false;
      loader.classList.add("tw-hidden");
    });
  });

  function saveEvent(postURL, data) {
    $.post(postURL, data, function (response) {
      $("input#save-event").prop("disabled", false);
      if (response.success) {
        window.location.href = "/profile-ru/?section=events";
      } else {
        Toastify({
          text: "Ошибка сохранения: " + response.data.message,
          duration: 3000,
          close: true,
          gravity: "top",
          position: "right",
          style: { background: "#E2474CCC" },
        }).showToast();
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.error("AJAX request failed: ", textStatus, errorThrown);
    });
  }

  function showConfirmationModal(message, callback) {
    // Устанавливаем текст сообщения
    $(".modal-message").text(message);

    // Показываем модальное окно
    $(".confirmation-modal").removeClass("tw-hidden").addClass("tw-flex");

    // Обработчики кнопок
    $(".confirm-yes")
      .off("click")
      .on("click", function () {
        callback(true);
        hideModal();
      });

    $(".confirm-no")
      .off("click")
      .on("click", function () {
        callback(false);
        hideModal();
      });
  }

  function hideModal() {
    // Прячем модальное окно
    $(".confirmation-modal").removeClass("tw-flex").addClass("tw-hidden");
  }

  $(".delete-event-button").on("click", function (e) {
    e.preventDefault();

    var ajaxurl = spacesMnuPluginEvent.ajaxurl;
    var eventId = $(this).data("event-id");
    var nonce = $(this).data("nonce");

    // Шаг 1: Проверка наличия бронирований
    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "check_event_bookings",
        event_id: eventId,
        nonce: nonce,
      },
      success: function (response) {
        if (response.success && response.data.hasBookings) {
          let bookingsList = response.data.bookings
            .map(
              (booking) =>
                `${spacesMnuPluginEvent.i18n.bookingId} ${booking.id}, ${spacesMnuPluginEvent.i18n.date} ${booking.date}, ${spacesMnuPluginEvent.i18n.user} ${booking.user}`
            )
            .join("\n");

          showModalEvent(
            spacesMnuPluginEvent.i18n.deleteEvent,
            spacesMnuPluginEvent.i18n.deleteWithBookings +
              " " +
              response.data.bookings.length +
              ". \n\n" +
              spacesMnuPluginEvent.i18n.confirmDeleteWithBookings,
            function () {
              deleteEventAndCancelBookings(eventId, nonce);
            }
          );
        } else {
          showModalEvent(
            spacesMnuPluginEvent.i18n.deleteEvent,
            spacesMnuPluginEvent.i18n.confirmDelete,
            function () {
              deleteEvent(eventId, nonce);
            }
          );
        }
      },
      error: function () {
        Toastify({
          text: spacesMnuPluginEvent.i18n.errorCheckingBookings,
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

    function deleteEvent(eventId, nonce) {
      $.ajax({
        url: spacesMnuPluginEvent.ajaxurl,
        type: "POST",
        data: {
          action: "delete_event",
          event_id: eventId,
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

    function deleteEventAndCancelBookings(eventId, nonce) {
      $.ajax({
        url: spacesMnuPluginEvent.ajaxurl,
        type: "POST",
        data: {
          action: "delete_event_and_cancel_bookings",
          event_id: eventId,
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
  var ajaxurl = spacesMnuPluginEvent.ajaxurl;
  var nonce = spacesMnuPluginEvent.nonce;
  var i18n = spacesMnuPluginEvent.i18n;
  window.initEventEditFormScripts = function () {
    if (typeof $.fn.tabs === "function") {
      $("#event-localization-tabs").tabs();
    }

    $(".add-metadata").on("click", function (e) {
      e.preventDefault();
      var locale = $(this).data("locale");
      var container = $(this).siblings(".metadata-container");
      container.append(
        '<div class="metadata-item tw-flex tw-gap-2 tw-mb-2">' +
          '<input type="text" name="event_metadata_' +
          locale +
          '_key[]" placeholder="' +
          i18n.keyPlaceholder +
          '" class="tw-border tw-rounded tw-px-3 tw-py-2" />' +
          '<input type="text" name="event_metadata_' +
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

    $(document).on("click", ".remove-metadata", function (e) {
      e.preventDefault();
      $(this).closest(".metadata-item").remove();
    });

    $(".duplicate-from-ru").on("click", function () {
      var locale = $(this).data("locale");

      var ruName = $("#event_name_ru").val();
      $("#event_name_" + locale).val(ruName);

      var ruDescription = $("#event_description_ru").val();
      $("#event_description_" + locale).val(ruDescription);

      var ruMetadataContainer = $("#tab-ru").find(".metadata-container");
      var targetContainer = $("#tab-" + locale).find(".metadata-container");
      targetContainer.empty();

      ruMetadataContainer.find(".metadata-item").each(function () {
        var key = $(this).find('input[name="event_metadata_ru_key[]"]').val();
        var value = $(this)
          .find('input[name="event_metadata_ru_value[]"]')
          .val();

        var metadataItem = $(
          '<div class="metadata-item tw-flex tw-gap-2 tw-mb-2"></div>'
        );
        var keyInput = $(
          '<input type="text" name="event_metadata_' +
            locale +
            '_key[]" placeholder="' +
            i18n.keyPlaceholder +
            '" class="tw-border tw-rounded tw-px-3 tw-py-2" />'
        ).val(key);
        var valueInput = $(
          '<input type="text" name="event_metadata_' +
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

    $("#event_category_search")
      .autocomplete({
        minLength: 0,
        source: function (request, response) {
          // Собираем выбранные категории
          var selectedCategories = $("#event_categories").val();

          // Отправляем запрос на сервер без зависимости от типа события
          $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {
              action: "search_event_categories", // PHP handler для поиска категорий
              term: request.term, // Строка поиска из поля ввода
              selected_categories: selectedCategories, // Выбранные категории для фильтрации
            },
            success: function (data) {
              response(data); // Возвращаем данные от сервера в autocomplete
            },
          });
        },
        select: function (event, ui) {
          var categorySlug = ui.item.slug; // Слаг категории
          var categoryName = ui.item.label; // Имя категории
          var selectedCategories = $("#event_categories")
            .val()
            .split(",")
            .filter(Boolean);

          // Если категория не выбрана, добавляем ее
          if (!selectedCategories.includes(categorySlug)) {
            selectedCategories.push(categorySlug); // Добавляем слаг в список
            $("#event_categories").val(selectedCategories.join(",")); // Обновляем скрытое поле

            // Добавляем выбранную категорию в интерфейс
            $("#event_categories_container").append(
              '<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">' +
                categoryName +
                '<span class="tw-ml-2 tw-cursor-pointer remove-event-category" data-category-slug="' +
                categorySlug +
                '">×</span></div>'
            );
          }

          $(this).val(""); // Очищаем поле поиска
          $(this).autocomplete("search", ""); // Обновляем список автозаполнения
          return false;
        },
      })
      .on("focus click", function () {
        $(this).autocomplete("search", $(this).val()); // Открыть автозаполнение при фокусе или клике
      });

    // Удаление категории из списка
    $(document).on("click", ".remove-event-category", function () {
      var categorySlug = $(this).data("category-slug"); // Получаем слаг категории для удаления
      var selectedCategories = $("#event_categories").val().split(",");

      // Убираем категорию из списка
      selectedCategories = selectedCategories.filter(function (slug) {
        return slug != categorySlug;
      });

      $("#event_categories").val(selectedCategories.join(",")); // Обновляем скрытое поле
      $(this).parent().remove(); // Удаляем тег категории из интерфейса
    });

    $("#event_responsible_user_search").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: ajaxurl, // Должен быть установлен через wp_localize_script
          type: "POST",
          dataType: "json",
          data: {
            action: "search_event_users", // Действие AJAX
            term: request.term, // Термин поиска
          },
          success: function (data) {
            response(data); // Передача данных в autocomplete
          },
          error: function (xhr) {
            console.error("Error fetching users:", xhr.responseText);
          },
        });
      },
      select: function (event, ui) {
        var userId = ui.item.id; // ID выбранного пользователя
        var userName = ui.item.label; // Имя выбранного пользователя

        var responsibleUsersEvent = $("#event_responsible")
          .val()
          .split(",")
          .filter(Boolean); // Получаем текущий список ответственных

        // Если пользователя еще нет в списке, добавляем его
        if (!responsibleUsersEvent.includes(userId.toString())) {
          responsibleUsersEvent.push(userId); // Добавляем нового пользователя
          $("#event_responsible").val(responsibleUsersEvent.join(",")); // Обновляем скрытое поле

          // Добавляем пользователя в интерфейс
          $("#event_responsible_users_container").append(
            '<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">' +
              userName +
              '<span class="tw-ml-2 tw-cursor-pointer remove-event-responsible-user" data-user-id="' +
              userId +
              '">×</span></div>'
          );
        }

        $(this).val(""); // Очищаем поле ввода
        return false;
      },
    });

    // Удаление пользователя из списка ответственных
    $(document).on("click", ".remove-event-responsible-user", function () {
      // console.log("Clicked remove button");
      var userId = $(this).data("user-id");
      // console.log("User ID to remove:", userId);
      var responsibleUsersEvent = $("#event_responsible").val().split(",");
      // console.log("Before removal:", responsibleUsersEvent);
      responsibleUsersEvent = responsibleUsersEvent.filter(function (id) {
        return id != userId;
      });
      // console.log("After removal:", responsibleUsersEvent);

      $("#event_responsible").val(responsibleUsersEvent.join(","));
      // console.log("Updated hidden input value:", $("#event_responsible").val());
      $(this).parent().remove();
      // console.log("User removed from UI");
    });

    $("#event_access_role_search")
      .autocomplete({
        minLength: 0,
        source: function (request, response) {
          $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {
              action: "search_event_roles",
              term: request.term,
              selected_roles: $("#event_access").val(),
            },
            success: function (data) {
              response(data);
            },
          });
        },
        select: function (event, ui) {
          // console.log('ui: ', ui);

          var roleId = ui.item.id;
          var roleName = ui.item.label;
          var excludedRoles = [
            "security_staff",
            "management_staff",
            "senior_management_staff",
          ];
          if (excludedRoles.includes(roleId.toString())) {
            return;
          }
          var accessRoles = $("#event_access").val().split(",").filter(Boolean);
          if (!accessRoles.includes(roleId.toString())) {
            accessRoles.push(roleId);
            $("#event_access").val(accessRoles.join(","));
            $("#event_access_roles_container").append(
              '<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">' +
                roleName +
                '<span class="tw-ml-2 tw-cursor-pointer remove-event-access-role" data-role-id="' +
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

    $(document).on("click", ".remove-event-access-role", function () {
      var roleId = $(this).data("role-id");
      var accessRoles = $("#event_access").val().split(",").filter(Boolean);
      accessRoles = accessRoles.filter(function (id) {
        return id != roleId;
      });
      $("#event_access").val(accessRoles.join(","));
      $(this).parent().remove();
    });

    $("#upload_event_images_button").on("click", function (e) {
      e.preventDefault();
      var imageFrame;
      var existingImageIds = $("#event_images")
        .val()
        .split(",")
        .filter(Boolean);

      if (imageFrame) {
        imageFrame.open();
        return;
      }

      imageFrame = wp.media({
        title: i18n.selectImages,
        button: {
          text: i18n.addImages,
        },
        multiple: "add",
      });

      imageFrame.on("open", function () {
        var selection = imageFrame.state().get("selection");
        existingImageIds.forEach(function (id) {
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add(attachment ? [attachment] : []);
        });
      });

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
            '<span class="tw-absolute tw-top-0 tw-right-0 tw-bg-red-500 tw-text-white tw-rounded-full tw-w-5 tw-h-5 tw-flex tw-items-center tw-justify-center tw-cursor-pointer remove-event-image" data-image-id="' +
            attachment.id +
            '">×</span>' +
            "</div>";
        });

        $("#event_images").val(imageIds.join(","));
        $("#event_images_preview").html(imageHtml);
      });

      imageFrame.open();
    });

    $(document).on("click", ".remove-event-image", function () {
      var imageId = $(this).data("image-id");
      var imageIds = $("#event_images").val().split(",");
      imageIds = imageIds.filter(function (id) {
        return id != imageId;
      });
      $("#event_images").val(imageIds.join(","));
      $(this).parent().remove();
    });

    // location

    // Инициализация autocomplete
    // Инициализация autocomplete
    $("#event_location_search")
      .autocomplete({
        minLength: 0,
        source: function (request, response) {
          console.log("Autocomplete request term:", request.term);
          $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {
              action: "get_event_spaces", // PHP handler for this action
              term: request.term, // search term from input
            },
            success: function (data) {
              // Get the currently selected location (either resource or custom text)
              var selectedLocations = $("#event_location")
                .val()
                .split(",")
                .filter(Boolean);

              // Filter out selected locations from the response
              var filteredData = data.success
                ? data.data.filter(function (space) {
                    return !selectedLocations.includes(space.id.toString());
                  })
                : [];

              // Return the filtered data to the autocomplete
              response(
                filteredData.map(function (space) {
                  // Display name based on current locale (e.g., ru_RU, en_US)
                  var currentLanguage = $("html").attr("lang") || "en"; // Assuming lang attribute is set in <html>

                  var label = space.name_en; // Default to English
                  if (currentLanguage === "ru-RU") {
                    label = space.name_ru;
                  } else if (currentLanguage === "kk") {
                    label = space.name_kk;
                  }

                  return {
                    label: label,
                    value: space.id,
                  };
                })
              );
            },
          });
        },
        select: function (event, ui) {
          var spaceId = ui.item.value;
          var spaceName = ui.item.label;

          // Get the current locations from the hidden input
          var locations = $("#event_location").val().split(",").filter(Boolean);

          // If a space is selected and it's not already in the list
          if (!locations.includes(spaceId.toString())) {
            // Clear any existing tag before adding the new one
            $("#location_tags_container").empty();

            locations = [spaceId]; // Keep only the selected space ID
            $("#event_location").val(locations.join(",")); // Update the hidden input

            // Add the selected space as a tag
            $("#location_tags_container").append(
              `<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">${spaceName}<span class="tw-ml-2 tw-cursor-pointer remove-location" data-location-id="${spaceId}">×</span></div>`
            );
          }

          $(this).val(""); // Clear the search field
          return false;
        },
      })
      .on("focus click", function () {
        $(this).autocomplete("search", $(this).val());
      });

    // Allow custom text entry (similar to adding tags)
    $("#event_location_search").on("blur", function () {
      var customText = $(this).val().trim();

      // If the input has some text, add it as a custom location (but avoid duplicates)
      if (customText) {
        var locations = $("#event_location").val().split(",").filter(Boolean);

        // Clear any existing tag before adding the new custom location
        $("#location_tags_container").empty();

        // Only add custom text if it's not already in the list
        if (!locations.includes(customText)) {
          locations = [customText]; // Keep only the custom text
          $("#event_location").val(locations.join(",")); // Update the hidden input

          // Add custom text as a tag
          $("#location_tags_container").append(
            `<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">${customText}<span class="tw-ml-2 tw-cursor-pointer remove-location" data-location-id="${customText}">×</span></div>`
          );
        }
      }

      $(this).val(""); // Clear the search field after adding custom text
    });

    // Prevent form submission when pressing Enter inside the input field
    $("#event_location_search").on("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault(); // Prevent form submission
        var customText = $(this).val().trim();

        // If the input has custom text, add it as a tag
        if (customText) {
          var locations = $("#event_location").val().split(",").filter(Boolean);

          // Clear any existing tag before adding the new custom location
          $("#location_tags_container").empty();

          // Add custom text as a tag if not already in the list
          if (!locations.includes(customText)) {
            locations = [customText]; // Keep only the custom text
            $("#event_location").val(locations.join(",")); // Update the hidden input

            // Add custom text as a tag
            $("#location_tags_container").append(
              `<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center">${customText}<span class="tw-ml-2 tw-cursor-pointer remove-location" data-location-id="${customText}">×</span></div>`
            );
          }
        }

        $(this).val(""); // Clear the search field after adding custom text
      }
    });

    // Remove location tag functionality
    $(document).on("click", ".remove-location", function () {
      var locationId = $(this).data("location-id");
      // var locations = $("#event_location").val().split(",").filter(Boolean);
      var eventLocationValue = $("#event_location").val();
      if (eventLocationValue) {
        var locations = eventLocationValue.split(",").filter(Boolean);
      } else {
        var locations = []; // или другое значение по умолчанию
      }

      // Remove the location
      locations = locations.filter(function (id) {
        return id != locationId;
      });

      $("#event_location").val(locations.join(",")); // Update hidden input
      $(this).parent().remove(); // Remove the tag from UI
    });

    // Re-render tags on page load (after form submission)
    $(document).ready(function () {
      // var locations = $("#event_location").val().split(",").filter(Boolean);
      var eventLocationValue = $("#event_location").val();
      if (eventLocationValue) {
        var locations = eventLocationValue.split(",").filter(Boolean);
      } else {
        var locations = []; // или другое значение по умолчанию
      }

      // If there are any saved locations, render them as tags
      locations.forEach(function (location) {
        // Check if the tag already exists before adding it
        if (
          $("#location_tags_container").find(`[data-location-id="${location}"]`)
            .length === 0
        ) {
          // Create the tag
          var label = location; // Default to the location text (could be space name or custom text)
          var tag = `<div class="tw-bg-gray-200 tw-px-3 tw-py-1 tw-rounded tw-flex tw-items-center" data-location-id="${location}">${label}<span class="tw-ml-2 tw-cursor-pointer remove-location" data-location-id="${location}">×</span></div>`;
          $("#location_tags_container").append(tag);
        }
      });
    });
  };
})(jQuery);
