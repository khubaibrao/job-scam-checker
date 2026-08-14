(function () {
  'use strict';

  var form = document.querySelector('[data-jsc-checker-form]');
  if (!form) {
    return;
  }

  var textarea = form.querySelector('textarea');
  var counter = form.querySelector('[data-jsc-character-count]');
  var checkButton = form.querySelector('[data-jsc-check-button]');
  var status = form.querySelector('[data-jsc-status]');
  var result = form.closest('.jsc-checker').querySelector('[data-jsc-result]');
  var config = window.JSCCheckerConfig;

  function updateCount() {
    counter.textContent = String(textarea.value.length);
  }

  textarea.addEventListener('input', updateCount);
  function addTextElement(parent, tag, className, text) {
    var element = document.createElement(tag);
    if (className) {
      element.className = className;
    }
    element.textContent = text;
    parent.appendChild(element);
    return element;
  }

  function renderResult(data) {
    result.className = 'jsc-basic-result jsc-basic-result--' + data.level.key;
    result.querySelector('[data-jsc-score]').textContent = String(data.score);
    result.querySelector('[data-jsc-level]').textContent = data.level.label;
    result.querySelector('[data-jsc-message]').textContent = data.level.message;
    result.querySelector('[data-jsc-count]').textContent = data.warning_count + ' ' + config.labels.warningSigns.toLowerCase() + '.';
    result.querySelector('[data-jsc-disclaimer]').textContent = data.disclaimer;

    var detections = result.querySelector('[data-jsc-detections]');
    detections.replaceChildren();
    if (data.detections.length) {
      addTextElement(detections, 'h4', '', config.labels.warningSigns);
      var list = document.createElement('ul');
      list.className = 'jsc-detection-list';
      data.detections.forEach(function (detection) {
        var item = document.createElement('li');
        addTextElement(item, 'strong', '', detection.name);
        addTextElement(item, 'p', '', detection.explanation);
        list.appendChild(item);
      });
      detections.appendChild(list);
    } else {
      addTextElement(detections, 'p', 'jsc-no-warnings', config.labels.noWarnings);
    }

    var domains = result.querySelector('[data-jsc-domains]');
    domains.replaceChildren();
    if (data.suspicious_links.length) {
      addTextElement(domains, 'h4', '', config.labels.domains);
      var domainList = document.createElement('ul');
      data.suspicious_links.forEach(function (link) {
        addTextElement(domainList, 'li', '', link.domain);
      });
      domains.appendChild(domainList);
    }

    result.hidden = false;
    result.focus();
  }

  function errorMessage(payload) {
    if (payload && payload.message) {
      return payload.message;
    }
    return config.labels.error;
  }

  checkButton.addEventListener('click', function () {
    if (!textarea.reportValidity()) {
      return;
    }
    if (!config || !config.endpoint || !config.nonce) {
      status.textContent = 'Checker configuration is unavailable. Refresh the page and try again.';
      return;
    }

    checkButton.disabled = true;
    checkButton.setAttribute('aria-busy', 'true');
    status.textContent = config.labels.checking;
    result.hidden = true;

    fetch(config.endpoint, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-JSC-Nonce': config.nonce
      },
      body: JSON.stringify({ message: textarea.value })
    }).then(function (response) {
      return response.json().then(function (payload) {
        if (!response.ok) {
          throw new Error(errorMessage(payload));
        }
        return payload;
      });
    }).then(function (data) {
      status.textContent = config.labels.verify;
      renderResult(data);
    }).catch(function (error) {
      status.textContent = error.message || config.labels.error;
    }).finally(function () {
      checkButton.disabled = false;
      checkButton.removeAttribute('aria-busy');
    });
  });
}());
