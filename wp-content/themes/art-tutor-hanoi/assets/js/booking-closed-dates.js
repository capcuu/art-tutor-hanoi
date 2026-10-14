/**
 * /book/ date rules: studio closed dates, weekly Thursday closure, workshop life drawing.
 *
 * Life drawing (Nude Life Drawing) is bookable on Fridays. The Fluent Forms date
 * field still disables every day except Saturday; while that workshop is selected
 * this script replaces that list so Friday stays selectable.
 */
(function ($) {
  'use strict';

  var cfg = window.athBookingClosedDates || {};
  var CLOSED = window.ATH_CLOSED_DATES || [];
  var closedSet = Object.create(null);
  var i;

  for (i = 0; i < CLOSED.length; i++) {
    closedSet[CLOSED[i]] = true;
  }

  var workshopFormId = parseInt(cfg.workshopFormId, 10) || 40;
  var lifeDrawingNeedle = String(cfg.lifeDrawingValue || 'Nude Life Drawing');
  var residencyNeedle = String(cfg.residencyValue || 'Art Residency');
  var programField = String(cfg.programField || 'dropdown');
  var workshopField = String(cfg.workshopField || 'dropdown_4');
  var LIFE_DRAWING_DAY = 5;

  function dateKey(date) {
    var y = date.getFullYear();
    var m = String(date.getMonth() + 1);
    var d = String(date.getDate());
    if (m.length < 2) {
      m = '0' + m;
    }
    if (d.length < 2) {
      d = '0' + d;
    }
    return y + '-' + m + '-' + d;
  }

  function isClosedDate(date) {
    return !!closedSet[dateKey(date)];
  }

  function getFormRoot(el) {
    if (!el) {
      return null;
    }

    return (
      el.closest('form[id^="fluentform_"]') ||
      el.closest('[id^="fluentform_"]') ||
      el.closest('.fluentform_wrapper_' + workshopFormId)
    );
  }

  function getFormId(formRoot) {
    var formEl;
    var match;

    if (!formRoot) {
      return 0;
    }

    if (formRoot.id && formRoot.id.indexOf('fluentform_') === 0) {
      match = String(formRoot.id).match(/^fluentform_(\d+)/);
      if (match) {
        return parseInt(match[1], 10) || 0;
      }
    }

    formEl = formRoot.querySelector
      ? formRoot.querySelector('form[id^="fluentform_"]')
      : null;
    if (formEl && formEl.id) {
      match = String(formEl.id).match(/^fluentform_(\d+)/);
      if (match) {
        return parseInt(match[1], 10) || 0;
      }
    }

    if (formRoot.classList && formRoot.classList.contains('fluentform_wrapper_' + workshopFormId)) {
      return workshopFormId;
    }

    return 0;
  }

  function matchesNeedle(value, needle) {
    if (!value || !needle) {
      return false;
    }
    return String(value).indexOf(needle) !== -1;
  }

  function getSelectValue(selectEl) {
    return selectEl ? String(selectEl.value || '') : '';
  }

  function findWorkshopSelect(formRoot) {
    var selectEl;
    var selects;
    var n;
    var m;

    if (!formRoot) {
      return null;
    }

    selectEl = formRoot.querySelector('select[name="' + workshopField + '"]');
    if (selectEl) {
      return selectEl;
    }

    selects = formRoot.querySelectorAll('select');
    for (n = 0; n < selects.length; n++) {
      for (m = 0; m < selects[n].options.length; m++) {
        if (
          matchesNeedle(selects[n].options[m].value, lifeDrawingNeedle) ||
          matchesNeedle(selects[n].options[m].text, lifeDrawingNeedle)
        ) {
          return selects[n];
        }
      }
    }

    return null;
  }

  function isLifeDrawingSelected(formRoot) {
    var selectEl;

    if (!formRoot || getFormId(formRoot) !== workshopFormId) {
      return false;
    }

    selectEl = findWorkshopSelect(formRoot);
    if (!selectEl) {
      return false;
    }

    if (matchesNeedle(getSelectValue(selectEl), lifeDrawingNeedle)) {
      return true;
    }

    if (selectEl.selectedIndex >= 0 && selectEl.options[selectEl.selectedIndex]) {
      return matchesNeedle(selectEl.options[selectEl.selectedIndex].text, lifeDrawingNeedle);
    }

    return false;
  }

  function isArtResidencySelected(inputEl) {
    var formRoot = getFormRoot(inputEl);
    var selectEl;

    if (!formRoot) {
      return false;
    }

    if (getFormId(formRoot) === workshopFormId) {
      selectEl = findWorkshopSelect(formRoot);
      return matchesNeedle(getSelectValue(selectEl), residencyNeedle);
    }

    selectEl = formRoot.querySelector('select[name="' + programField + '"]');
    return matchesNeedle(getSelectValue(selectEl), residencyNeedle);
  }

  function shouldDisableDate(date, inputEl) {
    var formRoot = getFormRoot(inputEl);

    if (isClosedDate(date)) {
      return true;
    }

    if (isLifeDrawingSelected(formRoot) && date.getDay() !== LIFE_DRAWING_DAY) {
      return true;
    }

    if (!isArtResidencySelected(inputEl) && date.getDay() === 4) {
      return true;
    }

    return false;
  }

  function themeDisableRule(inputEl) {
    var rule = function (date) {
      return shouldDisableDate(date, inputEl);
    };

    rule._athBookDatesRule = true;
    return rule;
  }

  function mergeDisable(val, inputEl) {
    var list = [];
    var themeRule = themeDisableRule(inputEl);
    var hasTheme = false;
    var j;
    var formRoot = getFormRoot(inputEl);

    if (isLifeDrawingSelected(formRoot)) {
      return [themeRule];
    }

    if (Array.isArray(val)) {
      for (j = 0; j < val.length; j++) {
        if (val[j] && val[j]._athBookDatesRule) {
          hasTheme = true;
          list.push(themeRule);
        } else if (typeof val[j] === 'function') {
          list.push(val[j]);
        }
      }
    } else if (typeof val === 'function') {
      if (val._athBookDatesRule) {
        hasTheme = true;
        list.push(themeRule);
      } else {
        list.push(val);
      }
    }

    if (!hasTheme) {
      list.push(themeRule);
    }

    return list;
  }

  function getDateInputs(formRoot) {
    var scope = formRoot || document;
    var inputs = [];
    var seen = [];
    var candidates;
    var n;

    function pushInput(el) {
      if (!el || seen.indexOf(el) !== -1) {
        return;
      }
      seen.push(el);
      inputs.push(el);
    }

    candidates = scope.querySelectorAll(
      'input[name="datetime"], input[name="booking_date"], .ff-el-datepicker input, input.flatpickr-input, input[type="text"][class*="flatpickr"]'
    );
    for (n = 0; n < candidates.length; n++) {
      pushInput(candidates[n]);
    }

    candidates = scope.querySelectorAll('input');
    for (n = 0; n < candidates.length; n++) {
      if (candidates[n]._flatpickr) {
        pushInput(candidates[n]);
      }
    }

    return inputs;
  }

  function applyPickerRules(inputEl) {
    var fp = inputEl && inputEl._flatpickr;
    var rule;

    if (!fp) {
      return false;
    }

    fp.set('disable', mergeDisable(fp.config.disable, inputEl));

    rule = themeDisableRule(inputEl);
    if (fp.selectedDates && fp.selectedDates[0] && rule(fp.selectedDates[0])) {
      fp.clear();
    }

    if (typeof fp.redraw === 'function') {
      fp.redraw();
    }

    return true;
  }

  function patchFlatpickr(inputEl) {
    var fp = inputEl && inputEl._flatpickr;
    var origSet;
    var origOnOpen;
    var origOnReady;

    if (!fp || fp._athBookDatesPatched) {
      return;
    }

    fp._athBookDatesPatched = true;
    origSet = fp.set.bind(fp);

    fp.set = function (key, val) {
      if (key === 'disable') {
        val = mergeDisable(val, inputEl);
      }
      return origSet(key, val);
    };

    origOnOpen = fp.config.onOpen;
    fp.set('onOpen', function () {
      applyPickerRules(inputEl);
      if (typeof origOnOpen === 'function') {
        origOnOpen.apply(this, arguments);
      } else if (Array.isArray(origOnOpen)) {
        for (var o = 0; o < origOnOpen.length; o++) {
          if (typeof origOnOpen[o] === 'function') {
            origOnOpen[o].apply(this, arguments);
          }
        }
      }
    });

    origOnReady = fp.config.onReady;
    fp.set('onReady', function () {
      applyPickerRules(inputEl);
      if (typeof origOnReady === 'function') {
        origOnReady.apply(this, arguments);
      } else if (Array.isArray(origOnReady)) {
        for (var r = 0; r < origOnReady.length; r++) {
          if (typeof origOnReady[r] === 'function') {
            origOnReady[r].apply(this, arguments);
          }
        }
      }
    });

    applyPickerRules(inputEl);
  }

  function refreshFormDatePickers(formRoot) {
    var inputs = getDateInputs(formRoot);
    var n;

    for (n = 0; n < inputs.length; n++) {
      patchFlatpickr(inputs[n]);
      applyPickerRules(inputs[n]);
    }
  }

  function scanBookPage() {
    var page = document.querySelector('.book-page');
    refreshFormDatePickers(page);
  }

  function bindWorkshopForm(formRoot) {
    var $form = $(formRoot);

    $form.off('.athBookDates');
    $form.on('change.athBookDates', 'select', function () {
      refreshFormDatePickers(formRoot);
    });
    $form.on('reInitExtras.athBookDates', function () {
      setTimeout(function () {
        refreshFormDatePickers(formRoot);
      }, 50);
    });
  }

  function bindForm(formId) {
    $(document.body).on('fluentform_init_' + String(formId), function (event, data) {
      var formRoot = null;

      if (data && data[0]) {
        formRoot = data[0].closest
          ? data[0].closest('form[id^="fluentform_"]') || data[0].closest('.fluentform_wrapper_' + formId)
          : null;
        if (!formRoot && data[0].id && String(data[0].id).indexOf('fluentform_') === 0) {
          formRoot = data[0];
        }
      }

      if (!formRoot) {
        formRoot = document.getElementById('fluentform_' + String(formId));
      }

      if (Number(formId) === workshopFormId && formRoot) {
        bindWorkshopForm(formRoot);
      }

      setTimeout(function () {
        refreshFormDatePickers(formRoot);
      }, 40);
      setTimeout(function () {
        refreshFormDatePickers(formRoot);
      }, 250);
      setTimeout(function () {
        refreshFormDatePickers(formRoot);
      }, 600);
    });
  }

  function tryScan() {
    scanBookPage();
    if (tryScan.attempts < 80) {
      tryScan.attempts += 1;
      setTimeout(tryScan, 150);
    }
  }
  tryScan.attempts = 0;

  $(function () {
    var formIds = cfg.formIds || [];
    for (i = 0; i < formIds.length; i++) {
      bindForm(formIds[i]);
    }
    tryScan();
  });

  document.addEventListener('ff_after_load', function () {
    setTimeout(scanBookPage, 60);
  });
})(jQuery);
