// assets/js/students.js

jQuery(document).ready(function ($) {
  let cachedStudentsData = null;
  let debounceTimeout;

  function filterStudents(searchQuery = "", mnu_role = "") {
    if (
      cachedStudentsData &&
      cachedStudentsData.search === searchQuery &&
      cachedStudentsData.mnu_role === mnu_role
    ) {
      $("#students-list").html(cachedStudentsData.html);
      return;
    }
    $.ajax({
      url: students_ajax_obj.ajax_url,
      type: "POST",
      data: {
        action: "filter_students",
        search: searchQuery,
        mnu_role: mnu_role,
        nonce: students_ajax_obj.nonce,
      },
      beforeSend: function () {
        $("#students-list").html(
          "<p>" + students_ajax_obj.i18n.loading_text + "</p>"
        );
      },
      success: function (response) {
        if (response.success) {
          cachedStudentsData = {
            search: searchQuery,
            mnu_role: mnu_role,
            html: response.data,
          };
          $("#students-list").html(response.data);
        } else {
          $("#students-list").html(
            "<p>" + students_ajax_obj.i18n.no_students_text + "</p>"
          );
        }
      },
      error: function () {
        $("#students-list").html(
          "<p>" + students_ajax_obj.i18n.error_text + "</p>"
        );
      },
    });
  }

  function debounceFilterStudents(searchQuery, mnu_role) {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(function () {
      filterStudents(searchQuery, mnu_role);
    }, 300);
  }

  const initialMNURole = $('select[name="mnu_role"]').val();
  filterStudents("", initialMNURole);

  $("#students-filter-form").on("submit", function (e) {
    e.preventDefault();
    let searchQuery = $('input[name="student_search"]').val();
    let mnu_role = $('select[name="student_mnu_role"]').val();
    debounceFilterStudents(searchQuery, mnu_role);
  });

  $(document).on("click", ".save-students-mnu-role-btn", function (e) {
    e.preventDefault();
    // alert('save-mnu-role-btn clicked');

    let $button = $(this);
    let userId = $button.closest("[data-user-id]").data("user-id");
    let selectedMnuRole = $button.siblings(".mnu-role-select").val();

    $.ajax({
      url: students_ajax_obj.ajax_url,
      type: "POST",
      data: {
        // action: 'save_student_mnu_role',
        // nonce: students_ajax_obj.save_student_mnu_role_nonce,
        action: "save_user_mnu_role",
        nonce: students_ajax_obj.save_user_mnu_role_nonce,
        user_id: userId,
        mnu_role: selectedMnuRole,
      },
      success: function (response) {
        if (response.success) {
          Toastify({
            text: response.data.message || "Role updated successfully!",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: { background: "#28a745" },
          }).showToast();

          setTimeout(function () {
            location.reload();
          }, 3000);
        } else {
          Toastify({
            text: response.data.message || "Failed to update role.",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: { background: "#dc3545" },
          }).showToast();
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("Error:", textStatus, errorThrown);
        Toastify({
          text: students_ajax_obj.i18n.error_text,
          duration: 2000,
          close: true,
          gravity: "top",
          position: "right",
          style: { background: "#dc3545" },
        }).showToast();
      },
    });
  });
});
