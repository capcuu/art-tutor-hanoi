/**
 * VietQR for Fluent Forms — shared engine (forms 13, 14, 21, 33, 35).
 *
 * Requires #qr_amount and #qr_img in form Custom HTML.
 * Config: window.athFluentQr (config/fluent-qr.php).
 */
(function ($) {
  'use strict';

  var GLOBAL = window.athFluentQr || {};
  var FORMS = GLOBAL.forms || {};
  var BANK_IMG = GLOBAL.bankImage || '';
  var ACCOUNT_NAME = GLOBAL.accountName || '';
  var USD2VND = GLOBAL.usd2vnd || 26250;
  var WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  var started = {};

  function sanitize(s) {
    if (!s) return '';
    s = s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    s = s.replace(/[^a-zA-Z0-9 \-_.]/g, '').trim().replace(/\s+/g, ' ');
    return s.slice(0, 60);
  }

  function findNameInput(container, nameFields) {
    var i, field, el, groups, lb, txt, inp;

    if (nameFields && nameFields.length) {
      for (i = 0; i < nameFields.length; i++) {
        el = container.querySelector('input[name="' + nameFields[i] + '"]');
        if (el) return el;
      }
    }

    groups = container.querySelectorAll('.ff-el-group');
    for (i = 0; i < groups.length; i++) {
      lb = groups[i].querySelector('label');
      txt = (lb && lb.textContent || '').toLowerCase();
      if (txt.indexOf('name') !== -1 || txt.indexOf('parent') !== -1 || txt.indexOf('họ') !== -1 || txt.indexOf('tên') !== -1) {
        inp = groups[i].querySelector('input[type="text"]');
        if (inp) return inp;
      }
    }

    return container.querySelector('input[name="input_text"]');
  }

  function getUsdTotal(container) {
    var tables = container.querySelectorAll('table');
    var i, j, tr, cells, hasTotal, lastText, m, t;

    for (i = 0; i < tables.length; i++) {
      tr = tables[i].querySelectorAll('tr');
      for (j = 0; j < tr.length; j++) {
        cells = Array.from(tr[j].children);
        if (!cells.length) continue;
        hasTotal = cells.some(function (td) {
          return (td.textContent || '').trim().toLowerCase() === 'total';
        });
        if (hasTotal) {
          lastText = (cells[cells.length - 1].textContent || '').trim();
          m = lastText.match(/\$?\s*([\d.,]+)/);
          if (m) return parseFloat(m[1].replace(/,/g, ''));
        }
      }
    }

    t = (container.textContent || '').replace(/\s+/g, ' ').trim();
    m = t.match(/Total[^0-9$]*\$?\s*([\d.,]+)/i);
    return m ? parseFloat(m[1].replace(/,/g, '')) : NaN;
  }

  function buildQR(amountVND, addInfo) {
    var u = new URL(BANK_IMG);
    u.searchParams.set('amount', amountVND);
    u.searchParams.set('addInfo', addInfo);
    u.searchParams.set('accountName', ACCOUNT_NAME);
    u.searchParams.set('cacheBust', Date.now());
    return u.toString();
  }

  function dispatchWeekday(weekdayInput, weekday) {
    if (!weekdayInput || !weekday) return;
    weekdayInput.value = weekday;
    ['change', 'input'].forEach(function (evt) {
      weekdayInput.dispatchEvent(new Event(evt, { bubbles: true }));
    });
  }

  function initQr(formId, formEl) {
    formId = String(formId);
    if (started[formId]) return;

    var cfg = FORMS[formId];
    var container = formEl || document.getElementById('fluentform_' + formId);
    var amountEl, qrImg, nameEl, nameFields, i;

    if (!cfg || !container) return;

    amountEl = container.querySelector('#qr_amount');
    qrImg = container.querySelector('#qr_img');
    if (!amountEl || !qrImg) return;

    started[formId] = true;
    nameFields = cfg.nameFields || [];

    function computeAddInfo() {
      var name = sanitize(findNameInput(container, nameFields) && findNameInput(container, nameFields).value || '');
      return name ? cfg.baseNote + ' - ' + name : cfg.baseNote;
    }

    function updateQR() {
      var amount = (amountEl.textContent.match(/\d/g) || []).join('');
      var spanText;
      if (!amount) return;
      qrImg.src = buildQR(amount, computeAddInfo());
      spanText = container.querySelector('#qr_amount_text');
      if (spanText) {
        spanText.textContent = Number(amount).toLocaleString('vi-VN') + ' VND';
      }
    }

    function refreshAmountUsd() {
      var usd = getUsdTotal(container);
      var vnd;
      if (isNaN(usd)) return;
      vnd = String(Math.round(usd * USD2VND));
      if (amountEl.textContent === vnd) return;
      amountEl.textContent = vnd;
    }

    function refreshAmountWeekday() {
      var dateInput = container.querySelector('[name="booking_date"]');
      var weekdayInput = container.querySelector('[name="booking_weekday"]');
      var amounts = cfg.amounts || {};
      var weekday = weekdayInput && weekdayInput.value ? weekdayInput.value : '';
      var vnd, parts, date;

      if (!weekday && dateInput && dateInput.value) {
        parts = dateInput.value.split('/');
        if (parts.length === 3) {
          date = new Date(Number(parts[2]), Number(parts[1]) - 1, Number(parts[0]));
          if (!isNaN(date.getTime())) weekday = WEEKDAYS[date.getDay()];
        }
      }

      dispatchWeekday(weekdayInput, weekday);
      vnd = amounts[weekday] != null ? amounts[weekday] : amounts.default;
      if (vnd == null) return;
      vnd = String(vnd);
      if (amountEl.textContent === vnd) return;
      amountEl.textContent = vnd;
    }

    function refreshAmount() {
      if (cfg.amountMode === 'booking_weekday') {
        refreshAmountWeekday();
      } else {
        refreshAmountUsd();
      }
    }

    nameEl = findNameInput(container, nameFields);
    if (nameEl) {
      nameEl.addEventListener('input', updateQR);
      nameEl.addEventListener('change', updateQR);
    }

    for (i = 0; i < nameFields.length; i++) {
      nameEl = container.querySelector('input[name="' + nameFields[i] + '"]');
      if (nameEl && nameEl !== findNameInput(container, nameFields)) {
        nameEl.addEventListener('input', updateQR);
        nameEl.addEventListener('change', updateQR);
      }
    }

    if (cfg.amountMode === 'usd_total') {
      container.addEventListener('input', refreshAmount, true);
      container.addEventListener('change', refreshAmount, true);

      var applyBtn = Array.prototype.find.call(
        container.querySelectorAll('button, input[type="button"], input[type="submit"]'),
        function (b) {
          return /apply\s*coupon|apply/i.test((b.textContent || b.value || ''));
        }
      );
      if (applyBtn) {
        applyBtn.addEventListener('click', function () {
          var n = 0;
          var timer = setInterval(function () {
            refreshAmount();
            if (++n > 6) clearInterval(timer);
          }, 300);
        });
      }
    }

    if (cfg.amountMode === 'booking_weekday') {
      var bookingDate = container.querySelector('[name="booking_date"]');
      if (bookingDate) {
        bookingDate.addEventListener('change', function () {
          refreshAmount();
          updateQR();
        });
      }
    }

    new MutationObserver(updateQR).observe(amountEl, {
      childList: true,
      characterData: true,
      subtree: true
    });

    var summaryEl = container.querySelector('.ff_payment_summary, .ff_dynamic_payment_summary');
    if (summaryEl) {
      new MutationObserver(refreshAmount).observe(summaryEl, {
        childList: true,
        subtree: true,
        characterData: true
      });
    }

    refreshAmount();
    updateQR();
  }

  function bindForm(formId) {
    formId = String(formId);
    if (!FORMS[formId]) return;

    $(document.body).on('fluentform_init_' + formId, function (event, data) {
      if (data && data[0]) {
        initQr(formId, data[0]);
      }
    });
  }

  Object.keys(FORMS).forEach(bindForm);

  $(function () {
    Object.keys(FORMS).forEach(function (formId) {
      setTimeout(function () {
        initQr(formId, document.getElementById('fluentform_' + formId));
      }, 120);
    });
  });

  document.addEventListener('ff_after_load', function (e) {
    if (e && e.form_id != null) {
      setTimeout(function () {
        initQr(e.form_id, document.getElementById('fluentform_' + e.form_id));
      }, 60);
    }
  });
})(jQuery);
