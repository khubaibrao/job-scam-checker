(function () {
  'use strict';

  var config = window.JSCCheckerConfig;

  function addTextElement(parent, tag, className, text) {
    var element = document.createElement(tag);
    if (className) {
      element.className = className;
    }
    element.textContent = text;
    parent.appendChild(element);
    return element;
  }

  function isValidResult(data) {
    return data && Number.isInteger(data.score) && data.score >= 0 && data.score <= 100 &&
      data.level && typeof data.level.key === 'string' && typeof data.level.label === 'string' &&
      Array.isArray(data.detections) && Array.isArray(data.suspicious_links) && Array.isArray(data.actions);
  }

  function scrollOptions(block) {
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    return { behavior: reduceMotion ? 'auto' : 'smooth', block: block };
  }

  function initChecker(form) {
    var root = form.closest('.jsc-checker');
    var textarea = form.querySelector('textarea');
    var counter = form.querySelector('[data-jsc-character-count]');
    var checkButton = form.querySelector('[data-jsc-check-button]');
    var status = form.querySelector('[data-jsc-status]');
    var result = root.querySelector('[data-jsc-result]');
    var errorPanel = root.querySelector('[data-jsc-error]');
    var errorText = root.querySelector('[data-jsc-error-message]');
    var retryButton = root.querySelector('[data-jsc-retry]');
    var resetButton = root.querySelector('[data-jsc-reset]');
    var printButton = root.querySelector('[data-jsc-print]');
    var followUp = root.querySelector('[data-jsc-follow-up]');
    var followUpStatus = root.querySelector('[data-jsc-follow-up-status]');
    var purposeWrap = root.querySelector('[data-jsc-purpose-wrap]');
    var purpose = root.querySelector('[data-jsc-purpose]');
    var collectionToken = '';

    function updateCount() {
      counter.textContent = String(textarea.value.length);
    }

    function setBusy(isBusy) {
      checkButton.disabled = isBusy;
      form.setAttribute('aria-busy', String(isBusy));
      if (isBusy) {
        checkButton.setAttribute('aria-busy', 'true');
      } else {
        checkButton.removeAttribute('aria-busy');
      }
    }

    function showError(message) {
      result.hidden = true;
      errorText.textContent = message;
      errorPanel.hidden = false;
      status.textContent = '';
      errorPanel.focus({ preventScroll: true });
      errorPanel.scrollIntoView(scrollOptions('nearest'));
    }

    function renderDetections(data) {
      var container = result.querySelector('[data-jsc-detections]');
      container.replaceChildren();
      addTextElement(container, 'h4', 'jsc-section-title', config.labels.warningSigns);

      if (!data.detections.length) {
        addTextElement(container, 'p', 'jsc-no-warnings', config.labels.noWarnings);
        return;
      }

      var list = document.createElement('ol');
      list.className = 'jsc-detection-list';
      data.detections.forEach(function (detection) {
        var item = document.createElement('li');
        item.className = 'jsc-detection-card';
        var heading = document.createElement('h5');
        var icon = addTextElement(heading, 'span', 'jsc-detection-card__icon', '!');
        icon.setAttribute('aria-hidden', 'true');
        heading.appendChild(document.createTextNode(detection.name));
        item.appendChild(heading);

        var details = document.createElement('dl');
        var whyTerm = addTextElement(details, 'dt', '', config.labels.whyItMatters);
        addTextElement(details, 'dd', '', detection.explanation);
        var actionTerm = addTextElement(details, 'dt', '', config.labels.whatToDo);
        addTextElement(details, 'dd', '', detection.recommendation);
        whyTerm.className = 'jsc-detail-label';
        actionTerm.className = 'jsc-detail-label';
        item.appendChild(details);
        list.appendChild(item);
      });
      container.appendChild(list);
    }

    function renderDomains(data) {
      var container = result.querySelector('[data-jsc-domains]');
      container.replaceChildren();
      container.hidden = !data.suspicious_links.length;
      if (!data.suspicious_links.length) {
        return;
      }

      addTextElement(container, 'h4', 'jsc-section-title', config.labels.domains);
      addTextElement(container, 'p', 'jsc-section-intro', 'These domains are shown as plain text for safety. Do not copy or open them until you verify the employer independently.');
      var list = document.createElement('ul');
      list.className = 'jsc-domain-list';
      data.suspicious_links.forEach(function (link) {
        var item = document.createElement('li');
        var domain = addTextElement(item, 'code', 'jsc-domain-name', link.domain);
        domain.setAttribute('dir', 'ltr');
        var reasons = document.createElement('ul');
        reasons.className = 'jsc-domain-reasons';
        link.reasons.forEach(function (reason) {
          addTextElement(reasons, 'li', '', reason);
        });
        item.appendChild(reasons);
        list.appendChild(item);
      });
      container.appendChild(list);
    }

    function renderActions(data) {
      var container = result.querySelector('[data-jsc-actions]');
      container.replaceChildren();
      addTextElement(container, 'h4', 'jsc-section-title', config.labels.recommended);
      var list = document.createElement('ul');
      list.className = 'jsc-action-list';
      data.actions.forEach(function (action) {
        var item = document.createElement('li');
        addTextElement(item, 'strong', '', action.title);
        addTextElement(item, 'p', '', action.description);
        list.appendChild(item);
      });
      container.appendChild(list);
    }

    function renderResult(data) {
      var countLabel = data.warning_count === 1 ? config.labels.warningSingular : config.labels.warningPlural;
      result.className = 'jsc-result jsc-result--' + data.level.key;
      result.querySelector('[data-jsc-score]').textContent = String(data.score);
      result.querySelector('[data-jsc-progress]').value = data.score;
      result.querySelector('[data-jsc-progress]').textContent = data.score + ' out of 100';
      result.querySelector('[data-jsc-progress]').setAttribute('aria-valuetext', data.score + ' out of 100, ' + data.level.label);
      result.querySelector('[data-jsc-level]').textContent = data.level.label;
      result.querySelector('[data-jsc-message]').textContent = data.level.message;
      result.querySelector('[data-jsc-count]').textContent = data.warning_count + ' ' + countLabel + '.';
      result.querySelector('[data-jsc-disclaimer]').textContent = data.disclaimer;
      result.style.setProperty('--jsc-score', String(data.score));

      renderDetections(data);
      renderDomains(data);
      renderActions(data);

      collectionToken = typeof data.collection_token === 'string' ? data.collection_token : '';
      followUp.hidden = !(config.followUpEnabled && collectionToken);
      followUpStatus.textContent = '';

      errorPanel.hidden = true;
      result.hidden = false;
      status.textContent = config.labels.resultReady + ' ' + data.level.label + ', score ' + data.score + ' out of 100, ' + data.warning_count + ' ' + countLabel + '.';
      result.focus({ preventScroll: true });
      result.scrollIntoView(scrollOptions('start'));
    }

    function parseResponse(response) {
      return response.text().then(function (body) {
        var payload;
        try {
          payload = JSON.parse(body);
        } catch (ignore) {
          throw new Error(config.labels.error);
        }
        if (!response.ok) {
          throw new Error(payload && payload.message ? payload.message : config.labels.error);
        }
        if (!isValidResult(payload)) {
          throw new Error(config.labels.error);
        }
        return payload;
      });
    }

    function runCheck() {
      if (!textarea.reportValidity()) {
        return;
      }
      if (!config || !config.endpoint || !config.nonce) {
        showError(config && config.labels ? config.labels.configuration : 'Checker configuration is unavailable. Refresh the page and try again.');
        return;
      }

      setBusy(true);
      errorPanel.hidden = true;
      result.hidden = true;
      status.textContent = config.labels.checking;

      var controller = typeof AbortController === 'function' ? new AbortController() : null;
      var timeout = controller ? window.setTimeout(function () { controller.abort(); }, 20000) : null;
      var requestOptions = {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-JSC-Nonce': config.nonce
        },
        body: JSON.stringify({ message: textarea.value })
      };
      if (controller) {
        requestOptions.signal = controller.signal;
      }

      fetch(config.endpoint, requestOptions).then(parseResponse).then(renderResult).catch(function (error) {
        showError(error.message || config.labels.error);
      }).finally(function () {
        if (timeout) {
          window.clearTimeout(timeout);
        }
        setBusy(false);
      });
    }

    textarea.addEventListener('input', updateCount);
    checkButton.addEventListener('click', runCheck);
    retryButton.addEventListener('click', runCheck);
    printButton.addEventListener('click', function () { window.print(); });
    resetButton.addEventListener('click', function () {
      result.hidden = true;
      errorPanel.hidden = true;
      textarea.value = '';
      updateCount();
      status.textContent = '';
      followUp.hidden = true;
      followUp.reset();
      purposeWrap.hidden = true;
      purpose.required = false;
      collectionToken = '';
      textarea.focus();
      textarea.scrollIntoView(scrollOptions('center'));
    });

    followUp.querySelectorAll('[data-jsc-money]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        purposeWrap.hidden = radio.value !== 'yes';
        purpose.required = radio.value === 'yes';
        if (radio.value !== 'yes') { purpose.value = ''; }
      });
    });

    followUp.addEventListener('submit', function (event) {
      event.preventDefault();
      if (!followUp.reportValidity() || !collectionToken) { return; }
      var selectedMoney = followUp.querySelector('[data-jsc-money]:checked');
      var submit = followUp.querySelector('button[type="submit"]');
      submit.disabled = true;
      fetch(config.followUpEndpoint, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-JSC-Nonce': config.nonce },
        body: JSON.stringify({ token: collectionToken, channel: followUp.querySelector('[data-jsc-channel]').value, money_requested: selectedMoney ? selectedMoney.value : '', payment_purpose: purpose.value })
      }).then(function (response) {
        if (!response.ok) { throw new Error(config.labels.followUpError); }
        collectionToken = '';
        followUp.querySelectorAll('select, input, button').forEach(function (control) { control.disabled = true; });
        followUpStatus.textContent = config.labels.followUpThanks;
      }).catch(function () { followUpStatus.textContent = config.labels.followUpError; submit.disabled = false; });
    });
  }

  document.querySelectorAll('[data-jsc-checker-form]').forEach(initChecker);
}());
