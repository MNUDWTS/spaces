// assets/js/bookings.js

(function ($) {
  // document.addEventListener('DOMContentLoaded', function () {

  window.bookingsCalendar = function () {
    if (typeof FullCalendar === "undefined") {
      // console.error("FullCalendar is not defined. Please ensure the library is loaded.");
      return;
    }

    const preloader = document.getElementById("users-bookings-preloader");
    const container = document.getElementById("users-bookings");

    function showPreloader() {
      container.classList.remove("tw-block");
      container.classList.add("tw-hidden");
      preloader.classList.remove("tw-hidden");
      preloader.classList.add("tw-flex");
    }

    function hidePreloader() {
      setTimeout(() => {
        preloader.classList.add("tw-hidden");
        preloader.classList.remove("tw-flex");
        container.classList.add("tw-block");
        container.classList.remove("tw-hidden");
      }, 200);
    }

    var calendarEl = document.getElementById("bookings-calendar");
    if (!calendarEl) {
      // console.error("Element with id 'bookings-calendar' not found.");
      return;
    }

    var currentLanguage = spacesMnuData.current_language; // '<?= $current_language; ?>';

    var localeMap = {
      ru_RU: "ru",
      kk: "kk",
      en_US: "en",
    };
    var calendarLocale = localeMap[currentLanguage] || "en";
    // console.log('calendarLocale: ', calendarLocale);

    var kazakhLocale = {
      monthNames: [
        "Қаңтар",
        "Ақпан",
        "Наурыз",
        "Сәуір",
        "Мамыр",
        "Маусым",
        "Шілде",
        "Тамыз",
        "Қыркүйек",
        "Қазан",
        "Қараша",
        "Желтоқсан",
      ],
      monthNamesShort: [
        "Қаң",
        "Ақп",
        "Нау",
        "Сәу",
        "Мам",
        "Мау",
        "Шіл",
        "Там",
        "Қыр",
        "Қаз",
        "Қар",
        "Жел",
      ],
      dayNames: [
        "Жексенбі",
        "Дүйсенбі",
        "Сейсенбі",
        "Сәрсенбі",
        "Бейсенбі",
        "Жұма",
        "Сенбі",
      ],
      dayNamesShort: ["Жек", "Дүй", "Сей", "Сәр", "Бей", "Жұм", "Сен"],
      dayNamesMin: ["Жк", "Дү", "Сс", "Ср", "Бс", "Жм", "Сб"],
    };

    var russianLocale = {
      monthNames: [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь",
      ],
      monthNamesShort: [
        "Янв",
        "Фев",
        "Мар",
        "Апр",
        "Май",
        "Июн",
        "Июл",
        "Авг",
        "Сен",
        "Окт",
        "Ноя",
        "Дек",
      ],
      dayNames: [
        "Воскресенье",
        "Понедельник",
        "Вторник",
        "Среда",
        "Четверг",
        "Пятница",
        "Суббота",
      ],
      dayNamesShort: ["Вск", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"],
      dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
    };

    if (FullCalendar.globalLocales) {
      var kkLocale = FullCalendar.globalLocales.find(function (locale) {
        return locale.code === "kk";
      });

      if (kkLocale) {
        kkLocale.buttonText = {
          prev: "Алдыңғы",
          next: "Келесі",
          today: "Бүгін",
          month: "Ай",
          week: "Апта",
          day: "Күн",
          list: "Күн тәртібі",
        };
        kkLocale.allDayText = "Күні бойы";
        kkLocale.moreLinkText = "тағы";
        kkLocale.noEventsText = "Көрсету үшін оқиғалар жоқ";
        kkLocale.weekText = "Апта";
      }
    }

    const dayMap = {
      понедельник: "monday",
      вторник: "tuesday",
      среда: "wednesday",
      четверг: "thursday",
      пятница: "friday",
      суббота: "saturday",
      воскресенье: "sunday",
      дүйсенбі: "monday",
      сейсенбі: "tuesday",
      сәрсенбі: "wednesday",
      бейсенбі: "thursday",
      жұма: "friday",
      сенбі: "saturday",
      жексенбі: "sunday",
      monday: "monday",
      tuesday: "tuesday",
      wednesday: "wednesday",
      thursday: "thursday",
      friday: "friday",
      saturday: "saturday",
      sunday: "sunday",
    };

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      contentHeight: 400,
      selectable: false,
      locale: calendarLocale,
      firstDay: 1,
      dayHeaderContent: function (arg) {
        if (calendarLocale === "kk") {
          return kazakhLocale.dayNamesMin[arg.date.getDay()];
        }
      },
      titleFormat: {
        year: "numeric",
        month: "long",
      },
      datesSet: function (arg) {
        var header = document.querySelector(
          "#bookings-calendar .fc-toolbar-title"
        );
        var month = new Date(arg.view.currentStart).getMonth();
        var year = new Date(arg.view.currentStart).getFullYear();
        if (calendarLocale === "kk") {
          if (header) {
            header.innerHTML = "";
            header.innerHTML = kazakhLocale.monthNames[month] + " " + year;
          }
        }
        if (calendarLocale === "ru") {
          if (header) {
            header.innerHTML = "";
            header.innerHTML = russianLocale.monthNames[month] + " " + year;
          }
        }
      },
      dateClick: function (info) {
        var clickedDate = new Date(info.date);
        var formattedDate = dateFormatter(clickedDate);
        let dateTitle = document.getElementById("bookings-title");
        dateTitle.innerHTML =
          spacesMnuData.i18n.bookingsForDate + ": " + formattedDate;
        var dayName = clickedDate
          .toLocaleString(
            calendarLocale === "ru"
              ? "ru-RU"
              : calendarLocale === "kk"
              ? "kk-KZ"
              : "en-US",
            {
              weekday: "long",
            }
          )
          .toLowerCase();
        var englishDayName = dayMap[dayName];
        document.querySelectorAll(".fc-selected-day").forEach(function (el) {
          el.classList.remove("fc-selected-day");
        });
        info.dayEl.classList.add("fc-selected-day");
        loadBookings(formattedDate);
      },
      dayCellClassNames: function (arg) {
        var day = arg.date.getDay();
        var dayName = new Date(arg.date)
          .toLocaleDateString(
            calendarLocale === "ru"
              ? "ru-RU"
              : calendarLocale === "kk"
              ? "kk-KZ"
              : "en-US",
            {
              weekday: "long",
            }
          )
          .toLowerCase();
        var englishDayName = dayMap[dayName];
        return [];
      },
    });

    calendar.render();

    function dateFormatter(date) {
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0"); // Месяцы начинаются с 0, поэтому добавляем 1
      const year = date.getFullYear();
      const formattedDate = `${day}.${month}.${year}`;
      return formattedDate;
    }

    function renderBookings(bookings) {
      // console.log('bookings: ', bookings);

      let html = "";
      container.innerHTML = html;

      if (bookings.length > 0) {
        // Группируем бронирования по пользователю и объединяем последовательные слоты
        const groupedBookings = [];

        bookings.forEach((booking) => {
          if (booking.isDeleted === "0") {
            const lastGroup = groupedBookings[groupedBookings.length - 1];

            // Проверяем, можно ли объединить с последней группой по `requestedBy`, `resource`, `status`, `reason`
            if (
              lastGroup &&
              lastGroup.requestedBy === booking.requestedBy &&
              lastGroup.resource === booking.resource &&
              lastGroup.status === booking.status &&
              lastGroup.reason === booking.reason &&
              getNextSlot(lastGroup.slots[lastGroup.slots.length - 1]) ===
                formatSlot(booking.slot)
            ) {
              lastGroup.slots.push(formatSlot(booking.slot));
              lastGroup.bookingIds.push(booking.id);
            } else {
              // Создаем новую группу для нового пользователя или несоответствующего слота
              groupedBookings.push({
                bookingDate: booking.bookingDate,
                requestedBy: booking.requestedBy,
                first_name: booking.first_name,
                last_name: booking.last_name,
                job_title: booking.job_title,
                department: booking.department,
                reqByEmail: booking.reqByEmail,
                avatar_url: booking.avatar_url,
                resource: booking.resource,
                resourceLink: booking.resourceLink,
                resourceName: booking.resourceName,
                reason: booking.reason,
                cancelledBy: booking.cancelledBy,
                cancelledAt: booking.cancelledAt,
                comment: booking.comment,
                status: booking.status,
                slots: [formatSlot(booking.slot)],
                bookingIds: [booking.id],
              });
            }
          }
        });

        html += '<div class="tw-overflow-auto tw-w-full tw-h-[80vh]">';
        html +=
          '<table class="tw-w-full tw-table tw-border-collapse tw-table-auto"><thead class="tw-table-header-group">';
        html += '<tr class="tw-bg-gray-200 tw-gap-2 tw-table-row">';
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.requestedBy +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.resource +
          "</th>";
        html +=
          '<th class="tw-px-12 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.slots +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.reason +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.comment +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none tw-table-cell">' +
          spacesMnuData.i18n.actions +
          "</th>";

        html += '</tr></thead><tbody class=" tw-table-row-group">';

        groupedBookings.forEach((group) => {
          // console.log('requestedBy: ', group.requestedBy);

          // Создаем строку диапазона, объединяя все слоты через запятую
          const slotRange = group.slots
            .map(
              (slot) =>
                `<span class="tw-mx-auto tw-bg-white tw-text-[10px] tw-p-1 tw-rounded-xl">${slot
                  .split(" - ")
                  .map((time) => time.slice(0, 5))
                  .join("-")}</span>`
            )
            .join("");
          const slotRangeContainer = `<div class="tw-grid tw-grid-cols-1 tw-gap-2 tw-w-full tw-justify-center tw-items-start">${slotRange}</div>`;
          html += `<tr class="tw-gap-2 tw-table-row ${
            group.status === "approved"
              ? "tw-bg-green-200/25"
              : "tw-bg-red-200/25"
          } tw-border-b-[1px] tw-border-zinc-400">`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">
                                <div class="tw-flex tw-flex-col tw-gap-2">
                                    <div class="tw-flex tw-justify-start tw-items-center tw-gap-1">
                                        <img class="tw-flex-shrink-0 tw-inline-block tw-h-10 tw-w-10 tw-rounded-full tw-ring-2 tw-ring-white tw-object-cover" src="${
                                          group.avatar_url ||
                                          "https://secure.gravatar.com/avatar/?s=32&d=mm&r=g"
                                        }" alt="Аватар">
                                        <h3 class="tw-text-black tw-text-base">${
                                          group.first_name +
                                            " " +
                                            group.last_name || group.requestedBy
                                        }</h3>
                                        </div>
                                    <div class="tw-space-y-1">
                                            <h4 class="tw-text-[#999999] tw-text-sm">${
                                              group.job_title || ""
                                            }</h4>
                                            <h4 class="tw-text-[#999999] tw-text-sm">${
                                              group.department || ""
                                            }</h4>
                                            <a href="mailto:${
                                              group.reqByEmail
                                            }"><h4 class="tw-text-blue-600 tw-text-sm">${
            group.reqByEmail || ""
          }</h4></a>
                                       
                                    </div>
                                </div>
                            </td>`;

          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none"><a href="${
            group.resourceLink ? group.resourceLink : "#"
          }">${
            group.resourceName ? group.resourceName : group.resource
          }</a></td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none tw-space-y-2 tw-space-x-2">${slotRangeContainer}</td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">${group.reason}</td>`;
          // html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip">${group.status}</td>`;
          // html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">${group.comment ? group.comment : ''}</td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">
                                ${
                                  group.comment && group.cancelledAt
                                    ? `${group.cancelledBy}: ${
                                        group.comment
                                      } | ${new Date(
                                        group.cancelledAt
                                      ).toLocaleString("ru-RU", {
                                        day: "2-digit",
                                        month: "2-digit",
                                        year: "numeric",
                                        hour: "2-digit",
                                        minute: "2-digit",
                                        hour12: false,
                                      })}`
                                    : ""
                                }
                            </td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">`;

          const today = new Date();
          today.setHours(0, 0, 0, 0);
          const now = new Date();
          const oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);
          if (
            group.status === "approved" &&
            new Date(group.bookingDate) > today
          ) {
            const allSlotsAreFuture = group.slots.every((slot) => {
              const slotStart = new Date(
                `${group.bookingDate}T${slot.split(" - ")[0]}`
              );
              return slotStart > oneHourLater;
            });
            if (allSlotsAreFuture) {
              html += `<button type="button" class="cancel-btn tw-bg-red-500 tw-text-white tw-px-2 tw-py-1 tw-rounded tw-text-sm" data-booking-ids="${group.bookingIds.join(
                ","
              )}">${spacesMnuData.i18n.cancel}</button>`;
            }
          }
          html += "</td></tr>";
        });

        html += "</tbody></table>";
      } else {
        html = "<p>" + spacesMnuData.i18n.noBookingsFound + "</p>";
      }
      container.innerHTML = html;
    }
    setupCancelButtons();

    // Функция для форматирования времени слотов без секунд
    function formatSlot(slot) {
      return slot
        .split(" - ")
        .map((time) => time.slice(0, 5))
        .join(" - ");
    }

    // Вспомогательная функция для получения следующего слота
    function getNextSlot(slot) {
      const [start, end] = slot.split("-").map((time) => time.trim());
      const [endHours, endMinutes] = end.split(":").map(Number);

      const nextSlotStart = new Date();
      nextSlotStart.setHours(endHours, endMinutes, 0, 0);

      const nextSlotEnd = new Date(nextSlotStart);
      nextSlotEnd.setMinutes(nextSlotEnd.getMinutes() + 30);

      return `${formatTime(nextSlotStart)} - ${formatTime(nextSlotEnd)}`;
    }

    // Вспомогательная функция для форматирования времени в HH:MM
    function formatTime(date) {
      return `${String(date.getHours()).padStart(2, "0")}:${String(
        date.getMinutes()
      ).padStart(2, "0")}`;
    }
    // Настройка кнопок отмены для группированных слотов
    function setupCancelButtons() {
      let selectedBookingIds = [];
      const cancelModalBookings = document.getElementById(
        "cancelModalBookings"
      );
      const cancelModalCloseBookings = document.getElementById(
        "cancelModalCloseBookings"
      );
      const confirmCancelBookings = document.getElementById(
        "confirmCancelBookings"
      );
      const cancelCommentBookings = document.getElementById(
        "cancelCommentBookings"
      );

      // Устанавливаем делегирование событий на контейнер с классом .users-bookings
      document
        .getElementById("users-bookings")
        .addEventListener("click", function (event) {
          if (event.target.classList.contains("cancel-btn")) {
            selectedBookingIds = event.target
              .getAttribute("data-booking-ids")
              .split(",");
            openCancelModalBookings();
          }
        });

      function openCancelModalBookings() {
        cancelModalBookings.classList.remove("tw-hidden");
        cancelModalBookings.classList.add("tw-flex");
      }

      function closeCancelModalBookings() {
        cancelModalBookings.classList.add("tw-hidden");
        cancelModalBookings.classList.remove("tw-flex");
        cancelCommentBookings.value = "";
        selectedBookingIds = [];
      }

      cancelModalCloseBookings.addEventListener(
        "click",
        closeCancelModalBookings
      );

      confirmCancelBookings.removeEventListener("click", handleConfirmCancel); // Удаляем, если он уже добавлен
      confirmCancelBookings.addEventListener("click", handleConfirmCancel); // Добавляем обработчик заново

      function handleConfirmCancel() {
        const comment = cancelCommentBookings.value.trim();
        if (!comment) {
          Toastify({
            text: spacesMnuData.i18n.enterCancellationReason,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
              background: "#E2474CCC",
            },
          }).showToast();
          return;
        }
        if (selectedBookingIds.length > 0) {
          updateBookingStatusBookings(selectedBookingIds, "cancelled", comment);
        }
        closeCancelModalBookings();
      }

      function updateBookingStatusBookings(bookingIds, status, comment) {
        const data = {
          action: "update_booking_status",
          booking_ids: bookingIds.join(","), // объединяем IDs в строку через запятую
          status: status,
          comment: comment,
          nonce: spacesMnuData.nonce, // Используем локализованный nonce
        };

        fetch(spacesMnuData.ajax_url, {
          // Используем локализованный ajax_url
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data),
        })
          .then((response) => response.json())
          .then((response) => {
            if (response.success) {
              Toastify({
                text:
                  spacesMnuData.i18n.bookingCancelled || response.data.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                  background: "#00b09b",
                },
              }).showToast();
              setTimeout(() => {
                location.reload();
              }, 3000);
            } else {
              Toastify({
                text:
                  spacesMnuData.i18n.cancellationFailed ||
                  response.data.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                  background: "#E2474CCC",
                },
              }).showToast();
            }
          })
          .catch((error) => console.error("Error:", error));
      }
    }

    function loadBookings(date) {
      showPreloader();
      fetch(spacesMnuData.ajax_url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "load_bookings_for_date",
          date: date,
          nonce: spacesMnuData.nonce,
        }),
      })
        .then((response) => response.json())
        .then((response) => {
          hidePreloader();
          response.success
            ? renderBookings(response.data.bookings)
            : (container.innerHTML = `<p>${response.data.message}</p>`);
        })
        .catch((error) => {
          hidePreloader();
          console.error("Error:", error);
        });
    }
  };

  window.myBookingsCalendar = function () {
    if (typeof FullCalendar === "undefined") {
      // console.error("FullCalendar is not defined. Please ensure the library is loaded.");
      return;
    }

    const preloader = document.getElementById("my-bookings-preloader");
    const container = document.getElementById("my-bookings");

    function showPreloader() {
      container.classList.remove("tw-block");
      container.classList.add("tw-hidden");
      preloader.classList.remove("tw-hidden");
      preloader.classList.add("tw-flex");
    }

    function hidePreloader() {
      setTimeout(() => {
        preloader.classList.add("tw-hidden");
        preloader.classList.remove("tw-flex");
        container.classList.add("tw-block");
        container.classList.remove("tw-hidden");
      }, 200);
    }

    var calendarEl = document.getElementById("my-calendar");
    if (!calendarEl) {
      // console.error("Element with id 'my-calendar' not found.");
      return;
    }

    var currentLanguage = spacesMnuData.current_language; // '<?= $current_language; ?>';

    var localeMap = {
      ru_RU: "ru",
      kk: "kk",
      en_US: "en",
    };
    var calendarLocale = localeMap[currentLanguage] || "en";
    // console.log('calendarLocale: ', calendarLocale);

    var kazakhLocale = {
      monthNames: [
        "Қаңтар",
        "Ақпан",
        "Наурыз",
        "Сәуір",
        "Мамыр",
        "Маусым",
        "Шілде",
        "Тамыз",
        "Қыркүйек",
        "Қазан",
        "Қараша",
        "Желтоқсан",
      ],
      monthNamesShort: [
        "Қаң",
        "Ақп",
        "Нау",
        "Сәу",
        "Мам",
        "Мау",
        "Шіл",
        "Там",
        "Қыр",
        "Қаз",
        "Қар",
        "Жел",
      ],
      dayNames: [
        "Жексенбі",
        "Дүйсенбі",
        "Сейсенбі",
        "Сәрсенбі",
        "Бейсенбі",
        "Жұма",
        "Сенбі",
      ],
      dayNamesShort: ["Жек", "Дүй", "Сей", "Сәр", "Бей", "Жұм", "Сен"],
      dayNamesMin: ["Жк", "Дү", "Сс", "Ср", "Бс", "Жм", "Сб"],
    };

    var russianLocale = {
      monthNames: [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь",
      ],
      monthNamesShort: [
        "Янв",
        "Фев",
        "Мар",
        "Апр",
        "Май",
        "Июн",
        "Июл",
        "Авг",
        "Сен",
        "Окт",
        "Ноя",
        "Дек",
      ],
      dayNames: [
        "Воскресенье",
        "Понедельник",
        "Вторник",
        "Среда",
        "Четверг",
        "Пятница",
        "Суббота",
      ],
      dayNamesShort: ["Вск", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"],
      dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
    };

    if (FullCalendar.globalLocales) {
      var kkLocale = FullCalendar.globalLocales.find(function (locale) {
        return locale.code === "kk";
      });

      if (kkLocale) {
        kkLocale.buttonText = {
          prev: "Алдыңғы",
          next: "Келесі",
          today: "Бүгін",
          month: "Ай",
          week: "Апта",
          day: "Күн",
          list: "Күн тәртібі",
        };
        kkLocale.allDayText = "Күні бойы";
        kkLocale.moreLinkText = "тағы";
        kkLocale.noEventsText = "Көрсету үшін оқиғалар жоқ";
        kkLocale.weekText = "Апта";
      }
    }

    const dayMap = {
      понедельник: "monday",
      вторник: "tuesday",
      среда: "wednesday",
      четверг: "thursday",
      пятница: "friday",
      суббота: "saturday",
      воскресенье: "sunday",
      дүйсенбі: "monday",
      сейсенбі: "tuesday",
      сәрсенбі: "wednesday",
      бейсенбі: "thursday",
      жұма: "friday",
      сенбі: "saturday",
      жексенбі: "sunday",
      monday: "monday",
      tuesday: "tuesday",
      wednesday: "wednesday",
      thursday: "thursday",
      friday: "friday",
      saturday: "saturday",
      sunday: "sunday",
    };

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      contentHeight: 400,
      selectable: false,
      locale: calendarLocale,
      firstDay: 1,
      dayHeaderContent: function (arg) {
        if (calendarLocale === "kk") {
          return kazakhLocale.dayNamesMin[arg.date.getDay()];
        }
      },
      titleFormat: {
        year: "numeric",
        month: "long",
      },
      datesSet: function (arg) {
        var header = document.querySelector("#my-calendar .fc-toolbar-title");
        var month = new Date(arg.view.currentStart).getMonth();
        var year = new Date(arg.view.currentStart).getFullYear();
        if (calendarLocale === "kk") {
          if (header) {
            header.innerHTML = kazakhLocale.monthNames[month] + " " + year;
          }
        }
        if (calendarLocale === "ru") {
          if (header) {
            header.innerHTML = russianLocale.monthNames[month] + " " + year;
          }
        }
      },
      dateClick: function (info) {
        var clickedDate = new Date(info.date);
        var formattedDate = dateFormatter(clickedDate);
        let dateTitle = document.getElementById("my-calendar-title");
        dateTitle.innerHTML =
          spacesMnuData.i18n.bookingsForDate + ": " + formattedDate;
        var dayName = clickedDate
          .toLocaleString(
            calendarLocale === "ru"
              ? "ru-RU"
              : calendarLocale === "kk"
              ? "kk-KZ"
              : "en-US",
            {
              weekday: "long",
            }
          )
          .toLowerCase();
        var englishDayName = dayMap[dayName];
        document.querySelectorAll(".fc-selected-day").forEach(function (el) {
          el.classList.remove("fc-selected-day");
        });
        info.dayEl.classList.add("fc-selected-day");
        loadMyBookings(formattedDate);
      },
      eventClick: function (info) {
        info.jsEvent.preventDefault();
        // Выделяем день, связанный с событием
        const clickedDate = info.event.start;
        const formattedDate = dateFormatter(clickedDate);

        let dateTitle = document.getElementById("my-calendar-title");
        dateTitle.innerHTML =
          spacesMnuData.i18n.bookingsForDate + ": " + formattedDate;

        document.querySelectorAll(".fc-selected-day").forEach(function (el) {
          el.classList.remove("fc-selected-day");
        });

        calendar.select(clickedDate); // Выделяем дату в календаре

        // В дополнение можно вызвать функцию, которая обновит отображение информации о бронированиях
        loadMyBookings(formattedDate);
      },
      dayCellClassNames: function (arg) {
        var day = arg.date.getDay();
        var dayName = new Date(arg.date)
          .toLocaleDateString(
            calendarLocale === "ru"
              ? "ru-RU"
              : calendarLocale === "kk"
              ? "kk-KZ"
              : "en-US",
            {
              weekday: "long",
            }
          )
          .toLowerCase();
        var englishDayName = dayMap[dayName];
        return [];
      },
    });

    // Загрузка и добавление дат с бронированиями в календарь
    loadEventDates(function (eventDates) {
      // console.log('eventDates: ', eventDates);
      calendar.addEventSource(eventDates);
    });

    calendar.render();

    function dateFormatter(date) {
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0"); // Месяцы начинаются с 0, поэтому добавляем 1
      const year = date.getFullYear();
      const formattedDate = `${day}.${month}.${year}`;
      return formattedDate;
    }

    function renderBookings(bookings) {
      // console.log('my bookings: ', bookings);

      let html = "";
      container.innerHTML = html;

      if (bookings.length > 0) {
        // Группируем бронирования по пользователю и объединяем последовательные слоты
        const groupedBookings = [];

        bookings.forEach((booking) => {
          if (booking.isDeleted === "0") {
            const lastGroup = groupedBookings[groupedBookings.length - 1];

            // Проверяем, можно ли объединить с последней группой по `requestedBy`, `resource`, `status`, `reason`
            if (
              lastGroup &&
              lastGroup.requestedBy === booking.requestedBy &&
              lastGroup.resource === booking.resource &&
              lastGroup.status === booking.status &&
              lastGroup.reason === booking.reason &&
              getNextSlot(lastGroup.slots[lastGroup.slots.length - 1]) ===
                formatSlot(booking.slot)
            ) {
              lastGroup.slots.push(formatSlot(booking.slot));
              lastGroup.bookingIds.push(booking.id);
            } else {
              // Создаем новую группу для нового пользователя или несоответствующего слота
              groupedBookings.push({
                bookingDate: booking.bookingDate,
                requestedBy: booking.requestedBy,
                first_name: booking.first_name,
                last_name: booking.last_name,
                job_title: booking.job_title,
                department: booking.department,
                reqByEmail: booking.reqByEmail,
                avatar_url: booking.avatar_url,
                resource: booking.resource,
                resourceLink: booking.resourceLink,
                resourceName: booking.resourceName,
                reason: booking.reason,
                comment: booking.comment,
                cancelledBy: booking.cancelledBy,
                cancelledAt: booking.cancelledAt,
                status: booking.status,
                slots: [formatSlot(booking.slot)],
                bookingIds: [booking.id],
              });
            }
          }
        });

        html += '<div class="tw-overflow-auto tw-w-full tw-h-[80vh]">';
        html +=
          '<table class="tw-w-full tw-table tw-border-collapse tw-table-auto"><thead class="tw-table-header-group">';
        html += '<tr class="tw-bg-gray-200 tw-gap-2 tw-table-row">';
        // html += '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' + spacesMnuData.i18n.requestedBy + '</th>';
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.resource +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.reason +
          "</th>";
        html +=
          '<th class="tw-px-12 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.slots +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.comment +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none tw-table-cell">' +
          spacesMnuData.i18n.actions +
          "</th>";

        html += '</tr></thead><tbody class=" tw-table-row-group">';

        groupedBookings.forEach((group) => {
          // console.log('resourceLink: ', group.resourceLink);

          // Создаем строку диапазона, объединяя все слоты через запятую
          const slotRange = group.slots
            .map(
              (slot) =>
                `<span class="tw-mx-auto tw-bg-white tw-text-[10px] tw-p-1 tw-rounded-xl">${slot
                  .split(" - ")
                  .map((time) => time.slice(0, 5))
                  .join("-")}</span>`
            )
            .join("");
          const slotRangeContainer = `<div class="tw-grid tw-grid-cols-1 tw-gap-2 tw-w-full tw-justify-center tw-items-start">${slotRange}</div>`;

          html += `<tr class="tw-gap-2 tw-table-row ${
            group.status === "approved"
              ? "tw-bg-green-200/25"
              : "tw-bg-red-200/25"
          } tw-border-b-[1px] tw-border-zinc-400">`;
          // html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">
          //             <div class="tw-flex tw-flex-col tw-gap-2">
          //                 <div class="tw-flex tw-justify-start tw-items-center tw-gap-1">
          //                     <img class="tw-flex-shrink-0 tw-inline-block tw-h-10 tw-w-10 tw-rounded-full tw-ring-2 tw-ring-white tw-object-cover" src="${group.avatar_url || 'https://secure.gravatar.com/avatar/?s=32&d=mm&r=g'}" alt="Аватар">
          //                     <h3 class="tw-text-black tw-text-base">${group.first_name + ' ' + group.last_name || group.requestedBy}</h3>
          //                     </div>
          //                 <div class="tw-space-y-1">
          //                         <h4 class="tw-text-[#999999] tw-text-sm">${group.job_title || ''}</h4>
          //                         <h4 class="tw-text-[#999999] tw-text-sm">${group.department || ''}</h4>
          //                         <a href="mailto:${group.reqByEmail}"><h4 class="tw-text-blue-600 tw-text-sm">${group.reqByEmail || ''}</h4></a>

          //                 </div>
          //             </div>
          //         </td>`;

          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none"><a href="${
            group.resourceLink ? group.resourceLink : "#"
          }">${
            group.resourceName ? group.resourceName : group.resource
          }</a></td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">${group.reason}</td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none tw-space-y-2 tw-space-x-2">${slotRangeContainer}</td>`;
          // html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip">${group.status}</td>`;

          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">
                                ${
                                  group.comment && group.cancelledAt
                                    ? `${group.cancelledBy}: ${
                                        group.comment
                                      } | ${new Date(
                                        group.cancelledAt
                                      ).toLocaleString("ru-RU", {
                                        day: "2-digit",
                                        month: "2-digit",
                                        year: "numeric",
                                        hour: "2-digit",
                                        minute: "2-digit",
                                        hour12: false,
                                      })}`
                                    : ""
                                }
                            </td>`;

          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">`;

          const today = new Date();
          today.setHours(0, 0, 0, 0);
          const now = new Date();
          const oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);
          if (
            group.status === "approved" &&
            new Date(group.bookingDate) > today
          ) {
            const allSlotsAreFuture = group.slots.every((slot) => {
              const slotStart = new Date(
                `${group.bookingDate}T${slot.split(" - ")[0]}`
              );
              return slotStart > oneHourLater;
            });
            if (allSlotsAreFuture) {
              html += `<button type="button" class="cancel-btn tw-bg-red-500 tw-text-white tw-px-2 tw-py-1 tw-rounded tw-text-sm" data-booking-ids="${group.bookingIds.join(
                ","
              )}">${spacesMnuData.i18n.cancel}</button>`;
            }
          }
          html += "</td></tr>";
        });

        html += "</tbody></table>";
      } else {
        html = "<p>" + spacesMnuData.i18n.noBookingsFound + "</p>";
      }
      container.innerHTML = html;
    }
    setupCancelButtonsMyBookings();

    // Функция для форматирования времени слотов без секунд
    function formatSlot(slot) {
      return slot
        .split(" - ")
        .map((time) => time.slice(0, 5))
        .join(" - ");
    }

    // Вспомогательная функция для получения следующего слота
    function getNextSlot(slot) {
      const [start, end] = slot.split("-").map((time) => time.trim());
      const [endHours, endMinutes] = end.split(":").map(Number);

      const nextSlotStart = new Date();
      nextSlotStart.setHours(endHours, endMinutes, 0, 0);

      const nextSlotEnd = new Date(nextSlotStart);
      nextSlotEnd.setMinutes(nextSlotEnd.getMinutes() + 30);

      return `${formatTime(nextSlotStart)} - ${formatTime(nextSlotEnd)}`;
    }

    // Вспомогательная функция для форматирования времени в HH:MM
    function formatTime(date) {
      return `${String(date.getHours()).padStart(2, "0")}:${String(
        date.getMinutes()
      ).padStart(2, "0")}`;
    }
    // Настройка кнопок отмены для группированных слотов
    function setupCancelButtonsMyBookings() {
      let selectedBookingIds = [];
      const cancelModalBookings = document.getElementById(
        "cancelModalMyBookings"
      );
      const cancelModalCloseBookings = document.getElementById(
        "cancelModalCloseMyBookings"
      );
      const confirmCancelBookings = document.getElementById(
        "confirmCancelMyBookings"
      );
      const cancelCommentBookings = document.getElementById(
        "cancelCommentMyBookings"
      );

      // Устанавливаем делегирование событий на контейнер с классом .users-bookings
      document
        .getElementById("my-bookings")
        .addEventListener("click", function (event) {
          if (event.target.classList.contains("cancel-btn")) {
            selectedBookingIds = event.target
              .getAttribute("data-booking-ids")
              .split(",");
            openCancelModalMyBookings();
          }
        });

      function openCancelModalMyBookings() {
        cancelModalBookings.classList.remove("tw-hidden");
        cancelModalBookings.classList.add("tw-flex");
      }

      function closeCancelModalMyBookings() {
        cancelModalBookings.classList.add("tw-hidden");
        cancelModalBookings.classList.remove("tw-flex");
        cancelCommentBookings.value = "";
        selectedBookingIds = [];
      }

      cancelModalCloseBookings.addEventListener(
        "click",
        closeCancelModalMyBookings
      );

      confirmCancelBookings.removeEventListener("click", handleConfirmMyCancel); // Удаляем, если он уже добавлен
      confirmCancelBookings.addEventListener("click", handleConfirmMyCancel); // Добавляем обработчик заново

      function handleConfirmMyCancel() {
        const comment = cancelCommentBookings.value.trim();
        if (!comment) {
          Toastify({
            text: spacesMnuData.i18n.enterCancellationReason,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
              background: "#E2474CCC",
            },
          }).showToast();
          return;
        }
        if (selectedBookingIds.length > 0) {
          updateBookingStatusMyBookings(
            selectedBookingIds,
            "cancelled",
            comment
          );
        }
        closeCancelModalMyBookings();
      }

      function updateBookingStatusMyBookings(bookingIds, status, comment) {
        const data = {
          action: "update_booking_status",
          booking_ids: bookingIds.join(","), // объединяем IDs в строку через запятую
          status: status,
          comment: comment,
          nonce: spacesMnuData.nonce, // Используем локализованный nonce
        };

        fetch(spacesMnuData.ajax_url, {
          // Используем локализованный ajax_url
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data),
        })
          .then((response) => response.json())
          .then((response) => {
            if (response.success) {
              Toastify({
                text:
                  spacesMnuData.i18n.bookingCancelled || response.data.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                  background: "#00b09b",
                },
              }).showToast();
              setTimeout(() => {
                location.reload();
              }, 3000);
            } else {
              Toastify({
                text:
                  spacesMnuData.i18n.cancellationFailed ||
                  response.data.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                  background: "#E2474CCC",
                },
              }).showToast();
            }
          })
          .catch((error) => console.error("Error:", error));
      }
    }

    function loadMyBookings(date) {
      showPreloader();
      fetch(spacesMnuData.ajax_url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "load_my_bookings_for_date",
          date: date,
          nonce: spacesMnuData.nonce,
        }),
      })
        .then((response) => response.json())
        .then((response) => {
          hidePreloader();
          response.success
            ? renderBookings(response.data.bookings)
            : (container.innerHTML = `<p>${response.data.message}</p>`);
        })
        .catch((error) => {
          hidePreloader();
          console.error("Error:", error);
        });
    }

    // Функция для загрузки дат с бронированиями
    function loadEventDates(callback) {
      showPreloader();

      jQuery.ajax({
        url: spacesMnuData.ajax_url,
        type: "POST",
        dataType: "json",
        data: {
          action: "load_all_my_bookings", // AJAX действие для получения дат с бронированиями
          nonce: spacesMnuData.nonce,
        },
        success: function (response) {
          hidePreloader();
          if (response.success) {
            // Создаём Set для хранения уникальных дат
            const uniqueDates = new Set();

            // Перебираем бронирования и добавляем уникальные даты в Set
            response.data.bookings.forEach((booking) => {
              uniqueDates.add(booking.bookingDate);
            });

            // Преобразуем Set в массив объектов для FullCalendar
            const eventDates = Array.from(uniqueDates).map((date) => ({
              title: "", // Пустой заголовок для точки
              start: date, // Уникальная дата бронирования
              display: "list-item", // auto, block, list-item, background, inverse-background, none
              color: "#E2474C",
            }));

            callback(eventDates);
          }
          // else {
          //   console.error(response.data.message);
          // }
        },
        error: function (xhr, status, error) {
          hidePreloader();
          console.error("Failed to load bookings: ", error);
        },
      });
    }
  };

  window.allBookingsCalendar = function () {
    if (typeof FullCalendar === "undefined") {
      // console.error("FullCalendar is not defined. Please ensure the library is loaded.");
      return;
    }

    const preloader = document.getElementById("users-all-bookings-preloader");
    const container = document.getElementById("users-all-bookings");

    function showPreloader() {
      container.classList.remove("tw-block");
      container.classList.add("tw-hidden");
      preloader.classList.remove("tw-hidden");
      preloader.classList.add("tw-flex");
    }

    function hidePreloader() {
      setTimeout(() => {
        preloader.classList.add("tw-hidden");
        preloader.classList.remove("tw-flex");
        container.classList.add("tw-block");
        container.classList.remove("tw-hidden");
      }, 200);
    }

    var calendarEl = document.getElementById("all-bookings-calendar");
    if (!calendarEl) {
      // console.error("Element with id 'my-calendar' not found.");
      return;
    }

    var currentLanguage = spacesMnuData.current_language; // '<?= $current_language; ?>';

    var localeMap = {
      ru_RU: "ru",
      kk: "kk",
      en_US: "en",
    };
    var calendarLocale = localeMap[currentLanguage] || "en";
    // console.log('calendarLocale: ', calendarLocale);

    var kazakhLocale = {
      monthNames: [
        "Қаңтар",
        "Ақпан",
        "Наурыз",
        "Сәуір",
        "Мамыр",
        "Маусым",
        "Шілде",
        "Тамыз",
        "Қыркүйек",
        "Қазан",
        "Қараша",
        "Желтоқсан",
      ],
      monthNamesShort: [
        "Қаң",
        "Ақп",
        "Нау",
        "Сәу",
        "Мам",
        "Мау",
        "Шіл",
        "Там",
        "Қыр",
        "Қаз",
        "Қар",
        "Жел",
      ],
      dayNames: [
        "Жексенбі",
        "Дүйсенбі",
        "Сейсенбі",
        "Сәрсенбі",
        "Бейсенбі",
        "Жұма",
        "Сенбі",
      ],
      dayNamesShort: ["Жек", "Дүй", "Сей", "Сәр", "Бей", "Жұм", "Сен"],
      dayNamesMin: ["Жк", "Дү", "Сс", "Ср", "Бс", "Жм", "Сб"],
    };

    var russianLocale = {
      monthNames: [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь",
      ],
      monthNamesShort: [
        "Янв",
        "Фев",
        "Мар",
        "Апр",
        "Май",
        "Июн",
        "Июл",
        "Авг",
        "Сен",
        "Окт",
        "Ноя",
        "Дек",
      ],
      dayNames: [
        "Воскресенье",
        "Понедельник",
        "Вторник",
        "Среда",
        "Четверг",
        "Пятница",
        "Суббота",
      ],
      dayNamesShort: ["Вск", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"],
      dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
    };

    if (FullCalendar.globalLocales) {
      var kkLocale = FullCalendar.globalLocales.find(function (locale) {
        return locale.code === "kk";
      });

      if (kkLocale) {
        kkLocale.buttonText = {
          prev: "Алдыңғы",
          next: "Келесі",
          today: "Бүгін",
          month: "Ай",
          week: "Апта",
          day: "Күн",
          list: "Күн тәртібі",
        };
        kkLocale.allDayText = "Күні бойы";
        kkLocale.moreLinkText = "тағы";
        kkLocale.noEventsText = "Көрсету үшін оқиғалар жоқ";
        kkLocale.weekText = "Апта";
      }
    }

    const dayMap = {
      понедельник: "monday",
      вторник: "tuesday",
      среда: "wednesday",
      четверг: "thursday",
      пятница: "friday",
      суббота: "saturday",
      воскресенье: "sunday",
      дүйсенбі: "monday",
      сейсенбі: "tuesday",
      сәрсенбі: "wednesday",
      бейсенбі: "thursday",
      жұма: "friday",
      сенбі: "saturday",
      жексенбі: "sunday",
      monday: "monday",
      tuesday: "tuesday",
      wednesday: "wednesday",
      thursday: "thursday",
      friday: "friday",
      saturday: "saturday",
      sunday: "sunday",
    };

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      contentHeight: 400,
      selectable: false,
      locale: calendarLocale,
      firstDay: 1,
      dayHeaderContent: function (arg) {
        if (calendarLocale === "kk") {
          return kazakhLocale.dayNamesMin[arg.date.getDay()];
        }
      },
      titleFormat: {
        year: "numeric",
        month: "long",
      },
      datesSet: function (arg) {
        var header = document.querySelector(
          "#all-bookings-calendar .fc-toolbar-title"
        );
        var month = new Date(arg.view.currentStart).getMonth();
        var year = new Date(arg.view.currentStart).getFullYear();
        if (calendarLocale === "kk") {
          if (header) {
            header.innerHTML = kazakhLocale.monthNames[month] + " " + year;
          }
        }
        if (calendarLocale === "ru") {
          if (header) {
            header.innerHTML = russianLocale.monthNames[month] + " " + year;
          }
        }
      },
      dateClick: function (info) {
        var clickedDate = new Date(info.date);
        var formattedDate = dateFormatter(clickedDate);
        let dateTitle = document.getElementById("all-bookings-title");
        let allBookingsFilters = document.getElementById(
          "all-bookings-filters"
        );
        dateTitle.innerHTML =
          spacesMnuData.i18n.bookingsForDate + ": " + formattedDate;
        allBookingsFilters.classList.remove("tw-hidden");
        var dayName = clickedDate
          .toLocaleString(
            calendarLocale === "ru"
              ? "ru-RU"
              : calendarLocale === "kk"
              ? "kk-KZ"
              : "en-US",
            {
              weekday: "long",
            }
          )
          .toLowerCase();
        var englishDayName = dayMap[dayName];
        document.querySelectorAll(".fc-selected-day").forEach(function (el) {
          el.classList.remove("fc-selected-day");
        });
        info.dayEl.classList.add("fc-selected-day");
        loadAllBookings(formattedDate);
      },
      eventClick: function (info) {
        info.jsEvent.preventDefault();
        // Выделяем день, связанный с событием
        const clickedDate = info.event.start;
        const formattedDate = dateFormatter(clickedDate);

        let dateTitle = document.getElementById("all-bookings-title");
        let allBookingsFilters = document.getElementById(
          "all-bookings-filters"
        );
        dateTitle.innerHTML =
          spacesMnuData.i18n.bookingsForDate + ": " + formattedDate;
        allBookingsFilters.classList.remove("tw-hidden");

        document.querySelectorAll(".fc-selected-day").forEach(function (el) {
          el.classList.remove("fc-selected-day");
        });

        calendar.select(clickedDate); // Выделяем дату в календаре

        // В дополнение можно вызвать функцию, которая обновит отображение информации о бронированиях
        loadAllBookings(formattedDate);
      },
      dayCellClassNames: function (arg) {
        var day = arg.date.getDay();
        var dayName = new Date(arg.date)
          .toLocaleDateString(
            calendarLocale === "ru"
              ? "ru-RU"
              : calendarLocale === "kk"
              ? "kk-KZ"
              : "en-US",
            {
              weekday: "long",
            }
          )
          .toLowerCase();
        var englishDayName = dayMap[dayName];
        return [];
      },
    });

    // Загрузка и добавление дат с бронированиями в календарь
    loadEventDates(function (eventDates) {
      // console.log('eventDates: ', eventDates);
      calendar.addEventSource(eventDates);
    });

    calendar.render();

    function dateFormatter(date) {
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0"); // Месяцы начинаются с 0, поэтому добавляем 1
      const year = date.getFullYear();
      const formattedDate = `${day}.${month}.${year}`;
      return formattedDate;
    }

    let allBookings = [];
    document.getElementById("apply-filters").addEventListener("click", () => {
      const requestedByFilter = document
        .getElementById("filter-requestedBy")
        .value.toLowerCase();
      const resourceNameFilter = document
        .getElementById("filter-resourceName")
        .value.toLowerCase();
      const statusFilter = document
        .getElementById("filter-status")
        .value.toLowerCase();
      const slotFilter = document.getElementById("filter-slot").value;

      const filteredBookings = allBookings.filter((booking) => {
        const matchesRequestedBy =
          requestedByFilter === "" ||
          (booking.requestedBy &&
            booking.requestedBy
              .toLowerCase()
              .includes(requestedByFilter.toLowerCase())) ||
          (booking.first_name &&
            booking.first_name
              .toLowerCase()
              .includes(requestedByFilter.toLowerCase())) ||
          (booking.last_name &&
            booking.last_name
              .toLowerCase()
              .includes(requestedByFilter.toLowerCase())) ||
          (booking.reqByEmail &&
            booking.reqByEmail
              .toLowerCase()
              .includes(requestedByFilter.toLowerCase()));

        const matchesResourceName =
          resourceNameFilter === "" ||
          (booking.resourceName &&
            booking.resourceName
              .toLowerCase()
              .includes(resourceNameFilter.toLowerCase())) ||
          (booking.resource &&
            booking.resource
              .toLowerCase()
              .includes(resourceNameFilter.toLowerCase()));
        const matchesStatus =
          statusFilter === "" ||
          (booking.status &&
            booking.status.toLowerCase() === statusFilter.toLowerCase());
        const matchesSlot =
          slotFilter === "" ||
          (booking.slot && booking.slot.includes(slotFilter));

        return (
          matchesRequestedBy &&
          matchesResourceName &&
          matchesStatus &&
          matchesSlot
        );
      });

      renderBookings(filteredBookings); // Повторный рендеринг с фильтрованными данными
    });

    function renderBookings(bookings) {
      // console.log('my bookings: ', bookings);

      let html = "";
      container.innerHTML = html;

      if (bookings.length > 0) {
        // Группируем бронирования по пользователю и объединяем последовательные слоты
        const groupedBookings = [];

        bookings.forEach((booking) => {
          if (booking.isDeleted === "0") {
            const lastGroup = groupedBookings[groupedBookings.length - 1];

            // Проверяем, можно ли объединить с последней группой по `requestedBy`, `resource`, `status`, `reason`
            if (
              lastGroup &&
              lastGroup.requestedBy === booking.requestedBy &&
              lastGroup.resource === booking.resource &&
              lastGroup.status === booking.status &&
              lastGroup.reason === booking.reason &&
              getNextSlot(lastGroup.slots[lastGroup.slots.length - 1]) ===
                formatSlot(booking.slot)
            ) {
              lastGroup.slots.push(formatSlot(booking.slot));
              lastGroup.bookingIds.push(booking.id);
            } else {
              // Создаем новую группу для нового пользователя или несоответствующего слота
              groupedBookings.push({
                bookingDate: booking.bookingDate,
                requestedBy: booking.requestedBy,
                first_name: booking.first_name,
                last_name: booking.last_name,
                job_title: booking.job_title,
                department: booking.department,
                reqByEmail: booking.reqByEmail,
                avatar_url: booking.avatar_url,
                resource: booking.resource,
                resourceLink: booking.resourceLink,
                resourceName: booking.resourceName,
                reason: booking.reason,
                comment: booking.comment,
                cancelledBy: booking.cancelledBy,
                cancelledAt: booking.cancelledAt,
                status: booking.status,
                slots: [formatSlot(booking.slot)],
                bookingIds: [booking.id],
              });
            }
          }
        });

        html +=
          '<div class="tw-overflow-y-auto tw-overflow-x-scroll tw-w-full tw-h-[80vh]">';
        html +=
          '<table class="tw-w-full tw-table tw-border-collapse tw-table-auto"><thead class="tw-table-header-group">';
        html += '<tr class="tw-bg-gray-200 tw-gap-2 tw-table-row">';
        // html += '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' + spacesMnuData.i18n.requestedBy + '</th>';
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.requestedBy +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.resource +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.reason +
          "</th>";
        html +=
          '<th class="tw-px-12 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.slots +
          "</th>";
        html +=
          '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none">' +
          spacesMnuData.i18n.comment +
          "</th>";

        if (spacesMnuData.current_users_role != "security_staff") {
          html +=
            '<th class="tw-px-4 tw-py-2 tw-text-base tw-border-none tw-table-cell">' +
            spacesMnuData.i18n.actions +
            "</th>";
        }

        html += '</tr></thead><tbody class=" tw-table-row-group">';

        groupedBookings.forEach((group) => {
          // console.log('resourceLink: ', group.resourceLink);

          // Создаем строку диапазона, объединяя все слоты через запятую
          const slotRange = group.slots
            .map(
              (slot) =>
                `<span class="tw-mx-auto tw-bg-white tw-text-[10px] tw-p-1 tw-rounded-xl">${slot
                  .split(" - ")
                  .map((time) => time.slice(0, 5))
                  .join("-")}</span>`
            )
            .join("");
          const slotRangeContainer = `<div class="tw-grid tw-grid-cols-1 tw-gap-2 tw-w-full tw-justify-center tw-items-start">${slotRange}</div>`;

          html += `<tr class="tw-gap-2 tw-table-row ${
            group.status === "approved"
              ? "tw-bg-green-200/25"
              : "tw-bg-red-200/25"
          } tw-border-b-[1px] tw-border-zinc-400">`;
          // html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">
          //             <div class="tw-flex tw-flex-col tw-gap-2">
          //                 <div class="tw-flex tw-justify-start tw-items-center tw-gap-1">
          //                     <img class="tw-flex-shrink-0 tw-inline-block tw-h-10 tw-w-10 tw-rounded-full tw-ring-2 tw-ring-white tw-object-cover" src="${group.avatar_url || 'https://secure.gravatar.com/avatar/?s=32&d=mm&r=g'}" alt="Аватар">
          //                     <h3 class="tw-text-black tw-text-base">${group.first_name + ' ' + group.last_name || group.requestedBy}</h3>
          //                     </div>
          //                 <div class="tw-space-y-1">
          //                         <h4 class="tw-text-[#999999] tw-text-sm">${group.job_title || ''}</h4>
          //                         <h4 class="tw-text-[#999999] tw-text-sm">${group.department || ''}</h4>
          //                         <a href="mailto:${group.reqByEmail}"><h4 class="tw-text-blue-600 tw-text-sm">${group.reqByEmail || ''}</h4></a>

          //                 </div>
          //             </div>
          //         </td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">
                                <div class="tw-flex tw-flex-col tw-gap-2">
                                    <div class="tw-flex tw-justify-start tw-items-center tw-gap-1">
                                        <img class="tw-flex-shrink-0 tw-inline-block tw-h-10 tw-w-10 tw-rounded-full tw-ring-2 tw-ring-white tw-object-cover" src="${
                                          group.avatar_url ||
                                          "https://secure.gravatar.com/avatar/?s=32&d=mm&r=g"
                                        }" alt="Аватар">
                                        <h3 class="tw-text-black tw-text-base">${
                                          group.first_name +
                                            " " +
                                            group.last_name || group.requestedBy
                                        }</h3>
                                        </div>
                                    <div class="tw-space-y-1">
                                            <h4 class="tw-text-[#999999] tw-text-sm">${
                                              group.job_title || ""
                                            }</h4>
                                            <h4 class="tw-text-[#999999] tw-text-sm">${
                                              group.department || ""
                                            }</h4>
                                            <a href="mailto:${
                                              group.reqByEmail
                                            }"><h4 class="tw-text-blue-600 tw-text-sm">${
            group.reqByEmail || ""
          }</h4></a>
                                       
                                    </div>
                                </div>
                            </td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none"><a href="${
            group.resourceLink ? group.resourceLink : "#"
          }">${
            group.resourceName ? group.resourceName : group.resource
          }</a></td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">${group.reason}</td>`;
          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none tw-space-y-2 tw-space-x-2">${slotRangeContainer}</td>`;
          // html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip">${group.status}</td>`;

          html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">
                                ${
                                  group.comment && group.cancelledAt
                                    ? `${group.cancelledBy}: ${
                                        group.comment
                                      } | ${new Date(
                                        group.cancelledAt
                                      ).toLocaleString("ru-RU", {
                                        day: "2-digit",
                                        month: "2-digit",
                                        year: "numeric",
                                        hour: "2-digit",
                                        minute: "2-digit",
                                        hour12: false,
                                      })}`
                                    : ""
                                }
                            </td>`;

          if (spacesMnuData.current_users_role != "security_staff") {
            html += `<td class="tw-border tw-px-4 tw-py-2 tw-text-sm tw-text-wrap tw-truncate hover:tw-text-clip tw-border-none">`;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const now = new Date();
            const oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);

            if (
              group.status === "approved" &&
              new Date(group.bookingDate) > today
            ) {
              const allSlotsAreFuture = group.slots.every((slot) => {
                const slotStart = new Date(
                  `${group.bookingDate}T${slot.split(" - ")[0]}`
                );
                return slotStart > oneHourLater;
              });

              // Условие для скрытия кнопки отмены
              if (allSlotsAreFuture) {
                html += `<button type="button" class="cancel-btn tw-bg-red-500 tw-text-white tw-px-2 tw-py-1 tw-rounded tw-text-sm" data-booking-ids="${group.bookingIds.join(
                  ","
                )}">${spacesMnuData.i18n.cancel}</button>`;
              }
            }

            html += "</td>";
          }
          html += "</tr>";
        });

        html += "</tbody></table>";
      } else {
        html = "<p>" + spacesMnuData.i18n.noBookingsFound + "</p>";
      }
      container.innerHTML = html;
    }
    setupCancelButtonsAllBookings();

    // Функция для форматирования времени слотов без секунд
    function formatSlot(slot) {
      return slot
        .split(" - ")
        .map((time) => time.slice(0, 5))
        .join(" - ");
    }

    // Вспомогательная функция для получения следующего слота
    function getNextSlot(slot) {
      const [start, end] = slot.split("-").map((time) => time.trim());
      const [endHours, endMinutes] = end.split(":").map(Number);

      const nextSlotStart = new Date();
      nextSlotStart.setHours(endHours, endMinutes, 0, 0);

      const nextSlotEnd = new Date(nextSlotStart);
      nextSlotEnd.setMinutes(nextSlotEnd.getMinutes() + 30);

      return `${formatTime(nextSlotStart)} - ${formatTime(nextSlotEnd)}`;
    }

    // Вспомогательная функция для форматирования времени в HH:MM
    function formatTime(date) {
      return `${String(date.getHours()).padStart(2, "0")}:${String(
        date.getMinutes()
      ).padStart(2, "0")}`;
    }
    // Настройка кнопок отмены для группированных слотов
    function setupCancelButtonsAllBookings() {
      let selectedBookingIds = [];
      const cancelModalBookings = document.getElementById(
        "cancelModalAllBookings"
      );
      const cancelModalCloseBookings = document.getElementById(
        "cancelModalCloseAllBookings"
      );
      const confirmCancelBookings = document.getElementById(
        "confirmCancelAllBookings"
      );
      const cancelCommentBookings = document.getElementById(
        "cancelCommentAllBookings"
      );

      // Устанавливаем делегирование событий на контейнер с классом .users-bookings
      document
        .getElementById("users-all-bookings")
        .addEventListener("click", function (event) {
          if (event.target.classList.contains("cancel-btn")) {
            selectedBookingIds = event.target
              .getAttribute("data-booking-ids")
              .split(",");
            openCancelModalMyBookings();
          }
        });

      function openCancelModalMyBookings() {
        cancelModalBookings.classList.remove("tw-hidden");
        cancelModalBookings.classList.add("tw-flex");
      }

      function closeCancelModalMyBookings() {
        cancelModalBookings.classList.add("tw-hidden");
        cancelModalBookings.classList.remove("tw-flex");
        cancelCommentBookings.value = "";
        selectedBookingIds = [];
      }

      cancelModalCloseBookings.addEventListener(
        "click",
        closeCancelModalMyBookings
      );

      confirmCancelBookings.removeEventListener("click", handleConfirmMyCancel); // Удаляем, если он уже добавлен
      confirmCancelBookings.addEventListener("click", handleConfirmMyCancel); // Добавляем обработчик заново

      function handleConfirmMyCancel() {
        const comment = cancelCommentBookings.value.trim();
        if (!comment) {
          Toastify({
            text: spacesMnuData.i18n.enterCancellationReason,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
              background: "#E2474CCC",
            },
          }).showToast();
          return;
        }
        if (selectedBookingIds.length > 0) {
          updateBookingStatusMyBookings(
            selectedBookingIds,
            "cancelled",
            comment
          );
        }
        closeCancelModalMyBookings();
      }

      function updateBookingStatusMyBookings(bookingIds, status, comment) {
        const data = {
          action: "update_booking_status",
          booking_ids: bookingIds.join(","), // объединяем IDs в строку через запятую
          status: status,
          comment: comment,
          nonce: spacesMnuData.nonce, // Используем локализованный nonce
        };

        fetch(spacesMnuData.ajax_url, {
          // Используем локализованный ajax_url
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(data),
        })
          .then((response) => response.json())
          .then((response) => {
            if (response.success) {
              Toastify({
                text:
                  spacesMnuData.i18n.bookingCancelled || response.data.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                  background: "#00b09b",
                },
              }).showToast();
              setTimeout(() => {
                location.reload();
              }, 3000);
            } else {
              Toastify({
                text:
                  spacesMnuData.i18n.cancellationFailed ||
                  response.data.message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                  background: "#E2474CCC",
                },
              }).showToast();
            }
          })
          .catch((error) => console.error("Error:", error));
      }
    }

    function loadAllBookings(date) {
      showPreloader();
      fetch(spacesMnuData.ajax_url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "load_all_bookings_for_date",
          date: date,
          nonce: spacesMnuData.nonce,
        }),
      })
        .then((response) => response.json())
        .then((response) => {
          hidePreloader();
          allBookings = response.success ? response.data.bookings : [];
          response.success
            ? renderBookings(response.data.bookings)
            : (container.innerHTML = `<p>${response.data.message}</p>`);
        })
        .catch((error) => {
          hidePreloader();
          console.error("Error:", error);
        });
    }

    // Функция для загрузки дат с бронированиями
    function loadEventDates(callback) {
      showPreloader();

      jQuery.ajax({
        url: spacesMnuData.ajax_url,
        type: "POST",
        dataType: "json",
        data: {
          action: "load_all_bookings", // AJAX действие для получения дат с бронированиями
          nonce: spacesMnuData.nonce,
        },
        success: function (response) {
          hidePreloader();
          if (response.success) {
            // Создаём Set для хранения уникальных дат
            const uniqueDates = new Set();

            // Перебираем бронирования и добавляем уникальные даты в Set
            response.data.bookings.forEach((booking) => {
              uniqueDates.add(booking.bookingDate);
            });

            // Преобразуем Set в массив объектов для FullCalendar
            const eventDates = Array.from(uniqueDates).map((date) => ({
              title: "", // Пустой заголовок для точки
              start: date, // Уникальная дата бронирования
              display: "list-item", // auto, block, list-item, background, inverse-background, none
              color: "#E2474C",
            }));

            callback(eventDates);
          } else {
            console.error(response.data.message);
          }
        },
        error: function (xhr, status, error) {
          hidePreloader();
          console.error("Failed to load bookings: ", error);
        },
      });
    }

    // $(document).ajaxComplete(function () {
    //   if (spacesMnuData.current_users_role === "security_staff") {
    //     $(".cancel-btn").attr("style", "display: none !important;");
    //     console.log("hided after AJAX");
    //   }
    // });
  };

  // })
})(jQuery);
