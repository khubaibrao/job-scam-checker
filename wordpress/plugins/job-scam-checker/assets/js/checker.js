(function () {
  'use strict';

  var form = document.querySelector('[data-jsc-checker-form]');
  if (!form) {
    return;
  }

  var textarea = form.querySelector('textarea');
  var counter = form.querySelector('[data-jsc-character-count]');
  var notice = form.querySelector('[data-jsc-phase-notice]');
  var checkButton = form.querySelector('[data-jsc-check-button]');

  function updateCount() {
    counter.textContent = String(textarea.value.length);
  }

  textarea.addEventListener('input', updateCount);
  checkButton.addEventListener('click', function () {
    if (!textarea.reportValidity()) {
      return;
    }
    notice.hidden = false;
    notice.focus();
  });
}());
